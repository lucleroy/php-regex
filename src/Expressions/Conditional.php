<?php

namespace LucLeroy\Regex\Expressions;

class Conditional extends RegularExpression implements Atomic
{

    private $cond;
    private $true;
    private $false;

    function __construct($cond, $true, $false)
    {
        $this->cond = $cond;
        $this->true = $true;
        $this->false = $false;
    }

    public function toString()
    {
        if ($this->cond instanceof MatchAssertion) {
            $cond = '(' . $this->cond->getNumber() . ')';
        } else {
            $cond = $this->cond;
        }
        $true = $this->true;
        if ($true instanceof Regex) {
            $exp = $true->getExpressions();
            if (count($exp) <= 1) {
                foreach ($exp as $value) {
                    $true = $value;
                }
            }
        }
        $false = $this->false;
        if ($false instanceof Regex) {
            $exp = $false->getExpressions();
            if (count($exp) <= 1) {
                foreach ($exp as $value) {
                    $false = $value;
                }
            }
        }
        if (!$true instanceof Atomic) {
            $true = '(?:' . $true . ')';
        }
        if (!$false instanceof Atomic) {
            $false = '(?:' . $false . ')';
        }
        return '(?' . $cond . $true . '|' . $false . ')';
    }
    
        protected function setQuantifierPolicy($policy, $recursive = false)
    {
        $this->cond->setQuantifierPolicy($policy, $recursive);
        $this->true->setQuantifierPolicy($policy, $recursive);
        $this->false->setQuantifierPolicy($policy, $recursive);
        
    }

}
