<?php
namespace api;

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
        if (!empty(self::$namespace)) {
            list($name, $class) = explode('\\', $class, 2);

            if (isset(self::$namespace[$name])) {
                $path = self::$namespace[$name];
            } else {
                throw new Exception("{$class} `{$name}`" . ' namespace is not define');
            }
        }

        $basename = $path . DS . str_replace('\\', DS, $class);

        // TODO 可以做成单例加载
        if (is_file($filename = $basename . PHP_EXT)) {
            include $filename;
        } else {
            throw new \Exception($filename . ' can not find');
        }
    }

    /**
     * 获取实例，可执行方法
     * @param  string $class  类名
     * @param  string $method 方法名
     *
     * @return mixed
     * @throws Exception
     */
    public static function instance($class, $method = '')
    {
        static $_instance = [];
        $indentity = $class . $method;

        if (!isset($_instance[$indentity])) {
            if (class_exists($class)) {
                $object = new $class();
                if (!empty($method) && method_exists($object, $method)) {
                    $_instance[$indentity] = call_user_func_array([&$object, $method], []);
                } else {
                    $_instance[$indentity] = $object;
                }
            } else {
                throw new Exception('class not exist :' . $class, 10007);
            }
        }

        return $_instance[$indentity];
    }

    /**
     * 添加命名空间，后续自动加载将根据namespace进行寻径
     * @param array|string $namespace 命名空间
     * @param string       $path      路径
     */
    public static function addNamespace($namespace, $path = '')
    {
        if (is_array($namespace)) {
            self::$namespace = array_merge(self::$namespace, $namespace);
        } else {
            self::$namespace[$namespace] = $path;
        }
    }
}
