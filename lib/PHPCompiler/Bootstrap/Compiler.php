<?php
declare(strict_types=1);

namespace PHPCompiler\Bootstrap;

class Compiler {
    public \gcc_jit_context_ptr $gccContext;
    public array $functionMap = [];
    public array $typeMap = [];
    public array $scope = [];
    public array $literalStringMap = [];

    public function __construct() {
    }

    public function compile(Context $context): CompileResult {
        $this->gccContext =\gcc_jit_context_acquire();
        $this->functionMap = [];
        $this->typeMap = [];
        $this->defineBuiltins();

        $decl = $context->decl;
        $this->defineFunctions($decl);
        $this->implementFunctions($decl);
        return new CompileResult($this->gccContext);
    }

    public function implementFunctions(?Decl $decl): void {
        while (!is_null($decl)) {
            if ($decl->type == Decl::TYPE_FUNCTION) {
                $func = $decl->function;
                $gccFunc = $this->functionMap[$func->name];
                $this->scope = [];
                $this->populateParameterScope($func->params);
                $this->populateExpressions(
                    $func->body,
                    \gcc_jit_function_new_block($gccFunc, 'entry')
                );

            }
            $decl = $decl->next;
        }
    }

    public function populateExpressions(Block $body, \gcc_jit_block_ptr $block): void {
        $expr = $body->expr;
        while (!is_null($expr)) {
            switch ($expr->type) {
                case Expr::TYPE_ASSIGN:
                    die("Not implemented yet \n");
                default:
                    //rvalue
                    \gcc_jit_block_add_eval(
                        $block,
                        null,
                        $this->compileExpressionRvalue($expr)
                    );
                    break;
            }
            $expr = $expr->next;
        }
        // if we get here, there was no other return/etc
        \gcc_jit_block_end_with_void_return($block, null);
    }


    public function compileExpressionRvalue(Expr $expr): \gcc_jit_rvalue_ptr {
        switch ($expr->type) {
            case Expr::TYPE_VAR:
                if (isset($this->scope[$expr->string])) {
                    return \gcc_jit_lvalue_as_rvalue(
                        $this->scope[$expr->string]
                    );
                }
                $this->compileError('using a variable before defining it: ' . $expr->string);
            case Expr::TYPE_STRING:
                return $this->literalString($expr->string);
            case Expr::TYPE_INTEGER:
                return \gcc_jit_context_new_rvalue_from_long(
                    $this->gccContext,
                    $this->getType('int'),
                    $expr->integer
                );
            case Expr::TYPE_FUNCTION_CALL:
                if ($expr->string === 'chr') {
                    // inbuilt intrinsic
                    if ($expr->child1 === null 
                        || $expr->child1->child1 !== null 
                        || $expr->child1->type !== Expr::TYPE_INTEGER
                    ) {
                        $this->compileError('chr() requires a single integer parameter');
                    }
                    return $this->literalString(chr($expr->child1->integer));
                } elseif (!isset($this->functionMap[$expr->string])) {
                    $this->compileError('unknown function ' . $expr->string);
                }
                return $this->funcCall(
                    $expr->string, 
                    $this->numberOfArgs($expr->child1), 
                    $this->compileArgs($expr->child1)
                );
            default:
        }
        switch ($expr->getNumberOfArgs()) {
            case 1:
                $arg = $this->compileExpressionRvalue($expr->child1);
                throw new \LogicException("Unary expressions not implemented yet");
            case 2:
                return $this->compileBinaryExpr($expr);
        }
        var_dump($expr);
    }

    public function funcCallArgs(
        string $name,
        \gcc_jit_rvalue_ptr ... $args
    ): \gcc_jit_rvalue_ptr {
        return $this->funcCall(
            $name, 
            count($args), 
            \gcc_jit_rvalue_ptr_ptr::fromArray(...$args)
        );
    }

    public function funcCall(
        string $name, 
        int $numArgs,
        \gcc_jit_rvalue_ptr_ptr $args
    ): \gcc_jit_rvalue_ptr {
        return \gcc_jit_context_new_call(
            $this->gccContext,
            null,
            $this->functionMap[$name],
            $numArgs,
            $args
        );
    }

    public function compileBinaryExpr(Expr $expr): \gcc_jit_rvalue_ptr {
        $left = $this->compileExpressionRvalue($expr->child1);
        $right = $this->compileExpressionRvalue($expr->child2);
        switch ($expr->type) {
            case Expr::TYPE_CONCAT:
                // concat isn't native, call a function to do it
                return $this->funcCallArgs('__concat__', $left, $right);
        }
    }

    public function populateParameterScope(?Param $param): void {
        while (!is_null($param)) {
            $this->scope[$param->name] = \gcc_jit_param_as_lvalue(
                $param->gccParam
            );
            $param = $param->next;
        }
    }

    public function defineFunctions(?Decl $decl): void {
        while (!is_null($decl)) {
            if ($decl->type == Decl::TYPE_FUNCTION) {
                // define the function
                $func = $decl->function;
                if (isset($this->functionMap[$func->name])) {
                    $this->compileError('Redeclaring function ' . $func->name);
                }
                $funcType = GCC_JIT_FUNCTION_INTERNAL;
                if ($func->name === 'main') {
                    $funcType = GCC_JIT_FUNCTION_EXPORTED;
                }
                $this->functionMap[$func->name] = \gcc_jit_context_new_function(
                      $this->gccContext,
                      null,
                      $funcType,
                      $this->getTypeFromParam($func->return),
                      $func->name,
                      $this->numberOfParams($func->params),
                      $this->getParams($func->params),
                      $this->isVariadic($func->params)
                );
            }
            $decl = $decl->next;
        }
    }

    public function isVariadic(?Param $param): int {
        //todo support variadics
        return 0;
    }

    public function getParams(?Param $param): ?\gcc_jit_param_ptr_ptr {
        $result = [];
        while (!is_null($param)) {
            $result[] = $param->gccParam = \gcc_jit_context_new_param(
                $this->gccContext, 
                null, 
                $this->getTypeFromParam($param), 
                $param->name
            );
            $param = $param->next;
        }
        return \gcc_jit_param_ptr_ptr::fromArray(...$result);
    }

    public function numberOfArgs(?Expr $arg): int {
        $n = 0;
        while (!is_null($arg)) {
            $n++;
            $arg = $arg->next;
        }
        return $n;
    }

    public function compileArgs(?Expr $arg): \gcc_jit_rvalue_ptr_ptr {
        $result = [];
        while (!is_null($arg)) {
            $result[] = $this->compileExpressionRvalue($arg);
            $arg = $arg->next;
        }
        return \gcc_jit_rvalue_ptr_ptr::fromArray(...$result);
    }

    public function numberOfParams(?Param $param): int {
        $n = 0;
        while (!is_null($param)) {
            $n++;
            $param = $param->next;
        }
        return $n;
    }

    public function getTypeFromParam(?Param $param): \gcc_jit_type_ptr {
        if (is_null($param) || $param->type === 'void') {
            return $this->typeMap['void'];
        }
        if ($param->type === 'string') {
            // for parameters only
            return $this->typeMap['const char*'];
        }
        return $this->getType($param->type);
    }

    public function getType(string $name): \gcc_jit_type_ptr {
        if (isset($this->typeMap[$name])) {
            return $this->typeMap[$name];
        }
    }

    public function definePrimitiveType(string $type, int $gccType): void {
        $this->typeMap[$type] = \gcc_jit_context_get_type (
            $this->gccContext, 
            $gccType
        );
    }

    public function definePointerForType(string $type) {
        $this->typeMap[$type . '*'] = \gcc_jit_type_get_pointer($this->getType($type));
    }

    public function defineBuiltins(): void {
        $this->definePrimitiveType('void', \GCC_JIT_TYPE_VOID);
        $this->definePrimitiveType('int', \GCC_JIT_TYPE_LONG_LONG);
        $this->definePrimitiveType('char', \GCC_JIT_TYPE_CHAR);
        $this->definePrimitiveType('size_t', \GCC_JIT_TYPE_SIZE_T);
        $this->definePrimitiveType(
            'const char*',
            \GCC_JIT_TYPE_CONST_CHAR_PTR
        );
        $this->definePointerForType('char');

        $this->functionMap['printf'] = \gcc_jit_context_new_function(
            $this->gccContext, 
            NULL,
            \GCC_JIT_FUNCTION_IMPORTED,
            $this->typeMap['int'],
            'printf',
            1, 
            \gcc_jit_param_ptr_ptr::fromArray(
                \gcc_jit_context_new_param(
                    $this->gccContext, 
                    null, 
                    $this->typeMap['const char*'], 
                    "format"
                )
            ),
            1
        );
    }

    public function literalString(string $literal): \gcc_jit_rvalue_ptr {
        if (!isset($this->literalStringMap[$literal])) {
            $this->literalStringMap[$literal] = \gcc_jit_context_new_string_literal(
                $this->gccContext,
                $literal
            );
        }
        return $this->literalStringMap[$literal];
    }
}


class CompileResult {
    public \gcc_jit_context_ptr $gccContext;

    public function __construct(\gcc_jit_context_ptr $gccContext) {
        $this->gccContext = $gccContext;
    }

    public function setOptimizationLevel(int $level): void {
        \gcc_jit_context_set_int_option(
            $this->gccContext,
            GCC_JIT_INT_OPTION_OPTIMIZATION_LEVEL,
            $level
        );
    }

    public function toFile(string $filename): void {
        \gcc_jit_context_compile_to_file(
            $this->gccContext, 
            \GCC_JIT_OUTPUT_KIND_EXECUTABLE,
            $filename
        );
    }

}