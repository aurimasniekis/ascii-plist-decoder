//<?php
%name Parser

%include {

/* include.start */

/* include.end */

}

/*  */

%declare_class {class Parser}
%include_class {

/* include_class.start */

public $data = null;

/* include_class.end */

}

%syntax_error {

/* syntax_error.start */

/* syntax_error.end */

}

%token_prefix  T_

start ::= data(A). { $this->data = A; }

data(A) ::= dict(B). { A = B; }
data(A) ::= array(B). { A = B; }
data(A) ::= string_literal(B). { A = B; }

string_literal(A) ::= STRINGLITERAL(B). { A = B; }

array(A) ::= LPAREN RPAREN. { A = array(); }
array(A) ::= LPAREN values(B) RPAREN. { A = B; }

values(A) ::= value(B). { A = array(B); }
values(A) ::= values(B) COMMA value(C). { A = array_merge(B, array(C)); }

value(A) ::= data(B). { A = B; }

dict(A) ::= LCURLY RCURLY. { A = array(); }
dict(A) ::= LCURLY key_values(B) RCURLY. { A = B; }

key_values(A) ::= key_value(B). { A = array(B[0] => B[1]); }
key_values(A) ::= key_values(B) key_value(C). { A = array_merge(B, array(C[0] => C[1])); }

key_value(A) ::= key(B) EQUALS value(C) SEMICOLON. { A = array(B, C); }

key(A) ::= STRINGLITERAL(B). { A = B; }
