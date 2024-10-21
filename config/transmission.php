<?php

return [
    'host' => env('TRANSMISSION_HOST', 'localhost'),
    'port' => env('TRANSMISSION_PORT', 9091),
    'path' => env('TRANSMISSION_PATH', '/transmission/rpc'),
    'username' => env('TRANSMISSION_USERNAME', ''),
    'password' => env('TRANSMISSION_PASSWORD', ''),
];