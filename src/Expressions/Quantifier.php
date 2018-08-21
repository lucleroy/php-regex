<?php

namespace LucLeroy\Regex\Expressions;

use LucLeroy\Regex\Regex;

class Quantifier extends RegularExpression implements Atomic
{

    const UNDEFINED = -1;
    const GREEDY = 0;
    const LAZY = 1;
    const POSSESSIVE = 2;

    private $expression;
    private $min;
    private $max;
    private $policy = self::UNDEFINED;

    function __construct($expression, $min, $max = null, $policy = self::UNDEFINED)
    {
        $this->expression = $expression;
        $this->min = $min;
        $this->max = $max;
        $this->policy = $policy;
    }

    public function toString()
    {
        if ($this->min === 0 && $this->max === 1) {
            $suffix = '?';
        } elseif ($this->min === 0 && $this->max === INF) {
            $suffix = '*';
        } elseif ($this->min === 1 && $this->max === INF) {
            $suffix = '+';
        } elseif ($this->max === null || $this->max == $this->min) {
            $suffix = '{' . $this->min . '}';
        } elseif ($this->max === INF) {
            $suffix = '{' . $this->min . ',}';
        } else {
            $suffix = '{' . $this->min . ',' . $this->max . '}';
        }
        if ($this->policy == self::LAZY) {
            $suffix .= '?';
        } elseif ($this->policy == self::POSSESSIVE) {
            $suffix .= '+';
        }
        if ($this->expression instanceof Regex) {
            $exp = $this->expression->getExpressions();
            if (count($exp) == 1) {
                $expression = $exp[0];
            }
        }
        if (!isset($expression)) {
            $expression = $this->expression;
        }
        if ($expression instanceof Atomic) {
            return $expression . $suffix;
        }
        return '(?:' . $this->expression . ')' . $suffix;
    }
   
    public function setPolicy($policy)
    {
        $this->policy = $policy;
    }
    
    public function getPolicy()
    {
        return $this->policy;
    }

    protected function setQuantifierPolicy($policy, $recursive = false)
    {
        if ($this->policy === self::UNDEFINED) {
            $this->policy = $policy;
        }
        if ($recursive) {
            $this->expression->setQuantifierPolicy($policy, $recursive);
        }
    }

}
