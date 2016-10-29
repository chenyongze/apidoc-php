<?php
namespace core;

class File
{
    public static function scan($files)
    {
        $scan = [];

        if (!empty($files)) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    // 加载文件
                    $scan[$file] = $file;
                } else if (is_dir($file)) {
                    // 加载目录
                    $scanFiles = array_diff(scandir($file), ['.', '..']);
                    foreach ($scanFiles as $scanFile) {
                        $file = $file . DS . $scanFile;
                        is_file($file) and $scan[$scanFile] = $file;
                    }
                }
            }
        }

        return $scan;
    }

    public static function load($files)
    {
        $load = [];

        if (!empty($files)) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    // 加载文件
                    $load[$file] = include $file;
                } else if (is_dir($file)) {
                    // 加载目录
                    $scanFiles = array_diff(scandir($file), ['.', '..']);
                    foreach ($scanFiles as $scanFile) {
                        $file = $file . DS . $scanFile;
                        is_file($file) and $load[$scanFile] = include $file;
                    }
                }
            }
        }

        return $load;
    }
}