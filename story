#!/usr/bin/env php
<?php

use Poem\Console\MigrateCommand;
use Poem\Console\SeedCommand;
use Poem\Director;
use Symfony\Component\Console\Application;

require __DIR__ . '/vendor/autoload.php';

/** @var Director $director */
$director = require __DIR__ . '/bootstrap.php';
$director->assign();

$application = new Application();
$application->add(new MigrateCommand());
$application->add(new SeedCommand());
$application->run();
