<?php
return [
    'parse' => function($content) {
        // Search: type, url and title
        // Example: {get} /user/:id Get User by ID.
        preg_match('/^(?:(?:\{(.+?)\})?\s*)?(.+?)(?:\s+(.+?))?$/', trim($content), $matches);

        if (empty($matches)) {
            return null;
        }
        return [
            'type'  => $matches[1],
            'url'   => $matches[2],
            'title' => $matches[3] ?: ''
        ];
    },

    'path'   => 'local',
    'method' => 'insert'
];
