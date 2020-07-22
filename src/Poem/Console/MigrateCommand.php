<?php

namespace Poem\Console;

use Poem\Data\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends Command 
{
    protected static $defaultName = 'migrate';

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Migrates the given model.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to migrate a model.')

            ->addArgument('type', true, 'The subject name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $appDir = constant('APP_ROOT') . "/app";
        $type = $input->getArgument('type');
        $subject = null;

        foreach (scandir($appDir) as $file) {
            if ($file !== '.' && $file !== '..') {
                $files[] = $file;

                $subjectClass = $file . "\\Model";
                if($subjectClass::Type === $type) {
                    $subject = $subjectClass;
                    break;
                }
            }
        }   

        if(!$subject) {
            return Command::FAILURE;
        }

        /** @var Connection $client */
        $client = $subject::connection();
        $client->createCollection($type, $subject::prepareSchema());

        return Command::SUCCESS;
    }
}