<?php
namespace api\parsers;

use core\common\Util;

return [
    'parse' => function($content, $source) {
        $content = str_replace('\n', "\n", $content);
        $source  = trim(str_replace('\n', "\n", $source));

        // Search for @apiExample "[{type}] title and content
        // /^(@\w*)?\s?(?:(?:\{(.+?)\})\s*)?(.*)$/gm;
        $parseRegExpFirstLine = '/(@\w*)?(?:(?:\s*\{\s*([a-zA-Z0-9\.\/\\\[\]_-]+)\s*\}\s*)?\s*(.*)?)?/';
        $parseRegExpFollowing = '/(^.*\s?)/m';

        preg_match($parseRegExpFirstLine, $source, $matches);
        list($text, $type, $title) = ['', $matches[2] ?: '', $matches[3] ?: ''];

        preg_match_all($parseRegExpFollowing, $content, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $text .= $match[1];
        }

        if (empty($text)) {
            return null;
        }

        return [
            'title'   => $title,
            'type'    => $type ?: 'json',
            // todo 这里因为unindent，所以跟原有的数据有所差别
            'content' => Util::unindent($text)
        ];
    },

    'path'   => 'local.examples',
    'method' => 'push'
];