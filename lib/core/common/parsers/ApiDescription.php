<?php
return [
    'parse' => function($content) {
        $description = trim($content);

        if (empty($description)) {
            return null;
        }

        // todo add `unindent` func
        return [
            'description' => $description
        ];
    },

    'path'           => 'local',
    'method'         => 'insert',
    'markdownFields' => ['description']
];
