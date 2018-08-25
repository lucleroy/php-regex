<?php

namespace LucLeroy\Regex;

use Exception;
use LucLeroy\Regex\Expressions\Alternative;
use LucLeroy\Regex\Expressions\AnsiCharacter;
use LucLeroy\Regex\Expressions\Assertion;
use LucLeroy\Regex\Expressions\AtomicGroup;
use LucLeroy\Regex\Expressions\BackReference;
use LucLeroy\Regex\Expressions\CapturingGroup;
use LucLeroy\Regex\Expressions\Char;
use LucLeroy\Regex\Expressions\ComplementCharset;
use LucLeroy\Regex\Expressions\Conditional;
use LucLeroy\Regex\Expressions\ControlCharacter;
use LucLeroy\Regex\Expressions\Literal;
use LucLeroy\Regex\Expressions\MatchAssertion;
use LucLeroy\Regex\Expressions\MatchRecursive;
use LucLeroy\Regex\Expressions\Nothing;
use LucLeroy\Regex\Expressions\Quantifier;
use LucLeroy\Regex\Expressions\RegularExpression;
use LucLeroy\Regex\Expressions\SpecialCharSet;
use LucLeroy\Regex\Expressions\UnicodeCharacter;
use LucLeroy\Regex\Expressions\UnicodeProperty;
use LucLeroy\Regex\Expressions\UnsignedIntRange;

class Regex extends RegularExpression {

    private $options;
    private $expressions = [];
    private $defaultQuantifierPolicy = Quantifier::UNDEFINED;

    function __construct($expressions = []) {
        $this->options = 'm';
        $this->expressions = $expressions;
    }

    /**
     *
     * @return Regex
     */
    public static function create($expressions = []) {
        return new self($expressions);
    }

    /**
     * Return an array containing the expressions of the sequence.
     * 
     * @return array
     */
    public function getExpressions() {
        return $this->expressions;
    }

    /**
     * Return the regex pattern (without delimiters and options).
     * 
     * @return string
     */
    public function toString() {
        if (count($this->expressions) === 1) {
            return $this->expressions[0] . '';
        }
        $result = '';
        foreach ($this->expressions as $expression) {
            if ($expression instanceof Alternative) {
                $result .= '(?:' . $expression . ')';
            } else {
                $result .= $expression;
            }
        }
        return $result;
    }

    /**
     * Return the PHP regex string with delimiters and options as used by the PCRE functions.
     * 
     * Note: Special characters and delimiter are escaped.
     *
     * @param string $delimiter
     *            The delimiter, '/' by default.
     * @return string The resulting regex.
     */
    public function getRegex($delimiter = '/') {
        return $delimiter . str_replace($delimiter, '\\' . $delimiter, $this) . $delimiter . $this->options;
    }

    public function getOptimizedRegex($delimiter = '/') {
        return $this->getRegex($delimiter) . 'S';
    }

    public function getUtf8Regex($delimiter = '/') {
        return $this->getRegex($delimiter) . 'u';
    }

    public function getUtf8OptimizedRegex($delimiter = '/') {
        return $this->getRegex($delimiter) . 'uS';
    }

    /**
     * Match a literal string.
     * 
     * @param string $string The string to match
     * @return Regex
     */
    public function literal($string) {
        is_string($string) || RegexException::argString(1)->throw();
        $string != '' || RegexException::argEmpty(1)->throw();

        if (strlen($string) > 1) {
            $this->expressions[] = new Literal($string);
        } else {
            $this->expressions[] = new Char($string);
        }
        return $this;
    }

    /**
     * Make a regex optional.
     * 
     * @param RegularExpression $expression The regular expression to make optional. If null, the preceding expression is made optional.
     * @return Regex
     */
    public function optional(RegularExpression $expression = null) {
        if ($expression === null) {
            !empty($this->expressions) || RegexException::notEnoughExpressions(1)->throw();
            $expression = array_pop($this->expressions);
        }
        $this->expressions[] = new Quantifier($expression, 0, 1, $this->defaultQuantifierPolicy);
        return $this;
    }

    /**
     * Repeat a regex zero or more times.
     * 
     * @param RegularExpression $expression The regular expression to repeat. If null, the the preceding expression is repeated.
     * @return Regex
     */
    public function anyTimes(RegularExpression $expression = null) {
        if ($expression === null) {
            !empty($this->expressions) || RegexException::notEnoughExpressions(1)->throw();
            $expression = array_pop($this->expressions);
        }
        $this->expressions[] = new Quantifier($expression, 0, INF, $this->defaultQuantifierPolicy);
        return $this;
    }

    /**
     * Repeat a regex one or more times.
     *
     * @param RegularExpression $expression The regular expression to repeat. If null, the the preceding expression is repeated.
     * @return Regex
     */
    public function atLeastOne(RegularExpression $expression = null) {
        if ($expression === null) {
            !empty($this->expressions) || RegexException::notEnoughExpressions(1)->throw();
            $expression = array_pop($this->expressions);
        }
        $this->expressions[] = new Quantifier($expression, 1, INF, $this->defaultQuantifierPolicy);
        return $this;
    }

    /**
     * Repeat a regex at least $min times.
     * 
     * @param int $min
     * @param RegularExpression $expression The regular expression to repeat. If null, the the preceding expression is repeated.
     * @return Regex
     */
    public function atLeast($min, RegularExpression $expression = null) {
        is_int($min) || RegexException::argInteger(1)->throw();
        $min >= 0 || RegexException::argRange(1, 0, null)->throw();

        if ($expression === null) {
            !empty($this->expressions) || RegexException::notEnoughExpressions(1)->throw();
            $expression = array_pop($this->expressions);
        }
        $this->expressions[] = new Quantifier($expression, $min, INF, $this->defaultQuantifierPolicy);
        return $this;
    }

    /**
     * Repeat a regex at least $min times and at most $max times.
     * 
     * @param int $min
     * @param int $max
     * @param RegularExpression $expression The regular expression to repeat. If null, the the preceding expression is repeated.
     * @return Regex
     */
    public function between($min, $max, RegularExpression $expression = null) {
        is_int($min) || RegexException::argInteger(1)->throw();
        $min >= 0 || RegexException::argRange(1, 0, null)->throw();
        is_int($max) || RegexException::argInteger(2)->throw();
        $max >= $min || RegexException::argRange(2, $min, null)->throw();

        if ($expression === null) {
            !empty($this->expressions) || RegexException::notEnoughExpressions(1)->throw();
            $expression = array_pop($this->expressions);
        }
        $this->expressions[] = new Quantifier($expression, $min, $max, $this->defaultQuantifierPolicy);
        return $this;
    }

    /**
     * Repeat a regex $times times.
     * 
     * @param int $times
     * @param RegularExpression $expression The regular expression to repeat.
     * @return Regex
     */
    public function times($times, RegularExpression $expression = null) {
        is_int($times) || RegexException::argInteger(1)->throw();
        $times >= 0 || RegexException::argRange(1, 0, null)->throw();

        if ($expression === null) {
            !empty($this->expressions) || RegexException::notEnoughExpressions(1)->throw();
            $expression = array_pop($this->expressions);
        }
        $this->expressions[] = new Quantifier($expression, $times, null, $this->defaultQuantifierPolicy);
        return $this;
    }

    /**
     * Make an atomic group.
     * 
     * @param RegularExpression $expression The regular expression to make atomic. If null, the the preceding expression is made atomic.
     * @return Regex
     */
    public function atomic(RegularExpression $expression = null) {
        if ($expression === null) {
            !empty($this->expressions) || RegexException::notEnoughExpressions(1)->throw();
            $expression = array_pop($this->expressions);
        }
        $this->expressions[] = new AtomicGroup($expression);
        return $this;
    }

    /**
     * Capture the preceding expression.
     * 
     * @param string $name If not null, a named capture is made, otherwise an ordinary capture is made.
     * @return Regex
     */
    public function capture($name = null) {
        !empty($this->expressions) || RegexException::notEnoughExpressions(1)->throw();
        $this->expressions[] = new CapturingGroup(array_pop($this->expressions), $name);
        return $this;
    }

    /**
     * Backreference.
     * 
     * @param mixed $name If a string, makes a backreference to a captured named group. 
     * If an integer, makes a normal backreference. 
     * @return Regex
     */
    public function ref($name) {
        $this->expressions[] = new BackReference($name);
        return $this;
    }

    /**
     * Group expressions.
     * 
     * @param mixed $expression If it is a RegularExpression, it turns into a group. 
     * If it is an integer, a group is made from the last $expression expressions.
     * If it is null, a group is made from all the preceding expressions.
     * @return Regex
     */
    public function group($expression = null) {
        if ($expression === null) {
            !empty($this->expressions) || RegexException::notEnoughExpressionsFromMark(1)->throw();
            $expression = new Regex($this->popExpressionsFromMark());
        } elseif (is_int($expression)) {
            $expression >= 1 || RegexException::argRange(1, 1, null)->throw();
            $expression <= count($this->expressions) || RegexException::notEnoughExpressions($expression)->throw();
            $expression = new Regex(array_splice($this->expressions, - $expression));
        } else {
            $expression instanceof RegularExpression || RegexException::argType(1, ['integer', RegularExpression::class]);
            $expression = new Regex([$expression]);
        }
        $this->expressions[] = $expression;
        return $this;
    }

    private function popExpressionsFromMark() {
        $expressions = [];
        $top = end($this->expressions);
        while ($top && $top->mark == 0) {
            array_unshift($expressions, array_pop($this->expressions));
            $top = end($this->expressions);
        }
        if ($top) {
            $top->mark--;
        }
        return $expressions;
    }

    /**
     * Alternation.
     * 
     * @param mixed $choices If it is an array of RegularExpression, an alternation is made from these.
     * If it is an integer, an alternation is made from the last $choices expressions.
     * If it is null, an alternation is made from all the preceding expressions.
     * @return Regex
     */
    public function alt($choices = null) {
        if ($choices === null) {
            $choices = $this->popExpressionsFromMark();
            isset($choices[1]) || RegexException::notEnoughExpressionsFromMark(2)->throw();
        }
        if (is_int($choices)) {
            $choices >= 2 && $choices <= count($this->expressions) || RegexException::argRange(1, 2,
                            count($this->expressions))->throw();
            $choices = array_splice($this->expressions, - $choices);
        }
        count($choices) >= 2 || RegexException::argLengthRange(1, 2, null)->throw();
        $this->expressions[] = new Alternative($choices);
        return $this;
    }

    /**
     * Alternation with literals only.
     * 
     * @param array $literals
     * @return Regex
     */
    public function literalAlt(array $literals) {
        isset($literals[1]) || RegexException::argLengthRange(1, 2, INF);
        $expressions = [];
        foreach ($literals as $literal) {
            $expressions[] = new Literal($literal);
        }
        $this->expressions[] = new Alternative($expressions);
        return $this;
    }

    /**
     * Match nothing.
     * 
     * @return Regex
     */
    public function nothing() {
        $this->expressions[] = new Nothing();
        return $this;
    }

    /**
     * Match any character.
     * 
     * @return Regex
     */
    public function anyChar() {
        $this->expressions[] = new SpecialCharSet('(?s:.)');
        return $this;
    }

    /**
     * Match any character.
     *
     * @return Regex
     */
    public function notNewline() {
        $this->expressions[] = new SpecialCharSet('.');
        return $this;
    }

    /**
     * Match the beginning of line.
     * 
     * @return Regex
     */
    public function startOfLine() {
        $this->expressions[] = new SpecialCharSet('^');
        return $this;
    }

    /**
     * Math the end of line.
     * 
     * @return Regex
     */
    public function endOfLine() {
        $this->expressions[] = new SpecialCharSet('$');
        return $this;
    }

    /**
     * Match a character used in the representation of a number written in a 
     * given base (allowed base from 2 to 36). If letters are necessary, lower
     * and upper versions are added.
     * 
     * @param int $base Base, between 2 and 36.
     * @return Regex
     */
    public function digit($base = null) {
        if (isset($base)) {
            $this->expressions[] = (new Charset())->digit($base);
        } else {
            $this->expressions[] = new SpecialCharSet('\\d');
        }
        return $this;
    }

    /**
     * Match any character but a digit.
     * 
     * @return Regex
     */
    public function notDigit($base = 10) {
        if (!isset($base)) {
            $this->expressions[] = new SpecialCharSet('\\D');
            return $this;
        }
        is_int($base) || RegexException::argInteger(1)->throw();
        $base >= 2 && $base <= 36 || RegexException::argRange(1, 2, 36)->throw();

        if ($base == 10) {
            $this->expressions[] = new SpecialCharSet('\\D');
        } else {
            $this->expressions[] = (new ComplementCharset())->digit($base);
        }
        return $this;
    }

    /**
     * Match a word character.
     * 
     * @return Regex
     */
    public function wordChar() {
        $this->expressions[] = new SpecialCharSet('\\w');
        return $this;
    }

    /**
     * Match any character but a word character.
     * 
     * @return Regex
     */
    public function notWordChar() {
        $this->expressions[] = new SpecialCharSet('\\W');
        return $this;
    }

    /**
     * Match a whitespace.
     * 
     * @return Regex
     */
    public function whitespace() {
        $this->expressions[] = new SpecialCharSet('\\s');
        return $this;
    }

    /**
     * Match anything but a whitespace.
     * 
     * @return Regex
     */
    public function notWhitespace() {
        $this->expressions[] = new SpecialCharSet('\\S');
        return $this;
    }

    /**
     * Match a limit of a word.
     * 
     * @return Regex
     */
    public function wordLimit() {
        $this->expressions[] = new SpecialCharSet('\\b');
        return $this;
    }

    /**
     * Match any character but a limit of a word.
     * 
     * @return Regex
     */
    public function notWordLimit() {
        $this->expressions[] = new SpecialCharSet('\\B');
        return $this;
    }

    /**
     * Match the begging of the string.
     * 
     * @return Regex
     */
    public function startOfString() {
        $this->expressions[] = new SpecialCharSet('\\A');
        return $this;
    }

    /**
     * Match the end of the string.
     *
     * @return Regex
     */
    public function endOfString() {
        $this->expressions[] = new SpecialCharSet('\\z');
        return $this;
    }

    /**
     * @return Regex
     */
    public function endOfStringIgnoreFinalBreak() {
        $this->expressions[] = new SpecialCharSet('\\Z');
        return $this;
    }

    /**
     * Match a tabulation.
     * 
     * @return Regex
     */
    public function tab() {
        $this->expressions[] = new SpecialCharSet('\\t');
        return $this;
    }

    /**
     * Match a carriage return.
     * 
     * @return Regex
     */
    public function cr() {
        $this->expressions[] = new SpecialCharSet('\\r');
        return $this;
    }

    /**
     * Match a line feed.
     * 
     * @return Regex
     */
    public function lf() {
        $this->expressions[] = new SpecialCharSet('\\n');
        return $this;
    }

    /**
     * Match ESC.
     * 
     * @return Regex
     */
    public function esc() {
        $this->expressions[] = new SpecialCharSet('\\e');
        return $this;
    }

    /**
     * Match BELL character.
     * 
     * @return Regex
     */
    public function bell() {
        $this->expressions[] = new SpecialCharSet('\\a');
        return $this;
    }

    /**
     * Match a Form Feed
     * 
     * @return Regex
     */
    public function ff() {
        $this->expressions[] = new SpecialCharSet('\\f');
        return $this;
    }

    /**
     * Match a Vertical Tabulation.
     * 
     * @return Regex
     */
    public function vtab() {
        $this->expressions[] = new SpecialCharSet('\\v');
        return $this;
    }

    /**
     * Match Backspace.
     * 
     * @return Regex
     */
    public function backspace() {
        $this->expressions[] = Charset::create()->backspace();
        return $this;
    }

    /**
     * Match a CR followed by a LF (Windows line break).
     * 
     * @return Regex
     */
    public function crlf() {
        $this->expressions[] = new Regex([
            new SpecialCharSet('\\r'),
            new SpecialCharSet('\\n')
        ]);
        return $this;
    }

    /**
     * Change the case sensitivity.
     * 
     * @param bool $enabled If true (the default), search is case insentitive.
     * @return Regex
     */
    public function caseInsensitive($enabled = true) {
        return $this->addOption('i', $enabled);
    }

    /**
     * Change the case sensitivity.
     *
     * @param bool $enabled If true (the default), search is case insentitive.
     * @return Regex
     */
    public function caseSensitive($enabled = true) {
        return $this->addOption('i', !$enabled);
    }

    protected function addOption($code, $enabled) {
        if (empty($this->expressions)) {
            $exp = $this;
        } else {
            $exp = end($this->expressions);
        }
        if ($enabled) {
            $exp->setOption($code);
        } else {
            $exp->unsetOption($code);
        }
        return $this;
    }

    /**
     * Zero-width positive lookahead.
     * 
     * @return Regex
     */
    public function after() {
        !empty($this->expressions) || RegexException::notEnoughExpressions(1)->throw();
        $assertion = new Assertion(array_pop($this->expressions), '?=');
        $this->checkRegex($assertion);
        $this->expressions[] = $assertion;
        return $this;
    }

    /**
     * Zero-width negative lookahead.
     * 
     * @return Regex
     */
    public function notAfter() {
        !empty($this->expressions) || RegexException::notEnoughExpressions(1)->throw();
        $assertion = new Assertion(array_pop($this->expressions), '?!');
        $this->checkRegex($assertion);
        $this->expressions[] = $assertion;
        return $this;
    }

    /**
     * Zero-width positive lookbehind.
     * 
     * @return Regex
     */
    public function before() {
        !empty($this->expressions) || RegexException::notEnoughExpressions(1)->throw();
        $assertion = new Assertion(array_pop($this->expressions), '?<=');
        $this->checkRegex($assertion);
        $this->expressions[] = $assertion;
        return $this;
    }

    private function checkRegex(RegularExpression $regex) {
        $errors = [];
        $errorHandler = set_error_handler(function($errno, $errstr) use (&$errors) {
            $errors[] = $errstr;
        }, E_WARNING);
        if (!$regex instanceof Regex) {
            $regex = (new Regex([$regex]));
        }
        $code = @preg_match($regex->getRegex(), '');
        set_error_handler($errorHandler);
        if ($errors) {
            $msg = 'Regex ' . $regex . ': ' . trim(end(explode(':', $errors[0], 3)));
            throw new Exception($msg);
        }
        return $errors;
    }

    /**
     * Zero-width negative lookbehind.
     * 
     * @return Regex
     */
    public function notBefore() {
        !empty($this->expressions) || RegexException::notEnoughExpressions(1)->throw();
        $assertion = new Assertion(array_pop($this->expressions), '?<!');
        $this->checkRegex($assertion);
        $this->expressions[] = $assertion;
        return $this;
    }

    /**
     * Character Class.
     * 
     * @param mixed $chars string or Charset 
     * @return Regex
     */
    public function chars($chars) {
        return $this->_chars(Charset::class, $chars);
    }

    /**
     * Exclude Character Class.
     *
     * @param mixed $chars Several (or array of) strings or CharacterClass.
     * @return Regex
     */
    public function notChars($chars) {
        return $this->_chars(ComplementCharset::class, $chars);
    }

    private function _chars($class, $chars) {
        $charClass = new $class;
        if (is_string($chars)) {
            $charClass->chars($chars);
        } elseif ($chars instanceof Charset) {
            $charClass->appendCharacterClass($chars);
        } else {
            RegexException::argType(1, [RegexException::TYPE_STRING, Charset::class])->skip()->throw();
        }
        $this->expressions[] = $charClass;
        return $this;
    }

    /**
     * Match a character with a given ansi code.
     * 
     * @param type $code
     * @return Regex
     */
    public function ansi($code) {
        $this->expressions[] = new AnsiCharacter($code);
        return $this;
    }

    /**
     * Match a control character.
     * 
     * @param string $letter
     * @return Regex
     */
    public function control($letter) {
        $this->expressions[] = new ControlCharacter($letter);
        return $this;
    }

    /**
     * Match a single Unicode code point that has a given property.
     * 
     * @param string $unicode Unicode property (use constants in Unicode class)
     * @return Regex
     */
    public function unicode($unicode) {
        $this->expressions[] = new UnicodeProperty($unicode);
        return $this;
    }

    /**
     * Match a single Unicode code point that has not a given property.
     *
     * @param string $unicode Unicode property (use constants in Unicode class)
     * @return Regex
     */
    public function notUnicode($unicode) {
        $this->expressions[] = new UnicodeProperty($unicode, true);
        return $this;
    }

    /**
     * Match a specific Unicode code point.
     * 
     * @param string $hex The code of the code point
     * @return Regex
     */
    public function unicodeChar($code) {
        $this->expressions[] = new UnicodeCharacter($code);
        return $this;
    }

    /**
     * Match a single Unicode grapheme.
     * 
     * @return Regex
     */
    public function extendedUnicode() {
        $this->expressions[] .= '\\X';
        return $this;
    }

    /**
     * Conditional.
     * 
     * There must be an Assertion in the 3 preceding expressions: the first assertion in this 3 is taken as $cond,
     * the following expression is taken as $true, and the following, if present, is taken as $false.
     * 
     * If $cond is verified, match $true, else match $false.
     * 
     * @return Regex
     */
    public function cond() {
        $count = count($this->expressions);
        if ($this->expressions[$count - 2] instanceof Assertion) {
            $false = new Nothing();
            $true = array_pop($this->expressions);
        } else {
            $false = array_pop($this->expressions);
            $true = array_pop($this->expressions);
        }
        $cond = array_pop($this->expressions);
        $this->expressions[] = new Conditional($cond, $true, $false);
        return $this;
    }

    /**
     * Inverse Conditional.
     *
     * There must be an Assertion in the 3 preceding expressions: the first assertion in this 3 is taken as $cond,
     * the following expression is taken as $true, and the following, if present, is taken as $false.
     *
     * If $cond is not verified, match $true, else match $false.
     * 
     * @return Regex
     */
    public function notCond($cond = null, $true = null, $false = null) {
        $count = count($this->expressions);
        if ($this->expressions[$count - 2] instanceof Assertion) {
            $false = new Nothing();
            $true = array_pop($this->expressions);
        } else {
            $false = array_pop($this->expressions);
            $true = array_pop($this->expressions);
        }
        $cond = array_pop($this->expressions);
        $this->expressions[] = new Conditional($cond, $false, $true);
        return $this;
    }

    /**
     * Assert that a captured group matches.
     * 
     * @param int $number The number of the captured group.
     * @return Regex
     */
    public function match($number) {
        $this->expressions[] = new MatchAssertion($number);
        return $this;
    }

    /**
     * If the regex is empty, set the default behavior for the next expressions.
     * If the last expression is a quantifier, it is made greedy. 
     * Else it is made greedy only if its behavior (greedy, 
     * lazy, possessive) is not set already.
     * 
     * @return Regex
     */
    public function greedy() {
        return $this->quantifierPolicy(Quantifier::GREEDY, false);
    }

    /**
     * If the regex is empty, set the default behavior for the next expressions.
     * If the last expression is a quantifier, it is made greedy. 
     * Else it is made greedy only if its behavior (greedy, 
     * lazy, possessive) is not set already.
     * If the expression is compound, its components are also set resursively.
     * 
     * @return Regex
     */
    public function greedyRecursive() {
        return $this->quantifierPolicy(Quantifier::GREEDY, true);
    }

    /**
     * If the regex is empty, set the default behavior for the next expressions.
     * If the last expression is a quantifier, it is made lazy. 
     * Else it is made lazy only if its behavior (greedy, 
     * lazy, possessive) is not set already.
     * 
     * @return Regex
     */
    public function lazy() {
        return $this->quantifierPolicy(Quantifier::LAZY, false);
    }

    /**
     * If the regex is empty, set the default behavior for the next expressions.
     * If the last expression is a quantifier, it is made lazy. 
     * Else it is made lazy only if its behavior (greedy, 
     * lazy, possessive) is not set already.
     * If the expression is compound, its components are also set resursively.
     * 
     * @return Regex
     */
    public function lazyRecursive() {
        return $this->quantifierPolicy(Quantifier::LAZY, true);
    }

    /**
     * If the regex is empty, set the default behavior for the next expressions.
     * If the last expression is a quantifier, it is made possessive. 
     * Else it is made possessive only if its behavior (greedy, 
     * lazy, possessive) is not set already.
     * 
     * @return Regex
     */
    public function possessive() {
        return $this->quantifierPolicy(Quantifier::POSSESSIVE, false);
    }

    /**
     * If the regex is empty, set the default behavior for the next expressions.
     * If the last expression is a quantifier, it is made possessive. 
     * Else it is made possessive only if its behavior (greedy, 
     * lazy, possessive) is not set already.
     * If the expression is compound, its components are also set resursively.
     * 
     * @return Regex
     */
    public function possessiveRecursive() {
        return $this->quantifierPolicy(Quantifier::POSSESSIVE, true);
    }

    private function quantifierPolicy($policy, $recursive) {
        if (empty($this->expressions)) {
            $this->defaultQuantifierPolicy = $policy;
        } else {
            $expression = end($this->expressions);
            if ($expression instanceof Quantifier) {
                $expression->setPolicy($policy);
            }
            $expression->setQuantifierPolicy($policy, $recursive);
        }
        return $this;
    }

    protected function setQuantifierPolicy($policy, $recursive = false) {
        foreach ($this->expressions as $expression) {
            $expression->setQuantifierPolicy($policy, $recursive);
        }
    }

    /**
     * Marks the start of a group or an alternative.
     * 
     * @return Regex
     */
    public function start() {
        if (!empty($this->expressions)) {
            $expression = end($this->expressions);
            $expression->mark++;
        }
        return $this;
    }

    /**
     * Match a non negative integer in the given range.
     * 
     * Allowed values for $leadingZeros are:
     * - false: leading zeros are not allowed
     * - true: leading zeros are required and must pad the integer to the same length as $max
     * - null: leading zeros are allowed and must pad the integer to a length less than or equal to $max
     * 
     * @param int $min
     * @param int $max
     * @param bool $leadingZeros
     * @return Regex
     */
    public function unsignedIntRange($min, $max, $leadingZeros = null) {
        is_int($min) || RegexException::argInteger(1)->throw();
        is_int($max) || RegexException::argInteger(2)->throw();
        $min >= 0 || RegexException::argRange(1, 0, null)->throw();
        $max >= $min || RegexException::argRange(2, $min, null)->throw();

        if ($min < 10 && $max < 10) {
            if ($min === $max) {
                return $this->literal("$min");
            } elseif ($min + 1 === $max) {
                return $this->chars("$min$max");
            } elseif ($min === 0 && $max === 9) {
                return $this->digit();
            } else {
                return $this->chars("$min..$max");
            }
        }

        $this->expressions[] = new UnsignedIntRange($min, $max, $leadingZeros);
        
        return $this;
    }
    
    /**
     * Match the whole pattern recursively.
     * 
     * @return Regex
     */
    public function matchRecursive()
    {
        $this->expressions[] = new MatchRecursive();
        return $this;
    }

}
