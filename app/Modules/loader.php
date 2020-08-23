<?php

namespace Modules;

use Poem\Director;

return function(Director $director) {

    // Booting all application modules
    foreach([
        User\Module::class,
        Retailer\Module::class,
        Product\Module::class,
        Market\Module::class,
        Order\Module::class,
        Info\Module::class
    ] as $m) {
        $m::boot($director);
    }

};
