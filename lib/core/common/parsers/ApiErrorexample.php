<?php
// Same as @ApiExample
namespace api\parsers;

use core\Loader;

$apiParser = Loader::instance('Parsers', NS_CORE)['ApiExample'];

return [
    'parse'  => $apiParser['parse'],
    'path'   => 'local.error.examples',
    'method' => $apiParser['method']
];