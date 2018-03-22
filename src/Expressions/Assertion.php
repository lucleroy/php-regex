<?php

namespace LucLeroy\Regex\Expressions;

class Assertion extends RegularExpression implements Atomic
{

    private $expression;
    private $code;

    function __construct($expression, $code)
    {
        $this->expression = $expression;
        $this->code = $code;
    }

    public function toString()
    {
        return '(' . $this->code . $this->expression . ')';
    }

    protected function setQuantifierPolicy($policy, $recursive = false)
    {
        $this->expression->setQuantifierPolicy($policy, $recursive);
    }

}
