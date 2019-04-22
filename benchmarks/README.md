# Examples

Each folder here contains a working PHP example `example.php`, and associated generated files (including LLVM IR generated from said file).

# Benchmark Results

Each example includes a benchmark that compares each mode of operation to just running the file with `php` directly.

<!-- benchmark table start -->

| Test Name          |            7.4 (s)| bin/jit.php (s) | bin/compile.php (s) | compiled time (s) |
|--------------------|-------------------|-----------------|---------------------|-------------------|
|          Ack(3,10) |            1.9212 |          0.8305 |              0.6508 |            0.1548 |
|           Ack(3,8) |            0.1005 |          0.6248 |              0.6501 |            0.0115 |
|           Ack(3,9) |            0.3677 |          0.6654 |              0.6507 |            0.0383 |
|           fibo(30) |            0.0876 |          0.6213 |              0.6543 |            0.0085 |
|           fibo(32) |            0.2082 |          0.6383 |              0.6514 |            0.0197 |
|         mandelbrot |            0.1556 |          0.6465 |              0.6618 |            0.0148 |
|             simple |            0.0640 |          0.6362 |              0.6540 |            0.0117 |

<!-- benchmark table end -->