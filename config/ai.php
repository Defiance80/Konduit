<?php

return [
    'anthropic' => [
        'api_key' => env('ANTHROPIC_API_KEY'),
        'model'   => env('ANTHROPIC_MODEL', 'claude-sonnet-4-6'),
        'base_url'=> 'https://api.anthropic.com/v1',
        'version' => '2023-06-01',
        'timeout' => 60,
    ],
];
