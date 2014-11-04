<?php

namespace LucLeroy\Regex\Expressions;

class Group
{

    private $group;

    function __construct($group)
    {
        $this->group = $group;
    }

    public function getText()
    {
        return $this->group[0];
    }

    public function getOffset()
    {
        return $this->group[1];
    }

}
