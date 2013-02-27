<?php

namespace AsciiPlist;

class Lexer {
    const T_LCURLY = 1;
    const T_RCURLY = 2;
    const T_LPAREN = 3;
    const T_RPAREN = 4;
    const T_SPACE = 5;
    const T_SEMICOLON = 6;
    const T_COMMA = 7;
    const T_EQUALS = 8;
    const T_STRINGLITERAL = 9;

    public $data;
    public $N;
    public $token;
    public $value;
    public $line;
    public $state = 1;

    function __construct($data) {
        $this->data = $data;
        $this->N = 0;
        $this->line = 1;
    }

    function advance() {
        if ($this->{'yylex' . $this->state}()) {
            return true;
        }
        return false;
    }

    private $_yy_state = 1;
    private $_yy_stack = array();

    function yylex() {
        return $this->{'yylex' . $this->_yy_state}();
    }

    function yypushstate($state) {
        array_push($this->_yy_stack, $this->_yy_state);
        $this->_yy_state = $state;
    }

    function yypopstate() {
        $this->_yy_state = array_pop($this->_yy_stack);
    }

    function yybegin($state) {
        $this->_yy_state = $state;
    }

    function yylex1() {
        if ($this->N >= strlen($this->data)) {
            return false; // end of input
        }
        $yy_global_pattern = "/^(\\{)|^(\\})|^(\\()|^(\\))|^([ \t\r\n]+)|^(;)|^(,)|^(\\=)|^(\\\"(\\\\.|[^\\\\\\\"])*\\\")/";

        do {
            if (preg_match($yy_global_pattern, substr($this->data, $this->N), $yymatches)) {
                $yymatches = array_filter($yymatches, 'strlen'); // remove empty sub-patterns
                if (!count($yymatches)) {
                    throw new \AsciiPlist\LexerException('Error: lexing failed because a rule matched an empty string');
                }
                next($yymatches); // skip global match
                $this->token = key($yymatches); // token number
                $this->value = current($yymatches); // token value
                $r = $this->{'yy_r1_' . $this->token}();
                if ($r === null) {
                    $this->N += strlen($this->value);
                    $this->line += substr_count("\n", $this->value);
                    // accept this token
                    return true;
                } elseif ($r === true) {
                    // we have changed state
                    // process this token in the new state
                    return $this->yylex();
                } elseif ($r === false) {
                    $this->N += strlen($this->value);
                    $this->line += substr_count("\n", $this->value);
                    if ($this->N >= strlen($this->data)) {
                        return false; // end of input
                    }
                    // skip this token
                    continue;
                } else {
                    $yy_yymore_patterns = array(
                        1 => "^(\\})|^(\\()|^(\\))|^([ \t\r\n]+)|^(;)|^(,)|^(\\=)|^(\\\"(\\\\.|[^\\\\\\\"])*\\\")",
                        2 => "^(\\()|^(\\))|^([ \t\r\n]+)|^(;)|^(,)|^(\\=)|^(\\\"(\\\\.|[^\\\\\\\"])*\\\")",
                        3 => "^(\\))|^([ \t\r\n]+)|^(;)|^(,)|^(\\=)|^(\\\"(\\\\.|[^\\\\\\\"])*\\\")",
                        4 => "^([ \t\r\n]+)|^(;)|^(,)|^(\\=)|^(\\\"(\\\\.|[^\\\\\\\"])*\\\")",
                        5 => "^(;)|^(,)|^(\\=)|^(\\\"(\\\\.|[^\\\\\\\"])*\\\")",
                        6 => "^(,)|^(\\=)|^(\\\"(\\\\.|[^\\\\\\\"])*\\\")",
                        7 => "^(\\=)|^(\\\"(\\\\.|[^\\\\\\\"])*\\\")",
                        8 => "^(\\\"(\\\\.|[^\\\\\\\"])*\\\")",
                        9 => "",
                    );

                    // yymore is needed
                    do {
                        if (!strlen($yy_yymore_patterns[$this->token])) {
                            throw new \AsciiPlist\LexerException('cannot do yymore for the last token');
                        }
                        if (preg_match($yy_yymore_patterns[$this->token],
                            substr($this->data, $this->N), $yymatches)
                        ) {
                            $yymatches = array_filter($yymatches, 'strlen'); // remove empty sub-patterns
                            next($yymatches); // skip global match
                            $this->token = key($yymatches); // token number
                            $this->value = current($yymatches); // token value
                            $this->line = substr_count("\n", $this->value);
                        }
                    } while ($this->{'yy_r1_' . $this->token}() !== null);
                    // accept
                    $this->N += strlen($this->value);
                    $this->line += substr_count("\n", $this->value);
                    return true;
                }
            } else {
                throw new \AsciiPlist\LexerException('Unexpected input at line' . $this->line . ': ' . $this->data[$this->N]);
            }
            break;
        } while (true);
    }

    function yy_r1_1() {}

    function yy_r1_2() {}

    function yy_r1_3() {}

    function yy_r1_4() {}

    function yy_r1_5() {}

    function yy_r1_6() {}

    function yy_r1_7() {}

    function yy_r1_8() {}

    function yy_r1_9() {}
}

class LexerException extends \Exception {}
