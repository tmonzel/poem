<?php

namespace Poem\Auth;

use Poem\Actor\ActionQuery;
use Poem\Actor\Behavior;
use Poem\Actor\Exceptions\UnauthorizedException;
use Poem\Auth;

/**
 * Guard behavior
 */
class JwtGuard extends Behavior 
{
    function initialize(ActionQuery $query) 
    {
        $except = [];
        $token = null;
        $headers = $query->getHeaders();

        extract($this->config);

        if(array_search($query->getType(), $except) !== false) {
            return;
        }

        if(isset($headers['authorization']) && isset($headers['authorization'][0])) {
            $token = $headers['authorization'][0];
        }
        
        if(!Auth::authorize($token)) {
            throw new UnauthorizedException('Action not allowed');
        }
    }
}