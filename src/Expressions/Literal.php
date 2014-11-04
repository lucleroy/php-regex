<?php

namespace LucLeroy\Regex\Expressions;

class Literal extends RegularExpression
{
    private $string;
    
    function __construct($string)
    {
        $this->string = $string;
    }

    public function toString()
    {
        return preg_quote($this->string);
    }
}
