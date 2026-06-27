<?php

return [
    'base_url' => env('API_BASE_URL', 'http://127.0.0.1:8000/api/v1'),
    'timeout' => env('API_TIMEOUT', 10),
    'retries' => env('API_RETRIES', 3),
];
