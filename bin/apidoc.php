#!/usr/bin/env php
<?php
namespace core;

define('ROOT_PATH', dirname(__DIR__));

require ROOT_PATH . '/base.php';
require CORE_PATH . DS . 'Loader.php';

Loader::addNamespace(NS_API, LIB_PATH);
Loader::addNamespace(NS_CORE, CORE_PATH);
Loader::register();

$argv = Loader::instance('api\core\Nomnom')
                ->option('file-filters', [
                    'abbr' => 'f', 'default' => '.*\\.(clj|coffee|cpp|cs|dart|erl|exs?|go|groovy|ino?|java|js|litcoffee|lua|php|py|rb|scala|ts|pm)$',
                    'list' => true,
                    'help' => 'RegEx-Filter to select files that should be parsed (multiple -f can be used).'
                ])
                ->parse();

$options = [
    'excludeFilters'=> $argv['exclude-filters'],
    'includeFilters'=> $argv['file-filters'],
    'src'           => $argv['input'],
    'dest'          => $argv['output'],
    'template'      => $argv['template'],
    'config'        => $argv['config'],
    'verbose'       => $argv['verbose'],
    'debug'         => $argv['debug'],
    'parse'         => $argv['parse'],
    'colorize'      => $argv['color'],
    'filters'       => null,
    'languages'     => null,
    'parsers'       => null,
    'workers'       => null,
    'silent'        => $argv['silent'],
    'simulate'      => $argv['simulate'],
    'markdown'      => $argv['markdown'],
    'lineEnding'    => isset($argv['line-ending']) ? $argv['line-ending'] : null,
    'encoding'      => $argv['encoding'],
];

Loader::instance('Launch', NS_API)->createDoc($options);

