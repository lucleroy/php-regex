# FluentRegex

PHP library with fluent interface to build regular expressions.

## Table of contents

* [Introduction](#introduction)
* [Requirements](#require)
* [Limitations](#limitations)
* [Installation](#install)
* [Usage](#usage)
    * [Workflow](#workflow)
    * [Literal Characters](#literal)
    * [Character Sets](#charset)
    * [Match any character](#anychar)
    * [Anchors](#anchors)
    * [Alternation](#alt)
    * [Quantifiers](#quantifiers)
    * [Greedy, Lazy, Possessive Quantifiers](#greedy)
    * [Grouping and Capturing](#capture)
    * [Backreferences](#backreferences)
    * [Atomic grouping](#atomic)
    * [Lookahead, Lookbehind](#lookaround)
    * [Conditionals](#cond)
    * [Case sensitivity](#case)
    * [Special Expressions](#special)

## <a name="introduction"></a> Introduction

Here is a simple example that creates a regular expression to recognize a PHP hexadecimal number (example: 0x1ff).

```php
$regex = Regex::create()
    ->literal('0')->chars('xX')->digit(16)->atLeastOne()
    ->getRegex();
```

This code is equivalent to:

```php
$regex = '/0[xX][0-9a-fA-F]+/m';
```
## <a name="require"></a> Requirements

PHP 5.5 or more.

## <a name="limitations"></a> Limitations

All the supported PHP PCRE features are listed in this document. Some features
are missing. In particular, the recursion is not supported.

## <a name="install"></a> Installation (with Composer)

Add the following to the `require` section of your composer.json file

```
"lucleroy/php-regex": "dev-master"
```
and run `composer update`.

## <a name="usage"></a> Usage

### <a name="workflow"></a> Workflow

Create a Regex object with `Regex::create`:

```php
use LucLeroy\Regex;

require 'vendor/autoload.php';

$regex = Regex::create();
```

Build the regular expression:

```php
$regex->literal('0')->chars('xX')->digit(16)->atLeastOne();
```

Retrieve the PHP Regular Expression string:

```php
echo $regex->getRegex();              // /0[xX][0-9a-fA-F]+/m
echo $regex->getUtf8Regex();          // /0[xX][0-9a-fA-F]+/mu
echo $regex->getOptimizedRegex();     // /0[xX][0-9a-fA-F]+/mS
echo $regex->getUtf8OptimizedRegex(); // /0[xX][0-9a-fA-F]+/muS
```

By default, the resulting string is surrounded with '/'. You can change this character:

```php
echo $regex->getRegex('%');              // %0[xX][0-9a-fA-F]+%m
echo $regex->getUtf8Regex('%');          // %0[xX][0-9a-fA-F]+%mu
echo $regex->getOptimizedRegex('%');     // %0[xX][0-9a-fA-F]+%mS
echo $regex->getUtf8OptimizedRegex('%'); // %0[xX][0-9a-fA-F]+%muS
```

The choosen character is automatically escaped:

```php
$regex = Regex::create()
    ->digit()->atLeastOne()->literal('%/')->digit()->atLeastOne()->literal('%');

echo $regex->getRegex();   // /\d+%\/\d+%/m
echo $regex->getRegex('%'); // %\d+\%/\d+\%%m
``` 

Note: when you convert a Regex instance to a string, you get the raw regular expression string. With the preceding example :

```php
echo "$regex"; // \d+%/\d+%
```

### <a name="literal"></a> Literal Characters

Use `Regex::literal` to match literal characters. Special characters are automatically escaped:

```php
echo Regex::create()
    ->literal('1+1=2'); // 1\+1\=2
``` 

The expression created by `Regex::literal` is indivisible: when you put a 
quantifier next to it, it applies to the whole expression and not only to the
last character:

```php
echo Regex::create()
    ->literal('ab')->anyTimes(); // (?:ab)*

echo Regex::create()
    ->literal('a')->literal('b')->anyTimes(); // ab*
``` 

### <a name="charset"></a> Character Sets

Use `Regex::chars` to match chars in a character set. Use two dots to specify a 
range of characters.

```php
echo Regex::create()
    ->chars('0..9-A..Z'); // [0-9\-A-Z]
``` 

If you want to match characters that are not in a specified set, use `Regex::notChars`:

```php
echo Regex::create()
    ->notChars('0..9'); // [^0-9]
``` 

If you need to add special characters to a character set, you can provide an 
instance of `Charset` to the methods `Regex::chars` and `Regex::notChars`. For
example, the following code matches letters and tabulations:

```php
echo Regex::create()
    ->chars(Charset::create()->chars('a..zA..Z')->tab()); // [a-zA-Z\t]
``` 

You can use the following methods to match non-printable characters:

Character | ASCII | Method
----------|-------|-------
tab | 0x09 | tab
carriage return | 0x0D | cr
line feed | 0x0A | lf
bell | 0x07 | bell
escape | 0x1B | esc
form feed | 0x0C | ff
vertical tab | 0x0B | vtab
backspace | 0x08 | backspace

You can use shorthands for common character classes:

Character Class | Method
------|-------
digit | digit
word character | wordChar
whitespace character | whitespace
not digit | notDigit
not word character | notWordChar
not whitespace character | notWhitespace

In addition, you can pass a base (from 2 to 26) to `Charset::digit` and `Charset::notDigit`:

```php
echo Regex::create()
    ->chars(Charset::create()->digit()); // [\d]

echo Regex::create()
    ->chars(Charset::create()->digit(2)); // [01]

echo Regex::create()
    ->chars(Charset::create()->digit(16)); // [0-9a-fA-F]
```

You can match control characters (ASCII codes from 1 to 26) with `Charset::control`:

```php
echo Regex::create()
    ->chars(Charset::create()->control('C')); // [\cC]
```

You can match an ANSI character with `Charset::ansi`:

```php
echo Regex::create()
    ->chars(Charset::create()->ansi(0x7f)); // [\x7F]
```

Finally, `Charset` provides some methods to work with Unicode characters.

Use `Charset::extendedUnicode` to match a Unicode grapheme:

```php
echo Regex::create()
    ->chars(Charset::create()->extendedUnicode()); // [\X]
```

Use `Charset::unicodeChar` to match a specific unicode point:

```php
echo Regex::create()
    ->chars(Charset::create()->unicodeChar(0x2122)); // [\x{2122}]
```

Use `Charset::unicodeCharRange` to match a range of unicode points:

```php
echo Regex::create()
    ->chars(Charset::create()->unicodeCharRange(0x80, 0xff)); // [\x{80}-\x{FF}]
```

Use `Charset::unicode` to match a a Unicode class or category. For your convenience,
a Unicode class with Unicode properties is provided:

```php
echo Regex::create()
    ->chars(Charset::create()->unicode(Unicode::Letter)); // [\pL]
```

**Note :** all the methods of `Charset` are available in `Regex`:

```php
echo Regex::create()
    ->digit();        // \d

echo Regex::create()
    ->digit(8);       // [0-7]
```

### <a name="anychar"></a> Match any character

If you want to match any character, use `Regex::anyChar`:

```php
echo Regex::create()
    ->anyChar(); // (?s:.)
```

Note that the regular expression generated by the previous method matches also newlines.
If you don't want to match newlines, use the method `Regex::notNewline`:

```php
echo Regex::create()
    ->notNewline();   // .
```

### <a name="anchors"></a> Anchors

To match at the start of the string or at the end of the string, use `Regex:startOfString`
and `Regex::endOfString`.

```php
echo Regex::create()
    ->startOfString()->literal('123')->endOfString(); // \A123\z
```

The preceding method matches **only** at the string ends. If you want
to match at the start of a line or at the end of a line, use `Regex:startOfLine`
and `Regex::endOfLine`.

```php
echo Regex::create()
    ->startOfLine()->literal('123')->endOfLine(); // ^123$
```

You can match at a word boundary with `Regex::wordLimit`. To match a position
which is not a word boundary, use `Regex::notWordLimit`:

```php
echo Regex::create()
    ->wordLimit();    // \b

echo Regex::create()
    ->notWordLimit(); // \B
```

### <a name="alt"></a> Alternation

Use `Regex::alt` to create an alternation. There are several ways to provide each 
choice.

Firstly, you can pass choices as arguments:

```php
$choices = [
    Regex::create()->literal('b'),
    Regex::create()->literal('c')
];

echo Regex::create()
    ->literal('a')
    ->alt($choices);  // a(?:b|c)
```

Secondly, you can give to the method the number of choices, which are taken from 
the previous expressions:

```php
echo Regex::create()
    ->literal('a')
    ->literal('b')
    ->literal('c')
    ->alt(2);       // a(?:b|c)
```

Finally, you can mark the position of the first choice with `Regex::start` and give
no argument to the `Regex::alt` method:

```php
echo Regex::create()
    ->literal('a')
    ->start()
    ->literal('b')
    ->literal('c')
    ->alt();       // a(?:b|c)
```

If you want to create an alternation with literals only, you can use `Regex::literalAlt`:

```php
echo Regex::create()
    ->literalAlt(['one', 'two', 'three']);  // one|two|three
```

### <a name="quantifiers"></a> Quantifiers

Use `Regex::optional` to match an optional expression:

```php
echo Regex::create()
    ->literal('a')
    ->literal('b')
    ->optional();     // ab?
```

Use `Regex::anyTimes` to match any number of consecutive occurences of the 
previous expression:

```php
echo Regex::create()
    ->literal('a')
    ->literal('b')
    ->anyTimes();     // ab*
```

Use `Regex::atLeastOne` to match at least one occurences of the 
previous expression:

```php
echo Regex::create()
    ->literal('a')
    ->literal('b')
    ->atLeastOne();   // ab+
```

Use `Regex::atLeast` to match a minimum number of occurences of the 
previous expression:

```php
echo Regex::create()
    ->literal('a')
    ->literal('b')
    ->atLeast(2);     // ab{2,}
```

Use `Regex::between` to match a number of occurences of the 
previous expression between two numbers:

```php
echo Regex::create()
    ->literal('a')
    ->literal('b')
    ->between(2,5);   // ab{2,5}
```

Use `Regex::times` to match a precise number of occurences of the 
previous expression:

```php
echo Regex::create()
    ->literal('a')
    ->literal('b')
    ->times(2);   // ab{2}
```

Note: instead of add the quantifier to the previous expression, you can provide 
a Regex instance as last argument of each of these methods.

### <a name="greedy"></a> Greedy, Lazy, Possessive Quantifiers

In the previous examples, the quantifiers are greedy. This is the default 
behavior. More precisely, a quantifier can have 4 modes: GREEDY, LAZY, POSSESSIVE,
and UNDEFINED. When the regular expression string is generated, a quantifier 
with the UNDEFINED mode is considered as GREEDY. UNDEFINED is the default mode
but you can use `Regex::greedy`, `Regex::lazy` and `Regex::possessive` on an 
empty Regex (just after the creation) to modify the default behavior:

```php
echo Regex::create()
    ->lazy()
    ->literal('a')
    ->anyTimes()
    ->literal('b')
    ->anyTimes();     // a*?b*?
```

The same methods can be used after a quantifier to change its behavior:

```php
echo Regex::create()
    ->lazy()
    ->literal('a')
    ->anyTimes()
    ->greedy()
    ->literal('b')
    ->anyTimes();    // a*b*?
```

You can also change the behavior of all quantifiers of a group:

```php
echo Regex::create()
    ->literal('a')->literal('b')->optional()->group(2)->anyTimes()
    ->literal('c')->anyTimes()
    ->alt(2)
    ->lazy();  // (?:ab?)*?|c*?
```

In the previous example, you can notice that the behavior does not apply to the
optional quantifier. You can use `Regex::greedyRecursive`,
`Regex::lazyRecursive` and `Regex::possessiveRecursive` to apply the behavior
recursively:

```php
echo Regex::create()
    ->literal('a')->literal('b')->optional()->group(2)->anyTimes()
    ->literal('c')->anyTimes()
    ->alt(2)
    ->lazyRecursive();  // (?:ab??)*?|c*?
```

When applied to a group, all these methods modify the behavior of a quantifier
only if it has the UNDEFINED mode. In the example, if the optional quantifier
is set to GREEDY, it retains its behavior:

```php
echo Regex::create()
    ->literal('a')->literal('b')->optional()->greedy()->group(2)->anyTimes()
    ->literal('c')->anyTimes()
    ->alt(2)
    ->lazyRecursive();  // (?:ab?)*?|c*?
```

### <a name="capture"></a> Grouping and Capturing

By default, when the library needs to create a group, it is not captured. To
capture an expression, you must use `Regex::capture`:

```php
echo Regex::create()
    ->literal('a')
    ->literal('b')
    ->literal('c')
    ->alt(2)->capture();  // a(b|c)
```

To create a named group, give an argument to `Regex::capture`:

```php
echo Regex::create()
    ->literal('a')->capture('myname'); // (?P<myname>a)
```

You can group several expressions with `Regex::group`. As with `Regex::alt`, you
can specify the expressions to group by using the `Regex::start` method or by
giving the number of expressions to group or by giving directly the expression
(a Regex instance):

```php
echo Regex::create()
    ->literal('a')
    ->start()
    ->literal('b')
    ->literal('c')
    ->group()->capture();        // a(bc)

echo Regex::create()
    ->literal('a')
    ->literal('b')
    ->literal('c')
    ->group(2)->capture();       // a(bc)

$group = Regex::create()->literal('b')->literal('c');
echo Regex::create()
    ->literal('a')
    ->group($group)->capture();  // a(bc)
```

### <a name="backreferences"></a> Backreferences

Use `Regex::ref` to make a backreference:

```php
echo Regex::create()
    ->literal('a')->anyTimes()->capture()
    ->literal('-')
    ->ref(1);  // (a*)\-\g{1}

echo Regex::create()
    ->literal('a')->anyTimes()->capture('myname')
    ->literal('-')
    ->ref('myname');  // (?P<myname>a*)\-(?P=myname)
```

### <a name="atomic"></a> Atomic grouping

Use `Regex::atomic` to make an atomic group:

```php
echo Regex::create()
    ->literal('a')->anyTimes()
    ->atomic();  // (?>a*)
```

### <a name="lookaround"></a> Lookahead, Lookbehind

Use `Regex::after`, `Regex::notAfter`, `Regex::before`, `Regex::notBefore`:

```php
echo Regex::create()
    ->literal('a')
    ->literal('b')
    ->after();        // a(?=b)

echo Regex::create()
    ->literal('a')
    ->literal('b')
    ->notAfter();     // a(?!b)

echo Regex::create()
    ->literal('a')
    ->before()
    ->literal('b');   // (?<=a)b

echo Regex::create()
    ->literal('a')
    ->notBefore()
    ->literal('b');   // (?<!a)b
```

### <a name="cond"></a> Conditionals

Create a conditional with `Regex::cond`. This method must be preceded by a 
condition, an expression to match when the condition is true, and an optional
expression to match when the condition is false.

Use `Regex::match` to check if a captured group matches:

```php
echo Regex::create()
    ->literal('a')->capture()->optional()
    ->match(1)
    ->literal('b')
    ->literal('c')
    ->cond();       // (a)?(?(1)b|c)

echo Regex::create()
    ->literal('a')->capture('myname')->optional()
    ->match('myname')
    ->literal('b')
    ->literal('c')
    ->cond();       // (?P<myname>a)?(?(myname)b|c)
```

`Regex::match` can also be used outside of a conditional. In this case, the
regular expression fails if captured group does not match:

```php
echo Regex::create()
    ->literal('a')->capture()->optional()
    ->match(1);     // (a)?(?(1)|(?!))
```

The others allowed conditions are  `Regex::after`, `Regex::notAfter`, 
`Regex::before`, `Regex::notBefore`:

```php
echo Regex::create()
    ->literal('a')->before()
    ->literal('b')
    ->literal('c')
    ->cond();      // (?(?<=a)b|c)
```

If you want the 'else' expression to match nothing, you can remove the 'else'
expression:

```php
echo Regex::create()
    ->literal('a')->before()
    ->literal('b')
    ->cond();      // (?(?<=a)b|)
```

If you want the 'then' expression to match nothing, you can use `Regex::notCond`
to inverse the condition:

```php
echo Regex::create()
    ->literal('a')->before()
    ->literal('c')
    ->notCond();   // (?(?<=a)|c)
```

You can also use `Regex::nothing`:

```php
echo Regex::create()
    ->literal('a')->before()
    ->nothing()
    ->literal('c')
    ->cond();      // (?(?<=a)|c)
```

### <a name="case"></a> Case sensitivity

By default, the regular expression is case sensitive. Use `Regex::caseSensitive`
or `Regex::caseInsensitive` to change this behavior. Each of these methods accepts
an optional boolean argument. If this argument is `false`, the behavior is 
inverted: `$regex->caseSensitive(false)` is equivalent to `$regex->caseInsensitive()`.

These methods change the behavior of the last expression:

```php
echo Regex::create()
    ->literal('a')
    ->literal('b')
    ->caseInsensitive()
    ->literal('c');   // a(?i)b(?-i)c
```
When used at the beginning of the Regex, the whole expression is affected:

```php
echo Regex::create()
    ->caseInsensitive()
    ->literal('a')
    ->literal('b')
    ->literal('c');   // (?i)abc(?-i)
```

### <a name="special"></a> Special Expressions

`Regex::crlf` matches a Carriage Return followed by a Line Feed (Windows line breaks):

```php
echo Regex::create()
    ->crlf();   // \r\n
```