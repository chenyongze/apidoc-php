<?php
namespace core;

use core\common\Gen;

class Parsers extends Gen
{
    /**
     * @param string $name 解析器名称(如:apisuccess)
     * @return null
     */
    public function getParser($name)
    {
        static $parsers;

        if (!isset($parsers[$name])) {
            $file = self::path($name);
            $result = reset(File::load($file));
            $parsers[$name] = $result ?: false;
        }

        return $parsers[$name] ?: null;
    }

    private static function path($name)
    {
        $prefix = Config::get('parser_prefix', 'regex');

        $name = ucfirst($prefix) . ucfirst(str_replace([$prefix, '_'], '', strtolower($name)));

        return PARSE_PATH . DS . $name . PHP_EXT;
    }

    public function offsetGet($name)
    {
        return $this->getParser($name);
    }
}