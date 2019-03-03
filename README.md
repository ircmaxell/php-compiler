# A compiler for PHP

Ok, so this used to be a dead project. It required calling out to all sorts of hackery to generate PHP extensions, or PHP itself.

Now, thanks to [FFI landing in PHP 7.4](https://wiki.php.net/rfc/ffi), the potential for all sorts of crazy is HUGE. 

So here we go :)

# Installation

Install PHP 7.4, being sure to enable the FFI extension (`--with-ffi`).

Also, you need to install the system dependency `libgccjit`. On Ubuntu:

```console
me@local:~$ sudo apt-get install libgccjit-6-dev
```


Then simply `composer install`.

# Running Code

There are three main ways of using this compiler:

## VM - Virtual Machine

This compiler mode implements its own PHP Virtual Machine, just like PHP does. This is effectively a giant switch statement in a loop.

No, seriously. It's literally [a giant switch statement](lib/PHPCompiler/VM/VM.php)...

Practically, it's a REALLY slow way to run your PHP code. Well, it's slow because it's in PHP, and PHP is already running on top of a VM written in C. 

But what if we could change that...

## JIT - Just In Time

This compiler mode takes PHP code and generates machine code out of it. Then, instead of letting the code run in the VM above, it just calls to the machine code.

It's WAY faster to run (faster than PHP 7.4, when you don't account for compile time).

But it also takes a long time to compile (compiling is SLOW, because it's being compiled from PHP).

Every time you run it, it compiles again. 

That brings us to our final mode:

## Compile - Ahead Of Time Compilation

This compiler mode actually generates native machine code, and outputs it to an executable.

This means, that you can take PHP code, and generate a standalone binary. One that's implemented **without a VM**. That means it's (in theory at least) as fast as native C.

Well, that's not true. But it's pretty dang fast.

# Okay, Enough, How can I try?

There are four CLI entrypoints, and all 4 behave (somewhat) like the PHP cli:

 * `php bin/vm.php` - Run code in a VM
 * `php bin/jit.php` - Compile all code, and then run it
 * `php bin/compile.php` - Compile all code, and output a `.o` file.
 * `php bin/print.php` - Compile and output CFG and the generated OpCodes (useful for debugging)

 Specifying code from `STDIN` (this works for all 4 entrypoints):

```console
me@local:~$ echo '<?php echo "Hello World\n";' | php bin/vm.php
Hello World
```

You can also specify on the CLI via `-r` argument:

```console
me@local:~$ php bin/jit.php -r 'echo "Hello World\n";'
Hello World
```

And you can specify a file:

```console
me@local:~$ php bin/vm.php test.php
```

When compiling using `bin/compile.php`, you can also specify an "output file" with `-o` (this defaults to the input file, with `.php` replaced with `.o`).

```console
me@local:~$ php bin/compile.php -o other.o test.php
```

Or, using the default:

```console
me@local:~$ php bin/compile.php test.php
// generates test.o
```