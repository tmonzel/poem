<?php

namespace Poem;

use Exception;
use JsonSerializable;
use Poem\Data\CollectionAdapter;
use Poem\Data\Connection;
use Poem\Model\AttributeAccessor;
use Poem\Model\Relationships;
use Poem\Model\Set;

class Model implements JsonSerializable 
{
    use AttributeAccessor, 
        Relationships;

    /**
     * Client connection name
     * 
     * @static
     * @var string
     */
    static $clientKey = 'default';

    /**
     * Primary key column
     * 
     * @static
     * @var string
     */
    static $primaryKey = 'id';

    /**
     * Create a new model instance
     * 
     * @param array $attributes
     */
    function __construct(array $attributes = [])
    {
        // Initialize relationships if there are any (!once per class)
        $this->initializeRelationships();

        // Write initial attributes
        $this->writeAttributes($attributes);
    }

    /**
     * Serialize for json_encode
     * 
     * @return array
     */
    function jsonSerialize(): array
    {
        return $this->toData();
    }

    /**
     * Serialize just the attributes
     * 
     * @return array
     */
    function serialize(): array 
    {
        $attributes = $this->attributes;
        $calledClass = get_called_class();

        if(defined($calledClass . '::Attributes')) {
            $attributes = [];
            foreach($calledClass::Attributes as $n) {
                $attributes[$n] = $this->attributes[$n];
            }
        }

        return $attributes;
    }

    function toData(array $options = []) 
    {
        $format = isset($options['format']) ? $options['format'] : null;
        $subject = get_called_class();
        
        if($format && defined($subject . '::Attributes')) {
            $attributes = [];
            foreach($format as $n) {
                if(array_search($n, $subject::Attributes) !== false) {
                    $attributes[$n] = $this->attributes[$n];
                }
            }

            return $attributes;
        }

        $attributes = $this->serialize();
        $id = (int)$this->attributes[static::$primaryKey];

        if(isset($attributes[static::$primaryKey])) {
            unset($attributes[static::$primaryKey]);
        }
        
        $type = $subject::Type;
        $data = [];
        $include = isset($options['include']) ? $options['include'] : null;

        if($include && isset(static::$relationships[$subject][$include])) {
            $data['relationships'] = [];

            foreach(static::$relationships[$subject] as $name => $relClass) {
                $result = $this->{$name};

                if($result) {
                    $data['relationships'][$name] = [
                        'data' => $result->toRelatedData()
                    ];
                }
            }
        }

        return compact('type', 'id', 'attributes') + $data;
    }

    function toArray(): array 
    {
        return $this->attributes;
    }

    /**
     * Remove this document from the collection
     * 
     */
    function destroy() 
    {
        return static::collection()->deleteFirst([static::$primaryKey => $this->id]);
    }

    /**
     * Does this document exist (has id attribute)
     * 
     * @return bool
     */
    function exists(): bool 
    {
        return isset($this->id);
    }

    /**
     * Update document if id is set, otherwise create a new one
     * 
     * @return mixed
     */
    function save() 
    {
        if($this->exists()) {
            // Update existing document
            return static::collection()->updateFirst(
                static::mutateAttributes($this->attributes), 
                [static::$primaryKey => $this->id]
            );

        } else {
            // Create new document
            $insertId = static::collection()->insert(
                static::mutateAttributes($this->attributes)
            );

            $this->id = $insertId;
            return true;
        }
    }

    /**
     * Mutate attributes before create or update
     * 
     * @static
     * @param array $attributes
     * @return array
     */
    protected static function mutateAttributes(array $attributes): array 
    {
        return $attributes;
    }

    /**
     * Prepare schema for migration
     * 
     * @return array
     */
    static function prepareSchema(): array 
    {
        $calledClass = get_called_class();
        $schema = [];

        if(defined($calledClass . '::Schema')) {
            foreach($calledClass::Schema as $name => $type) {
                if(class_exists($type)) {
                    $schema[$type::foreignKey()] = 'fk';
                } else {
                    $schema[$name] = $type;
                }
            }
        }

        return $schema;
    }

    /**
     * Return the selected connection
     * 
     * @static
     * @return Connection
     */
    static function connection(): Connection 
    {
        return Data::resolveConnection(static::$clientKey);
    }

    /**
     * Return the related persistant collection e.g. a table
     * 
     * @static
     * @return CollectionAdapter
     */
    static function collection(): CollectionAdapter 
    {
        return static::connection()->accessCollection(get_called_class()::Type);
    }

    /**
     * Shorthand for static::find()
     * Returns all documents
     * 
     * @static
     * @return Set
     */
    static function all(): Set 
    {
        return static::find();
    }

    /**
     * Picks a single document
     * 
     * @return static
     */
    static function pick(int $id) 
    {
        return static::first(compact('id'));
    }

    /**
     * Find documents by conditions
     * 
     * @static
     * @param array $conditions
     * @return Set
     */
    static function find(array $conditions = []): Set 
    {
        $resultSet = static::collection()->findMany($conditions);
        $items = [];

        foreach($resultSet->all() as $item) {
            $items[] = new static($item);
        }
        
        return new Set($items);
    }

    /**
     * Find the first occurrent document
     * 
     * @static
     * @param array $conditions
     * @return static
     */
    static function first(array $conditions = []) 
    {
        $document = static::collection()->findFirst($conditions);

        if(!$document) {
            return false;
        }

        return new static((array)$document);
    }

    /**
     * Create a new document instance
     * 
     * @static
     * @param array $attributes
     * @return static
     */
    static function create(array $attributes) 
    {
        $attributes = static::mutateAttributes($attributes);
        $insertId = static::collection()->insert($attributes);
        return new static($attributes + ['id' => $insertId]);
    }

    /**
     * Update documents by conditions
     * 
     * @static
     * @param array $attributes
     * @param array $conditions
     */
    static function update(array $attributes, array $conditions = []) 
    {
        return static::collection()->updateMany($attributes, $conditions);
    }

    /**
     * Delete a single document by id
     * 
     * @static
     * @param int $id
     * @return bool
     */
    static function delete(int $id) 
    {
        $document = static::first(compact('id'));
        return $document->destroy();
    }

    /**
     * Return the foreign key for this model
     * 
     * @static
     * @return string
     */
    static function foreignKey(): string 
    {
        $className = get_called_class();
        return strtolower(substr($className, 0, strrpos($className, '\\'))) . "_" . static::$primaryKey;
    }

    /**
     * Syncronize the schema for this model
     */
    static function sync() 
    {
        $calledClass = get_called_class();

        if(!defined($calledClass . '::Schema')) {
            throw new Exception('No schema defined for ' . static::class);
        }

        $collection = static::collection();
        
        if($collection->exists()) {
            // sync
            $collection->sync(static::prepareSchema());
        } else {
            static::connection()->createCollection($calledClass::Type, static::prepareSchema());
        }
    }
}