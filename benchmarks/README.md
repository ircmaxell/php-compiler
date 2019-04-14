# Examples

Each folder here contains a working PHP example `example.php`, and associated generated files (including LLVM IR generated from said file).

# Benchmark Results

Each example includes a benchmark that compares each mode of operation to just running the file with `php` directly.

<!-- benchmark table start -->

| Test Name          |            7.4 (s)| bin/jit.php (s) | bin/compile.php (s) | compiled time (s) |
|--------------------|-------------------|-----------------|---------------------|-------------------|
|          Ack(3,10) |            1.7641 |          0.3593 |              0.1824 |            0.1612 |
|           Ack(3,8) |            0.1062 |          0.1607 |              0.1689 |            0.0108 |
|           Ack(3,9) |            0.3880 |          0.1984 |              0.1831 |            0.0392 |
|           fibo(30) |            0.0937 |          0.1652 |              0.1690 |            0.0083 |
|         mandelbrot |            0.1597 |          0.1770 |              0.1881 |            0.0135 |
|             simple |            0.0682 |          0.1670 |              0.1738 |            0.0114 |

<!-- benchmark table end -->