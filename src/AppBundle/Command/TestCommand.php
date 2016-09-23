<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use GraphAware\Neo4j\Client\ClientBuilder;

class TestCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('test:neoapi')
            ->setDescription('Test API');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

       //die();

        return true;
    }
}
