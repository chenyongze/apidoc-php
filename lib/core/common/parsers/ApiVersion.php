<?php
return [
    'parse' => function($content) {
        $name = trim($content);

        if (empty($name)) {
            return null;
        }

        // todo version check

        return [
            'version' => preg_replace('/(\s+)/', '_', $content)
        ];
    },

    'path'       => 'local',
    'method'     => 'insert',
    'extendRoot' => true
];