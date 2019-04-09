<?php

$sum = 0;

$values = [1, 2, 3, 4, 5];
for ($i = 0; $i < 100000; $i++) {
    $sum += $values[$i % 2];
    for ($j = 0; $j < 1000; $j++) {
        $sum += $values[$j % 3];
    }
}

echo $sum;