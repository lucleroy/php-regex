<?php

namespace LucLeroy\Regex\Expressions;

class MatchRecursive extends RegularExpression implements Atomic
{
    
    protected function toString()
    {
        return '(?R)';
    }

}
