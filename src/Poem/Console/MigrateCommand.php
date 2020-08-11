<?php

namespace Poem\Console;

use Poem\Actor\Accessor as ActorAccessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends Command 
{
    use ActorAccessor;
    
    protected static $defaultName = 'migrate';

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Migrates the given model.')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command allows you to migrate a model.')

            ->addArgument('type', true, 'The collection type')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getArgument('type');
        $actor = static::Actor()->access($type);
        $actor->migrate();

        $output->writeln('Migrated ' . $actor->getType() . ' schema');

        return Command::SUCCESS;
    }
}