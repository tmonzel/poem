<?php

namespace Poem\Model;

use Exception;
use Poem\Data\Accessor as DataAccessor;
use Poem\Data\CollectionAdapter;
use Poem\Data\Connection;
use Poem\Data\Statement;
use Poem\Mutable;

class Collection
{
    use DataAccessor,
        Relationships,
        Mutable;

    /**
     * Before save event key
     * 
     * @var string
     */
    const BEFORE_SAVE_EVENT = 'collection.before_save';

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
     * 
     * @var array
     */
    protected $validations = [];

    /**
     * Creates a new collection instance.
     * 
     * @param string $type
     */
    function __construct(array $options = [])
    {
        $this->type = $options['type'];

        if(isset($options['name'])) {
            $this->name = $options['name'];
        }

        if(isset($options['documentClass'])) {
            $this->documentClass = $options['documentClass'];
        }

        if(isset($options['validations'])) {
            $this->validations = $options['validations'];
        }

        if(isset($options['relationships'])) {
            foreach($options['relationships'] as $type => $config) {
                $this->addRelationship($type, $config);
            }
        }

        // Initialize behaviors once per class
        static::initializeBehaviors();

        $this->initialize();
    }

    /**
     * Returns the collection type
     * 
     * @return string
     */
    function getType(): string
    {
        return $this->type;
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
     * 
     * @return Connection
     */
    static function connection(): Connection
    {
        return static::Data()->resolveConnection(static::$connection);
    }

    /**
     * 
     * @return CollectionAdapter
     */
    function accessAdapter(): CollectionAdapter 
    {
        return static::connection()->accessAdapter($this->type);
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

        /*static::withNamespaceClass('Document', function($documentClass) {
            $this->documentClass = $documentClass;
        });*/

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
     * Validates and mutates the given document.
     * 
     * @param Document $document
     * @return mixed
     */
    function save(Document $document, bool $includeRelated = true) 
    {
        if(!$this->validate($document)) {
            return false;
        }
        
        $this->dispatchEvent(self::BEFORE_SAVE_EVENT, [$document]);

        $result = null;
        $data = $document->toArray();

        // Remove all relationship fields before persist
        $data = array_filter($data, function($name) {
            return !$this->hasRelationship($name);
        }, ARRAY_FILTER_USE_KEY);
        
        if($document->exists()) {
            // Update existing document
            $dirtyAttributes = array_filter($data, function($name) use($document) {
                return $document->isDirty($name);
            }, ARRAY_FILTER_USE_KEY);

            if(empty($dirtyAttributes)) {
                $result = true;
            } else {
                $result = $this->accessAdapter()->update(
                    [static::$primaryKey => $document->id],
                    $dirtyAttributes
                );
            }

        } else {
            // Create new document
            $insertId = $this->accessAdapter()->insert($data);

            $document->id = $insertId;
            $result = true;
        }

        if($includeRelated) {
            foreach($this->relationships as $relationship) {
                $relationship->saveTo($document);
            }
        }

        return $result;
    }

    /**
     * Validates a given document
     * 
     * @param Document $document
     * @return bool
     */
    function validate(Document $document): bool 
    {
        $errors = [];

        foreach($this->validations as $name => $config) {
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

    function addValidation($field, $validators) 
    {
        $this->validations[$field] = $validators;
    }

    /**
     * Override this for initialization
     * 
     * @return void
     */
    function initialize(): void
    {
        
    }

    /**
     * Picks a single document
     * 
     * @param int $id
     * @param array $options
     * @return Document
     */
    function pick(int $id, array $options = []): Document 
    {
        return $this->first(compact('id'), $options);
    }

    /**
     * 
     * @return FindQuery
     */
    function pickMany(array $ids): FindQuery 
    {
        return $this->find()->filter(['id' => $ids]);
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

        $statement = $this->accessAdapter()->find($filter);
        $hiddenAttributes = [];

        foreach($this->relationships as $relationship) {
            // Hide all related foreign keys from document attributes
            $hiddenAttributes[] = $relationship->getForeignKey();
        }

        if(isset($include) && $include === '*') {
            // Include all relationships
            $include = array_keys($this->relationships);
        }

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

        $statement->addMapper(function($attributes) use($format, $hiddenAttributes) {
            $document = $this->buildDocument($attributes);
            $document->setFormat($format);
            $document->hide($hiddenAttributes);
            
            return $document;
        });

        return $statement;
    }

    /**
     * Find the first occurrent document
     * 
     * @param array $filter
     * @param array $options
     * @return Document|null
     */
    function first(array $filter = [], array $options = []): ?Document
    {
        $query = $this->find();
        $query->filter($filter);
        $query->limit(1);

        if(isset($options['include'])) {
            $query->include($options['include']);
        }

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
        $insertId = $this->accessAdapter()->insert(
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

        return $this->accessAdapter()->delete([static::$primaryKey => $document->id], ['limit' => 1]);
    }

    /**
     * 
     * @return void
     */
    function truncate(): void
    {
        $this->accessAdapter()->truncate();
    }

    /**
     * 
     * @return int
     */
    function count(): int
    {
        return $this->accessAdapter()->count();
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

        $this->accessAdapter()->migrate($schema);
    }
}
