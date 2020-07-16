<?php

namespace Poem;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;

class Set implements ArrayAccess, Countable, IteratorAggregate, JsonSerializable 
{
    /**
     * The items contained in the set.
     *
     * @var array
     */
    protected $items = [];

    /**
     * Create a new set.
     * 
     * @param array $items
     * @return void
     */
    function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * Get all of the items in the set.
     *
     * @return array
     */
    public function all(): array 
    {
        return $this->items;
    }

    /**
     * Run a map over each of the items.
     *
     * @param  callable  $callback
     * @return static
     */
    public function map(callable $callback) 
    {
        $keys = array_keys($this->items);
        $items = array_map($callback, $this->items, $keys);

        return new static(array_combine($keys, $items));
    }

    /**
     * Serialize items for json_encode.
     * 
     * @return array
     */
    public function jsonSerialize(): array 
    {
        return $this->items;
    }

    /**
     * Count the number of items in the set.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Get an iterator for the items.
     *
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Determine if an item exists at an offset.
     *
     * @param  mixed  $key
     * @return bool
     */
    public function offsetExists($key): bool
    {
        return isset($this->items[$key]);
    }

    /**
     * Get an item at a given offset.
     *
     * @param  mixed  $key
     * @return mixed
     */
    public function offsetGet($key) 
    {
        return $this->items[$key];
    }

    /**
     * Set the item at a given offset.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value): void 
    {
        if (is_null($key)) {
            $this->items[] = $value;
        } else {
            $this->items[$key] = $value;
        }
    }

    /**
     * Unset the item at a given offset.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key): void 
    {
        unset($this->items[$key]);
    }
}