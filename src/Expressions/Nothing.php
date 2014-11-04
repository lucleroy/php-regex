<?php

namespace LucLeroy\Regex\Expressions;


class Nothing extends RegularExpression implements Atomic
{
    public function toString()
    {
        return '';
    }
}
