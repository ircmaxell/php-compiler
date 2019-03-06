<?php

function simple(): void {
  $a = 0;
  for ($i = 0; $i < 1000000; $i++)
    $a++;
  $thisisanotherlongname = 0;
  for ($thisisalongname = 0; $thisisalongname < 1000000; $thisisalongname++)
    $thisisanotherlongname++;
}
/****/
function simplecall(): void {
  for ($i = 0; $i < 1000000; $i++)
    strlen("hallo");
}
/****/
function hallo(string $a): void {
}
function simpleucall(): void {
  for ($i = 0; $i < 1000000; $i++)
    hallo("hallo");
}
/****/
function simpleudcall(): void {
  for ($i = 0; $i < 1000000; $i++)
    hallo2("hallo");
}
function hallo2(string $a): void {
}

simple();
simplecall();
simpleucall();
simpleudcall();

echo "Done\n";