<?php
namespace core;

class Apidoc
{
    protected static $app = [
        'options'        =>  [],
        'logger'         =>  null,
        'markdownParser' =>  false,
        'generator'      =>  [],
        'package'        =>  [],
        'filters'        =>  [],
        'languages'      =>  [],
        'parsers'        =>  [],
        'workers'        =>  [],
        'hooks'          =>  [],
        'addHook'        =>  null,
        'hook'           =>  null,
    ];

    public static function parse(array $options)
    {

    }

    public static function app($name, $arguments)
    {
        if (isset(static::$app[$name])) {
            return self::$app[$name] = $arguments;
        }

        return self::$app;
    }
}