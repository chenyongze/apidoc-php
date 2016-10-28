<?php
namespace core;

class Nomnom
{
    protected static $specs;

    public function option($name, $spec)
    {
        self::$specs[$name] = $spec;

        return $this;
    }

    public function parse()
    {
        $parseResult['_'] = [];
        $parseResult['file-filters'] = '.*\\.(clj|coffee|cpp|cs|dart|erl|exs?|go|groovy|ino?|java|js|litcoffee|lua|php|py|rb|scala|ts|pm)$';
        $parseResult['exclude-filters'] = '';
        $parseResult['input'] = './';
        $parseResult['output'] = './doc/';
        $parseResult['template'] = '/data/node/apidoc/template/';
        $parseResult['config'] = './';
        $parseResult['verbose'] = false;
        $parseResult['debug'] = false;
        $parseResult['color'] = true;
        $parseResult['parse'] = false;
        $parseResult['silent'] = false;
        $parseResult['simulate'] = false;
        $parseResult['markdown'] = true;
        $parseResult['encoding'] = 'utf8';

        return $parseResult;
    }
}