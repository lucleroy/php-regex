<?php

namespace LucLeroy\Regex\Expressions;

class BackReference extends RegularExpression implements Atomic
{
    private $name;
    
    function __construct($name)
    {
        $this->name = $name;
    }

    public function toString()
    {
        if (is_int($this->name)) {
            return '\\g{' . $this->name . '}';
        }
        return "(?P=$this->name)";
    }
}
