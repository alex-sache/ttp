<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Service\LanguageService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="home")
     */
    public function homeAction(Request $request)
    {
        return $this->redirect('/now', 301);
    }

    /**
     * @Route("/now", name="now")
     */
    public function uiAction(Request $request)
    {
        $days = array();
        return $this->render('ui.html.twig', array(
            'days' => $days,
            'baseref' => $this->getParameter('ttp_host')
        ));
    }

    /**
     * @Route("/plan", name="plan")
     */
    public function planAction(Request $request)
    {
        if (($request->request->get('info') !== null) && (!empty($request->get('info')))) {

            /** @var LanguageService $languageService */
            $languageService = $this->get('app_bundle.service.language');
            $languageService->dataClassification($request->request->get('info'));
        }

        return $this->redirect('/now', 301);
    }

    /**
     * @Route("/get-data", name="get-data")
     */
    public function getDataAction()
    {
        /** @var LanguageService $languageService */
        $languageService = $this->get('app_bundle.service.language');
        return new JsonResponse($languageService->getAutocompleteData());
    }
}