<?php

use Poem\Auth\Guardable;

/**
 * Abstract actor for all application actors
 */
class Actor extends \Poem\Actor 
{
    use Guardable;
}