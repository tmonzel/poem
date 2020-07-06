<?php

namespace Poem;

use JsonSerializable;
use Poem\Data\Client;
use Poem\Data\Collection;
use Poem\Model\AttributeAccessor;
use Poem\Model\Set;

class Model implements JsonSerializable {
    use AttributeAccessor;

    static $clientId;
    static $type;
    static $serializable;
    static $primaryKey = 'id';

    function __construct(array $attributes = []) {
        $this->writeAttributes($attributes);
    }

    function jsonSerialize() {
        $attributes = $this->attributes;
        $id = (int)$attributes[static::$primaryKey];
        unset($attributes[static::$primaryKey]);

        if(static::$serializable) {
            $attributes = [];
            foreach(static::$serializable as $n) {
                $attributes[$n] = $this->attributes[$n];
            }
        }

        $type = static::type();

        return compact('type', 'id', 'attributes');
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
        return static::client()->getCollection(static::$type);
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

    static function type(): string {
        return static::$type;
    }
}