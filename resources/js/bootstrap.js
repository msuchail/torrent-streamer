
/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });

import videojs from "video.js";
window.videojs = videojs;


import "video.js/dist/video-js.css";
import "@silvermine/videojs-chromecast"
import '@videojs/themes/dist/fantasy/index.css';


import Swal from "sweetalert2/dist/sweetalert2.min.js";
import "@sweetalert2/theme-dark/dark.css";
window.Swal = Swal;
