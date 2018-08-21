<?php

namespace LucLeroy\Regex\Expressions;

use LucLeroy\Regex\Regex;

class UnsignedIntRange extends RegularExpression
{

    private $min;
    private $max;
    private $leadingZeros;
    
    function __construct($min, $max, $leadingZeros = null)
    {
        $this->min = $min;
        $this->max = $max;
        $this->leadingZeros = $leadingZeros;
    }

    public function toString()
    {
        $expressions = $this->expressions($this->min, $this->max);
        return implode('|', $expressions);
    }

    private function expressions($min, $max) {
        $expressions = [];

        $t1 = intval(substr($min, 0, -1) . "9");
        if ($t1 >= $max) {
            $t1 = $max;
        } else {
            $t2 = $t1 + 1;
            $t4 = intval(substr($max, 0, -1) . "0");
            $t3 = $t4 - 1;
        }

        if (isset($t4) && $t4 <= $max) {
            $expressions[] = $this->sameTensRange($t4, $max, 0);
        }

        if (isset($t2) && $t2 <= $t3) {
            $exp = $this->expressions(intval(substr($t2, 0, -1)), intval(substr($t3, 0, -1)));
            foreach ($exp as $e) {
                $expressions[] = $e . '\d';
            }
        }

        if ($min <= $t1) {
            $expressions[] = $this->sameTensRange($min, $t1, strlen("$max") - strlen("$min"));
        }
        
        return $expressions;
    }

    private function digitRange($min, $max) {
        if ($min === 0 && $max === 9) {
            return '\d';
        } elseif ($min === $max) {
            return "$min";
        } elseif ($min + 1 === $max) {
            return  "[$min$max]";
        } else {
            return "[$min-$max]";
        }
    }

    private function sameTensRange($min, $max, $zeros) {
        $tens = substr($min, 0, -1);
        $exp = $this->paddingZeros($zeros);
        if (!empty($tens)) {
            $exp .= $tens;
        }
        $exp .= $this->digitRange($min % 10, $max % 10);
        return $exp;
    }

    private function paddingZeros($count) {
        if ($this->leadingZeros === false || $count === 0) {
            return '';
        }
        if ($this->leadingZeros) {
            return $count > 1 ? '0{' . $count . '}' : '0';
        } elseif ($count === 1) {
            return '0?';
        } else {
            return '0{0,' . $count . '}';
        }
    }
}