<?php
namespace api\parsers;

use core\Loader;

$apiParser = Loader::instance('Parsers', NS_CORE)['api_param'];

return [
    'parse'         => function($content, $source) use ($apiParser) {
        return $apiParser['parse']($content, $source, 'Error 4xx');
    },
    'path'          => 'local.error.fields.',
    'method'        => $apiParser['method'],
    'markdownFields'=> ['description', 'type'],
    'markdownRemovePTags'=> ['type']
];
