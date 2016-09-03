<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use GraphAware\Neo4j\Client\ClientBuilder;


class Neo4jControllerTest extends WebTestCase
{
    public function testIndex()
    {

        $client = ClientBuilder::create()
            ->addConnection('default', 'http://neo4j:parolaneo4j@46.101.106.91:7474');

        $client->run("CREATE (n:TestPersoana) SET n += {infos}", ['atribute' => ['name' => 'Alex', 'age' => 35]]);

    }
}
