<?php

namespace LucLeroy\Regex\Expressions;

class Alternative extends RegularExpression
{

    private $expressions = [];

    function __construct($expressions)
    {
        foreach ($expressions as $expression) {
            $this->expressions[] = $expression;
        }
    }

    public function toString()
    {
        $result = '';
        foreach ($this->expressions as $expression) {
            if ($result !== '') {
                $result .= '|';
            }
            $result .= $expression;
        }
        return $result;
    }
    
    protected function setQuantifierPolicy($policy, $recursive = false)
    {
        foreach ($this->expressions as $expression) {
            $expression->setQuantifierPolicy($policy, $recursive);
        }
    }

}
