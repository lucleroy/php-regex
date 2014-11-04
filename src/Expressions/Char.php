<?php

namespace LucLeroy\Regex\Expressions;


class Char extends RegularExpression implements Atomic
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
