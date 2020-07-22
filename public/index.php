<?php

/**
 * Serve api endpoint
 */
namespace Poem {
    require __DIR__ . "/../bootstrap.php";

    // Tell a new story
    $story = Story::new();
    
    // Introduce all public actors
    $story->about(\Product\Actor::class);
    $story->about(\User\Actor::class);
    $story->about(\Retailer\Actor::class);
    $story->about(\Market\Actor::class);
    $story->about(\Order\Actor::class);
    
    // Send compiled json response output
    $story->tell();
}
