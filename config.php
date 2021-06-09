<?php

use Illuminate\Support\Str;

return [
    'production' => false,
    'baseUrl' => 'localhost:3000',
    'title' => 'Tony\'s Blog',
    'description' => 'Personal Website and Blog.',
    'collections' => [
        'posts' => [
            'path' => function ($page) {
                return $page->permalink ?: Str::slug($page->title);
            },
            'author' => 'Tony Messias',
            'sort' => '-date',
        ],
    ],
];
