<?php
namespace core;

class Config
{
    const DEF_RANGE = '_sys_';

    // 配置参数
    protected static $config = [];

    /**
     * 加载配置文件
     *
     * @param  string $files 配置文件名
     * @param  string $range 变量空间
     * @return mixed
     */
    public static function import($files, $range = self::DEF_RANGE)
    {
        if (!empty($files)) {
            foreach (File::scan($files) as $scanFile) {
                $name = strtolower(pathinfo($scanFile)['filename']);
                self::set($range, $name, include $scanFile);
            }
        }
    }

    public static function set($range, $name, $value)
    {
        return self::$config[$range][$name] = $value;
    }

    public static function get($key, $name = '', $range = self::DEF_RANGE)
    {
        $name = strtolower($name);

        if ($name) {
            return self::$config[$range][$name][$key];
        } else {
            foreach (self::$config[$range] as $name) {
                if (isset($name[$key])) {
                    return $name[$key];
                }
            }
        }

        return null;
    }
}