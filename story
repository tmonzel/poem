#!/usr/bin/env php
<?php

use Poem\Console\MigrateCommand;
use Poem\Console\SeedCommand;
use Symfony\Component\Console\Application;

require __DIR__ . "/bootstrap.php";

$application = new Application();
$application->add(new MigrateCommand());
$application->add(new SeedCommand());
$application->run();
