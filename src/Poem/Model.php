<?php

namespace Poem;

use JsonSerializable;
use Poem\Data\Client;
use Poem\Data\Collection;
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

    function toData(array $options = []) {
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

    function toArray(): array {
        return $this->attributes;
    }

    function destroy() {
        return static::collection()->deleteFirst([static::$primaryKey => $this->id]);
    }

    function exists(): bool {
        return isset($this->id);
    }

    function save() {
        if($this->exists()) {
            // Update existing document
            return static::collection()->updateFirst($this->attributes, [static::$primaryKey => $this->id]);
        } else {
            // Create new document
            $insertId = static::collection()->insert($this->attributes);
            $this->id = $insertId;
            return true;
        }
    }

    /**
     * Return the client
     */
    static function client(): Client 
    {
        return Data::clients()->resolveClient(static::$clientKey);
    }

    /**
     * Return the related persistant collection e.g. a table
     * 
     * @static
     * @return Collection
     */
    static function collection(): Collection 
    {
        return static::client()->getCollection(get_called_class()::Type);
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
        $document = new static($attributes);
        $document->save();
        return $document;
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
}