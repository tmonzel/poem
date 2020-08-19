<?php

namespace Poem\Auth\Actions;

use Poem\Actor\Action;
use Poem\Actor\Exceptions\UnauthorizedException;
use Poem\Auth\Accessor as AuthAccessor;

class MeAction extends Action 
{
    use AuthAccessor;

    /**
     * Me action type
     * 
     * @static
     * @var string
     */
    static $type = 'me';
    
    /**
     * Prepare me action data
     * 
     * @return mixed
     */
    function prepareData() 
    {
        if(!static::Auth()->authorized()) {
            throw new UnauthorizedException('No authorized user found');
        }

        return static::Auth()->user();
    }
}
