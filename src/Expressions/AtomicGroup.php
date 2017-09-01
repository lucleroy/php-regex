<?php

namespace LucLeroy\Regex\Expressions;

class AtomicGroup extends RegularExpression implements Atomic
{
    private $expression;
    
    function __construct($expression)
    {
        $this->expression = $expression;
    }

    public function toString()
    {
        return '(?>' . $this->expression . ')';
    }
    
    protected function setQuantifierPolicy($policy, $recursive = false)
    {
        $this->expression->setQuantifierPolicy($policy, $recursive);
    }
}
