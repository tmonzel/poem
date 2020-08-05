<?php

namespace Poem\Model;

use Exception;
use Poem\Actable;
use Poem\Data\Accessor as DataAccessor;
use Poem\Data\CollectionAdapter;
use Poem\Data\Cursor;
use Poem\Module;

class Collection
{
    use Module,
        DataAccessor,
        Relationships,
        Actable;

    const BEFORE_SAVE_EVENT = 'model_before_save';

    /**
     * Applied adapter
     * 
     * @var CollectionAdapter
     */
    protected $adapter;

    /**
     * Collection type
     * 
     * @var string
     */
    protected $type;

    /**
     * Client connection name
     * 
     * @static
     * @var string
     */
    static $connection = 'default';

    /**
     * Primary key column
     * 
     * @static
     * @var string
     */
    static $primaryKey = 'id';

    /**
     * Used document class
     * 
     * @var string
     */
    protected $documentClass;

    /**
     * Create a new model instance
     * 
     * @param string $type
     */
    function __construct(string $type)
    {
        $this->type = $type;
        $this->adapter = static::Data()
            ->resolveConnection(static::$connection)
            ->getCollectionAdapter($type);

        // Initialize behaviors once per class
        static::initializeBehaviors();
        
        // Initialize relationships once per class 
        static::initializeRelationships();
    }

    /**
     * Return the foreign key for this collection
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
     * Guessing the document class for this collection
     * 
     * @return string
     */
    function guessDocumentClass(): string
    {
        if(isset($this->documentClass)) {
            return $this->documentClass;
        }

        $this->documentClass = Document::class;

        static::withNamespaceClass('Document', function($documentClass) {
            $this->documentClass = $documentClass;
        });

        return $this->documentClass;
    }

    /**
     * Builds a new document with the given attributes
     * 
     * @param array $attributes
     * @return Document
     */
    function buildDocument(array $attributes = []): Document
    {
        $documentClass = $this->guessDocumentClass();
        return new $documentClass($this->type, $attributes);
    }

    /**
     * Update document if exists, 
     * otherwise create a new one
     * 
     * @param Document $document
     * @return mixed
     */
    function save(Document $document) 
    {
        $this->dispatchEvent(self::BEFORE_SAVE_EVENT, [$document]);
        
        if($document->exists()) {
            // Update existing document
            return $this->update(
                $document->toArray(), 
                [static::$primaryKey => $document->id]
            );

        } else {
            // Create new document
            $insertId = $this->adapter->insert(
                $document->toArray()
            );

            $document->id = $insertId;
            return true;
        }
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
     * Picks a single document
     * 
     * @return Document
     */
    function pick(int $id) 
    {
        return $this->first(compact('id'));
    }

    /**
     * Creates a find query builder
     * 
     * @return FindQuery
     */
    function find(): FindQuery 
    {
        return new FindQuery($this);
    }

    /**
     * Execute a direct find query by conditions
     * 
     * @param array $conditions
     * @return Cursor
     */
    function findWith(array $conditions): Cursor 
    {
        $result = $this->adapter->find($conditions['filter']);
        $format = isset($conditions['format']) ? $conditions['format'] : null;

        $result->map(function($attributes) use($format) {
            $document = $this->buildDocument($attributes);
            $document->setFormat($format);
            
            return $document;
        });

        return $result;
    }

    /**
     * Find the first occurrent document
     * 
     * @param array $filter
     * @return Document|null
     */
    function first(array $filter = []): ?Document
    {
        $query = $this->find();
        $query->filter($filter);
        $query->limit(1);

        return $query->first();
    }   

    /**
     * Create a new document instance
     * 
     * @param array $attributes
     * @return Document
     */
    function create(array $attributes): Document 
    {
        $insertId = $this->adapter->insert(
            $attributes
        );

        return $this->buildDocument($attributes + [static::$primaryKey => $insertId]);
    }

    /**
     * Update many documents
     * 
     * @param array $filter
     * @param array $attributes
     * @return int affected rows or throw error (TODO)
     */
    function update(array $filter, array $attributes) 
    {
        return $this->adapter->update($filter, $attributes);
    }

    /**
     * Removes a single document
     * 
     * @param Document $document
     * @return int
     */
    function destroy(Document $document) 
    {
        if(!$document->exists()) {
            return false;
        }

        return $this->delete([static::$primaryKey => $document->id], ['limit' => 1]);
    }

    /**
     * Delete many documents
     * 
     * @param array $filter
     * @return int affected rows or throw error (TODO)
     */
    function delete(array $filter, array $options = []) 
    {
        return $this->adapter->delete($filter, $options);
    }

    /**
     * Syncronize the schema for this model
     */
    function sync() 
    {
        $calledClass = get_called_class();

        if(!defined($calledClass . '::Schema')) {
            throw new Exception('No schema defined for ' . static::class);
        }

        /*$collection = static::collection();
        
        if($collection->exists()) {
            // sync
            $collection->sync(static::prepareSchema());
        } else {
            static::connection()->createCollection($calledClass::Type, static::prepareSchema());
        }*/
    }
}