<?php
namespace core\common;

abstract class ArrayAccess implements \ArrayAccess
{
    function offsetExists($index){}

    function offsetGet($index){}

    function offsetSet($index, $newvalue){}

    function offsetUnset($index){}
}