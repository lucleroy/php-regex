<?php

namespace LucLeroy\Regex\Expressions;

class AnsiCharacter extends RegularExpression
{

    private $code;

    function __construct($code)
    {
        $this->code = $code;
    }

    protected function toString()
    {
        
        return sprintf('\\x%02X', $this->code);
    }

}
