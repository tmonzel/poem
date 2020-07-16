<?php

namespace Retailer {

    class Model extends \Poem\Model {
        const Type = 'retailers';
        
        /**
         * Public attributes
         * 
         * @var array
         */
        const Attributes = [
            'id',
            'name'
        ];
        
        /**
         * Data representation structure
         * 
         * @var array
         */
        const Schema = [
            'id' => 'pk',
            'name' => 'string',
            'created_at' => 'date',
            'updated_at' => 'date'
        ];
        
        /**
         * A retailer as many markets
         * 
         * @var array
         */
        const HasMany = [
            'markets' => \Market\Model::class
        ];
    }
}