<?php

namespace AppBundle\Controller;

use AppBundle\Service\GraphService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class GraphController extends Controller
{
    /**
     * @Route("/create_node", name="Create Node")
     */
    public function createNodeAction($type, $label)
    {
        /** @var GraphService $graphService */
        $graphService = $this->get('app_bundle.service.graph');

        $node = $graphService->createNode($type, $label);

        return $node;
    }

    /**
     * @Route("/get_events_from_date", name="Get events from date")
     */
    public function getEventsFromDateAction()
    {
        /** @var DataService $dataService */
        $dataService = $this->get('app_bundle.service.data');
    }
}