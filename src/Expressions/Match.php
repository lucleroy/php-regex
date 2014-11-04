<?php

namespace LucLeroy\Regex\Expressions;

class Match
{

    private $matches;

    function __construct($match)
    {
        $this->matches = $match;
    }

    public function getText()
    {
        return $this->matches[0][0];
    }

    public function getOffset()
    {
        return $this->matches[0][1];
    }

    public function getGroup($group)
    {
        if (isset($this->matches[$group])) {
            return new Group($this->matches[$group]);
        }
        return null;
    }

    public function getAllGroups()
    {
        $groups = [];
        foreach ($this->matches as $key => $value) {
            $groups[$key] = new Group($value);
        }
        unset($groups[0]);
        return $groups;
    }

}
