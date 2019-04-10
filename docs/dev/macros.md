# Macros

Due to the complexities of code generating, we've implemented a macro language within PHP-Compiler to make it easier to define and use intrinsics and builtins. These macros are very tailored to this project, so use at your own risk.

## declare

A series of declarations are currently supported. All declarations are wrapped with `declare {}` inside of your PHP code. See below for some examples:


### Struct declarations

Struct declarations look a bit like a mix between C and PHP. All structs must have a name. The basic format is:

```php
declare {
    struct foo {
        int64 $a;
        int32 $b;
    }
}
```

The first thing that may look weird, is that fields are prefixed with dollar signs just like PHP. Of course we just need to make it slightly more "weird".

Second, the types are any string type that's known by the system. See [types.md](types.md) for more info.

### Function declaration

The macros split function declaration from definition. You must have both. They look just like "normal" PHP functions, but with a few limtiations:

```php
declare {
    function foo(int64, int64): int64;
    function bar(const char*, ...): void;
    inline function baz(int32): void;
    static function buz(): void;
}
```

All types must be specified fully, and no variable names are allowed (that comes in the definition).

Note the `inline`, `static`, and `const` markers, they behave just like they would in C. `inline` tells the compiler to try to inline the function. `static` tells it not to externally link the function. 

`const` is a touch different, in that it applies two LLVM attributes to the argument: `readonly` and `nocapture`. Check out the llvm docs for more.

## compile

Just like declarations, definitions are available as macros as well. All definitions are wrapped with `compile {}`:

### Function definitions

Function definition is basically the opposite of declaration. It contains no type information, but only implementation.

```php
compile {
    function foo($a, $b) {
        $c = $a & $b;
        return $c;
    }
}
```

What's allowed inside, well, that's up next:

### Other Statements

All statements here are supported in function definitions, or just inline with other code. It uses the current block being executed, so it's safe to just "jump inline".

#### Supported Operators

I suspect this list will change rapidly. It's worth noting, very little type checking is done, and ensuring type compatibility is left to the implementer. 

* `$result = $var === null;` // checks to see if the `PHPLLVM\Value` in `$var` is a null pointer, and stores the `int1` (`boolean`) result in `$result`. Note the variables can be any PHP variable (but not other expressions).
* `$result = $var <= 0;` // checks to see if the integer type `$var` is less than or equal to a constant integer (not just 0, but any literal integer)
* `return;` // issues a void return
* `return $value;` // returns the `PHPLLVM\Value` stored in `$value`.
* `$value++;` // basically `$value = $value + 1;` for integer typed values.
* `$value--;` // the same for `- 1`.
* `$value = "string";` // allocate a global variable, set it to the constant is string, and cast it to a usable pointer in the local scope.
* `$value = (int64) $variable;` // cast the variable into a specific basic type (can be a struct). Variable here can also be an integer and will be promoted to a constant
* `$value = (int64) FOO;` // cast the variable into a specific basic type (non-pointer). `FOO` here is any PHP expression, but it should return an integer. Useful for fetching constants and class constants.
* `$value = (foo*) $bar;` // useful for casting between pointer types
* `$value = $left & $right;` // bitwise and between integer types left and right.
* `$value = $struct.field;` // extract a field from a struct (non-pointer), and store the value in `$value`.
* `$struct.fieldName = $value;` // store `$value` inside of the field 
* `$struct.fieldName = 0;` // integer constant support as well
* `$value = $structPointer->field;` // Dereference the pointer to a struct, and then extract the field.
* `$structPointer->field = $value;` // Dereference the pointer to a struct, and store a value in a field.
* `free $var;` // call `free($var)` to free allocated memory
* `$value = malloc typename;` // allocate a new instance of a type
* `memcpy $dest $src $size;` // call `memcpy($dest, $src, $size)`...
* `$value = funcName($arg, $arg2, $arg3);` // call the function with the specified args, and then store the result.
* `funcName($arg1, $arg2);` // Call the function, but ignore the result (void result)

More will be implemented, but there you have it... :)