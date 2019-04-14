# Examples

Each folder here contains a working PHP example `example.php`, and associated generated files (including LLVM IR generated from said file).

# Benchmark Results

Each example includes a benchmark that compares each mode of operation to just running the file with `php` directly.

<!-- benchmark table start -->

|         Example Name |      Native PHP |      bin/vm.php |     bin/jit.php | bin/compile.php |      ./compiled |
|----------------------|-----------------|-----------------|-----------------|-----------------|-----------------|
|       000-HelloWorld |         0.01191 |         0.08080 |         0.14650 |         0.22190 |         0.00117 |
<!-- benchmark table end -->
