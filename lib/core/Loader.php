<?php
namespace core;

use \Exception;

class Loader
{
    protected static $namespace = [];

    // 加载器注册
    public static function register($autoload = '')
    {
        spl_autoload_register($autoload ? $autoload : [__CLASS__, 'autoload']);
    }

    /**
     * 自动加载
     *
     * @param string $class
     * @return bool
     * @throws Exception
     */
    public static function autoload($class)
    {
        if (!empty(static::$namespace)) {
            list($name, $class) = explode(BS, $class, 2);

            if (isset(static::$namespace[$name])) {
                $path = static::$namespace[$name];
            } else {
                throw new Exception("{$class} `{$name}`" . ' namespace is not define');
            }
        }

        $basename = $path . DS . str_replace(BS, DS, $class);

        // todo 可以做成单例加载
        if (is_file($filename = $basename . PHP_EXT)) {
            include $filename;
        } else {
            throw new \Exception($filename . ' can not find');
        }
    }

    /**
     * 获取实例
     *
     * @param  string  $class  类名
     * @param  string  $namespace 命名空间
     * @param  boolean $singleton 是否单例
     *
     * @throws Exception
     */
    public static function instance($class, $namespace = '', $singleton = true)
    {
        static $_instance = [];

        $class = rtrim($namespace, BS) . BS . $class;

        if (!isset($_instance[$class])) {
            if (class_exists($class)) {
                $indentity = new $class();

                // 非单例模式下直接返回实例
                if (!$singleton) {
                    return $indentity;
                }

                $_instance[$class] = $indentity;
            } else {
                throw new Exception('class not exist :' . $class, 10007);
            }
        }

        return $_instance[$class];
    }

    /**
     * 添加命名空间，后续自动加载将根据namespace进行寻径
     *
     * @param array|string $namespace 命名空间
     * @param string       $path      路径
     */
    public static function addNamespace($namespace, $path = '')
    {
        if (is_array($namespace)) {
            static::$namespace = array_merge(static::$namespace, $namespace);
        } else {
            static::$namespace[$namespace] = $path;
        }
    }
}
