<?php

# This file is generated, changes you make will be lost.
# Make your changes in /compiler/lib/JIT.pre instead.

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler;

use PHPCfg\Operand;
use PHPCfg\Op;
use PHPTypes\Type;
use PHPCompiler\JIT\Context;
use PHPCompiler\JIT\Variable;

use PHPCompiler\Func as CoreFunc;

use PHPLLVM;

class JIT {

    private static int $functionNumber = 0;
    private static int $blockNumber = 0;

    public int $optimizationLevel = 3;


    private array $stringConstant = [];
    private array $intConstant = [];
    private array $builtIns = [];

    private array $queue = [];

    public Context $context;

    public function __construct(Context $context) {
        $this->context = $context;
    }

    public function compile(Block $block): PHPLLVM\Value {
        $return = $this->compileBlock($block);
        $this->runQueue();
        return $return;
    }

    public function compileFunc(CoreFunc $func): void {
        if ($func instanceof CoreFunc\PHP) {
            $this->compileBlock($func->block, $func->getName());
            $this->runQueue();
            return;
        } elseif ($func instanceof CoreFunc\JIT) {
            // No need to do anything, already compiled
            return;
        } elseif ($func instanceof CoreFunc\Internal) {
            $this->context->functionProxies[strtolower($func->getName())] = $func;
            return;
        }
        throw new \LogicException("Unknown func type encountered: " . get_class($func));
    }

    private function runQueue(): void {
        while (!empty($this->queue)) {
            $run = array_shift($this->queue);
            $this->compileBlockInternal($run[0], $run[1], ...$run[2]);
        }
    }

    private function compileBlock(Block $block, ?string $funcName = null): PHPLLVM\Value {
        if (!is_null($funcName)) {
            $internalName = $funcName;
        } else {
            $internalName = "internal_" . (++self::$functionNumber);
        }
        $args = [];
        $rawTypes = [];
        $argVars = [];
        if (!is_null($block->func)) {
            $callbackType = '';
            if ($block->func->returnType instanceof Op\Type\Literal) {
                switch ($block->func->returnType->name) {
                    case 'void':
                        $callbackType = 'void';
                        break;
                    case 'int':
                        $callbackType = 'long long';
                        break;
                    case 'string':
                        $callbackType = '__string__*';
                        break;
                    default:
                        throw new \LogicException("Non-void return types not supported yet");
                }
            } else {
                $callbackType = '__value__';
            }
            $returnType = $this->context->getTypeFromString($callbackType);
            $this->context->functionReturnType[strtolower($internalName)] = $callbackType;

            $callbackType .= '(*)(';
            $callbackSep = '';
            foreach ($block->func->params as $idx => $param) {
                if (empty($param->result->usages)) {
                    // only compile for param
                    assert($param->declaredType instanceof Op\Type\Literal);
                    $rawType = Type::fromDecl($param->declaredType->name);
                } else {
                    $rawType = $param->result->type;
                }
                $type = $this->context->getTypeFromType($rawType);
                $callbackType .= $callbackSep . $this->context->getStringFromType($type);
                $callbackSep = ', ';
                $rawTypes[] = $rawType;
                $args[] = $type;
            }
            $callbackType .= ')';
        } else {
            $callbackType = 'void(*)()';
            $returnType = $this->context->getTypeFromString('void');
        }

        $isVarArgs = false;

        $func = $this->context->module->addFunction(
            $internalName,
            $this->context->context->functionType(
                $returnType,
                $isVarArgs,
                ...$args
            )
        );

        foreach ($args as $idx => $arg) {
            $argVars[] = new Variable($this->context, Variable::getTypeFromType($rawTypes[$idx]), Variable::KIND_VALUE, $func->getParam($idx));
        }

        if (!is_null($funcName)) {
            $lcname = strtolower($funcName);
            $this->context->functions[$lcname] = $func;
            if ($isVarArgs) {
                $this->context->functionProxies[$lcname] = new JIT\Call\Vararg($func, $funcName, count($args));
            } else {
                $this->context->functionProxies[$lcname] = new JIT\Call\Native($func, $funcName, $args);
            }
        }

        $this->queue[] = [$func, $block, $argVars];
        if ($callbackType === 'void(*)()') {
            $this->context->addExport($internalName, $callbackType, $block);
        }
        return $func;
    }
    
    private function compileBlockInternal(
        PHPLLVM\Value $func,
        Block $block,
        Variable ...$args
    ): PHPLLVM\BasicBlock {
        if ($this->context->scope->blockStorage->contains($block)) {
            return $this->context->scope->blockStorage[$block];
        }
        self::$blockNumber++;
        $origBasicBlock = $basicBlock = $func->appendBasicBlock('block_' . self::$blockNumber);
        $this->context->scope->blockStorage[$block] = $basicBlock;
        $builder = $this->context->builder;
        $builder->positionAtEnd($basicBlock);
        // Handle hoisted variables
        foreach ($block->orig->hoistedOperands as $operand) {
            $this->context->makeVariableFromOp($func, $basicBlock, $block, $operand);
        }

        for ($i = 0, $length = count($block->opCodes); $i < $length; $i++) {
            $op = $block->opCodes[$i];
            switch ($op->type) {
                case OpCode::TYPE_ARG_RECV:
                    $this->assignOperand($block->getOperand($op->arg1), $args[$op->arg2]);
                    break;
                case OpCode::TYPE_ASSIGN:
                    $value = $this->context->getVariableFromOp($block->getOperand($op->arg3));
                    $this->assignOperand($block->getOperand($op->arg2), $value);
                    $this->assignOperand($block->getOperand($op->arg1), $value);
                    break;  
                // case OpCode::TYPE_ARRAY_DIM_FETCH:
                //     $value = $this->context->getVariableFromOp($block->getOperand($op->arg2));
                //     $dimOp = $block->getOperand($op->arg3);
                //     $dim = $this->context->getVariableFromOp($dimOp);
                //     if ($value->type & Variable::IS_NATIVE_ARRAY && $this->context->analyzer->needsBoundsCheck($value, $dimOp)) {
                //         // compile bounds check
                //         $builder->call(
                //             $this->context->lookupFunction('__nativearray__boundscheck'),
                //             $dim->value,
                //             $this->context->constantFromInteger($value->nextFreeElement)
                //         );
                //     }
                //     $this->assignOperand(
                //         $block->getOperand($op->arg1),
                //         $value->dimFetch($dim)
                //     );
                //     break;
                // case OpCode::TYPE_INIT_ARRAY:
                // case OpCode::TYPE_ADD_ARRAY_ELEMENT:
                //     $result = $this->context->getVariableFromOp($block->getOperand($op->arg1));
                //     if ($result->type & Variable::IS_NATIVE_ARRAY) {
                //         if (is_null($op->arg3)) {
                //             $idx = $result->nextFreeElement;
                //         } else {
                //             // this is safe, since we only compile to native array if it's checked to be good
                //             $idx = $block->getOperand($op->arg3)->value;
                //         }
                //         $this->context->helper->assign(
                //             $gccBlock,
                //             \gcc_jit_context_new_array_access(
                //                 $this->context->context,
                //                 $this->context->location(),
                //                 $result->rvalue,
                //                 $this->context->constantFromInteger($idx, 'size_t')
                //             ),
                //             $this->context->getVariableFromOp($block->getOperand($op->arg2))->rvalue
                //         );
                //         $result->nextFreeElement = max($result->nextFreeElement, $idx + 1);
                //     } else {
                //         throw new \LogicException('Hash tables not implemented yet');
                //     }
                //     break;
                case OpCode::TYPE_BOOLEAN_NOT:
                    $from = $this->context->getVariableFromOp($block->getOperand($op->arg2));
                    if ($from->type === Variable::TYPE_NATIVE_BOOL) {
                        $value = $this->context->helper->loadValue($from);
                    } else {
                        $value = $this->context->castToBool($this->context->helper->loadValue($from));
                    }
                    $__right = $value->typeOf()->constInt(1, false);
                            
                        

                        

                        

                        $result = $this->context->builder->bitwiseXor($value, $__right);
    

                    $this->assignOperandValue($block->getOperand($op->arg1), $result);
                    break;
                case OpCode::TYPE_CONCAT:
                    if (!$this->context->hasVariableOp($block->getOperand($op->arg1))) {
                        // don't bother with constant operations
                        break;
                    }
                    $result = $this->context->getVariableFromOp($block->getOperand($op->arg1));
                    $left = $this->context->getVariableFromOp($block->getOperand($op->arg2));
                    $right = $this->context->getVariableFromOp($block->getOperand($op->arg3));
                    $this->context->type->string->concat($result, $left, $right);
                    break;
                case OpCode::TYPE_CONST_FETCH:
                    $value = null;
                    if (!is_null($op->arg3)) {
                        // try NS constant fetch
                        $value = $this->context->constantFetch($block->getOperand($op->arg3));
                    }
                    if (is_null($value)) {
                        $value = $this->context->constantFetch($block->getOperand($op->arg2));
                    }
                    if (is_null($value)) {
                        throw new \RuntimeException('Unknown constant fetch');
                    }
                    $this->assignOperand($block->getOperand($op->arg1), $value);
                    break;
                case OpCode::TYPE_CAST_BOOL:
                    $value = $this->context->getVariableFromOp($block->getOperand($op->arg2));
                    $this->assignOperand($block->getOperand($op->arg1), $value->castTo(Variable::TYPE_NATIVE_BOOL));
                    break;
                case OpCode::TYPE_ECHO:
                case OpCode::TYPE_PRINT:
                    $argOffset = $op->type === OpCode::TYPE_ECHO ? $op->arg1 : $op->arg2;
                    $arg = $this->context->getVariableFromOp($block->getOperand($argOffset));
                    $argValue = $this->context->helper->loadValue($arg);
                    switch ($arg->type) {
                        case Variable::TYPE_VALUE:
                            $argValue = $this->context->builder->call(
                        $this->context->lookupFunction('__value__readString') , 
                        $argValue
                        
                    );
    
                            // Fall through intentional                
                        case Variable::TYPE_STRING:            
                            $fmt = $this->context->builder->pointerCast(
                        $this->context->constantFromString("%.*s"),
                        $this->context->getTypeFromString('char*')
                    );
    $offset = $this->context->structFieldMap[$argValue->typeOf()->getElementType()->getName()]['length'];
                    $__str__length = $this->context->builder->load(
                        $this->context->builder->structGep($argValue, $offset)
                    );
    $offset = $this->context->structFieldMap[$argValue->typeOf()->getElementType()->getName()]['value'];
                    $__str__value = $this->context->builder->structGep($argValue, $offset);
    $this->context->builder->call(
                    $this->context->lookupFunction('printf') , 
                    $fmt
                    , $__str__length
                    , $__str__value
                    
                );
    
                            break;
                        case Variable::TYPE_NATIVE_LONG:
                            $fmt = $this->context->builder->pointerCast(
                        $this->context->constantFromString("%lld"),
                        $this->context->getTypeFromString('char*')
                    );
    $this->context->builder->call(
                    $this->context->lookupFunction('printf') , 
                    $fmt
                    , $argValue
                    
                );
    
                            break;
                        case Variable::TYPE_NATIVE_DOUBLE:
                            $fmt = $this->context->builder->pointerCast(
                        $this->context->constantFromString("%G"),
                        $this->context->getTypeFromString('char*')
                    );
    $this->context->builder->call(
                    $this->context->lookupFunction('printf') , 
                    $fmt
                    , $argValue
                    
                );
    
                            break;
                        case Variable::TYPE_NATIVE_BOOL:
                            $bool = $this->context->castToBool($argValue);
                $prev = $this->context->builder->getInsertBlock();
                $ifBlock = $prev->insertBasicBlock('ifBlock');
                $prev->moveBefore($ifBlock);
                
                $endBlock[] = $tmp = $ifBlock->insertBasicBlock('endBlock');
                    $this->context->builder->branchIf($bool, $ifBlock, $tmp);
                
                $this->context->builder->positionAtEnd($ifBlock);
                { $fmt = $this->context->builder->pointerCast(
                        $this->context->constantFromString("1"),
                        $this->context->getTypeFromString('char*')
                    );
    $this->context->builder->call(
                    $this->context->lookupFunction('printf') , 
                    $fmt
                    
                );
    }
                if ($this->context->builder->getInsertBlock()->getTerminator() === null) {
                    $this->context->builder->branch(end($endBlock));
                }
                
                $this->context->builder->positionAtEnd(array_pop($endBlock));
    
                            break;

                        default: 
                            throw new \LogicException("Echo for type $arg->type not implemented");
                    }
                    if ($op->type === OpCode::TYPE_PRINT) {
                        $this->assignOperand(
                            $block->getOperand($op->arg1),
                            new Variable($this->context, Variable::TYPE_NATIVE_LONG, Variable::KIND_VALUE, $this->context->constantFromInteger(1))
                        );
                    }
                    break;
                case OpCode::TYPE_MUL:
                case OpCode::TYPE_PLUS:
                case OpCode::TYPE_MINUS:
                case OpCode::TYPE_DIV:
                case OpCode::TYPE_MODULO:
                case OpCode::TYPE_BITWISE_AND:
                case OpCode::TYPE_BITWISE_OR:
                case OpCode::TYPE_BITWISE_XOR:
                case OpCode::TYPE_GREATER_OR_EQUAL:
                case OpCode::TYPE_SMALLER_OR_EQUAL:
                case OpCode::TYPE_GREATER:
                case OpCode::TYPE_SMALLER:
                case OpCode::TYPE_IDENTICAL:
                case OpCode::TYPE_EQUAL:
                    $this->assignOperand(
                        $block->getOperand($op->arg1),
                        $this->context->helper->binaryOp(
                            $op,
                            $this->context->getVariableFromOp($block->getOperand($op->arg2)),
                            $this->context->getVariableFromOp($block->getOperand($op->arg3))
                        )
                    );
                    break;
                case OpCode::TYPE_UNARY_MINUS:
                    $this->assignOperand(
                        $block->getOperand($op->arg1),
                        $this->context->helper->unaryOp(
                            $op,
                            $this->context->getVariableFromOp($block->getOperand($op->arg2)),
                        )
                    );
                    break;
                // case OpCode::TYPE_CASE:
                case OpCode::TYPE_JUMP:
                    $newBlock = $this->compileBlockInternal($func, $op->block1, ...$args);
                    $builder->positionAtEnd($basicBlock);
                    $this->context->freeDeadVariables($func, $basicBlock, $block);
                    $builder->branch($newBlock);
                    return $origBasicBlock;
                case OpCode::TYPE_JUMPIF:
                    $if = $this->compileBlockInternal($func, $op->block1, ...$args);
                    $else = $this->compileBlockInternal($func, $op->block2, ...$args);

                    $builder->positionAtEnd($basicBlock);

                    $condition = $this->context->castToBool(
                        $this->context->helper->loadValue($this->context->getVariableFromOp($block->getOperand($op->arg1)))
                    );

                    $this->context->freeDeadVariables($func, $basicBlock, $block);
                    $builder->branchIf($condition, $if, $else);
                    return $origBasicBlock;
                case OpCode::TYPE_RETURN_VOID:
                    $this->context->freeDeadVariables($func, $basicBlock, $block);
                    $this->context->builder->returnVoid();
    
                    return $origBasicBlock;
                case OpCode::TYPE_RETURN:
                    $return = $this->context->getVariableFromOp($block->getOperand($op->arg1));
                    $return->addref();
                    $retval = $this->context->helper->loadValue($return);
                    $this->context->freeDeadVariables($func, $basicBlock, $block);
                    $this->context->builder->returnValue($retval);
    
                    return $origBasicBlock;
                case OpCode::TYPE_FUNCDEF:
                    $nameOp = $block->getOperand($op->arg1);
                    assert($nameOp instanceof Operand\Literal);
                    $this->compileBlock($op->block1, $nameOp->value);
                    break;
                case OpCode::TYPE_FUNCCALL_INIT:
                    $nameOp = $block->getOperand($op->arg1);
                    if (!$nameOp instanceof Operand\Literal) {
                        throw new \LogicException("Variable function calls not yet supported");
                    }
                    $lcname = strtolower($nameOp->value);
                    if (isset($this->context->functionProxies[$lcname])) {
                        $this->context->scope->toCall = $this->context->functionProxies[$lcname];
                    } else {
                        throw new \RuntimeException("Call to undefined function $lcname");
                    }
                    $this->context->scope->args = [];
                    break;
                case OpCode::TYPE_ARG_SEND:
                    $this->context->scope->args[] = $this->context->getVariableFromOp($block->getOperand($op->arg1));
                    break;
                case OpCode::TYPE_FUNCCALL_EXEC_NORETURN:
                    if (is_null($this->context->scope->toCall)) {
                        // short circuit
                        break;
                    }
                    $this->context->scope->toCall->call($this->context, ...$this->context->scope->args);
                    break;
                case OpCode::TYPE_FUNCCALL_EXEC_RETURN:
                    $result = $this->context->scope->toCall->call($this->context, ...$this->context->scope->args);
                    $this->assignOperandValue($block->getOperand($op->arg1), $result);
                    break;
                // case OpCode::TYPE_DECLARE_CLASS:
                //     $this->context->pushScope();
                //     $this->context->scope->classId = $this->context->type->object->declareClass($block->getOperand($op->arg1));
                //     $this->compileClass($op->block1, $this->context->scope->classId);
                //     $this->context->popScope();
                //     break;
                // case OpCode::TYPE_NEW:
                //     $class = $this->context->type->object->lookupOperand($block->getOperand($op->arg2));
                //     $this->context->helper->assign(
                //         $gccBlock,
                //         $this->context->getVariableFromOp($block->getOperand($op->arg1))->lvalue,
                //         $this->context->type->object->allocate($class)
                //     );
                //     $this->context->scope->toCall = null;
                //     $this->context->scope->args = [];
                //     break;
                // case OpCode::TYPE_PROPERTY_FETCH:
                //     $result = $block->getOperand($op->arg1);
                //     $obj = $block->getOperand($op->arg2);
                //     $name = $block->getOperand($op->arg3);
                //     assert($name instanceof Operand\Literal);
                //     assert($obj->type->type === Type::TYPE_OBJECT);
                //     $this->context->scope->variables[$result] = $this->context->type->object->propertyFetch(
                //         $this->context->getVariableFromOp($obj)->rvalue,
                //         $obj->type->userType,
                //         $name->value
                //     );
                //     break;
                default:
                    throw new \LogicException("Unknown JIT opcode: ". $op->getType());
            }
        }
        throw new \LogicException("Reached the end of the loop, this shouldn't happen...");
    }

    private function compileClass(?Block $block, int $classId) {
        if ($block === null) {
            return;
        }
        foreach ($block->opCodes as $op) {
            switch ($op->type) {
                case OpCode::TYPE_DECLARE_PROPERTY:
                    $name = $block->getOperand($op->arg1);
                    assert($name instanceof Operand\Literal);
                    assert(is_null($op->arg2)); // no defaults for now
                    $type = Variable::getTypeFromType($block->getOperand($op->arg3)->type);
                    $this->context->type->object->defineProperty($classId, $name->value, $type);
                    break;
                default:
                    var_dump($op);
                    throw new \LogicException('Other class body types are not jittable for now');
            }
            
        }
    }

    private function assignOperand(Operand $result, Variable $value): void {
        if (empty($result->usages) && !$this->context->scope->variables->contains($result)) {
            return;
        }
        if (!$this->context->hasVariableOp($result)) {
            // it's a kind!
            $this->context->makeVariableFromValueOp($this->context->helper->loadValue($value), $result);
            return;
        }
        $result = $this->context->getVariableFromOp($result);
        if ($result->kind !== Variable::KIND_VARIABLE) {
            throw new \LogicException("Cannot assign to a value");
        }
        if ($value->type === $result->type) {
            $result->free();
            if ($value->type & Variable::IS_NATIVE_ARRAY) {
                // copy over the nextfreelement
                //$result->nextFreeElement = $value->nextFreeElement;
            }
            $this->context->builder->store(
                $this->context->helper->loadValue($value),
                $result->value
            );
            $result->addref();
            return;
        } elseif ($result->type === Variable::TYPE_VALUE) {
            // wrap
            $valueRef = $result->value;
            $valueFrom = $value->value;
            switch ($value->type) {
                case Variable::TYPE_NULL:
                    $this->context->builder->call(
                    $this->context->lookupFunction('__value__writeNull') , 
                    $valueRef
                    
                );
    
                    return;
                case Variable::TYPE_NATIVE_LONG:
                    $this->context->builder->call(
                    $this->context->lookupFunction('__value__writeLong') , 
                    $valueRef
                    , $valueFrom
                    
                );
    
                    return;
                case Variable::TYPE_NATIVE_DOUBLE:
                    $this->context->builder->call(
                    $this->context->lookupFunction('__value__writeDouble') , 
                    $valueRef
                    , $valueFrom
                    
                );
    
                    return;
                default:
                    throw new \LogicException("Source type: {$value->type}");
            }
        }
        throw new \LogicException("Cannot assign operands of different types (yet): {$value->type}, {$result->type}");
    }

    private function assignOperandValue(Operand $result, PHPLLVM\Value $value): void {
        if (empty($result->usages) && !$this->context->scope->variables->contains($result)) {
            return;
        }
        if (!$this->context->hasVariableOp($result)) {
            // it's a kind!
            $this->context->makeVariableFromValueOp($value, $result);
            return;
        }
        $result = $this->context->getVariableFromOp($result);
        if ($result->kind !== Variable::KIND_VARIABLE) {
            throw new \LogicException("Cannot assign to a value");
        }
        $result->free();

        $this->context->builder->store(
            $value,
            $result->value
        );
        $result->addref();
    }

}
