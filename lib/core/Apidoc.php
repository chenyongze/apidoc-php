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
        // todo merge options

        $parseFiles = $parseFileNames = [];

        if (is_array($options['src'])) {
            foreach ($options['src'] as $folder) {
                $options['src'] = $folder and Parser::parseFiles($options, $parseFiles, $parseFileNames);
            }
        } else {
            Parser::parseFiles($options, $parseFiles, $parseFileNames);
        }

        return true;
    }

    public static function app($name, $arguments)
    {
        if (isset(static::$app[$name])) {
            return self::$app[$name] = $arguments;
        }

        return self::$app;
    }
}