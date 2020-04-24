<?php
declare(strict_types=1);

return [
    'host' => 'http://localhost:3002',
    'urlKey' => 'page',
    'useURLFriendly' => true, // This configuration should combine with **handlerUrl**
    'handlerUrl' => function () {
        \preg_match('/\/page\/(\d+)/', $_SERVER['REQUEST_URI'], $matches);
        return $matches[1] ?? 1;
    },
    // 'handlerUrl' => function () {
    //     return \filter_input(INPUT_GET, 'page', FILTER_SANITIZE_SPECIAL_CHARS);
    // },
    'max_per_page' => 3,
    'links' => [
        'max_links' => 4,
        'labels' => [
            'first_page' => 'first',
            'next_page' => 'next',
            'last_page' => 'last',
        ],

    ],
    'show_first_last_page' => false,
];
