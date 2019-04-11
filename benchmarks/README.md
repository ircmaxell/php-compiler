# Examples

Each folder here contains a working PHP example `example.php`, and associated generated files (including LLVM IR generated from said file).

# Benchmark Results

Each example includes a benchmark that compares each mode of operation to just running the file with `php` directly.

<!-- benchmark table start -->

| Test Name          |            7.3 (s)| 7.3.NO.OPCACHE (s)|            7.4 (s)| 7.4.NO.OPCACHE (s)|          8.JIT (s)|        8.NOJIT (s)| bin/jit.php (s) | bin/compile.php (s) | compiled time (s) |
|--------------------|-------------------|-------------------|-------------------|-------------------|-------------------|-------------------|-----------------|---------------------|-------------------|
|          Ack(3,10) |            1.2762 |            1.9123 |            1.3731 |            1.8261 |            0.7674 |            1.2012 |          0.4202 |              0.2602 |            0.1690 |
|           Ack(3,8) |            0.0878 |            0.1050 |            0.0893 |            0.1033 |            0.0549 |            0.0867 |          0.2235 |              0.2620 |            0.0107 |
|           Ack(3,9) |            0.3088 |            0.3850 |            0.3101 |            0.3844 |            0.1797 |            0.3041 |          0.2626 |              0.2622 |            0.0395 |
|           fibo(30) |            0.0753 |            0.0922 |            0.0766 |            0.0921 |            0.0461 |            0.0804 |          0.2413 |              0.2621 |            0.0083 |
|         mandelbrot |            0.0433 |            0.1232 |            0.0424 |            0.1177 |            0.0248 |            0.0433 |          0.2561 |              0.2725 |            0.0187 |
|             simple |            0.0598 |            0.0705 |            0.0599 |            0.0747 |            0.0295 |            0.0591 |          0.2399 |              0.3150 |            0.0116 |

<!-- benchmark table end -->