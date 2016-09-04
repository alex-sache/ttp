<?php

namespace AppBundle\Controller;

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

        /*$response = new Response();
        $response->setContent($results);
        $response->headers->set('Content-Type', 'application/json');

        return $response;*/

        return $results;

        $response = new JsonResponse();
        $response->setData($results);

        return $response;
    }
}
