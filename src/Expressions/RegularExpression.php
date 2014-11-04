<?php

namespace LucLeroy\Regex\Expressions;

abstract class RegularExpression
{
    protected $optionsToSet = '';
    protected $optionsToUnset = '';
    protected $mark = 0;
    
    function __toString()
    {
        return $this->getInitOptionsString() . $this->toString() . $this->getResetOptionsString();
    }


    protected function getInitOptionsString()
    {
        $result = $this->optionsToSet;
        if (!empty($this->optionsToUnset)) {
            $result .= '-' . $this->optionsToUnset;
        }
        if (!empty($result)) {
            $result = "(?$result)";
        }
        return $result;
    }

    protected function getResetOptionsString()
    {
        $result = $this->optionsToUnset;
        if (!empty($this->optionsToSet)) {
            $result .= '-' . $this->optionsToSet;
        }
        if (!empty($result)) {
            $result = "(?$result)";
        }
        return $result;
    }

    public function setOption($code)
    {
        if (strpos($this->optionsToSet, $code) === false) {
            $this->optionsToSet .= $code;
        }
    }

    public function unsetOption($code)
    {
        if (strpos($this->optionsToUnset, $code) === false) {
            $this->optionsToUnset .= $code;
        }
    }
    
    protected function setQuantifierPolicy($policy, $recursive = false)
    {
        
    }
    
    abstract protected function toString();
}
