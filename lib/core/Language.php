<?php
namespace core;

class Language
{
    public static function load($lang, $path = LANG_PATH)
    {
        $path = rtrim($path, DS) . DS . $lang . PHP_EXT;

        return reset(File::load(is_file($path) ? $path : static::defaults()));
    }

    protected static function defaults()
    {
        // todo add default load configure
        return LANG_PATH . DS . 'default' . PHP_EXT;
    }
}