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
     * @Route("/plan", name="plan")
     */
    public function planAction(Request $request)
    {
        if (($request->request->get('info') !== null) && (!empty($request->get('info')))) {

            /** @var LanguageService $languageService */
            $languageService = $this->get('app_bundle.service.language');
            $languageService->dataClassification($request->request->get('info'));
        }

        return $this->redirect('/ui/', 200);
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