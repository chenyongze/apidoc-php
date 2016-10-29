<?php
namespace core;

class Parser
{
    static $ext;

    /**
     * 解析文件
     *
     * @param array $options
     * @param $parseFiles
     * @param $parseFillNames
     */
    public static function parseFiles(array $options, &$parseFiles, &$parseFillNames)
    {
        // todo add includes & exclude filter

        $files = File::scan($options['src']);

        foreach ($files as $fileName) {
            $parseFile = static::parseFile($fileName, $options['encoding']);
            if ($parseFile) {
                $parseFiles[]     = $parseFile;
                $parseFillNames[] = $fileName;
            }
        }
    }

    public static function parseFile($fileName, $encoding = 'utf8')
    {
        static::$ext = strtolower(pathinfo($fileName)['extension']);

        // todo encoding file content
        $content = preg_replace('/\r\n/g', "\n", File::load($fileName));

        $blocks = static::findBlock($content);
        if (empty($blocks)) {
            return false;
        }

        // todo BEGIN
        return '';
    }

    private static function findBlock($content)
    {
        $blocks = [];

        $content = preg_replace('/\n/g', "\uffff", $content);
        $blockRegExp = Language::load(self::$ext);

        preg_match_all($blockRegExp['docBlocksRegExp'], $content, $matches, PREG_SET_ORDER);

        // todo 可能有点问题
        foreach ($matches as $match) {
            $block = preg_replace(["/\uffff/", $blockRegExp['inlineRegExp']], ["\n", ''], $match[2] ?: $match[1]);
            $blocks[] = $block;
        }

        return $blocks;
    }
}