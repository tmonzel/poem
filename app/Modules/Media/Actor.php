<?php

namespace Modules\Media;

class Actor extends \Poem\Actor
{
    /**
     * Initialize media actor
     * 
     * @return void
     */
    function initialize(): void
    {
        $this->registerAction('');
        $this->guardActions();
    }
}
