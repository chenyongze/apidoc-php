<?php
namespace api\parsers;

use core\Loader;

$apiParser = Loader::instance('Parsers', NS_CORE)['apiparam'];

return [
    'parse'                 => function($content, $source) use ($apiParser) {
        return $apiParser['parse']($content, $source, 'Success 200');
    },
    'path'                  => function() use ($apiParser) {
        return 'local.success.fields.' . $apiParser['getGroup']();
    },
    'method'                => $apiParser['method'],
    'markdownFields'        => ['description', 'type'],
    'markdownRemovePTags'   => ['type']
];