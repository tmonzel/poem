<?php

namespace Poem\Data\MySql;

use PDO;
use PDOStatement;
use Poem\Data\Statement;

class FindResult implements Statement 
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
     * Related table
     * 
     * @var Table
     */
    protected $table;

    /**
     * Query statement
     * 
     * @var PDOStatement
     */
    protected $statement;

    /**
     * All applied mapper callables
     * 
     * @var array
     */
    protected $mappers = [];

    /**
     * Columns meta data
     * 
     * @var array
     */
    protected $columns = [];

    /**
     * Options
     * 
     * @var array
     */
    protected $options = [];

    protected $join;

    /**
     * Creates a new find result.
     * 
     * @param PDOStatement $statement
     */
    function __construct(Table $table, PDOStatement $statement, array $options = [])
    {
        $this->table = $table;
        $this->statement = $statement;
        $this->options = $options;
        $this->mapper = function($props) {
            return $props;
        };

        foreach(range(0, $statement->columnCount() - 1) as $columnIndex) {
            $this->columns[] = $statement->getColumnMeta($columnIndex);
        }

        if(isset($options['join'])) {
            $this->join = $options['join'];
        }
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
     * Add a mapper which gets called every iteration
     * 
     * @param callable $mapper
     * @return void
     */
    function addMapper(callable $mapper): void 
    {
        $this->mappers[] = $mapper;
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

        foreach($this->mappers as $mapper) {
            $record = $mapper($record);
        }

        return $record;
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
