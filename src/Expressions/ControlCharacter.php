<?php

namespace LucLeroy\Regex\Expressions;

class ControlCharacter extends RegularExpression
{
    private $letter;
    
    function __construct($letter)
    {
        $this->letter = strtoupper($letter);    
    }

    protected function toString()
    {
        return '\\c' . $this->letter;
    }

}
