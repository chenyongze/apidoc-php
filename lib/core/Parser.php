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
        $content = preg_replace('/\r\n/', "\n", file_get_contents($fileName));

        // 分解块注释文件
        $blocks = static::findBlock($content);

        if (empty($blocks)) {
            return false;
        }

        // 将各块注释分解成元素
        $elements = array_map(function($block) use ($fileName) {
            $elements = static::findElements($block, $fileName);
            return $elements;
        }, $blocks);

        if (empty($elements)) {
            return false;
        }

        $indexApiBlocks = static::findBlockWithApiGetIndex($elements);
        if (empty($indexApiBlocks)) {
            return false;
        }

        return static::parseBlockElements($indexApiBlocks, $elements, $fileName);
    }

    private static function findBlock($content)
    {
        $blocks = [];

        $content = preg_replace('/\n/', WRAP, $content);

        $blockRegExp = Language::load(self::$ext);

        preg_match_all($blockRegExp['docBlocksRegExp'], $content, $matches, PREG_SET_ORDER);

        // todo 可能有点问题
        foreach ($matches as $match) {
            preg_match($blockRegExp['inlineRegExp'], $match[1] ?: $match[0], $ret);
            $block = preg_replace(['/' . WRAP . '/u', $blockRegExp['inlineRegExp']], ['\n', ''], $match[1] ?: $match[0]);
            $blocks[] = $block;
        }

        return $blocks;
    }

    private static function findElements($block)
    {
        $elements = [];

        // Replace Linebreak with Unicode
        $block = preg_replace('/\\\n/', WRAP, $block);

        // Elements start with @
        $elementsRegExp = "/(@(\w*)\s?(.+?)(?=" . WRAP . "[\s\*]*@|$))/m";
        preg_match_all($elementsRegExp, $block, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $element = [
                'source'     => preg_replace('/' . WRAP . '/', '\n', $match[1]),
                'name'       => strtolower($match[2]),
                'sourceName' => $match[2],
                'content'    => preg_replace('/' . WRAP . '/', '\n', $match[3])
            ];
            // todo add hook func

            $elements[] = $element;
        }

        return $elements;
    }

    private static function findBlockWithApiGetIndex($blocks)
    {
        $foundIndexes = [];

        for ($i = 0; $i < count($blocks); $i++) {
            $found = false;
            for ($j = 0; $j < count($blocks[$i]); $j++) {
                if (substr($blocks[$i][$j]['name'], 0, 9) === 'apiignore') {
                    $found = false;
                    break;
                }

                if (substr($blocks[$i][$j]['name'], 0, 3) === 'api') {
                    $found = true;
                }
            }

            $found and $foundIndexes[] = $i;
        }

        return $foundIndexes;
    }

    private static function parseBlockElements($indexApiBlocks, $detectedElements, $fileName)
    {
        $parsedBlocks = [];
        for ($i = 0; $i < count($indexApiBlocks); $i++) {
            list($blockIndex, $elements) = [$indexApiBlocks[$i], $detectedElements[$indexApiBlocks[$i]]];
            $blockData = ['global' => [], 'local'  => []];
            $countAllowedMultiple = 0;

            $index = 1; // apiname
            $name = $elements[$index]['name'];
            $content = $elements[$index]['content'];
            $source = $elements[$index]['source'];

            $parser = Loader::instance('Parsers', NS_CORE)[$name];
            $values = $parser['parse']($content, $source);
            var_dump($values);
            exit;

            for ($j = 0; $j < count($elements); $j++) {
                list($element, $elementParser) = [$elements[$j], Loader::instance('Parsers', NS_CORE)[$elements[$j]['name']]];

                $pathTo = $attachMethod = '';

                //todo Deprecation warning
                try {
                    // parse element and retrieve values
                    $values = $elementParser['parse']($element['content'], $element['source']);

                    // HINT: pathTo MUST be read after elementParser.parse, because of dynamic paths
                    // Add all other options after parse too, in case of a custom plugin need to modify params.

                    // path to an array, where the values should be attached
                    $pathTo = is_callable($elementParser['path']) ? $elementParser['path']() : $elementParser['path'];
                    if (empty($pathTo)) {
                        throw new \Exception('pathTo is not defined in the parser file.', '', '', $element['sourceName']);
                    }

                    // method how the values should be attached (insert or push)
                    $attachMethod = $elementParser['method'] ?: 'push';

                    // todo : put this into "converters"

                } catch (\Exception $e) {
                    // todo write params
                }

                // todo A lot of exception
                if (!$blockData[$pathTo]) {
                    static::createObjectPath($blockData, $pathTo, $attachMethod);
                }

                $blockDataPath = static::pathToObject($pathTo, $blockData);

                // insert Fieldvalues in Path-Array
                if ($attachMethod === 'push') {
                    array_push($blockDataPath, $values);
                } else {
                    $blockDataPath = array_merge($blockDataPath, $values);
                }

                // insert Fieldvalues in Mainpath
                if ($elementParser['extendRoot'] === true) {
                    $blockData = array_merge($blockData, $values);
                }

                $blockData['index']++;
            }

            if ($blockData['index'] && $blockData['index'] > 0) {
                array_push($parsedBlocks, $blockData);
            }
        }
var_dump($parsedBlocks);exit;
        return $parsedBlocks;
    }

    // todo 看不太懂这里的做法
    private static function createObjectPath(&$src, $path, $attachMethod)
    {
        if (!$path) {
            return $src;
        }

        $pathParts = explode('.', $path);
        $current = &$src;

        for ($i = 0; $i < count($pathParts); $i++) {
            $current = &$current[$pathParts[$i]];
        }

        return $current;
    }

    private static function pathToObject($path, &$src)
    {
        if (!$path) {
            return $src;
        }

        $pathParts = explode('.', $path);
        $current = &$src;
        for ($i = 0; $i < count($pathParts); $i++) {
            $part = $pathParts[$i];
            $current = &$current[$part];
        }

        return $current;
    }
}