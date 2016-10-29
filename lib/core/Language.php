<?php
namespace core;

use \core\common\ArrayAccess;

class Language extends ArrayAccess
{
    public static function load($lang, $path = LANG_PATH)
    {
        $path = rtrim($path, DS) . DS . $lang . PHP_EXT;

        return include File::load(is_file($path) ? $path : static::defaults());
    }

    protected static function defaults()
    {
        // todo add default load configure
        return LANG_PATH . DS . 'default' . PHP_EXT;
    }

    function __get($lang)
    {
        return static::load($lang);
    }
}