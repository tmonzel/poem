<?php

/**
 * Abstract module for all application modules
 */
class Module extends \Poem\Module 
{
    /**
     * Build all application actors with this base class
     * 
     * @static
     * @var string
     */
    static $actorClass = Actor::class;
}
