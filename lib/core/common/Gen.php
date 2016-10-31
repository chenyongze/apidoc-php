<?php
/**
 * 提供了对象属性的数组式访问，实现了对象的遍历、计数接口
 */
namespace core\common;

use \IteratorAggregate;
use \Countable;
use \ArrayAccess;
use \ArrayIterator;
use \ReflectionObject;

class Gen implements IteratorAggregate, Countable, ArrayAccess
{
    /**
     * @return array
     */
    public function getProperties()
    {
        $data = [];

        foreach ($this->getPropertyNames() as $property_name) {
            $data[$property_name] = $this->offsetGet($property_name);
        }

        return $data;
    }

    /**
     * @param array $properties
     *
     * @return $this
     */
    public function setProperties(array $properties)
    {
        foreach ($properties as $name => $property) {
            if (in_array($name, $this->getPropertyNames())) {
                $this->offsetSet($name, $property);
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getPropertyNames()
    {
        static $property_names;

        if (!isset($property_names) || !is_array($property_names)) {
            $reflection = new ReflectionObject($this);
            foreach ($reflection->getProperties() as $property) {
                $name = $property->getName();
                $property_names[] = $name;
            }
        }

        return $property_names;
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->getProperties());
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        return count($this->getProperties());
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->$offset : null;
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        $this->$offset = $value;
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return property_exists($this, $offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        unset($this->$offset);
    }
}