<?php
namespace core\common;

abstract class ArrayAccess implements \ArrayAccess
{
    function offsetExists($index)
    {
        return isset($this->{$index});
    }

    function offsetGet($index)
    {
        return $this->$index;
    }

    function offsetSet($index, $newvalue)
    {
        $this->{$index} = $newvalue;
    }

    function offsetUnset($index)
    {
        unset($this->{$index});
    }
}