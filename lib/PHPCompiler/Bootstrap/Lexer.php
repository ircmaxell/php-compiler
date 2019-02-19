<?php

namespace PHPCompiler\Bootstrap;

class Lexer {

    private string $file;
    private int $fileLength;
    private int $filePos = 0;
    private int $lineNumber = 0;
    private int $columnNumber = 0;
    private string $filename;

    public function begin(string $file, string $filename): void {
        $this->file = $file;
        $this->fileLength = strlen($file);
        $this->filename = $filename;
        $this->filePos = 0;
        $this->lineNumber = 0;
        $this->columnNumber = 0;
    }

    public function next(): Token {
restart:
        if ($this->filePos >= $this->fileLength) {
            return $this->token(Token::T_END_OF_FILE, '');
        }
        $char = $this->file[$this->filePos++];
        switch ($char) {
            case ';':
                return $this->token(Token::T_SEMICOLON, ';');
            case '$':
                return $this->token(Token::T_DOLLAR, '$');
            case '\\':
                return $this->token(Token::T_BACKSLASH, '\\');
            case '{':
                return $this->token(Token::T_OB, '{');
            case '}':
                return $this->token(Token::T_CB, '}');
            case '[':
                return $this->token(Token::T_OSB, '[');
            case ']':
                return $this->token(Token::T_CSB, ']');
            case '(':
                return $this->token(Token::T_OP, '(');
            case ')':
                return $this->token(Token::T_CP, ')');
            case ',':
                return $this->token(Token::T_COMMA, ',');
            case '/':
                if ($this->filePos < $this->fileLength && $this->file[$this->filePos] === '/') {
                    $this->filePos++;
                    // consume comment
                    while ($this->filePos < $this->fileLength) {
                        $char = $this->file[$this->filePos++];
                        if ($char === chr(10)) {
                            break;
                        }
                        if ($char === chr(13)) {
                            if ($this->filePos < $this->fileLength && $this->file[$this->filePos] === chr(10)) {
                                $this->filePos++;
                            }
                            break;
                        }
                    }
                    $this->lineNumber++;
                    $this->columnNumber = 0;
                    goto restart;
                }
                goto syntax_error;
            case chr(13):
                if ($this->filePos < $this->fileLength && $this->file[$this->filePos] === chr(10)) {
                    $this->filePos++;
                }
            case chr(10):
                $this->lineNumber++;
                $this->columnNumber = 0;
            case ' ': // ignore whitespace
            case chr(9):
                goto restart;
            case '&':
                if ($this->filePos < $this->fileLength 
                    && $this->file[$this->filePos] === '&' 
                ) {
                    $this->filePos++;
                    return $this->token(Token::T_LOGICAL_AND, '&&');
                }
                return $this->token(Token::T_BITWISE_AND, '&');
            case '|':
                if ($this->filePos < $this->fileLength 
                    && $this->file[$this->filePos] === '|' 
                ) {
                    $this->filePos++;
                    return $this->token(Token::T_LOGICAL_OR, '||');
                }
                return $this->token(Token::T_BITWISE_OR, '|');
            case '+':
                if ($this->filePos < $this->fileLength 
                    && $this->file[$this->filePos] === '+' 
                ) {
                    $this->filePos++;
                    return $this->token(Token::T_INCREMENT, '++');
                }
                return $this->token(Token::T_PLUS, '+');
            case '-':
                if ($this->filePos < $this->fileLength 
                    && $this->file[$this->filePos] === '-' 
                ) {
                    $this->filePos++;
                    return $this->token(Token::T_DECREMENT, '--');
                }
                if ($this->filePos < $this->fileLength 
                    && ctype_digit($this->file[$this->filePos]) 
                ) {
                    return $this->emitNumber($char);
                }
                return $this->token(Token::T_MINUS, '-');
            case '=':
                if ($this->filePos + 1 < $this->fileLength 
                    && $this->file[$this->filePos] === '=' 
                    && $this->file[$this->filePos + 1] === '='
                ) {
                    $this->filePos += 2;
                    return $this->token(Token::T_IDENTICAL, '===');
                }
                return $this->token(Token::T_ASSIGN, '=');
            case '\'':
                return $this->emitString();
            case '.':
                if ($this->filePos + 1 < $this->fileLength 
                    && $this->file[$this->filePos] === '.' 
                    && $this->file[$this->filePos + 1] === '.'
                ) {
                    $this->filePos += 2;
                    return $this->token(Token::T_SPLAT, '...');
                }
                return $this->token(Token::T_CONCAT, '.');
            case '<':
                if ($this->filePos < $this->fileLength && $this->file[$this->filePos] === '=') {
                    $this->filePos++;
                    return $this->token(Token::T_LTE, '<=');
                }
                if ($this->filePos + 3 < $this->fileLength) {
                    if (
                           $this->file[$this->filePos] === '?' 
                        && $this->file[$this->filePos + 1] === 'p'
                        && $this->file[$this->filePos + 2] === 'h'
                        && $this->file[$this->filePos + 3] === 'p'
                    ) {
                        $this->filePos += 4;
                        return $this->token(Token::T_OPEN_PHP, '<?php');
                    }
                }
                return $this->token(Token::T_LT, '<');
            case '>':
                if ($this->filePos < $this->fileLength && $this->file[$this->filePos] === '=') {
                    $this->filePos++;
                    return $this->token(Token::T_GTE, '>=');
                }
                return $this->token(Token::T_GT, '>');
            case ':':
                if ($this->filePos < $this->fileLength && $this->file[$this->filePos] === ':') {
                    $this->filePos++;
                    return $this->token(Token::T_SCOPE, '::');
                }
                return $this->token(Token::T_COLON, ':');
        }
        if (ctype_alpha($char) || $char === '_') {
            // identifier
            return $this->emitIdentifier($char);
        } elseif (ctype_digit($char)) {
            return $this->emitNumber($char);
        }
syntax_error:
        throw new \RuntimeException(
            printf('Syntax error: unexpected token \'%s\' in %s on line %d:%d', $char, $this->filename, $this->lineNumber, $this->columnNumber)
        );
    }

    private function emitString(): Token {
        $buffer = '';
        $columnNumber = $this->columnNumber;
        while ($this->filePos < $this->fileLength) {
            $char = $this->file[$this->filePos++];
            switch ($char) {
                case '\\':
                    if ($this->filePos < $this->fileLength) {
                        $char = $this->file[$this->filePos++];
                        $columnNumber++;
                        goto append_buffer;
                    }
                    break 2;
                case '\'':
                    $token = $this->token(Token::T_STRING, $buffer);
                    $this->columnNumber = $columnNumber + 1;
                    return $token;
                case chr(13):
                    if ($this->filePos < $this->fileLength && $this->file[$this->filePos] === chr(10)) {
                        $buffer .= $char;
                        $char = $this->file[$this->filePos++];
                    }
                case chr(10):
                    $this->lineNumber++;
                    $columnNumber = -1;
                default:
append_buffer:
                    $columnNumber++;
                    $buffer .= $char;
            }
        }
        throw new \RuntimeException(
            printf('Syntax Error: unexpected end of file, expecting \' in %s on line %d:%d', $this->filename, $this->lineNumber, $this->columnNumber)
        );
    }

    private function emitNumber(string $char): Token {
        $buffer = $char;
        while ($this->filePos < $this->fileLength) {
            $tmp = $this->file[$this->filePos];
            if (ctype_digit($tmp)) {
                $buffer .= $tmp;
                $this->filePos++;
            } else {
                break;
            }
        }
        return $this->token(Token::T_INTEGER, $buffer);
    }

    private function emitIdentifier(string $char): Token {
        $buffer = $char;
        while ($this->filePos < $this->fileLength) {
            $tmp = $this->file[$this->filePos];
            if (ctype_alnum($tmp) || $tmp === '_') {
                $buffer .= $tmp;
                $this->filePos++;
            } else {
                break;
            }
        }
        return $this->token(Token::T_IDENTIFIER, $buffer);
    }

    private function token(int $type, string $string): Token {
        $node = new Token(
            $type, 
            $string, 
            $this->filename, 
            $this->lineNumber, 
            $this->columnNumber
        );
        $this->columnNumber += strlen($string);
        return $node;
    }
    
}

class Token {
    const T_OPEN_PHP = 1;
    const T_LTE = 2;
    const T_LT = 3;
    const T_IDENTIFIER = 4;
    const T_OB = 5; // {
    const T_CB = 6; // }
    const T_OP = 7; // (
    const T_CP = 8; // )
    const T_IDENTICAL = 9; // ===
    const T_ASSIGN = 10; // =
    const T_INTEGER = 11;
    const T_SEMICOLON = 12; // ;
    const T_CONCAT = 13; // .
    const T_SPLAT = 14; // ...
    const T_STRING = 15;
    const T_DOLLAR = 16; // $
    const T_BACKSLASH = 17; // \
    const T_PLUS = 18; // +
    const T_INCREMENT = 19; // ++
    const T_MINUS = 20; // -
    const T_DECREMENT = 21; // --
    const T_GT = 22;  // >
    const T_GTE = 23; // >=
    const T_COMMA = 24; // ,
    const T_COLON = 25; // :
    const T_SCOPE = 26; // ::
    const T_OSB = 27; // [
    const T_CSB = 28; // ]
    const T_BITWISE_AND = 29; // &
    const T_LOGICAL_AND = 30; // &&
    const T_BITWISE_OR = 31; // |
    const T_LOGICAL_OR = 32; // ||

    const T_END_OF_FILE = 512;


    public int $type;
    public string $value;
    public string $filename;
    public int $line;
    public int $column;

    public function __construct(int $type, string $value, string $filename, int $line, int $column) {
        $this->type = $type;
        $this->value = $value;
        $this->filename = $filename;
        $this->line = $line;
        $this->column = $column;
    }
}