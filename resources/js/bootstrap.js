import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
// Real-time broadcasting can be configured here if needed (Echo/Pusher)
