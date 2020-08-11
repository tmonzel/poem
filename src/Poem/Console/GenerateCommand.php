<?php

namespace Poem\Console;

use Poem\Actor\Accessor as ActorAccessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends Command 
{
    use ActorAccessor;
    
    protected static $defaultName = 'generate';

    protected function configure()
    {
        $this->setDescription('Generates the given actor.');
        $this->setHelp('This command allows you to generate an actor.');
        $this->addArgument('name', true, 'The actor name');
        $this->addArgument('type', true, 'The actor type');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $type = $input->getArgument('type');

        $moduleDir = APP_DIR . "/$name";

        if(!is_dir($moduleDir)) {
            mkdir($moduleDir);
        }

        $actorFile = $moduleDir . "/Actor.php";
        
        if(!file_exists($actorFile)) {
            file_put_contents($actorFile, $this->generateContent($input));
        } else {
            $output->writeln("Actor `$name` already exists");
            return Command::FAILURE;
        }
        
        $output->writeln('Generated actor `' . $name . '` with type `' . $type . '`');

        return Command::SUCCESS;
    }

    private function generateContent(InputInterface $input) 
    {
        $name = $input->getArgument('name');
        $type = $input->getArgument('type');

        return <<<TPL
<?php

namespace $name;

class Actor extends \Poem\Actor
{
    /**
     * $name actor type
     * 
     * @var string
     */
    const Type = '$type';
}

TPL;
    }
}