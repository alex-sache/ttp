<?php

namespace AppBundle\Service;

use GraphAware\Neo4j\Client\ClientBuilder;

class GraphService
{
    /**
     * @var string
     */
    protected $neo4jConnection;

    /**
     * @var string
     */
    protected $neo4jUri;

    /**
     * @param string $neo4jConnection
     */
    public function setNeo4jConnection($neo4jConnection)
    {
        $this->neo4jConnection = $neo4jConnection;
    }

    /**
     * @param string $neo4jUri
     */
    public function setNeo4jUri($neo4jUri)
    {
        $this->neo4jUri = $neo4jUri;
    }

    /**
     * @return \GraphAware\Neo4j\Client\Client
     */
    protected function buildClient()
    {
        $client = ClientBuilder::create()
            ->addConnection($this->neo4jConnection, $this->neo4jUri)
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

    public function searchIn($queryParams = [])
    {
        $client = $this->buildClient();

        $nodeSource = $queryParams['nodeSource'];
        $nodeSourceType = $nodeSource['type'];
        $nodeSourceLabelKey = $nodeSource['labelKey'];
        $nodeSourceLabelValue = $nodeSource['labelValue'];

        $nodeDestination = [];
        if (isset($queryParams['nodeDestination'])) {
            $nodeDestination = $queryParams['nodeDestination'];
            $nodeDestinationType = $nodeDestination['type'];
            $nodeDestinationLabelKey = $nodeDestination['labelKey'];
            $nodeDestinationLabelValue = $nodeDestination['labelValue'];
        }

        $lastNode = [];
        if (isset($queryParams['lastNode'])) {
            $lastNode = $queryParams['lastNode'];
            $lastNodeType = $lastNode['type'];
            $lastNodeLabelKey = $lastNode['labelKey'];
            $lastNodeLabelValue = $lastNode['labelValue'];
        }


        $firstRelType = $queryParams['firstRelType'];
        $lastRelType = [];
        if (isset($queryParams['lastRelType'])) {
            $lastRelType = $queryParams['lastRelType'];
        }

        $query = "MATCH (a:$nodeSourceType)";

        if (!empty($firstRelType) && empty($nodeDestination)) {
            $query .= "-[r:$firstRelType]->()";
        } elseif (!empty($firstRelType) && !empty($nodeDestination)) {
            $query .= "-[r:$firstRelType]->(b:$nodeDestinationType)";
        }


        if (!empty($lastRelType) && empty($lastNode)) {
            $query .= "-[r:$lastRelType]->()";
        } elseif (!empty($lastRelType) && !empty($lastNode)) {
            $query .= "-[r:$lastRelType]->(c:$lastNodeType)";
        }

        $query .= " WHERE a.$nodeSourceLabelKey = '$nodeSourceLabelValue'";

        if(!empty($nodeDestination)) {
            $query .= "AND b.$nodeDestinationLabelKey = '$nodeDestinationLabelValue'";
        }

        if(!empty($lastNode)) {
            $query .= "AND c.$lastNodeLabelKey = '$lastNodeLabelValue'";
        }

        $query .= " RETURN *";
        $query .= " LIMIT 5";

        $result = $client->run($query);

        return $result;
    }
}
