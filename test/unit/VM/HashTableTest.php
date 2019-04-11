<?php

declare(strict_types=1);

/**
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\VM;

use PHPUnit\Framework\TestCase;

class HashTableTest extends TestCase
{
    public function testAdd(): void
    {
        $ht = new HashTable();
        $var = $this->int(123);
        $result = $ht->add('test', $var);

        $this->assertNotNull($result);
        $this->assertTrue($result->identicalTo($var));
    }

    public function testAddCopiesvariable(): void
    {
        $ht = new HashTable();
        $var = $this->int(123);

        $result = $ht->add('test', $var);

        $var->int(456);

        $this->assertNotNull($result);
        $this->assertFalse($result->identicalTo($var));
    }

    public function testAddThenFind(): void
    {
        $ht = new HashTable();

        $var = $this->int(123);
        $ht->add('test', $var);

        $result = $ht->find('test');
        $this->assertNotNull($result);
        $this->assertTrue($result->identicalTo($var));
    }

    public function testAddTwoElements(): void
    {
        $ht = new HashTable();

        $a = $this->int(123);
        $ht->add('test', $a);

        $b = $this->int(456);
        $ht->add('other', $b);

        $resulta = $ht->find('test');
        $this->assertNotNull($resulta);
        $this->assertTrue($resulta->identicalTo($a));

        $resultb = $ht->find('other');
        $this->assertNotNull($resultb);
        $this->assertTrue($resultb->identicalTo($b));
    }

    public function testAddThenUpdateThenFind(): void
    {
        $ht = new HashTable();
        $var = $this->int(123);
        $ht->add('test', $var);

        $var2 = $this->int(456);
        $ht->update('test', $var2);

        $result = $ht->find('test');
        $this->assertNotNull($result);
        $this->assertTrue($result->identicalTo($var2));
    }

    public function testNumericKeyAppend(): void
    {
        $ht = new HashTable();
        $vars = [
            $this->int(1),
            $this->int(2),
            $this->int(3),
            $this->int(4),
        ];
        foreach ($vars as $var) {
            $ht->append($var);
        }
        foreach ($vars as $idx => $var) {
            $result = $ht->findIndex($idx);

            $this->assertNotNull($result, 'ht->findIndex failed for index '.$idx);
            $this->assertTrue($result->identicalTo($var));
        }
    }

    public function testResize(): void
    {
        $ht = new HashTable();
        $vars = [];
        for ($i = 0; $i < HashTable::MIN_SIZE + 1; ++$i) {
            $vars[$i] = $var = $this->int($i + 1);
            $ht->append($var);
        }
        // resize triggers during MIN_SIZE + 1
        for ($i = 0; $i < HashTable::MIN_SIZE + 1; ++$i) {
            $result = $ht->findIndex($i);
            $this->assertNotNull($result, 'ht->findIndex failed for index '.$i);
            $this->assertTrue($result->identicalTo($vars[$i]), 'result is identical to variable at index '.$i);
        }
    }

    public function testStringResize(): void
    {
        $ht = new HashTable();
        $vars = [];
        for ($i = 0; $i < HashTable::MIN_SIZE + 1; ++$i) {
            $vars[$i] = $var = $this->int($i + 1);
            $ht->add("${i}", $var);
        }
        // resize triggers during MIN_SIZE + 1
        for ($i = 0; $i < HashTable::MIN_SIZE + 1; ++$i) {
            $result = $ht->find("${i}");
            $this->assertNotNull($result, 'ht->findIndex failed for index '.$i);
            $this->assertTrue($result->identicalTo($vars[$i]), 'result is identical to variable at index '.$i);
        }
    }

    private function int(int $value): Variable
    {
        $var = new Variable();
        $var->int($value);

        return $var;
    }
}
