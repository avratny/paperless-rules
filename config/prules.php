<?php

return [
    'paperless' => [
        'url' => env('PAPERLESS_URL', 'http://localhost:8010'),
        'api_key' => env('PAPERLESS_API_KEY', 'A_VERY_SECRET_KEY'),
    ],

    'limits' => [
        'max_dsl_length' => env('PRULES_MAX_DSL_LENGTH', 10000),
        'max_let_count' => env('PRULES_MAX_LET_COUNT', 50),
        'max_do_count' => env('PRULES_MAX_DO_COUNT', 50),
        'max_nesting_depth' => env('PRULES_MAX_NESTING_DEPTH', 10),
    ],
];
