<?php

namespace Poem\Auth;

use Poem\Actor\ActionQuery;

trait Guardable 
{
    use Accessor;

    function guardActions(array $except = [])
    {
        $this->canActivate(function(ActionQuery $query) use($except) {
            if(array_search($query->getType(), $except) !== false) {
                return true;
            }

            return static::Auth()->authorized();
        });
    }
}
