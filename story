#!/usr/bin/env php
<?php

use Poem\Console\MigrateCommand;
use Symfony\Component\Console\Application;

require __DIR__ . "/bootstrap.php";

$application = new Application();
$application->add(new MigrateCommand());
$application->run();
