<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Adrian Ispas
 * Date: 03.09.2016
 * Time: 23:34
 * To change this template use File | Settings | File Templates.
 */

namespace AppBundle\Service;

use GraphAware\Neo4j\Client\ClientBuilder;

class DataService {

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
     * @param $date
     * @return array
     */
    public function getEventsFromDate($date)
    {
        $events = array();
        $client = $this->buildClient();

        return $events;
    }

}