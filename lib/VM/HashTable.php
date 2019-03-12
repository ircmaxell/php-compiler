<?php

/*
 * This file is part of PHP-Compiler, a PHP CFG Compiler for PHP code
 *
 * @copyright 2015 Anthony Ferrara. All rights reserved
 * @license MIT See LICENSE at the root of the project for more info
 */

namespace PHPCompiler\VM;

use PHPCompiler\NativeType\NativeArray;

final class HashTable {
    const OKAY                     = 0b0000000;
    const IS_DESTROYING            = 0b0000001;
    const DESTROYED                = 0b0000010;
    const CLEANING                 = 0b0000011;
    const FLAG_CONSISTENCY         = 0b0000011;
    const FLAG_PACKED              = 0b0000100;
    const FLAG_UNINITIALIZED       = 0b0001000;
    const FLAG_STATIC_KEYS         = 0b0010000;
    const FLAG_HAS_EMPTY_IND       = 0b0100000;
    const FLAG_ALLOW_COW_VIOLATION = 0b1000000;

    const MIN_SIZE = 8;
    const INVALID_INDEX = -1;

    const UPDATE          = 0b000001;
    const ADD             = 0b000010;
    const UPDATE_INDIRECT = 0b000100;
    const ADD_NEW         = 0b001000;
    const ADD_NEXT        = 0b010000;

    public Refcount $refcount;
    public int $flags = 0;
    public int $tableMask = 0;
    public NativeArray $indexes;
    public NativeArray $buckets;
    public int $numUsed = 0;
    public int $numElements = 0;
    public int $tableSize = self::MIN_SIZE;
    public int $internalPointer = 0;
    public int $nextFreeElement = 0;


    public function __construct() {
        $this->refcount = new Refcount;
        $this->flags = self::FLAG_UNINITIALIZED;
        $this->indexes = NativeArray::allocate($this->tableSize);
        $this->buckets = NativeArray::allocate($this->tableSize);
    }

    public function iterate(bool $resolveIndirect = false): \Traversable {
        for ($i = 0; $i < $this->numUsed; $i++) {
            $bucket = $this->buckets[$i];
            if ($bucket->value->isUndefined()) {
                continue;
            }
            $value = $bucket->value;
            if ($resolveIndirect) {
                $value = $value->resolveIndirect;
            }
            yield $value;
        }
    }

    public function findVariable(Variable $index, bool $forWrite): Variable {
        switch ($index->type) {
            case Variable::TYPE_INTEGER:
                $result = $this->findIndex($index->toInt());
                break;
            case Variable::TYPE_STRING:
                $result = $this->find($index->toString());
                break;
            default:
                throw new \LogicException("Unknown index type {$index->type}");
        }
        if (is_null($result)) {
            $result = new Variable;
            if ($forWrite) {
                if ($index->type === Variable::TYPE_INTEGER) {
                    return $this->addIndex($index->toInt(), $result);
                } else {
                    return $this->add($index->toString(), $result);
                }
            }
        }
        return $result;
    }

    public function findIndex(int $index): ?Variable {
        $this->assertConsistent();
        $bucket = $this->findBucket($index, null);
        if (is_null($bucket)) {
            return null;
        }
        return $bucket->value;
    }

    public function find(string $key): ?Variable {
        $this->assertConsistent();
        $bucket = $this->findBucket($this->hash($key), $key);
        if (is_null($bucket)) {
            return null;
        }
        return $bucket->value;
    }

    public function append(Variable $data): ?Variable {
        return $this->addOrUpdate($this->nextFreeElement, null, $data, self::ADD | self::ADD_NEXT);
    }

    public function addIndex(int $index, Variable $data): ?Variable {
        return $this->addOrUpdate($hash, null, $data, self::ADD);
    }

    public function addNewIndex(int $index, Variable $data): ?Variable {
        return $this->addOrUpdate($hash, null, $data, self::ADD | self::ADD_NEW);
    }

    public function updateIndex(int $index, Variable $data): ?Variable {
        return $this->addOrUpdate($hash, null, $data, self::UPDATE);
    }

    public function updateIndirectIndex(int $index, Variable $data): ?Variable {
        return $this->addOrUpdate($index, null, $data, self::UPDATE | self::UPDATE_INDIRECT);
    }

    public function add(string $key, Variable $data): ?Variable {
        return $this->addOrUpdate($this->hash($key), $key, $data, self::ADD);
    }

    public function addNew(string $key, Variable $data): ?Variable {
        return $this->addOrUpdate($this->hash($key), $key, $data, self::ADD_NEW);
    }

    public function update(string $key, Variable $data): ?Variable {
        return $this->addOrUpdate($this->hash($key), $key, $data, self::UPDATE);
    }

    public function updateIndirect(string $key, Variable $data): ?Variable {
        return $this->addOrUpdate($this->hash($key), $key, $data, self::UPDATE | self::UPDATE_INDIRECT);
    }

    private function addOrUpdate(int $hash, ?string $key, Variable $data, int $flags): ?Variable {
        $this->assertConsistent();
        $this->refcount->assertSeparated();
        if ($this->flags & self::FLAG_UNINITIALIZED) {
            $this->initMixed();
        }
        $this->resizeIfFull();
        if (($flags & self::ADD_NEW) === 0) {
            $bucket = $this->findBucket($hash, $key);
            if ($bucket) {
                if ($flags & self::ADD) {
                    if (!($flags & self::UPDATE_INDIRECT)) {
                        return null;
                    }
                    $bucketData = $bucket->value;
                    if ($bucketData->isIndirect()) {
                        $bucketData = $bucketData->resolveIndirect();
                        if (!$bucketData->isUndefined()) {
                            return null;
                        }
                    } else {
                        return null;
                    }
                } else {
                    $bucketData = $bucket->value;
                    if (($flags & self::UPDATE_INDIRECT) && $bucketData->isIndirect()) {
                        $bucketData = $bucketData->resolveIndirect();
                    }
                }
                $bucketData->copyFrom($data);
                return $bucketData;
            }
        }
        $this->resizeIfFull();
        $id = $this->numUsed++;
        $this->numElements++;
        $bucket = $this->buckets[$id];
        $bucket->key = $key;
        $bucket->hash = $hash;
        $nIndex = $hash & $this->tableMask;
        $bucket->value->next = $this->indexes[$nIndex];
        $this->indexes[$nIndex] = $id;
        $bucket->value->copyFrom($data);
        if (is_null($key) && $hash >= $this->nextFreeElement) {
            $this->nextFreeElement = $hash + 1;
        }
        return $bucket->value;
    }

    private function findBucket(int $hash, ?string $key): ?HashTableBucket {
        $nIndex = $hash & $this->tableMask;
        $idx = $this->indexes[$nIndex];
        do {
            if ($idx === self::INVALID_INDEX) {
                return null;
            }
            $bucket = $this->buckets[$idx];
            if ($bucket->key === $key) {
                return $bucket;
            }
            $idx = $bucket->value->next;
        } while (true);
    }

    private function assertUninitialized(): void {
        if (0 === ($this->flags & self::FLAG_UNINITIALIZED)) {
            throw new \LogicException('Hash table was asserted to be uninitialized, but was initialized');
        }
    }

    private function assertConsistent(): void {
        if (($this->flags & self::FLAG_CONSISTENCY) === self::OKAY) {
            return;
        }
        switch ($this->flags & self::FLAG_CONSISTENCY) {
            case self::IS_DESTROYING:
                throw new \LogicException('Hash table is being destroyed');
            case self::DESTROYED:
                throw new \LogicException('Hash table is already destroyed');
            case self::CLEANING:
                throw new \LogicException('Hash table is being cleaned');
        }
        // Should never happen
        throw new \LogicException('Hash table is inconsistent');
    }

    private function init(bool $packed) {
        $this->refcount->assertSeparated();
        $this->assertUninitialized();
        if ($packed) {
            $this->initPacked();
        } else {
            $this->initMixed();
        }
    }

    private function initMixed(): void {
        $this->tableMask = $this->computeTableMask();
        $this->flags = $this->flags & ~self::FLAG_UNINITIALIZED;
        $this->rehash();
    }

    private function resizeIfFull(): void {
        if ($this->numUsed >= $this->tableSize) {
            $this->resize();
        }
    }

    private function resize(): void {
        if ($this->numUsed > $this->numElements + ($this->numElements >> 5)) {
            $this->rehash();
            return;
        }
        $newSize = $this->tableSize + $this->tableSize;
        $this->indexes = NativeArray::reallocate($this->indexes, $newSize);
        $this->buckets = NativeArray::reallocate($this->buckets, $newSize);
        for ($i = $this->tableSize; $i < $newSize; $i++) {
            $this->indexes[$i] = self::INVALID_INDEX;
            $this->buckets[$i] = new HashTableBucket(new Variable(Variable::TYPE_UNDEFINED), 0, '');
        }
        $this->tableSize = $newSize;
        $this->tableMask = $this->computeTableMask();
        $this->rehash();
    }

    private function rehash(): void {
        if ($this->numElements === 0) {
            if (!($this->flags & self::FLAG_UNINITIALIZED)) {
                $this->numUsed = 0;
                for ($i = 0; $i < $this->tableSize; $i++) {
                    $this->indexes[$i] = self::INVALID_INDEX;
                    $this->buckets[$i] = new HashTableBucket(new Variable(Variable::TYPE_UNDEFINED), 0, '');
                }
            }
            return;
        }
        $this->reset();
        $bucketIndex = 0;
        if ($this->isWithoutHoles()) {
            do {
                $index = $this->buckets[$bucketIndex]->hash & $this->tableMask;
                $this->buckets[$bucketIndex]->value->next = $index;
                $this->indexes[$index] = $bucketIndex;
            } while (++$bucketIndex < $this->numUsed);
            return;
        }
        //todo
        throw new \LogicException('Need to implement rehash');
    }

    private function computeTableMask(): int {
        return ($this->tableSize - 1);
    }

    private function isWithoutHoles(): bool {
        return $this->numUsed === $this->numElements;
    }

    private function reset() {
        for ($i = 0; $i < $this->tableSize; $i++) {
            $this->indexes[$i] = self::INVALID_INDEX;
        }
    }

    private function hash(string $key): int {
        $hash = 5381;
        for ($i = 0, $len = strlen($key); $i < $len; $i++) {
            $hash = (($hash << 5) + $hash) + ord($key[$i]);
        }
        return $hash | 0x8000000000000000;
    }
}

final class HashTableBucket {
    public Variable $value;
    public int $hash;
    public ?string $key;

    public function __construct(Variable $value, int $hash, ?string $key) {
        $this->value = $value;
        $this->hash = $hash;
        $this->key = $key;
    }
}