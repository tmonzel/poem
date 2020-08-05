<?php

/**
 * Serve api endpoint
 */
namespace Poem {
    require __DIR__ . '/../vendor/autoload.php';
    
    /** @var Director $director */
    $director = require __DIR__ . "/../bootstrap.php";

    // Auth worker only needed in http endpoint app
    $director->add(Auth\Worker::class);

    // Tell a new story
    $story = $director->newStory();

    // Send compiled json response output
    $story->tell();
}
