<?php

namespace LucLeroy\Regex;

use Exception;
use LucLeroy\Regex\Expressions\Atomic;
use LucLeroy\Regex\Expressions\RegularExpression;
use LucLeroy\Regex\Expressions\UnicodeProperty;

class Charset extends RegularExpression implements Atomic
{

    protected $chars;

    function __construct($chars = '')
    {
        $this->chars = $chars;
    }

    public static function create($chars = '')
    {
        return new self($chars);
    }

    public function toString()
    {
        return '[' . $this->chars . ']';
    }

    /**
     * Add the characters of another charset.
     * 
     * @param Charset $charClass
     * @return Charset
     */
    public function appendCharacterClass(Charset $charClass)
    {
        $this->chars .= $charClass->chars;
        return $this;
    }

    /**
     * Add characters of $chars to the charset. An interval of characters is denoted
     * by two characters separated by a `..`.
     * 
     * @param string $chars
     * @return Charset
     */
    public function chars($chars)
    {
        $this->chars .= str_replace('\.\.', '-', preg_quote($chars));
        return $this;
    }

    /**
     * Add an interval of characters.
     * 
     * @param string $start The first character of the interval
     * @param string $end The last character of the interval
     * @return Charset
     */
    public function charRange($start, $end)
    {
        $this->chars .= preg_quote($start) . '-' . preg_quote($end);
        return $this;
    }

    /**
     * Add the characters used in the representation of a number written in a 
     * given base (allowed base from 2 to 36). If letters are necessary, lower
     * and upper versions are added.
     * 
     * @param int $base Base, between 2 and 36.
     * @return Charset
     * @throws Exception
     */
    public function digit($base = null)
    {
        if (!isset($base)) {
            $this->chars .= "\\d";
            return $this;
        }
        if (!is_int($base) || $base < 2 || $base > 36) {
            throw new Exception('digit base must be an integer between 2 and 36');
        }
        if ($base >= 10) {
            $this->chars .= '0-9';
            if ($base == 11) {
                $this->chars .= 'aA';
            } elseif ($base == 12) {
                $this->chars .= 'abAB';
            } elseif ($base > 12) {
                $range = 'a-' . chr(ord('a') + $base - 11);
                $this->chars .= $range . strtoupper($range);
            }
        } elseif ($base == 2) {
            $this->chars .= '01';
        } else {
            $this->chars .= '0-' . ($base - 1);
        }
        return $this;
    }

    /**
     * Add any character but a digit (from 0 to 9).
     * 
     * @return Charset
     */
    public function notDigit()
    {
        $this->chars .= '\\D';
        return $this;
    }

    /**
     * Add any word character.
     * 
     * @return Charset
     */
    public function wordChar()
    {
        $this->chars .= '\\w';
        return $this;
    }

    /**
     * Add any character but a word character.
     * 
     * @return Charset
     */
    public function notWordChar()
    {
        $this->chars .= '\\W';
        return $this;
    }

    /**
     * Add a whitespace.
     * 
     * @return Charset
     */
    public function whitespace()
    {
        $this->chars .= '\\s';
        return $this;
    }

    /**
     * Add anything but a whitespace.
     * 
     * @return Charset
     */
    public function notWhitespace()
    {
        $this->chars .= '\S';
        return $this;
    }

    /**
     * Add a tabulation.
     * 
     * @return Charset
     */
    public function tab()
    {
        $this->chars .= '\t';
        return $this;
    }

    /**
     * Add a carriage return.
     * 
     * @return Charset
     */
    public function cr()
    {
        $this->chars .= '\\r';
        return $this;
    }

    /**
     * Add a line feed.
     * 
     * @return Charset
     */
    public function lf()
    {
        $this->chars .= '\\n';
        return $this;
    }
    
    /**
     * Add an escape character.
     * 
     * @return Charset
     */
    public function esc()
    {
        $this->chars .= '\\e';
        return $this;
    }

    /**
     * Add a bell character.
     * 
     * @return Charset
     */
    public function bell()
    {
        $this->chars .= '\\a';
        return $this;
    }

    /**
     * Add a form feed.
     * 
     * @return Charset
     */
    public function ff()
    {
        $this->chars .= '\\f';
        return $this;
    }

    /**
     * Add a vertical tabulation.
     * 
     * @return Charset
     */
    public function vtab()
    {
        $this->chars .= '\\v';
        return $this;
    }

    /**
     * Add a backspace.
     * 
     * @return Charset
     */
    public function backspace()
    {
        $this->chars .= '\\b';
        return $this;
    }

    /**
     * Add a control character.
     * 
     * @param string $letter
     * @return Charset
     */
    public function control($letter)
    {
        $this->chars .= '\\c' . strtoupper($letter);
        return $this;
    }

    /**
     * Add a character with a given ansi code.
     * 
     * @param int $code
     * @return Charset
     */
    public function ansi($code)
    {
        $this->chars .= sprintf('\\x%02X', $code);
        return $this;
    }

    /**
     * Add unicode characters with a given unicode property.
     * 
     * @param string $unicode Unicode property (use constants in Unicode class)
     * @return Charset
     */
    public function unicode($unicode)
    {
        $this->chars .= new UnicodeProperty($unicode);
        return $this;
    }

    /**
     * Add unicode characters that have not a given unicode property.
     * 
     * @param string $unicode Unicode property (use constants in Unicode class)
     * @return Charset
     */
    public function notUnicode($unicode)
    {
        $this->chars .= new UnicodeProperty($unicode, true);
        return $this;
    }

    /**
     * Add a specific Unicode code point.
     * 
     * @param int $code
     * @return Charset
     */
    public function unicodeChar($code)
    {
        $this->chars .= sprintf('\\x{%X}', $code);
        return $this;
    }

    /**
     * Add an interval of unicode characters.
     * 
     * @param int $start The code of the first code point
     * @param int $end The code of the last code point
     * @return Charset
     */
    public function unicodeCharRange($start, $end)
    {
        $this->chars .= sprintf('\\x{%X}-\\x{%X}', $start, $end);
        return $this;
    }

    /**
     * Add Unicode grapheme.
     * 
     * @return Charset
     */
    public function extendedUnicode()
    {
        $this->chars .= '\\X';
        return $this;
    }

}
