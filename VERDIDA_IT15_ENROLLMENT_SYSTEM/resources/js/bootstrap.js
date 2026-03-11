import axios from 'axios';
window.axios = axios;

window.axios.defaults.baseURL = import.meta.env.VITE_API_URL || 'https://VERDIDA_IT15_ENROLLMENT_SYSTEM.test/api';
window.axios.defaults.headers.common.Accept = 'application/json';
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const apiKey = import.meta.env.VITE_API_KEY;
const apiKeyHeader = import.meta.env.VITE_API_KEY_HEADER || 'X-API-KEY';

if (apiKey) {
	window.axios.defaults.headers.common[apiKeyHeader] = apiKey;
}
