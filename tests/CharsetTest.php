<?php

namespace LucLeroy\Regex;

use PHPUnit_Framework_TestCase;

class CharsetTest extends PHPUnit_Framework_TestCase
{

    public function test_non_printable_chars()
    {
        $this->assertEquals('[\a\e\f\n\r\t\v\b]',
            Charset::create()->bell()->esc()->ff()->lf()->cr()->tab()->vtab()->backSpace());
    }

    public function testControl()
    {
        $this->assertEquals('[\cG]', Charset::create()->control('g'));
    }

    public function testAnsi()
    {
        $this->assertEquals('[\x1B]', Charset::create()->ansi(0x1b));
    }

    public function testUnicodeChar()
    {
        $this->assertEquals('[\x{123F}]', Charset::create()->unicodeChar(0x123F));
    }

    public function testDigit()
    {
        $this->assertEquals('[\d]', Charset::create()->digit());
        $this->assertEquals('[01]', Charset::create()->digit(2));
        $this->assertEquals('[0-7]', Charset::create()->digit(8));
        $this->assertEquals('[0-9aA]', Charset::create()->digit(11));
        $this->assertEquals('[0-9abAB]', Charset::create()->digit(12));
        $this->assertEquals('[0-9a-fA-F]', Charset::create()->digit(16));
    }

    public function testNotDigit()
    {
        $this->assertEquals('[\D]', Charset::create()->notDigit());
    }

    public function testWordChar()
    {
        $this->assertEquals('[\w]', Charset::create()->wordChar());
    }

    public function testNotWordChar()
    {
        $this->assertEquals('[\W]', Charset::create()->notWordChar());
    }

    public function testWhitespace()
    {
        $this->assertEquals('[\s]', Charset::create()->whitespace());
    }

    public function testNotWhitespace()
    {
        $this->assertEquals('[\S]', Charset::create()->notWhitespace());
    }

    public function testExtendedUnicode()
    {
        $this->assertEquals('[\X]', Charset::create()->extendedUnicode());
    }

    public function testUnicodeCharRange()
    {
        $this->assertEquals('[\x{123F}-\x{1300}]',
            Charset::create()->unicodeCharRange(0x123F, 0x1300));
    }

    public function testUnicode()
    {
        $this->assertEquals('[\pL]', Charset::create()->unicode(Unicode::Letter));
        $this->assertEquals('[\p{Ll}]',
            Charset::create()->unicode(Unicode::LetterLower));
        $this->assertEquals('[\p{Arabic}]',
            Charset::create()->unicode(Unicode::ScriptArabic));
    }

    public function testNotUnicode()
    {
        $this->assertEquals('[\PL]',
            Charset::create()->notUnicode(Unicode::Letter));
        $this->assertEquals('[\P{Ll}]',
            Charset::create()->notUnicode(Unicode::LetterLower));
        $this->assertEquals('[\P{Arabic}]',
            Charset::create()->notUnicode(Unicode::ScriptArabic));
    }

    public function testCharRange()
    {
        $this->assertEquals('[a-z]', Charset::create()->charRange('a', 'z'));
        $this->assertEquals('[a-z0-9_\-\+]',
            Regex::create()->chars(Charset::create()->chars('a..z0..9_-+')));
    }

}
