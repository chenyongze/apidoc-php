<?php
namespace api\core;
final class Nomnom
{
    protected static $specs;

    public static function instance()
    {
        static $instance = null;

        if (empty($instance)) {
            $instance = new self();
        }

        return $instance;
    }

    public function option($name, $spec)
    {
        self::$specs[$name] = $spec;

        return $this;
    }
}