<?php
namespace OutputControl;

/**
 * A simple container class that enables getters
 * and setters for the stored registry item.
 */
class RegistryItem
{
    private $data = array();
    private $template;

    public function __construct($template)
    {
        $this->template = $template;
    }

    public function __get($name)
    {
        return $this->data[$name];
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function getData()
    {
        return $this->data;
    }
}
