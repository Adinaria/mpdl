<?php

/**
 *  Конфиг отвечает за кеширование сущностей
 */
return [
    'role' => [
        'mode'       => env('CACHE_ENTITY_ROLE', true),
        'cache_keys' => [
            'list'   => 'role-list',
            'entity' => 'role-', // role - $uuid
        ],
    ],
    'user' => [
        'mode'   => env('CACHE_ENTITY_USER', true),
        'cache_keys' => [
            'list'   => 'user-list',
            'entity' => 'user-', // user - $uuid
        ],
    ],
];
