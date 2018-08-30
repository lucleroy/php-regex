<?php

namespace LucLeroy\Regex\Expressions;

class MatchRecursive extends RegularExpression
{
    
    protected function toString()
    {
        return '(?R)';
    }

}
