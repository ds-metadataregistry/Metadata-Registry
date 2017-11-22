<?php

return [
    'enabled' => env('APP_TRACY'),
    'showBar' => env('APP_TRACY'),
    'accepts' => [
        'text/html',
    ],
    'appendTo' => 'body',
    'editor' => 'phpstorm://open?file=%file&line=%line',
    'maxDepth' => 4,
    'maxLength' => 1000,
    'scream' => true,
    'showLocation' => true,
    'strictMode' => true,
    'editorMapping' => [],
    'panels' => [
        'routing' => true,
        'database' => true,
        'view' => true,
        'event' => false,
        'session' => true,
        'request' => true,
        'auth' => true,
        'html-validator' => true,
        'terminal' => true,
    ],
];
