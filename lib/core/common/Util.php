<?php
namespace core\common;

class Util
{
    public static function unindent($string)
    {
        $lines = explode("\n", $string);

        $xs = array_filter($lines, function($x) {
            return preg_match('/\S/', $x);
        }) and sort($xs);

        if (empty($x)) {
            return $string;
        }

        list($start, $end) = [reset($xs), end($xs)];

        // todo 移植过程中发现不是很明白这个的意思，有点复杂
        return $string;
    }

    public static function objectValuesToString($obj)
    {
        $str = '';

        foreach ($obj as $el) {
            if (is_string($el)) {
                $str .= $el;
            } else {
                $str .= static::objectValuesToString($el);
            }
        }
        return $str;
    }
}