<?php
namespace api;

use core\Apidoc;

class Launch
{
    protected static $app;

    function __construct()
    {
        static::$app = [
            'log'            => [],
            'options'        => [],
            'markdownParser' => null
        ];
    }

    public function createDoc(array &$options)
    {
        $options = array_merge(static::defaults(), $options);

        static::$app['options'] = $options;

        // todo 引入markdown解析器

        // todo 获取包解析类
        $package = json_decode(file_get_contents(ROOT_PATH . DS . 'package.json'), true);

        Apidoc::app('generator', [
            'name'    => $package['name'],
            'time'    => date('Y-m-d'),
            'url'     => $package['homepage'],
            'version' => $package['version']
        ]);
        // todo 设置apidoc app相关设置

        $api = Apidoc::parse($options);

        if ($api === true || $api === false) {
            return $api;
        }

        if (self::$app['options']['parse'] !== true) {
            static::createOutputFiles($api);
        }

        return $api;
    }

    protected static function createOutputFiles($api)
    {
        // todo write api to file
        var_dump($api);
    }

    protected static function defaults()
    {
        return [
            'dest'     => DEF_DOC_PATH,
            'template' => DEF_TPL_PATH,
            'debug'    => false,
            'silent'   => false,
            'verbose'  => false,
            'simulate' => false,
            'parse'    => false,
            'colorize' => true,
            'markdown' => true,
            'config'   => './',
            'encoding' => 'utf8'
        ];
    }
}