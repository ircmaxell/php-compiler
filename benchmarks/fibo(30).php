<?php

function fibo_r(int $n) : int {
    return(($n < 2) ? 1 : fibo_r($n - 2) + fibo_r($n - 1));
}
function fibo(int $n): void {
  $r = fibo_r($n);
  print $r;
  print "\n";
}

fibo(30);
