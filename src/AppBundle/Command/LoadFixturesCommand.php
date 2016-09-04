<?php

namespace AppBundle\Command;

use AppBundle\Service\GraphService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use GraphAware\Neo4j\Client\ClientBuilder;

class LoadFixturesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('load:fixtures')
            ->setDescription('Load fixtures');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var GraphService $graphService */
        $graphService = $this->getContainer()->get('app_bundle.service.graph');

        $nodes = $this->getNodes();
        foreach ($nodes as $key => $node) {
            $graphService->createNode($node['type'], $node['labels']);

        }

        $relations = $this->getRelations();
        foreach ($relations as $relation) {
            $graphService->createRelationship(
                $relation['nodeSource'],
                $relation['nodeDestination'],
                $relation['relType']
            );
        }

        return true;
    }

    protected function getNodes()
    {
        $nodes[] = ['type' => 'person', 'labels' => ['NAME' => 'Alex', 'age' => 35]];
        $nodes[] = ['type' => 'person', 'labels' => ['NAME' => 'Adi', 'age' => 21]];
        $nodes[] = ['type' => 'person', 'labels' => ['NAME' => 'Marty', 'age' => 19]];

        $nodes[] = ['type' => 'activity', 'labels' => ['NAME' => 'stuff']];
        $nodes[] = ['type' => 'place', 'labels' => ['NAME' => 'Home']];

        $nodes[] = ['type' => 'EVENT', 'labels' => ['NAME' => 'I do stuff with Adi saturday', 'WEIGHT' => 20, 'UNI_EVENT'=> 'ev1']];
        $nodes[] = ['type' => 'EVENT', 'labels' => ['NAME' => 'I do stuff sunday', 'WEIGHT' => 20, 'UNI_EVENT'=> 'ev2']];
        $nodes[] = ['type' => 'EVENT', 'labels' => ['NAME' => 'Marty drives home tomorow', 'WEIGHT' => 20, 'UNI_EVENT'=> 'ev3']];

        $nodes[] = ['type' => 'EVENT_TIME', 'labels' => ['NAME' => 'Day', 'DATE' => '03.09.2016']];
        $nodes[] = ['type' => 'EVENT_TIME', 'labels' => ['NAME' => 'Day', 'DATE' => '04.09.2016']];
        $nodes[] = ['type' => 'EVENT_TIME', 'labels' => ['NAME' => 'Day', 'DATE' => '05.09.2016']];

        return $nodes;
    }

    protected function getRelations()
    {
        /*verify
        with MATCH(t:EVENT_TIME)--(e)-[*0..3]-(p) RETURN t,e,p;
        MATCH(t:EVENT_TIME)--(e) RETURN t.DATE, count(DISTINCT e);

        */
        /* ev1*/
        $relations[] = [
            'nodeSource' => ['type' => 'EVENT', 'labelKey' => 'UNI_EVENT', 'labelValue' => 'ev1'],
            'nodeDestination' => ['type' => 'person', 'labelKey' => 'NAME', 'labelValue' => 'Alex'],
            'relType' => 'EVENT_HAS'
        ];

        $relations[] = [
            'nodeSource' => ['type' => 'person', 'labelKey' => 'NAME', 'labelValue' => 'Alex'],
            'nodeDestination' => ['type' => 'activity', 'labelKey' => 'NAME', 'labelValue' => 'stuff'],
            'relType' => 'do'
        ];

        $relations[] = [
            'nodeSource' => ['type' => 'person', 'labelKey' => 'NAME', 'labelValue' => 'Adi'],
            'nodeDestination' => ['type' => 'activity', 'labelKey' => 'NAME', 'labelValue' => 'stuff'],
            'relType' => 'do'
        ];

        $relations[] = [
            'nodeSource' => ['type' => 'EVENT', 'labelKey' => 'UNI_EVENT', 'labelValue' => 'ev1'],
            'nodeDestination' => ['type' => 'EVENT_TIME', 'labelKey' => 'DATE', 'labelValue' => '03.09.2016'],
            'relType' => 'EVENT_HAPPENS'
        ];

        /* ev2*/
        $relations[] = [
            'nodeSource' => ['type' => 'EVENT', 'labelKey' => 'UNI_EVENT', 'labelValue' => 'ev2'],
            'nodeDestination' => ['type' => 'person', 'labelKey' => 'NAME', 'labelValue' => 'Alex'],
            'relType' => 'EVENT_HAS'
        ];

        $relations[] = [
            'nodeSource' => ['type' => 'person', 'labelKey' => 'NAME', 'labelValue' => 'Alex'],
            'nodeDestination' => ['type' => 'activity', 'labelKey' => 'NAME', 'labelValue' => 'stuff'],
            'relType' => 'do'
        ];

        $relations[] = [
            'nodeSource' => ['type' => 'EVENT', 'labelKey' => 'UNI_EVENT', 'labelValue' => 'ev2'],
            'nodeDestination' => ['type' => 'EVENT_TIME', 'labelKey' => 'DATE', 'labelValue' => '04.09.2016'],
            'relType' => 'EVENT_HAPPENS'
        ];

        /* ev3*/
        $relations[] = [
            'nodeSource' => ['type' => 'EVENT', 'labelKey' => 'UNI_EVENT', 'labelValue' => 'ev3'],
            'nodeDestination' => ['type' => 'person', 'labelKey' => 'NAME', 'labelValue' => 'Marty'],
            'relType' => 'EVENT_HAS'
        ];

        $relations[] = [
            'nodeSource' => ['type' => 'person', 'labelKey' => 'NAME', 'labelValue' => 'Marty'],
            'nodeDestination' => ['type' => 'place', 'labelKey' => 'NAME', 'labelValue' => 'Home'],
            'relType' => 'drive'
        ];

        $relations[] = [
            'nodeSource' => ['type' => 'EVENT', 'labelKey' => 'UNI_EVENT', 'labelValue' => 'ev3'],
            'nodeDestination' => ['type' => 'EVENT_TIME', 'labelKey' => 'DATE', 'labelValue' => '05.09.2016'],
            'relType' => 'EVENT_HAPPENS'
        ];

        return $relations;
    }
}
