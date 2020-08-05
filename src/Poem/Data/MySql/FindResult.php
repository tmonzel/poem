<?php

namespace Poem\Data\MySql;

use PDO;
use PDOStatement;
use Poem\Data\Cursor;

class FindResult implements Cursor 
{
    /**
     * Current iterator index
     * 
     * @var int
     */
    protected $_index = 0;

    /**
     * Current iterator value
     * 
     * @var mixed
     */
    protected $_current;

    /**
     * Query statement
     * 
     * @var PDOStatement
     */
    protected $statement;

    /**
     * Mapper callback
     * 
     * @var callable
     */
    protected $mapper;

    /**
     * Creates a new find result.
     * 
     * @param PDOStatement $statement
     */
    function __construct(PDOStatement $statement)
    {
        $this->statement = $statement;
        $this->mapper = function($props) {
            return $props;
        };
    }

    /**
     * Serialize to json
     * 
     * @return array
     */
    function jsonSerialize(): array
    {
        return iterator_to_array($this);
    }

    /**
     * Closes the statement cursor
     * 
     * @return void
     */
    function close(): void
    {
        $this->statement->closeCursor();
    }

    /**
     * Set a mapper which gets called every iteration
     * 
     * @param callable $mapper
     * @return void
     */
    function map(callable $mapper): void 
    {
        $this->mapper = $mapper;
    }

    /**
     * Fetches the current record
     * 
     * @return mixed
     */
    protected function fetch() 
    {
        $record = $this->statement->fetch(PDO::FETCH_ASSOC);

        if($record === false) {
            return false;
        }

        return call_user_func($this->mapper, $record);
    }

    /**
     * Returns the current record in the result iterator
     *
     * Part of Iterator interface.
     *
     * @return array|object
     */
    public function current()
    {
        return $this->_current;
    }

    /**
     * Returns the key of the current record in the iterator
     *
     * Part of Iterator interface.
     *
     * @return int
     */
    public function key(): int
    {
        return $this->_index;
    }

    /**
     * Advances the iterator pointer to the next record
     *
     * Part of Iterator interface.
     *
     * @return void
     */
    public function next(): void
    {
        $this->_index++;
    }

    /**
     * Rewinds the result
     *
     * Part of Iterator interface.
     *
     * @return void
     */
    public function rewind(): void
    {
        if ($this->_index === 0) {
            return;
        }

        $this->_index = 0;
    }

    /**
     * Whether there are more results to be fetched from the iterator
     *
     * Part of Iterator interface.
     *
     * @return bool
     */
    public function valid(): bool
    {
        $this->_current = $this->fetch();
        $valid = $this->_current !== false;

        if (!$valid && $this->statement !== null) {
            $this->statement->closeCursor();
        }

        return $valid;
    }
}
