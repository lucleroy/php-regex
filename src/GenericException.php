<?php

namespace LucLeroy\Regex;

use Exception;

/**
 * @method static throw($code = 0, Exception $previous = null)
 */
class GenericException extends Exception
{

    const TYPE_INTEGER = 'integer';
    const TYPE_STRING = 'string';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_ARRAY = 'array';
    const TYPE_FLOAT = 'float';
    
    protected $skip = 0;
    protected $msg;
    protected $previous;

    public function __call($name, $arguments)
    {
       if ($name === 'throw') {
            call_user_func_array([$this, 'throwException'], $arguments);
        }
    }

    public function throwException($code = 0, Exception $previous = null)
    {
        $this->code = $code;
        $this->previous = $previous;
        $skip = $this->skip;
        $trace = debug_backtrace();
        foreach ($trace as $call) {
            if ($call['function'] === 'call_user_func_array')
                continue;
            if (!isset($call['class']) || !is_a($call['class'], __CLASS__, true)) {
                if ($skip === 0)
                    break;
                else
                    $skip--;
            }
        }
        $loc = '';
        if (isset($call['class'])) {
            $loc .= $call['class'] . '::';
        }
        if (isset($call['function'])) {
            $loc .= $call['function'] . ': ';
        }
        $this->message = $loc . $this->msg;
        $this->file = $call['file'];
        $this->line = $call['line'];
        throw $this;
    }

    public function __construct($message)
    {
        $this->msg = $message;
    }

    public function skip($count = 1)
    {
        $this->skip = $count;
        return $this;
    }

    protected static function getArgText($argNumber)
    {
        if (!is_array($argNumber)) {
            $argNumber = [$argNumber];
        }
        $arg = array_shift($argNumber);
        $argText = 'argument #' . (isset($arg) ? $arg : '?');
        foreach ($argNumber as $value) {
            if (isset($value)) {
                $argText .= "[$value]";
            } else {
                $argText .= "[?]";
            }
        }
        return $argText;
    }

    public static function argType($argNumber, $acceptedTypes)
    {
        $argText = ucfirst(self::getArgText($argNumber));
        if (!is_array($acceptedTypes)) {
            $acceptedTypes = [$acceptedTypes];
        }
        if (isset($acceptedTypes[1])) {
            $msg = "$argText must have one of this types: " . implode(', ', $acceptedTypes) . '.';
        } else {
            $msg = "$argText must be of type " . $acceptedTypes[0] . '.';
        }
        return new static($msg);
    }
    
    public static function argInteger($argNumber) {
        return static::argType($argNumber, self::TYPE_INTEGER);
    }
    
    public static function argBoolean($argNumber) {
        return static::argType($argNumber, self::TYPE_BOOLEAN);
    }
    
    public static function argString($argNumber) {
        return static::argType($argNumber, self::TYPE_STRING);
    }
    
    public static function argFloat($argNumber) {
        return static::argType($argNumber, self::TYPE_FLOAT);
    }
    
    public static function argArray($argNumber) {
        return static::argType($argNumber, self::TYPE_ARRAY);
    }

    public static function argEmpty($argNumber)
    {
        $argText = ucfirst(self::getArgText($argNumber));
        $msg = "$argText must not be empty.";
        return new static($msg);
    }

    public static function argRange($argNumber, $min, $max, $includeMin = true, $includeMax = true)
    {
        $argText = ucfirst(self::getArgText($argNumber));
        $minMsg = $includeMin ? 'at least' : 'greater than';
        $maxMsg = $includeMax ? 'at most' : 'less than';
        if ($min === $max) {
            $msg = "$argText must be equal to $min.";
        } elseif (isset($min) && isset($max)) {
            $msg = "$argText must be $minMsg $min and $maxMsg $max.";
        } elseif (isset($min)) {
            $msg = "$argText must be $minMsg $min.";
        } else {
            $msg = "$argText must be $maxMsg $max.";
        }
        return new static($msg);
    }

    public static function argLengthRange($argNumber, $min, $max)
    {
        $argText = self::getArgText($argNumber);
        if ($min === $max) {
            $msg = "The length of $argText must be equal to $min.";
        } elseif (isset($min) && isset($max)) {
            $msg = "The length of $argText must be at least $min and at most $max.";
        } elseif (isset($min)) {
            $msg = "The length of $argText must be at least $min.";
        } else {
            $msg = "The length of $argText must be at most $max.";
        }
        return new static($msg);
    }

    public static function invalidState()
    {
        $msg = "The method cannot be applied to this object (invalid state).";
        return new static($msg);
    }

    public static function custom($msg)
    {
        return new static($msg);
    }

}
