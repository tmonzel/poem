<?php

namespace Services\Mail;

use Poem\Director;

trait Accessor {
    
    /**
     * Provide the mail service
     * 
     * @static
     * @return Service
     */
    static function Mail(): Service {
        return Director::provide(Service::class);
    }
}