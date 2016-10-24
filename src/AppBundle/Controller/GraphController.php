<?php

namespace AppBundle\Controller;

use AppBundle\Service\DataService;
use AppBundle\Service\GraphService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class GraphController extends Controller
{
    /**
     * @Route("/create_node/{node}", name="Create Node")
     */
    public function createNodeAction(Request $request)
    {
        $nodeRequest = $request->get("node");
        $node = json_decode($nodeRequest, true);

        /** @var GraphService $graphService */
        $graphService = $this->get('app_bundle.service.graph');

        $graphService->createNode($node['type'], $node['labels']);

        return true;
    }

    /**
     * @Route("/create_relationship/{relationship}", name="Create Relationship")
     */
    public function createRelationship(Request $request)
    {
        $relationshipRequest = $request->get("relationship");
        $relationship = json_decode($relationshipRequest, true);

        /** @var GraphService $graphService */
        $graphService = $this->get('app_bundle.service.graph');

        $graphService->createNode($relationship['nodeSource'], $relationship['nodeDestination'], $relationship['relType']);

        return true;
    }

    /**
     * @Route("/get_events_from_date/{date}", name="Get events from date")
     */
    public function getEventsFromDateAction(Request $request)
    {
        $data = $request->get("date");

        /** @var DataService $dataService */
        $dataService = $this->get('app_bundle.service.data');
        $results = $dataService->getEventsFromDate($data);

        $response = new Response();
        $response->setContent(json_encode($results));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/count_events_from_date/{date}", defaults={"date"="03.09.2016"}, name="Count events from date")
     */
    public function countEventsFromDateAction(Request $request)
    {
        $dataRequest = $request->request->get("date");
        $data = json_decode($dataRequest, true);

        /** @var DataService $dataService */
        $dataService = $this->get('app_bundle.service.data');

        $results = $dataService->countEventsFromDate($data);
        $out=[];
        foreach($results as $recordObject) {
            $retDate = $recordObject->get('t.DATE');
            $key = implode(array_reverse(explode('.',$retDate)));
            $out[$key] = strtr($retDate, array('.'=>'/'));
        }
        ksort($out);
        return new JsonResponse(array_values($out));
    }
}
