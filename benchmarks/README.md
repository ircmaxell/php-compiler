# Examples

Each folder here contains a working PHP example `example.php`, and associated generated files (including LLVM IR generated from said file).

# Benchmark Results

Each example includes a benchmark that compares each mode of operation to just running the file with `php` directly.

<!-- benchmark table start -->

| Test Name          |            7.4 (s)| bin/jit.php (s) | bin/compile.php (s) | compiled time (s) |
|--------------------|-------------------|-----------------|---------------------|-------------------|
|          Ack(3,10) |            1.9183 |          0.4112 |              0.2330 |            0.1654 |
|           Ack(3,8) |            0.1006 |          0.2105 |              0.2337 |            0.0115 |
|           Ack(3,9) |            0.3685 |          0.2519 |              0.2339 |            0.0403 |
|           fibo(30) |            0.0880 |          0.2071 |              0.2325 |            0.0083 |
|           fibo(32) |            0.2091 |          0.2221 |              0.2334 |            0.0192 |
|         mandelbrot |            0.1574 |          0.2302 |              0.2451 |            0.0135 |
|             simple |            0.0641 |          0.2156 |              0.2363 |            0.0114 |

<!-- benchmark table end -->