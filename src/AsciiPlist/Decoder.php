<?php

namespace AsciiPlist;

class Decoder {
    /**
     * @param   string      $in
     * @return  array|null
     */
    static function decode($in) {
        $out = null;
        try {
            $lexer_map  = (new \ReflectionClass('\AsciiPlist\Lexer'))->getConstants();
            $parser_map = (new \ReflectionClass('\AsciiPlist\Parser'))->getConstants();
            $tokens_map = array();
            foreach ($lexer_map as $k => $v) {
                if (isset($parser_map[$k])) {
                    $tokens_map[$v] = $parser_map[$k];
                }
            }
            $lex = new \AsciiPlist\Lexer((string)$in);
            $parser = new \AsciiPlist\Parser($lex);
            while ($lex->yylex()) {
                if (isset($tokens_map[$lex->token])) {
                    $value = ($lex->token == \AsciiPlist\Lexer::T_STRINGLITERAL)
                        ? substr(stripslashes($lex->value), 1, -1)
                        : $lex->value;
                    $parser->doParse($tokens_map[$lex->token], $value);
                }
            }
            $parser->doParse(0, 0);
            $out = $parser->data;
        }
        catch (\AsciiPlist\LexerException $e) {}
        return $out;
    }
}
