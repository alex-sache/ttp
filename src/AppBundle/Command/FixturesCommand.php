<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use GraphAware\Neo4j\Client\ClientBuilder;

class FixturesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('test:fixtures')
            ->setDescription('Load fixtures');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        echo 'deprecated';
        return true;
        $this->runLocal($client);
    }

    protected function runLocal($client)
    {
        $query = $this->getRunQuery();
        $params = $this->getArrayParameters();
        foreach ($query as $key => $value) {
            if (array_key_exists($key, $params)) {
                foreach ($params[$key] as $param) {
                    //print_r($param);
                    $client->run($value, $param);
                }
            } else {
                print_r($value);
                $client->run($value);
            }
        }
        return true;
    }

    protected function getRunQuery()
    {
        $query = [
            1 => "CREATE (n:TestI) SET n += {labels}",
            2 => "CREATE (n:TestStuff) SET n += {labels}",
            3 => "MATCH (a:TestI),(b:TestStuff)
                  WHERE a.name = 'Alex' AND b.work = 'Hackathon'
                  CREATE (a)-[r:DO {name : a.TestI + '->' + b.TestStuff}]->(b)",
            4 => "CREATE (n:TestEvent) SET n += {labels}",
            5 => "MATCH (a:TestEvent),(b:TestData)
                  WHERE a.TestEvent = 'I do stuff saturday' AND b.TestData = '03.09.2016'
                  CREATE (a)-[r:MOMENT {name : a.TestEvent + '->' + b.TestEvent}]->(b)"
            ];

        return $query;
    }

    protected function getArrayParameters()
    {
        $params[1] = [
            1 => ['labels' => ['name' => 'Alex', 'age' => 35]],
            2 => ['labels' => ['name' => 'Adi', 'age' => 21]],
        ];

        $params[2] = [
            1 => ['labels' => ['work' => 'Hackathon', 'distractie' => 'Stand Up']],
        ];

        $params[4] = [
            1 => ['labels' => ['TESTNAMEVENT' => 'I do stuff saturday', 'WEIGHT' => 20]],
        ];

        return $params;
    }
}
