# Internal types

The compiler internally uses types in a pretty weird way and will need to be refactored.

All of these types can be converted to LLVM types by using the method `JIT\Context->getTypeFromString($string);`

## Primitive types

 * `void` - A type used to indicate no returns. Maps to LLVM's `void` type.
 * `int1` - This aliases to `bool` and is implemented as LLVM's internal `i1`. It is an integer type 1 bit wide.
 * `int32` - This aliases to `int` and `unsigned int` and is implemented as a `i32`. It is a 32 bit wide integer
 * `int64` - This aliases to `long long` and `unsigned long long`. It is implemented as a 64 bit wide integer
 * `size_t` - This is a type that is equivalent to the C type `size_t`. It is currently hard coded as a `i64`, but before long should be refactored to be dependent on the implementing machine
 * `int8` - This aliases to `char` and `const char` as well, and maps to LLVM's `i8` type.

## Pointer Types

Any type can be suffixed with `*` to be turned into a pointer to said type.

## Array Types

Any type can be suffixed with `[n]` where `n` is an integer, to create a type that represents a fixed-sized array.

## Complex Types

Complex types are defined by extensions and helpers. Currently, there are a few complex types built in (this is expected to grow significantly):

### Refcounting

The refcounting system defines two structs:

```php
struct __ref__ {
    int32 $refcount;
    int32 $typeinfo;
};
struct __ref__virtual {
    __ref__ $ref;
};
```

The `__ref__` type is a struct to implement refcounting on other structs. It has two fields, an integer to count the number of references (`$refcount`), and `$typeinfo` which stores two important pieces of information: a bit flag to indicate if the struct is refcounted (if it's a constant/interened or not), and 31 bits to indicate the type (used for separation semantics).

Let's say we wanted to make a new structure refcounted. We'd first add the `__ref__` field as the first member of the struct:

```php
struct foo {
    __ref__ $ref;
    int64 $blah;
}
```

What comes after doesn't matter for refcounting.

Then, when you initialize the structure, pass it to `$context->refcount->init($struct, $typeinfo)` where `$typeinfo` is a bit mast of `$type | IS_REFCOUNTED` or `$type | IS_NOT_REFCOUNTED`. It internally will cast the struct to a `__ref__virtual` type, and then initialize from there.

### Strings

Strings are implemented using the following structure:

```php
struct __string__ {
    __ref__ $ref;
    int64 $length;
    int8 $value;
};
```

The only real special thing is that `__string__`'s are allocated together with the string structure. So `$value` is not a pointer, but indeed the first character of the string.
