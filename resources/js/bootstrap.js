import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-with'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Aquí activamos y configuramos Laravel Echo.
// Le decimos que use Pusher como broadcaster y le pasamos las credenciales
// y la dirección de nuestro servidor Soketi que configuramos en el archivo .env
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    wsHost: import.meta.env.VITE_PUSHER_HOST,
    wsPort: import.meta.env.VITE_PUSHER_PORT,
    wssPort: import.meta.env.VITE_PUSHER_PORT,
    forceTLS: false,
    disableStats: true,
    enabledTransports: ['ws', 'wss'],
});