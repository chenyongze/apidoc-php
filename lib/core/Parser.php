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

        $elements = array_map(function($block) use ($fileName) {
            return static::findElements($block, $fileName);
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

    private static function findElements($block, $fileName)
    {
        $elements = [];

        // Replace Linebreak with Unicode
        $block = preg_replace('/\n/g', "\uffff", $block);
        // Elements start with @
        $elementsRegExp = "/(@(\w*)\s?(.+?)(?=\uffff[\s\*]*@|$))/m";
        preg_match_all($elementsRegExp, $block, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $element = [
                'source'     => preg_replace('/\uffff/g', "\n", $matches[1]),
                'name'       => strtolower($matches[2]),
                'sourceName' => $matches[2],
                'content'    => preg_replace('/\uffff/g', "\n", $matches[3])
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

                $found and $foundIndexes[] = $i;
            }
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

            for ($j = 0; $j < count($elements); $j++) {
                list($element, $elementParser) = [$elements[$j], static::parsers($elements[$j])];

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

    private static function parsers($name)
    {
        static $parser;
        $name = preg_replace(Config::get('parser_prefix_reg'), '$1_', $name);

        if (!isset($parser[$name])) {
            $parser[$name] = File::load(COMMON_PATH . DS . 'parsers' . $name . PHP_EXT);
        }

        return $parser[$name];
    }
}