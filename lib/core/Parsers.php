<?php
namespace core;

class Parsers extends common\ArrayAccess
{
    protected static $instance;

    public static function instance()
    {
        if (empty(static::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function getParser($name)
    {
        $class = NS_COMM . BS . $name;

        if (class_exists($name)) {
            return Loader::instance($class);
        }

        return null;
    }

    function offsetGet($index)
    {
        return $this->getParser($index);
    }
}