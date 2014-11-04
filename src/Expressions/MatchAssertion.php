<?php

namespace LucLeroy\Regex\Expressions;

class MatchAssertion extends Assertion
{

    private $number;

    function __construct($number)
    {
        $this->number = $number;
    }

    public function toString()
    {
        return '(?(' . $this->number . ')|(?!))';
    }

    public function getNumber()
    {
        return $this->number;
    }

}
