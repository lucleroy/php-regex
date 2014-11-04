<?php

namespace LucLeroy\Regex\Expressions;

class UnicodeProperty extends RegularExpression implements Atomic
{
    private $code;
    private $exclude;
    
    function __construct($code, $exclude = false)
    {
        $this->code = $code;
        $this->exclude = $exclude;
    }
    
    public function toString()
    {
        if (strlen($this->code) > 1) {
            $code = '{' . $this->code . '}';
        } else {
            $code = $this->code;
        }
        if ($this->exclude) {
            return '\\P' . $code;
        }
        return '\\p' . $code;
    }

}
