<?php
declare(strict_types=1);

namespace PHPCompiler\Bootstrap;

class Parser {

    public Lexer $lexer;
    public Context $context;
    public array $nodes;

    public string $namespace = '\\';

    public function __construct(Context $context) {
        $this->lexer = new Lexer;
        $this->context = $context;
    }

    public function parse(string $file, string $filename): void {
        $this->namespace = '\\';
        $this->lexer->begin($file, $filename);
        $this->assertAndConsumeSequence(
            Seq::open_php(),
            Seq::identifier('declare'),
            Seq::type(Token::T_OP, '('),
            Seq::identifier('strict_types'),
            Seq::type(Token::T_ASSIGN, '='),
            Seq::integer('1'),
            Seq::type(Token::T_CP, ')'),
            Seq::semicolon()
        );

        $node = $this->lexer->next();
        while ($node->type !== Token::T_END_OF_FILE) {
            if ($node->type !== Token::T_IDENTIFIER) {
                $this->syntaxErrorExpecting(
                    new Seq(Token::T_IDENTIFIER, 'T_IDENTIFIER'),
                    $node
                );
            }
            switch ($node->value) {
                case 'namespace':
                    $this->parseNamespace($node);
                    break;
                case 'function':
                    $this->context->addDecl($this->parseFunction($node));
                    break;
                case 'class':
                    $this->context->addDecl($this->parseClass($node));
                    break;
                default:
                    $this->syntaxError(', only namespace, class, and function declarations allowed at top level', $node);
            }


            $node = $this->lexer->next();
        }
    }



    public function parseClass(Token $node): Decl {
        $decl = Decl::_class($node);
        $class = $decl->class;
        $node = $this->lexer->next();
        $this->assertSequence(Seq::type(Token::T_IDENTIFIER, 'identifier'), $node);
        $class->name = $node->value;
        $this->assertAndConsumeSequence(Seq::ob());

        $node = $this->lexer->next();
        while ($node->type !== Token::T_CB) {
            $this->assertSequence(Seq::identifier('public'), $node);
            $class->addPublicDecl($this->parseClassDecl());
            $node = $this->lexer->next();
        }

        $class->endLine = $node->line;
        return $decl;
    }

    public function parseClassDecl(): Decl {
        $node = $this->lexer->next();
        if ($node->type === Token::T_IDENTIFIER && $node->value === 'function') {
            return $this->parseMethod($node);
        } elseif ($node->type === Token::T_BACKSLASH || $node->type === Token::T_IDENTIFIER) {
            // property
            $decl = Decl::property($node);
            $property = $decl->property;
            $property->type = $this->extractType($node);
            $this->assertSequence(Seq::dollar(), $this->lexer->next());
            $node = $this->lexer->next();
            $this->assertSequence(Seq::type(Token::T_IDENTIFIER, 'identifier'), $node);
            $property->name = $node->value;
            $node = $this->lexer->next();
            $this->assertSequence(Seq::semicolon(), $node);
            $decl->finish($node);
            return $decl;
        }
    }

    public function extractType(Token $node): string {
        $type = '';
        if ($node->type === Token::T_IDENTIFIER) {
            switch ($node->value) {
                case 'int':
                case 'float':
                case 'bool':
                case 'void':
                case 'string':
                    return $node->value;
            }
            $type = $this->namespace . '\\' . $node->value;
        }
        $node = $this->lexer->peek();
        while ($node->type === Token::T_BACKSLASH) {
            $node = $this->lexer->next();
            $type .= $node->value;
            $node = $this->lexer->next();
            $this->assertSequence(Seq::type(Token::T_IDENTIFIER, 'identifier'), $node);
            $type .= $node->value;
            $node = $this->lexer->peek();
        }
        if ($type === '') {
            $this->syntaxError('expected type declaration', $node);
        }
        return $type;
    }

    public function parseFunction(Token $node): Decl {
        $decl = Decl::_function($node);
        $func = $decl->function;
        $node = $this->lexer->next();
        $this->assertSequence(Seq::type(Token::T_IDENTIFIER, 'identifier'), $node);
        $func->name = $node->value;
        $this->assertAndConsumeSequence(Seq::op());

        $func->params = $this->parseParams();
        $this->assertAndConsumeSequence(Seq::cp());
        $func->return = $this->parseReturn();

        $func->body = $this->parseBlock();

        $decl->finish($node);
        return $decl;
    }

    public function parseMethod(Token $node): Decl {
        $decl = Decl::method($node);
        $method = $decl->method;
        $method->filename = $node->filename;
        $method->startLine = $node->line;
        $node = $this->lexer->next();
        $this->assertSequence(Seq::type(Token::T_IDENTIFIER, 'identifier'), $node);
        $method->name = $node->value;
        $this->assertAndConsumeSequence(Seq::op());

        $method->params = $this->parseParams();
        $this->assertAndConsumeSequence(Seq::cp());
        $method->return = $this->parseReturn();

        $method->body = $this->parseBlock();

        $decl->finish($node);
        return $decl;
    }

    public function parseBlock(): Block {
        $block = $returnBlock = new Block;
        $this->assertAndConsumeSequence(Seq::ob());
        $node = $this->lexer->next();
        while ($node->type !== Token::T_CB) {
            $this->assertNotEOF('expected expr or }', $node);
            if ($node->type === Token::T_IDENTIFIER) {
                switch ($node->value) {
                    case 'switch':
                    case 'if':
                    case 'while':
                        $block = $this->parseBranch($node, $block);
                        break;
                    default:
                        $block->addExpression($this->parseExpression($node));
                } 
            } else {
                $block->addExpression($this->parseExpression($node));
            }

            $node = $this->lexer->next();
        }
        return $returnBlock;
    }

    public function parseExpression(Token $node): Expr {
        $exprStack = new ExprStack;
        $opStack = new OperatorStack;
        while (true) {
            $exprStack->push($this->parseUnaryExpression($node));
            $node = $this->lexer->peek();
            switch ($node->type) {
                // Look for an operator
                case Token::T_SEMICOLON:
                    // consume the semicolon
                    $this->lexer->next();
                case Token::T_COMMA:
                case Token::T_CP:
                case Token::T_CSB:
                    // end of expression
                    goto result;
                case Token::T_CONCAT:
                    $this->lexer->next();
                    $op = new Op(Op::TYPE_CONCAT);
                    $this->normalizeStacks($exprStack, $opStack, $op);
                    $opStack->push($op);
                    break;
                default:
                    $this->syntaxError('unknown expression combinator', $node);
            }
            $node = $this->lexer->next();
        }
result:
        while (!$opStack->isEmpty()) {
            $exprStack->push($opStack->pop()->toExpr());
        }
        $result = $exprStack->flatten();
        if (is_null($result)) {
            $this->syntaxError('expression expected', $this->lexer->peek());
        }
        return $result;
    }

    public function normalizeStacks(ExprStack $exprs, OperatorStack $ops, Op $op): void {
        while ($ops->shouldPopForNextOp($op)) {
            $exprs->push($ops->pop()->toExpr());
        }
    }

    public function parseUnaryExpression(Token $node): Expr {
        switch ($node->type) {
            case Token::T_IDENTIFIER:
                // Could be a function call or a static method call
                $peek = $this->lexer->peek();
                if ($peek->type === Token::T_SCOPE) {
                    $this->lexer->next();
                    return $this->parseStaticMethodCall($node);
                } elseif ($peek->type === Token::T_OP) {
                    $this->lexer->next();
                    return $this->parseFunctionCall($node);
                }
                $this->syntaxError('unexpected identifier, expecting expression', $peek);
            case Token::T_STRING:
                $expr = new Expr(Expr::TYPE_STRING);
                $expr->string = $node->value;
                return $expr;
            case Token::T_INTEGER:
                $expr = new Expr(Expr::TYPE_INTEGER);
                $expr->integer = (int) $node->value;
                return $expr;
            case Token::T_DOLLAR:
                $expr = new Expr(Expr::TYPE_VAR);
                $node = $this->lexer->next();
                $this->assertSequence(Seq::type(Token::T_IDENTIFIER, 'identifier'), $node);
                $expr->string = $node->value;
                return $expr;

        }
        var_dump($node);
    }

    public function parseFunctionCall(Token $node): Expr {
        $expr = new Expr(Expr::TYPE_FUNCTION_CALL);
        $expr->string = $node->value;
        $expr->child1 = $this->parseArgList();
        $this->assertAndConsumeSequence(Seq::cp());
        return $expr;
    }

    public function parseArgList(): ?Expr {
        $node = $this->lexer->peek();
        $expr = $exprTail = null;
        while ($node->type !== Token::T_CP) {
            $node = $this->lexer->next();
            $tmp = $this->parseExpression($node);

            if (is_null($expr)) {
                $expr = $exprTail = $tmp;
            } else {
                $exprTail = $exprTail->next = $tmp;
            }
            $node = $this->lexer->peek();
            if ($node->type === Token::T_CP) {
                return $expr;
            } elseif ($node->type !== Token::T_COMMA) {
                $this->syntaxError("expecting , or )", $node);
            }
            $this->lexer->next();
            $node = $this->lexer->peek();
        }
        return $expr;
    }

    public function parseReturn(): Param {
        $this->assertSequence(Seq::colon(), $this->lexer->next());
        $type = $this->extractType($this->lexer->next());
        $param = new Param;
        $param->name = 'return';
        $param->type = $type;
        return $param;
    }

    public function parseParams(): ?Param {
        $node = $this->lexer->peek();
        $param = null;
        $paramTail = null;
        while ($node->type !== Token::T_CP) {
            if (is_null($param)) {
                $param = $paramTail = new Param();
            } else {
                $paramTail = $paramTail->next = new Param();
            }
            $paramTail->type = $this->extractType($this->lexer->next());
            $this->assertSequence(Seq::dollar(), $this->lexer->next());
            $node = $this->lexer->next();
            $this->assertSequence(Seq::type(Token::T_IDENTIFIER, 'identifier'), $node);
            $paramTail->name = $node->value;
            $node = $this->lexer->peek();
            // if it's a comma, consume
            if ($node->type === Token::T_COMMA) {
                $this->lexer->next();
                $node = $this->lexer->peek();
                continue;
            } elseif ($node->type === Token::T_CP) {
                return $param;
            }
            $this->syntaxError('expected , or )', $node);
        }
        return $param;
    }


    public function parseNamespace(Token $node): void {
        if ($this->namespace !== '\\') {
            $this->syntaxError(
                ', namespace already defined',
                $node
            );
        }
        $node = $this->lexer->next();
        $this->namespace = '';
        while ($node->type === Token::T_IDENTIFIER) {
            $this->namespace .= '\\' . $node->name;
            $node = $this->lexer->next();
            if ($node->type === Token::T_BACKSLASH) {
                $node = $this->lexer->next();
            } else {
                break;
            }
        }
        $this->assertSequence(Seq::semicolon(), $node);
    }

    public function assertNotEOF(string $message, Token $node): void {
        if ($token->type === Token::T_END_OF_FILE) {
            $this->syntaxError($message, $node);
        }
    }

    public function assertSequence(Seq $sequence, Token $node): void {
        if ($assert->type !== $token->type) {
            $this->syntaxErrorExpecting($sequence, $token);
        }
        if (!$assert->typeOnly && $assert->value !== $token->value) {
            $this->syntaxErrorExpecting($sequence, $token);
        }
    }

    public function assertAndConsumeSequence(Seq ... $sequence) {
        $size = count($sequence);
        $i = 0;
        while ($i < $size) {
            $assert = $sequence[$i++];
            $token = $this->lexer->next();
            $this->assertSequence($assert, $token);
        }
    }

    public function syntaxError(string $message, Token $found): void {
        $warning = '\'' . $found->value . '\'';
        if ($found->type === Token::T_END_OF_FILE) {
            $warning = 'end of file';
        }
        throw new \RuntimeException(
            sprintf(
                'Syntax Error: Unexpected %s %s in %s on line %s:%s',
                $warning,
                $message,
                $found->filename,
                $found->line,
                $found->column
            )
        );
    }

    public function syntaxErrorExpecting(Seq $expected, Token $found): void {
        $this->syntaxError(sprintf(', expecting %s', $expected->value), $found);
    }
}

class Seq {
    public int $type;
    public string $value;
    public bool $typeOnly = false;

    public function __construct(int $type, string $value, bool $typeOnly = false) {
        $this->type = $type;
        $this->value = $value;
        $this->typeOnly = $typeOnly;
    }


    public static function type(int $type, string $value): self {
        return new self($type, $value, true);
    }

    public static function ob(): self {
        return new self(Token::T_OB, '{', true);
    }

    public static function cb(): self {
        return new self(Token::T_CB, '}', true);
    }

    public static function op(): self {
        return new self(Token::T_OP, '(', true);
    }

    public static function cp(): self {
        return new self(Token::T_CP, ')', true);
    }

    public static function dollar(): self {
        return new self(Token::T_DOLLAR, '$', true);
    }

    public static function colon(): self {
        return new self(Token::T_COLON, ':', true);
    }

    public static function semicolon(): self {
        return new self(Token::T_SEMICOLON, ';', true);
    }

    public static function open_php(): self {
        return new self(Token::T_OPEN_PHP, '<?php', true);
    }

    public static function identifier(string $name): self {
        return new self(Token::T_IDENTIFIER, $name);
    }

    public static function integer(string $name): self {
        return new self(Token::T_INTEGER, $name);
    }
}