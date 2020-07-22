<?php

namespace Poem\Console;

use Poem\Data\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SeedCommand extends Command 
{
    protected static $defaultName = 'seed';

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Seeds the fixtures for the given model.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to seed the fixtures for a model.')

            ->addArgument('type', true, 'The subject type')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getArgument('type');
        $subject = $subjectDir = null;

        if(strpos($type, '.') !== false) {
            $subject = str_replace('.', '\\', $type) . "\\Model";
            $subjectDir = APP_DIR . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $type);
        } else {
            $subject = $type . '\\Model';
            $subjectDir = APP_DIR . DIRECTORY_SEPARATOR . $type;
        }

        $fixturesFile = $subjectDir . "/Fixtures.json";

        if(!file_exists($fixturesFile)) {
            $output->writeln("Fixtures not found for $type");
            return Command::FAILURE;
        }
        
        if(!$subject || !class_exists($subject)) {
            $output->writeln("Model not found for $type");
            return Command::FAILURE;
        }

        $rawContent = file_get_contents($fixturesFile);

        $data = json_decode($rawContent, true);

        if(!is_array($data)) {
            $output->writeln("Error in fixtures for $type");
            return Command::FAILURE;
        }

        /** @var Connection */
        $connection = $subject::connection();
        $connection->truncateCollection($subject::Type);

        foreach($data as $attrs) {
            $subject::create($attrs);
        }

        $output->writeln("Fixtures seeded for $type");

        return Command::SUCCESS;
    }
}