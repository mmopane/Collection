<?php

namespace MMOPANE\Collection;

use ArgumentCountError;
use ArrayAccess;
use ArrayIterator;
use Closure;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * @template TKey of array-key
 * @template-covariant TValue
 * @implements ArrayAccess<TKey, TValue>
 */
class Collection implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * The items contained in the collection.
     * @var array<TKey, TValue>
     */
    protected array $items = [];

    /**
     * Create a new collection.
     * @param array<TKey, TValue> $items
     * @return void
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * Get an item from the collection by key.
     * @template TValueDefault
     * @param TKey $key
     * @param TValueDefault|mixed $default
     * @return TValue|TValueDefault
     */
    public function get(mixed $key, mixed $default = null): mixed
    {
        if(array_key_exists($key, $this->items))
            return $this->items[$key];

        return $default instanceof Closure ? $default($key) : $default;
    }

    /**
     * Add an item to the collection.
     * @param TValue $value
     * @return static
     */
    public function add(mixed $value): static
    {
        $this->items[] = $value;
        return $this;
    }

    /**
     * Put an item in the collection by key.
     * @param TKey $key
     * @param TValue $value
     * @return static
     */
    public function put(mixed $key, mixed $value): static
    {
        $this->offsetSet($key, $value);
        return $this;
    }

    /**
     * Determine if an item exists in the collection by key.
     * @param TKey|array<array-key, TKey> $key
     * @return bool
     */
    public function has(mixed $key): bool
    {
        $keys = is_array($key) ? $key : func_get_args();
        foreach ($keys as $value)
        {
            if (! array_key_exists($value, $this->items))
                return false;
        }
        return true;
    }

    /**
     * Remove an item from the collection by key.
     * @param TKey|array<array-key, TKey> $key
     * @return static
     */
    public function forget(mixed $key): static
    {
        $keys = is_array($key) ? $key : func_get_args();
        foreach ($keys as $value)
            $this->offsetUnset($value);
        return $this;
    }

    /**
     * Get first item from the collection.
     * @template TValueDefault
     * @param TValueDefault|mixed $default
     * @return TValue|TValueDefault
     */
    public function first(mixed $default = null): mixed
    {
        $key = array_key_first($this->items);
        if(is_null($key))
            return $default instanceof Closure ? $default($key) : $default;
        return $this->items[$key];
    }

    /**
     * Get last item from the collection.
     * @template TValueDefault
     * @param TValueDefault|mixed $default
     * @return TValue|TValueDefault
     */
    public function last(mixed $default = null): mixed
    {
        $key = array_key_last($this->items);
        if(is_null($key))
            return $default instanceof Closure ? $default($key) : $default;
        return $this->items[$key];
    }

    /**
     * Get all the items in the collection.
     * @return array<TKey, TValue>
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Count the number of items in the collection.
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Determine if the collection is empty or not.
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->count() <= 0;
    }

    /**
     * Get the keys of the collection items.
     * @return static<int, TKey>
     */
    public function keys(): static
    {
        return new static(array_keys($this->items));
    }

    /**
     * Get the values of the collection items.
     * @return static<int, TValue>
     */
    public function values(): static
    {
        return new static(array_values($this->items));
    }

    /**
     * Run a map over each of the items.
     * @template TMapValue
     * @param callable(TValue, TKey): TMapValue $callback
     * @return static<TKey, TMapValue>
     */
    public function map(callable $callback): static
    {
        $keys = array_keys($this->items);
        try
        {
            $items = array_map($callback, $this->items, $keys);
        }
        catch (ArgumentCountError)
        {
            $items = array_map($callback, $this->items);
        }
        return new static(array_combine($keys, $items));
    }

    /**
     * Run a map over each of the items.
     * @template TMapValue
     * @param callable(TValue, TKey): TMapValue|null $callback
     * @return static<TKey, TMapValue>
     */
    public function filter(callable $callback = null): static
    {
        if(!is_null($callback))
            return new static(array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH));
        return new static(array_filter($this->items));
    }

    /**
     * Determine if an item exists at an offset.
     * @param TKey $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->items[$offset]);
    }

    /**
     * Get an item at a given offset.
     * @param TKey $offset
     * @return TValue
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->items[$offset];
    }

    /**
     * Set the item at a given offset.
     * @param TKey|null $offset
     * @param TValue $value
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_null($offset))
            $this->items[] = $value;
        else
            $this->items[$offset] = $value;
    }

    /**
     * Unset the item at a given offset.
     * @param TKey $offset
     * @return void
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->items[$offset]);
    }

    /**
     * Get an iterator for the items.
     * @return ArrayIterator<TKey, TValue>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }
}