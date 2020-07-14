<?php

namespace Poem;

use JsonSerializable;
use Poem\Data\Client;
use Poem\Data\Collection;
use Poem\Model\AttributeAccessor;
use Poem\Model\Relationships;
use Poem\Model\Set;

class Model implements JsonSerializable {
    use AttributeAccessor, 
        Relationships;

    static $clientId;
    static $serializable;
    static $primaryKey = 'id';

    function __construct(array $attributes = []) {
        
        // Initialize relationships if there are any (!once per class)
        $this->initializeRelationships();

        // Write initial attributes
        $this->writeAttributes($attributes);
    }

    function jsonSerialize() {
        $subject = get_called_class();
        $attributes = $this->serialize();
        $id = (int)$this->attributes[static::$primaryKey];

        if(isset($attributes[static::$primaryKey])) {
            unset($attributes[static::$primaryKey]);
        }
        
        $type = $subject::Type;
        $data = [];

        if(count(static::$relationships[$subject]) > 0) {
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

    function serialize() {
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

    static function client(): Client {
        return Data::clients()->resolveClient(static::$clientId);
    }

    static function collection(): Collection {
        return static::client()->getCollection(get_called_class()::Type);
    }

    static function all(): Set {
        return static::find();
    }

    static function pick(int $id) {
        return static::first(compact('id'));
    }

    static function find($conditions = []): Set {
        $resultSet = static::collection()->findMany($conditions);
        $items = [];

        foreach($resultSet->all() as $item) {
            $items[] = new static($item);
        }
        
        return new Set($items);
    }

    static function first($conditions = []) {
        $document = static::collection()->findFirst($conditions);

        if(!$document) {
            return false;
        }

        return new static((array)$document);
    }

    static function create(array $attributes): self {
        $document = new static($attributes);
        $document->save();
        return $document;
    }

    static function update(array $attributes, array $conditions = []) {
        return static::collection()->updateMany($attributes, $conditions);
    }

    static function delete(int $id) {
        $document = static::first(compact('id'));
        return $document->destroy();
    }

    static function foreignKey(): string {
        $className = get_called_class();
        return strtolower(substr($className, 0, strrpos($className, '\\'))) . "_" . static::$primaryKey;
    }
}