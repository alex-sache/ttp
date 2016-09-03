<?php

namespace AppBundle\Service;

use GraphAware\Neo4j\Client\ClientBuilder;

class GraphService
{
    /**
     * @return \GraphAware\Neo4j\Client\Client
     */
    protected function buildClient()
    {
        $client = ClientBuilder::create()
            ->addConnection('default', 'http://neo4j:parolaneo4j@46.101.106.91:7474')
            ->build();

        return $client;
    }

    /**
     * @param $type
     * @param array $labels
     * @return bool
     * @throws \GraphAware\Neo4j\Client\Exception\Neo4jException
     */
    public function createNode($type, $labels = [])
    {
        $client = $this->buildClient();

        $query = "CREATE (n:$type) SET n += {infos}";
        $parameters = ['infos' => $labels];
        $client->run($query, $parameters);

        return true;
    }

    /**
     * @param array $nodeSource
     * @param array $nodeDestination
     * @param $relType
     * @return bool
     * @throws \GraphAware\Neo4j\Client\Exception\Neo4jException
     */
    public function createRelationship($nodeSource, $nodeDestination, $relType)
    {
        $client = $this->buildClient();

        $nodeSourceType = $nodeSource['type'];
        $nodeSourceLabelKey = $nodeSource['labelKey'];
        $nodeSourceLabelValue = $nodeSource['labelValue'];

        $nodeDestinationType = $nodeDestination['type'];
        $nodeDestinationLabelKey = $nodeDestination['labelKey'];
        $nodeDestinationLabelValue = $nodeDestination['labelValue'];

        $query = "MATCH (a:$nodeSourceType),(b:$nodeDestinationType)
            WHERE a.$nodeSourceLabelKey = '$nodeSourceLabelValue'
            AND b.$nodeDestinationLabelKey = '$nodeDestinationLabelValue'
            CREATE (a)-[r:$relType]->(b)
            RETURN r
        ";

        $client->run($query);

        return true;
    }
}
