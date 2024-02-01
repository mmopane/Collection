<?php

namespace MMOPANE\Collection;

use ArrayAccess;

/**
 * @template TKey of array-key
 * @template-covariant TValue
 * @implements ArrayAccess<TKey, TValue>
 */
interface CollectionInterface extends ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * Get an item from the collection by key.
     * @template TValueDefault
     * @param TKey $key
     * @param TValueDefault|mixed $default
     * @return TValue|TValueDefault
     */
    public function get(mixed $key, mixed $default = null): mixed;

    /**
     * Add an item to the collection.
     * @param TValue $value
     * @return static
     */
    public function add(mixed $value): static;

    /**
     * Put an item in the collection by key.
     * @param TKey $key
     * @param TValue $value
     * @return static
     */
    public function put(mixed $key, mixed $value): static;

    /**
     * Determine if an item exists in the collection by key.
     * @param TKey ...$keys
     * @return bool
     */
    public function has(mixed ...$keys): bool;

    /**
     * Remove an item from the collection by key.
     * @param TKey|array<array-key, TKey> $key
     * @return static
     */
    public function forget(mixed $key): static;

    /**
     * Get all the items in the collection.
     * @return array<TKey, TValue>
     */
    public function all(): array;

    /**
     * Delete all items in the collection.
     * @return static<int, TKey>
     */
    public function clear(): static;

    /**
     * Determine if the collection is empty or not.
     * @return bool
     */
    public function isEmpty(): bool;
}