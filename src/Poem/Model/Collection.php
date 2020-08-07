<?php

namespace Poem\Model;

use Exception;
use Poem\Data\Accessor as DataAccessor;
use Poem\Data\CollectionAdapter;
use Poem\Data\Statement;
use Poem\Module;
use Poem\Mutable;

class Collection
{
    use Module,
        DataAccessor,
        Relationships,
        Mutable;

    /**
     * Before save event key
     * 
     * @var string
     */
    const BEFORE_SAVE_EVENT = 'collection.before_save';

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
     * Singularized version of the collection type
     * 
     * @var string
     */
    protected $name;

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
    function __construct(array $options = [])
    {
        $this->type = $options['type'];
        $this->adapter = static::Data()
            ->resolveConnection(static::$connection)
            ->accessAdapter($this->type);

        if(isset($options['name'])) {
            $this->name = $options['name'];
        }

        // Initialize behaviors once per class
        static::initializeBehaviors();
        
        // Initialize relationships once per class 
        static::initializeRelationships();
    }

    /**
     * Returns the collection name which is the singularized
     * version of the type
     * 
     * @return string
     */
    function getName(): string
    {
        return $this->name;
    }

    /**
     * Return the foreign key for this collection
     * 
     * @return string
     */
    function foreignKey(): string 
    {
        return $this->name . "_" . static::$primaryKey;
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
     * Shorthand for buildDocument
     * 
     * @param array $attributes
     * @return Document
     */
    function new(array $attributes): Document
    {
        return $this->buildDocument($attributes);
    }

    /**
     * Updates or creates a given document.
     * 
     * @param Document $document
     * @return mixed
     */
    function save(Document $document) 
    {
        if(!$this->validate($document)) {
            return false;
        }
        
        $this->dispatchEvent(self::BEFORE_SAVE_EVENT, [$document]);
        
        if($document->exists()) {
            // Update existing document
            return $this->adapter->update(
                [static::$primaryKey => $document->id],
                $document->toArray()
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
     * Validates a given document
     * 
     * @param Document $document
     * @return bool
     */
    function validate(Document $document): bool 
    {
        $validations = $this->validations();
        $errors = [];

        foreach($validations as $name => $config) {
            if(!is_array($config)) {
                $config = [$config];   
            }

            foreach($config as $test) {
                switch($test) {
                    case 'required':
                        if(!$document->has($name)) {
                            $errors[$name] = [
                                'status' => 400,
                                'title' => $name . " is required"
                            ];
                        }
                }
            }
        }
        
        $document->setErrors($errors);

        return !$document->hasErrors();
    }

    /**
     * Override this to specify validations
     * 
     * @return array
     */
    function validations(): array
    {
        return [];
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
     * @return Statement
     */
    function findWith(array $conditions): Statement 
    {
        extract($conditions);

        $statement = $this->adapter->find($filter);

        if(isset($include) && is_array($include)) {
            // Find relationships
            foreach($include as $accessor => $target) {
                if(is_numeric($accessor)) {
                    $accessor = $target;
                }

                if($this->hasRelationship($accessor)) {
                    $relationship = $this->getRelationship($accessor);
                    $relationship->attachTo($this, $statement);
                }
            }
        }

        $statement->addMapper(function($attributes) use($format) {
            $document = $this->buildDocument($attributes);
            $document->setFormat($format);
            
            return $document;
        });

        return $statement;
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

        return $this->adapter->delete([static::$primaryKey => $document->id], ['limit' => 1]);
    }

    /**
     * Syncronize the schema for this model
     * 
     * @return void
     */
    function migrate(): void 
    {
        $calledClass = get_called_class();

        if(!defined($calledClass . '::Schema')) {
            throw new Exception('No schema defined for ' . static::class);
        }

        $schema = [];

        foreach($calledClass::Schema as $name => $type) {
            $schema[$name] = $type;
        }

        $this->adapter->migrate($schema);
    }
}