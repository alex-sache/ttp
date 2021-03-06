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

        $query = "MATCH(t:EVENT_TIME)--(e)-[r*0..3]-(p) WHERE (t.DATE = " . '\'' . $date . '\'' . ") RETURN t,e,p,r;";

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

                            $newEntry =  array('id' => $value['id'], 'weight' => 20, 'source' => $value['startNodeIdentity'], 'target' => $value['endNodeIdentity'], 'label' => $value['type']);
                            if(!in_array($newEntry,$graphData['edges'])) {
                                $graphData['edges'][] = $newEntry;
                            }
                        }
                    } break;
                }
            }
        }

        $graphDataJSON = '{' . PHP_EOL . 'nodes: [' . PHP_EOL;

        $nodeArr = [];
        foreach($graphData['nodes'] as $node) {
            $nodeArr[] = ['data' => [
                'id' => $node,
                'name' => $this->getNodeName($node)
            ]];
            //$graphDataJSON .= '{data: {id:' . '\'' . json_encode($node) .'\'' . ', ' . 'name:' . '\'' . $this->getNodeName(json_encode($node)) . '\'' .'}},' . PHP_EOL;
        }
        $graphDataJSON .= '],' . PHP_EOL . 'edges: [' . PHP_EOL;

        $edgeArr = [];
        foreach($graphData['edges'] as $edge) {
            $edgeArr[] = ['data' => [
                'id' => $edge['id'],
                'source' => $edge['source'],
                'target' => $edge['target'],
                'label'  => !in_array($edge['label'], ['EVENT_HAS', 'EVENT_HAPPENS']) ? $edge['label'] : ''
            ]];
//            $graphDataJSON .= '{data: {id:' . '\'' . $edge['id'] . '\'' . ', weight:' . '\'' . $edge['weight'] . '\'' .
//                ', source:' . '\'' . $edge['source'] . '\'' . ', target:' . '\'' . $edge['target'] . '\'' . '}},' . PHP_EOL;
        }

        //$graphDataJSON .= ']' . PHP_EOL .'}';
        //$graphDataJSON = str_replace("},". PHP_EOL ."]", "}" . PHP_EOL . "]", $graphDataJSON);

        $final = [
            'nodes' => $nodeArr,
            'edges' => $edgeArr
        ];

        return $final;
    }


    /**
     * @param $date
     * @return String
     */
    public function countEventsFromDate($date = '')
    {
        $client = $this->buildClient();

        if ($date) {
            $query = "MATCH(t:EVENT_TIME)--(e) WHERE (t.DATE = '{date}') RETURN t.DATE as date, count(DISTINCT e) as nr;";
            $parameters = ['date' => $date];
            $result = $client->run($query, $parameters);

        } else {
            $query = "MATCH(t:EVENT_TIME)--(e) RETURN t.DATE, count(DISTINCT e);";
            $result = $client->run($query);
        }
        $records = $result->records();

        return $records;
    }

    /**
     * @param $id
     * @return string
     */
    private function getNodeName($id) {
        $client = $this->buildClient();

        $query = "MATCH (s) WHERE ID(s) = " . $id . " RETURN s.NAME";
        $result = $client->run($query);

        $responseObject = $result->getRecord();
        $response = $this->ObjetToArray($responseObject);

        $name = $response['values'][0];

        return $name;
    }

    /**
     * @param $adminBar
     * @return array
     */
    private function ObjetToArray($adminBar){

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