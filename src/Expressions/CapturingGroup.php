<?php

namespace LucLeroy\Regex\Expressions;

class CapturingGroup extends RegularExpression implements Atomic
{

    private $expression;
    private $name;

    function __construct($expression, $name)
    {
        $this->expression = $expression;
        $this->name = $name;
    }

    public function toString()
    {
        if ($this->name) {
            return '(?P<' . $this->name . '>' . $this->expression . ')';
        }
        return '(' . $this->expression . ')';
    }

    protected function setQuantifierPolicy($policy, $recursive)
    {
        $this->expression->setQuantifierPolicy($policy, $recursive);
    }

}
