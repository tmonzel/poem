<?php

namespace User;

use Poem\Model as BaseModel;

class Role extends BaseModel {
    const Type = 'roles';
    const HasMany = [
        'users' => Model::class
    ];
}