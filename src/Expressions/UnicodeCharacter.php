<?php

namespace LucLeroy\Regex\Expressions;

class UnicodeCharacter extends RegularExpression
{

    private $code;

    function __construct($code)
    {
        $this->code = $code;
    }

    protected function toString()
    {
        return sprintf('\\x{%X}', $this->code);
    }

}
