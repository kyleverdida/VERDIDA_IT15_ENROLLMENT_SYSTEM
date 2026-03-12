<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class WeatherController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'city' => ['nullable', 'string', 'max:120'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lon' => ['nullable', 'numeric', 'between:-180,180'],
            'days' => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        $hasCity = !empty($validated['city']);
        $hasCoordinates = isset($validated['lat'], $validated['lon']);

        if (!$hasCity && !$hasCoordinates) {
            return response()->json([
                'message' => 'Please provide either city or both lat and lon.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $days = (int) ($validated['days'] ?? 5);
        $query = $hasCoordinates
            ? (string) $validated['lat'] . ',' . (string) $validated['lon']
            : trim((string) $validated['city']);

        $cacheKey = $this->cacheKey($query, $days);
        $staleKey = $cacheKey . ':stale';

        $freshPayload = Cache::get($cacheKey);
        if (is_array($freshPayload)) {
            return response()->json($freshPayload);
        }

        $apiKey = (string) config('services.weather.key', '');
        if ($apiKey === '') {
            $openMeteoPayload = $this->fetchFromOpenMeteo($query, $days, $hasCoordinates);

            if ($openMeteoPayload !== null) {
                $freshTtl = max(1, (int) config('services.weather.cache_ttl_minutes', 10));
                $staleTtl = max($freshTtl, (int) config('services.weather.stale_ttl_minutes', 180));

                Cache::put($cacheKey, $openMeteoPayload, now()->addMinutes($freshTtl));
                Cache::put($staleKey, $openMeteoPayload, now()->addMinutes($staleTtl));

                return response()->json($openMeteoPayload);
            }

            return $this->fromStaleOrError(
                stalePayload: Cache::get($staleKey),
                message: 'Weather service is unavailable. Set WEATHER_API_KEY in backend .env to use WeatherAPI.',
                status: Response::HTTP_BAD_GATEWAY,
            );
        }

        $baseUrl = rtrim((string) config('services.weather.base_url', 'https://api.weatherapi.com/v1'), '/');

        try {
            $upstream = Http::acceptJson()
                ->timeout(10)
                ->get($baseUrl . '/forecast.json', [
                    'key' => $apiKey,
                    'q' => $query,
                    'days' => $days,
                    'aqi' => 'no',
                    'alerts' => 'no',
                ]);
        } catch (\Throwable $e) {
            Log::warning('WeatherAPI request failed', [
                'message' => $e->getMessage(),
                'query' => $query,
                'days' => $days,
            ]);

            return $this->fromStaleOrError(
                stalePayload: Cache::get($staleKey),
                message: 'Weather service is currently unavailable. Please try again later.',
                status: Response::HTTP_BAD_GATEWAY,
            );
        }

        if ($upstream->status() === Response::HTTP_TOO_MANY_REQUESTS) {
            return $this->fromStaleOrError(
                stalePayload: Cache::get($staleKey),
                message: 'Weather service rate limit reached. Please retry shortly.',
                status: Response::HTTP_TOO_MANY_REQUESTS,
            );
        }

        if ($upstream->failed()) {
            $status = $upstream->status() >= 400 ? $upstream->status() : Response::HTTP_BAD_GATEWAY;
            $message = $status === Response::HTTP_BAD_REQUEST
                ? 'Invalid weather location query.'
                : 'Unable to fetch weather data at the moment.';

            return $this->fromStaleOrError(
                stalePayload: Cache::get($staleKey),
                message: $message,
                status: $status,
            );
        }

        $data = $upstream->json();
        $payload = $this->transformWeatherPayload($data, $query, $days);

        $freshTtl = max(1, (int) config('services.weather.cache_ttl_minutes', 10));
        $staleTtl = max($freshTtl, (int) config('services.weather.stale_ttl_minutes', 180));

        Cache::put($cacheKey, $payload, now()->addMinutes($freshTtl));
        Cache::put($staleKey, $payload, now()->addMinutes($staleTtl));

        return response()->json($payload);
    }

    private function transformWeatherPayload(array $data, string $query, int $days): array
    {
        $current = $data['current'] ?? [];
        $location = $data['location'] ?? [];
        $forecastDays = $data['forecast']['forecastday'] ?? [];

        return [
            'data' => [
                'location' => [
                    'name' => (string) ($location['name'] ?? ''),
                    'region' => (string) ($location['region'] ?? ''),
                    'country' => (string) ($location['country'] ?? ''),
                    'lat' => (float) ($location['lat'] ?? 0),
                    'lon' => (float) ($location['lon'] ?? 0),
                    'localtime' => (string) ($location['localtime'] ?? ''),
                ],
                'current' => [
                    'temperature_c' => (float) ($current['temp_c'] ?? 0),
                    'temperature_f' => (float) ($current['temp_f'] ?? 0),
                    'humidity' => (int) ($current['humidity'] ?? 0),
                    'wind_kph' => (float) ($current['wind_kph'] ?? 0),
                    'wind_mps' => round(((float) ($current['wind_kph'] ?? 0)) / 3.6, 2),
                    'condition' => (string) ($current['condition']['text'] ?? ''),
                    'icon' => $this->normalizeIcon((string) ($current['condition']['icon'] ?? '')),
                    'last_updated' => (string) ($current['last_updated'] ?? ''),
                ],
                'forecast' => collect($forecastDays)->map(function (array $item): array {
                    $day = $item['day'] ?? [];
                    return [
                        'date' => (string) ($item['date'] ?? ''),
                        'max_temp_c' => (float) ($day['maxtemp_c'] ?? 0),
                        'min_temp_c' => (float) ($day['mintemp_c'] ?? 0),
                        'avg_temp_c' => (float) ($day['avgtemp_c'] ?? 0),
                        'humidity' => (int) ($day['avghumidity'] ?? 0),
                        'max_wind_kph' => (float) ($day['maxwind_kph'] ?? 0),
                        'condition' => (string) ($day['condition']['text'] ?? ''),
                        'icon' => $this->normalizeIcon((string) ($day['condition']['icon'] ?? '')),
                    ];
                })->values()->all(),
            ],
            'meta' => [
                'provider' => 'weatherapi',
                'query' => $query,
                'days' => $days,
                'cached' => false,
                'stale' => false,
            ],
        ];
    }

    private function fetchFromOpenMeteo(string $query, int $days, bool $hasCoordinates): ?array
    {
        $lat = null;
        $lon = null;
        $locationName = '';
        $region = '';
        $country = '';

        try {
            if ($hasCoordinates) {
                [$latRaw, $lonRaw] = array_pad(explode(',', $query), 2, null);
                $lat = is_numeric($latRaw) ? (float) $latRaw : null;
                $lon = is_numeric($lonRaw) ? (float) $lonRaw : null;
                $locationName = 'Selected location';
            } else {
                $searchCandidates = array_values(array_unique(array_filter([
                    trim($query),
                    str_ireplace(' City', '', trim($query)),
                    str_ireplace(', Philippines', '', trim($query)),
                ])));

                $result = null;
                foreach ($searchCandidates as $candidate) {
                    $geo = Http::acceptJson()
                        ->timeout(10)
                        ->get('https://geocoding-api.open-meteo.com/v1/search', [
                            'name' => $candidate,
                            'count' => 1,
                            'language' => 'en',
                            'format' => 'json',
                        ]);

                    if ($geo->failed()) {
                        Log::warning('Open-Meteo geocoding request failed', [
                            'status' => $geo->status(),
                            'body' => $geo->body(),
                            'query' => $query,
                            'candidate' => $candidate,
                        ]);

                        continue;
                    }

                    $result = data_get($geo->json(), 'results.0');
                    if (is_array($result)) {
                        break;
                    }
                }

                if (!is_array($result)) {
                    $manual = $this->resolveManualCoordinates($query);
                    if ($manual !== null) {
                        $result = $manual;
                    }
                }

                if (!is_array($result)) {
                    Log::warning('Open-Meteo geocoding returned no results', [
                        'query' => $query,
                        'candidates' => $searchCandidates,
                    ]);
                    return null;
                }

                $lat = (float) ($result['latitude'] ?? 0);
                $lon = (float) ($result['longitude'] ?? 0);
                $locationName = (string) ($result['name'] ?? $query);
                $region = (string) ($result['admin1'] ?? '');
                $country = (string) ($result['country'] ?? '');
            }

            if (!is_float($lat) || !is_float($lon)) {
                Log::warning('Open-Meteo coordinates invalid after parsing', [
                    'query' => $query,
                    'lat' => $lat,
                    'lon' => $lon,
                ]);
                return null;
            }

            $forecast = Http::acceptJson()
                ->timeout(10)
                ->get('https://api.open-meteo.com/v1/forecast', [
                    'latitude' => $lat,
                    'longitude' => $lon,
                    'timezone' => 'auto',
                    'forecast_days' => $days,
                    'wind_speed_unit' => 'kmh',
                    'current' => 'temperature_2m,relative_humidity_2m,wind_speed_10m,weather_code,is_day,time',
                    'daily' => 'weather_code,temperature_2m_max,temperature_2m_min',
                ]);

            if ($forecast->failed()) {
                Log::warning('Open-Meteo forecast request failed', [
                    'status' => $forecast->status(),
                    'body' => $forecast->body(),
                    'lat' => $lat,
                    'lon' => $lon,
                    'days' => $days,
                ]);
                return null;
            }

            $data = $forecast->json();
            $current = (array) ($data['current'] ?? []);
            $daily = (array) ($data['daily'] ?? []);
            $times = (array) ($daily['time'] ?? []);
            $maxTemps = (array) ($daily['temperature_2m_max'] ?? []);
            $minTemps = (array) ($daily['temperature_2m_min'] ?? []);
            $codes = (array) ($daily['weather_code'] ?? []);

            $isDay = ((int) ($current['is_day'] ?? 1)) === 1;
            $currentMap = $this->mapOpenMeteoCode((int) ($current['weather_code'] ?? 0), $isDay);

            $forecastItems = [];
            foreach ($times as $index => $date) {
                $forecastMap = $this->mapOpenMeteoCode((int) ($codes[$index] ?? 0), true);
                $maxTemp = (float) ($maxTemps[$index] ?? 0);
                $minTemp = (float) ($minTemps[$index] ?? 0);

                $forecastItems[] = [
                    'date' => (string) $date,
                    'max_temp_c' => $maxTemp,
                    'min_temp_c' => $minTemp,
                    'avg_temp_c' => round(($maxTemp + $minTemp) / 2, 1),
                    'humidity' => 0,
                    'max_wind_kph' => 0,
                    'condition' => $forecastMap['condition'],
                    'icon' => $forecastMap['icon'],
                ];
            }

            return [
                'data' => [
                    'location' => [
                        'name' => $locationName,
                        'region' => $region,
                        'country' => $country,
                        'lat' => $lat,
                        'lon' => $lon,
                        'localtime' => (string) ($current['time'] ?? ''),
                    ],
                    'current' => [
                        'temperature_c' => (float) ($current['temperature_2m'] ?? 0),
                        'temperature_f' => round((((float) ($current['temperature_2m'] ?? 0)) * 9 / 5) + 32, 1),
                        'humidity' => (int) ($current['relative_humidity_2m'] ?? 0),
                        'wind_kph' => (float) ($current['wind_speed_10m'] ?? 0),
                        'wind_mps' => round(((float) ($current['wind_speed_10m'] ?? 0)) / 3.6, 2),
                        'condition' => $currentMap['condition'],
                        'icon' => $currentMap['icon'],
                        'last_updated' => (string) ($current['time'] ?? ''),
                    ],
                    'forecast' => $forecastItems,
                ],
                'meta' => [
                    'provider' => 'open-meteo',
                    'query' => $query,
                    'days' => $days,
                    'cached' => false,
                    'stale' => false,
                    'warning' => 'Using fallback weather provider because WEATHER_API_KEY is not configured.',
                ],
            ];
        } catch (\Throwable $e) {
            Log::warning('Open-Meteo fallback failed', [
                'message' => $e->getMessage(),
                'query' => $query,
                'days' => $days,
                'has_coordinates' => $hasCoordinates,
            ]);

            return null;
        }
    }

    private function resolveManualCoordinates(string $query): ?array
    {
        $key = Str::lower(trim($query));

        $manualLocations = [
            'tagum' => ['name' => 'Tagum', 'latitude' => 7.4475, 'longitude' => 125.8092, 'admin1' => 'Davao Region', 'country' => 'Philippines'],
            'tagum city' => ['name' => 'Tagum', 'latitude' => 7.4475, 'longitude' => 125.8092, 'admin1' => 'Davao Region', 'country' => 'Philippines'],
            'davao' => ['name' => 'Davao City', 'latitude' => 7.0731, 'longitude' => 125.6128, 'admin1' => 'Davao Region', 'country' => 'Philippines'],
            'davao city' => ['name' => 'Davao City', 'latitude' => 7.0731, 'longitude' => 125.6128, 'admin1' => 'Davao Region', 'country' => 'Philippines'],
        ];

        return $manualLocations[$key] ?? null;
    }

    private function mapOpenMeteoCode(int $code, bool $isDay): array
    {
        $dayIcon = 'https://cdn.weatherapi.com/weather/64x64/day/113.png';
        $nightIcon = 'https://cdn.weatherapi.com/weather/64x64/night/113.png';

        $map = [
            0 => ['condition' => 'Clear sky', 'day' => '113', 'night' => '113'],
            1 => ['condition' => 'Mainly clear', 'day' => '116', 'night' => '116'],
            2 => ['condition' => 'Partly cloudy', 'day' => '116', 'night' => '116'],
            3 => ['condition' => 'Overcast', 'day' => '122', 'night' => '122'],
            45 => ['condition' => 'Fog', 'day' => '248', 'night' => '248'],
            48 => ['condition' => 'Depositing rime fog', 'day' => '248', 'night' => '248'],
            51 => ['condition' => 'Light drizzle', 'day' => '296', 'night' => '296'],
            53 => ['condition' => 'Moderate drizzle', 'day' => '296', 'night' => '296'],
            55 => ['condition' => 'Dense drizzle', 'day' => '296', 'night' => '296'],
            61 => ['condition' => 'Slight rain', 'day' => '176', 'night' => '176'],
            63 => ['condition' => 'Moderate rain', 'day' => '308', 'night' => '308'],
            65 => ['condition' => 'Heavy rain', 'day' => '308', 'night' => '308'],
            71 => ['condition' => 'Slight snow fall', 'day' => '326', 'night' => '326'],
            73 => ['condition' => 'Moderate snow fall', 'day' => '326', 'night' => '326'],
            75 => ['condition' => 'Heavy snow fall', 'day' => '338', 'night' => '338'],
            80 => ['condition' => 'Rain showers', 'day' => '353', 'night' => '353'],
            81 => ['condition' => 'Moderate rain showers', 'day' => '356', 'night' => '356'],
            82 => ['condition' => 'Violent rain showers', 'day' => '359', 'night' => '359'],
            95 => ['condition' => 'Thunderstorm', 'day' => '389', 'night' => '389'],
            96 => ['condition' => 'Thunderstorm with slight hail', 'day' => '392', 'night' => '392'],
            99 => ['condition' => 'Thunderstorm with heavy hail', 'day' => '395', 'night' => '395'],
        ];

        $entry = $map[$code] ?? ['condition' => 'Unknown', 'day' => '116', 'night' => '116'];
        $iconCode = $isDay ? $entry['day'] : $entry['night'];
        $icon = $isDay
            ? "https://cdn.weatherapi.com/weather/64x64/day/{$iconCode}.png"
            : "https://cdn.weatherapi.com/weather/64x64/night/{$iconCode}.png";

        if ($iconCode === '113') {
            $icon = $isDay ? $dayIcon : $nightIcon;
        }

        return [
            'condition' => $entry['condition'],
            'icon' => $icon,
        ];
    }

    private function fromStaleOrError(mixed $stalePayload, string $message, int $status): JsonResponse
    {
        if (is_array($stalePayload)) {
            $stalePayload['meta']['cached'] = true;
            $stalePayload['meta']['stale'] = true;
            $stalePayload['meta']['warning'] = $message;

            return response()->json($stalePayload);
        }

        return response()->json(['message' => $message], $status);
    }

    private function cacheKey(string $query, int $days): string
    {
        return 'weather:' . Str::lower(trim($query)) . ':days:' . $days;
    }

    private function normalizeIcon(string $icon): string
    {
        if ($icon === '') {
            return '';
        }

        if (str_starts_with($icon, '//')) {
            return 'https:' . $icon;
        }

        return $icon;
    }
}
