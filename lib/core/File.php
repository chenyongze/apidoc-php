<?php
namespace core;

// todo 说真的，我觉得我这个File类写的真是一坨屎
class File
{
    public static function scan($files)
    {
        $scan = [];

        !is_array($files) and $files = [$files];

        if (!empty($files)) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    // 加载文件
                    $scan[$file] = $file;
                } else if (is_dir($file)) {
                    // 加载目录
                    $scanFiles = array_diff(scandir($file), ['.', '..']);
                    foreach ($scanFiles as $scanFile) {
                        $loadfile = rtrim($file, DS) . DS . $scanFile;
                        is_file($loadfile) and $scan[$scanFile] = $loadfile;
                    }
                }
            }
        }

        return $scan;
    }

    public static function load($files)
    {
        $load = [];

        !is_array($files) and $files = [$files];

        if (!empty($files)) {
            foreach ($files as $file) {
                if (is_file($file)) {
                    // 加载文件
                    $load[$file] = include $file;
                } else if (is_dir($file)) {
                    // 加载目录
                    $scanFiles = array_diff(scandir($file), ['.', '..']);
                    foreach ($scanFiles as $scanFile) {
                        $loadfile = rtrim($file, DS) . $scanFile;
                        is_file($loadfile) and $load[$scanFile] = include $loadfile;
                    }
                }
            }
        }

        return $load;
    }
}