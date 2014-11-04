<?php

namespace LucLeroy\Regex;

class RegexException extends GenericException
{
   
    public static function notEnoughExpressions($min)
    {
        $s = $min > 1 ? 's' : '';
        $msg = "Regex must contain at least $min expression$s.";
        return new self($msg);
    }
    
    public static function notEnoughExpressionsFromMark($min)
    {
        $s = $min > 1 ? 's' : '';
        $msg = "Regex must contain at least $min expression$s from last mark (or start of sequence).";
        return new self($msg);
    }
    
    public static function quantifierExpected()
    {
        $msg = "The last expression must be a quantifier.";
        return new self($msg);
    }
}
