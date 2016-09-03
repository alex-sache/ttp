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
}
