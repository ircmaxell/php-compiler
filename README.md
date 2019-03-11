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

No, seriously. It's literally [a giant switch statement](lib/VM.php)...

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

## Executing Code

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
me@local:~$ echo '<?php echo "Hello World\n";' > test.php
me@local:~$ php bin/vm.php test.php
```

When compiling using `bin/compile.php`, you can also specify an "output file" with `-o` (this defaults to the input file, with `.php` removed). This will generate an executable binary on your system, ready to execute

```console
me@local:~$ echo '<?php echo "Hello World\n";' > test.php
me@local:~$ php bin/compile.php -o other test.php
me@local:~$ ./other
Hello World
```

Or, using the default:

```console
me@local:~$ echo '<?php echo "Hello World\n";' > test.php
me@local:~$ php bin/compile.php test.php
me@local:~$ ./test
Hello World
```

## Linting Code

If you pass the `-l` parameter, it will not execute the code, but instead just perform the compilation. This will allow you to test to see if the code even will compile (hint: most currently will not).

## Debugging

Sometimes, you want to see what's going on. If you do, try the `bin/print.php` entrypoint. It will output two types of information. The first is the Control Flow Graph, and the second is the compiled opcodes.

```console
me@local:~$ php bin/print.php -r 'echo "Hello World\n";'

Control Flow Graph:

Block#1
    Terminal_Echo
        expr: LITERAL<inferred:string>('Hello World
        ')
    Terminal_Return


OpCodes:

block_0:
  TYPE_ECHO(0, null, null)
  TYPE_RETURN_VOID(null, null, null)
```

# Future Work

Right now, this only supports an EXTREMELY limited subset of PHP. There is no support for dynamic anything. Arrays aren't supported. Neither Object properties nor methods are supported. And the only builtin functions that are supported are `var_dump` and `strlen`.

But it's a start...

# Debugging

Since this is bleeding edge, debuggability is key. To that vein, both `bin/jit.php` and `bin/compile.php` accept a `-y` flag which will output a pair of debugging files (they default to the prefix of the name of the script, but you can specify another prefix following the flag).

```console
me@local:~$ echo '<?php echo "Hello World\n";' > demo.php
me@local:~$ php bin/compile.php -y demo.php
# Produces: 
#   demo - executable of the code
#   demo.debug.c - A pseudo-c version of the PHP code
#   demo.reproduce.c - All the libgccjit calls needed to reproduct the output
```

Checkout the committed [`demo.debug.c`](demo.debug.c) and [`demo.reproduce.c`](demo.reproduce.c) for more info...

# Performance

So, is this thing any fast? Well, let's look at the internal benchmarks. You can run them yourself with `php bench.php`, and it'll give you the following output (running 5 iterations of each test, and averaging the time):


| Test Name          |            7.3 (s)| 7.3.NO.OPCACHE (s)|            7.4 (s)| 7.4.NO.OPCACHE (s)|          8.JIT (s)|        8.NOJIT (s)| bin/jit.php (s) | bin/compile.php (s) | compiled time (s) |
|--------------------|-------------------|-------------------|-------------------|-------------------|-------------------|-------------------|-----------------|---------------------|-------------------|
|          Ack(3,10) |            1.1761 |            1.8715 |            1.1850 |            1.9010 |            0.6605 |            1.1573 |          0.4914 |              0.2830 |            0.2128 |
|           Ack(3,8) |            0.0805 |            0.0991 |            0.0824 |            0.1096 |            0.0459 |            0.0791 |          0.2940 |              0.2817 |            0.0146 |
|           Ack(3,9) |            0.3018 |            0.3616 |            0.3009 |            0.3709 |            0.1683 |            0.2931 |          0.3342 |              0.2818 |            0.0546 |
|           fibo(30) |            0.0691 |            0.0837 |            0.0748 |            0.0890 |            0.0437 |            0.0684 |          0.2906 |              0.2808 |            0.0108 |
|         mandelbrot |            0.0374 |            0.1298 |            0.0393 |            0.1493 |            0.0213 |            0.0374 |          0.3071 |              0.2969 |            0.0142 |
|             simple |            0.0490 |            0.0729 |            0.0533 |            0.0758 |            0.0215 |            0.0581 |          0.3021 |              0.2864 |            0.0119 |


To run the benchmarks yourself, you need to pass a series of ENV vars for each PHP version you want to test. For example, the above chart is generated with::

```console
me@local:~$ PHP_7_3=../../PHP/php-7.3 PHP_7_4=../../PHP/php-7.4 PHP_8_JIT=../../PHP/php-8-jit PHP_8_NOJIT=../../PHP/php-8-nojit PHP_7_3_NO_OPCACHE="../../PHP/php-7.3 -dopcache.enable=0" PHP_7_4_NO_OPCACHE="../../PHP/php-7.4 -dopcache.enable=0" php bench.php 
```

Without opcache doing optimizations, the `bin/jit.php` is actually able to hang up with ack(3,9) and mandelbrot for 7.3 and 7.4. It's even able to hang with PHP 8's experimental JIT compiler for ack(3,9). 

Most other tests are actually WAY slower with the `bin/jit.php` compiler. That's because the test itself is slower than the baseline time to parse and compile a file (about 0.12 seconds right now).

And note that this is running the compiler on top of PHP. At some point, the goal is to get the compiler to compile itself, hopefully cutting the time to compile down by at least a few hundred percent.

Simply look at the difference between everything and the "compiled time" column (which is the result of the AOT compiler generating a binary). This shows the potential in this compilation approach. If we can solve the overhead of parsing/compiling in PHP for the `bin/jit.php` examples, then man could this fly...

So yeah, there's definitely potential here... *evil grin*
