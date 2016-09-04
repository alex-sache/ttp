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

class DataService extends GraphService {

    /**
     * @param $date
     * @return String
     */
    public function getEventsFromDate($date)
    {
        $client = $this->buildClient();

        $query = "MATCH(t:EVENT_TIME)--(e)-[r*0..3]-(p) WHERE (t.DATE = " . $date .") RETURN t,e,p,r;";

        $result = $client->run($query);
        $records = $result->records();

        $graphData = array();
        $graphData['nodes'] = array();
        $graphData['edges'] = array();
        foreach($records as $recordObject) {

            $record = $this->ObjetToArray($recordObject);

            foreach($record['values'] as $key => $values) {

                switch($key) {
                    case '0' : {error_log("0");} break;
                    case '1' : {error_log("1");} break;
                    case '2' : {error_log("2");} break;
                    case '3' : {

                        foreach($values as $valueObject) {
                            $value = $this->ObjetToArray($valueObject);

                            if(!in_array($value['startNodeIdentity'],$graphData['nodes'])) {
                                $graphData['nodes'][] = $value['startNodeIdentity'];
                            }

                            if(!in_array($value['endNodeIdentity'],$graphData['nodes'])) {
                                $graphData['nodes'][] = $value['endNodeIdentity'];
                            }

                            $newEntry =  array('id' => $value['id'], 'weight' => 20, 'source' => $value['startNodeIdentity'], 'target' => $value['endNodeIdentity']);
                            if(!in_array($newEntry,$graphData['edges'])) {
                                $graphData['edges'][] = $newEntry;
                            }
                        }
                    } break;
                }
            }
        }

        $graphDataJSON = '{ ' . PHP_EOL . 'nodes: [' . PHP_EOL;
        foreach($graphData['nodes'] as $node) {
            $graphDataJSON .= '{data: {id:' . '\'' . json_encode($node) .'\'' . '}},' . PHP_EOL;
        }
        $graphDataJSON .= '],' . PHP_EOL . 'edges: [' . PHP_EOL;

        foreach($graphData['edges'] as $edge) {
            $graphDataJSON .= '{data: {id:' . '\'' . $edge['id'] . '\'' . ', weight:' . '\'' . $edge['weight'] . '\'' .
                ', source:' . '\'' . $edge['source'] . '\'' . ', target:' . '\'' . $edge['target'] . '\'' . '}},' . PHP_EOL;
        }
        $graphDataJSON .= ']' . PHP_EOL .'}';

        return $graphDataJSON;
    }

    function ObjetToArray($adminBar){

        $reflector = new \ReflectionObject($adminBar);
        $nodes = $reflector->getProperties();
        $out=[];

        foreach ($nodes as  $node) {
            $nod=$reflector->getProperty($node->getName());
            $nod->setAccessible(true);
            $out[$node->getName()]=$nod->getValue($adminBar);
        }

        return $out;
    }
}