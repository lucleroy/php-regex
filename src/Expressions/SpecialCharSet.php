<?php

namespace LucLeroy\Regex\Expressions;

class SpecialCharSet extends RegularExpression implements Atomic
{
    private $code;
    
    function __construct($code)
    {
        $this->code = $code;
    }
    
    public function toString()
    {
        return $this->code;
    }

}
