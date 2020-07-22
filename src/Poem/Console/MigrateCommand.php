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
        $type = $input->getArgument('type');
        $subject = null;

        if(strpos($type, '.') !== false) {
            $subject = str_replace('.', '\\', $type);
        } else {
            $subject = $type . '\\Model';
        }
        
        if(!$subject || !class_exists($subject)) {
            return Command::FAILURE;
        }

        // Syncronize subject
        $subject::sync();

        return Command::SUCCESS;
    }
}