<?php
namespace LucLeroy\Regex\Expressions;

use LucLeroy\Regex\Charset;

class ComplementCharset extends Charset
{

    public function toString()
    {
        return '[^' . $this->chars . ']';
    }
}