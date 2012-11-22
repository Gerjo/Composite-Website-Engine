<?php

class HssDecoder {
    private $hss;
    private $mapping;
    private $isDirty;
    private $asCss;

    // I'd use const, but a class constante is always public,
    // which is less semantically correct than this.
    private static $START_NEST    = '{';
    private static $END_NEST      = '}';
    private static $END_RULE      = ';';
    private static $START_PSEUDO  = ':';
    private static $GLUE          = '&';

    public function __construct() {
        $this->isDirty = false;
        $this->hss     = "";
        $this->mapping = array();
    }

    public function toCss() {
        // Retrieve the cached version:
        if(!$this->isDirty) {
            return $this->asCss;
        }

        // Since CSS has no root-node, and my parser expects one,
        // I'm wrapping the lot into a HTML selector. Shouldn't break
        // any existing documents.
        $withHack = "html { " . $this->hss . " }";
        $this->parseStyle($withHack);

        $this->asCss = "";

        ksort($this->mapping);

        foreach($this->mapping as $selector => $rules) {
            $this->asCss .= $selector . ' {' . PHP_EOL;
            $this->asCss .= $rules;
            $this->asCss .= '}' . PHP_EOL . PHP_EOL;
        }

        return $this->asCss;
    }

    public function addHss($hss) {
        $this->isDirty = true;
        $this->hss    .= $hss;
    }

    private function addSelector($selector, $rules) {
        if(!empty($rules)) {
            if(isset($mapping[$selector])) {
                // TODO: merge? For now, override.
                $this->mapping[$selector] = $rules;
            } else {
                // New CSS selector:
                $this->mapping[$selector] = $rules;
            }
        } else {
            // A selector without rules, don't even bother with anything.
            // TODO: permit override to reset a style?
        }
    }

    private function parseStyle($string, $start = 0, $selectorHint = "", $selectorFamily = "") {
        $hasSelector = false;
        $selector    = $selectorHint;
        $style       = "";
        $buffer      = "";

        for($i = $start; $i < strlen($string); ++$i) {
            $char = $string[$i];

            if($char == self::$START_NEST) {
                $tentativeSelector = trim($buffer);

                if(!$hasSelector) {
                    // Oh, we found a selector, jolly good.
                    $selector .= $tentativeSelector;

                } else {

                    // Selectors prefixed with the GLUE symbol, receive no space.
                    if(substr($tentativeSelector, 0, 1) == self::$GLUE) {
                        // Trim the glue symbol:
                        $tentativeSelector = ltrim($tentativeSelector, self::$GLUE);

                    } else if(substr($tentativeSelector, 0, 1) == self::$START_PSEUDO) {
                        // Do nothing. Generally PSEUDO's need no space.

                    } else {
                        // Prefix with a space:
                        $tentativeSelector = ' ' . $tentativeSelector;
                    }

                    // So we already have a selector, yet we found another. This means
                    // we just encountered a sub-selector. Let's recurse!
                    $i = $this->parseStyle($string, $i, $tentativeSelector, $selectorFamily . $selector);
                }

                $buffer      = "";
                $hasSelector = true;

            } else if($char == self::$END_NEST) {
                // Finished parsing this (sub) style hierarchy.
                $this->addSelector($selectorFamily . $selector, $style);

                return $i;

            } else if($char == self::$END_RULE) {
                // The buffer turns out to be a CSS rule! Note we apply some
                // not really needed formatting here.
                $style .= "\t" . ltrim($buffer . $char) . PHP_EOL;
                $buffer = "";

            } else {
                // We don't know what we just parsed. Could be a selector or
                // part of a CSS rule.
                $buffer .= $char;
            }

        }

        // Sensible, incase of error.
        return strlen($string);
    }

}