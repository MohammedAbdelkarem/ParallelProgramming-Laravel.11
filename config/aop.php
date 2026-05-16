<?php

return [

    'performance_monitoring' => [
        'enabled'         => env('AOP_PERFORMANCE_MONITORING_ENABLED', true),

        'slow_request_ms' => env('AOP_SLOW_REQUEST_MS', 500),

        'log_channel'     => env('AOP_PERFORMANCE_LOG_CHANNEL', 'performance'),
    ],

];
