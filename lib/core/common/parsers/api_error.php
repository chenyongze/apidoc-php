<?php
namespace api\parsers;

use core\File;

// todo 这里有问题，没有加单例，而且会造成变量有污染
$apiParser = File::load('./api_param.php');
return [
    'parse'         => function($content, $source) use ($apiParser) {
        return $apiParser['parse']($content, $source, 'Error 4xx');
    },
    'path'          => 'local.error.fields.',
    'method'        => $apiParser['method'],
    'markdownFields'=> ['description', 'type'],
    'markdownRemovePTags'=> ['type']
];
