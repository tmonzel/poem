<?php

namespace Poem\Console;

use Poem\Data\Connection;
use Poem\Actor\Accessor as ActorAccessor;
use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SeedCommand extends Command 
{
    use ActorAccessor;
    
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
        $subjectDir = null;
        $actor = static::Actor()->access($type);

        if(!$actor) {
            $output->writeln("Actor not found for $type");
            return Command::FAILURE;
        }
        
        $actorReflection = new ReflectionClass($actor);
        $subjectDir = dirname($actorReflection->getFilename());

        /*if(strpos($type, '.') !== false) {
            $subjectDir = APP_DIR . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $type);
        } else {
            $subjectDir = APP_DIR . DIRECTORY_SEPARATOR . $type;
        }*/

        $fixturesFile = $subjectDir . "/Fixtures.json";

        if(!file_exists($fixturesFile)) {
            $output->writeln("Fixtures not found for $type");
            return Command::FAILURE;
        }

        $rawContent = file_get_contents($fixturesFile);

        $data = json_decode($rawContent, true);

        if(!is_array($data)) {
            $output->writeln("Error in fixtures for $type");
            return Command::FAILURE;
        }

        /** @var Model $model */
        $model = $actor->accessModel();
        $model->truncate();

        foreach($data as $attrs) {
            $model->create($attrs);
        }

        $output->writeln("Fixtures seeded for $type");

        return Command::SUCCESS;
    }
}