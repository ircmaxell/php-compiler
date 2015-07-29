# A compiler for PHP

Right now, this doesn't do anything...

Well, that's a lie.

Right now, it produces errors on large scales.

Errors upon errors upon errors.

Some in PHP.

Some in C.

Some in unknown places.

Some are easy to fix.

Some are ungodly security holes.

But every once in a while.

When the planets and the code aligns *just* right.

Out pops some working code.

And that moment is what it's all about.

## Example

Given the following PHP code:

```php
<?php

class Foo {
    /**
     * @var int
     */
    public $foo;
}
?>
```

Fed into the compiler, will output a metric-ton of C code.

Check out `demo.php` to see how.

But when you load the compiled extension, you get the following result:

    $ php -d "extension=modules/compiled_123.so" -r "var_dump(new Foo);"
    object(Foo)#1 (1) {
      ["foo"]=>
      int(0)
    }

Which is just what you'd expect.

Wait. Why is it `0`? We never defaulted it...

I wonder...

    $foo = new Foo;
    $foo->foo = "123";

    // PHP Fatal error:  Uncaught Error: Parameter is not an int in Command line code:1

Woah. Where's that error coming from?

This is weird.

And this is compiled code. You declared it as an integer, and as an integer it will be.

## Types supported

Too few to note right now

## Why?

I'm crazy. Simple as that.
