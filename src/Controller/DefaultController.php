<?php

namespace App\Controller;

use App\Weekend;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    public function indexAction(Weekend $weekend)
    {
        $response = $this->render('index.html.twig', [
            'text'    => $weekend->getText(),
            'subtext' => $weekend->getSubText(),
        ]);

        $response->setPublic();
        $response->setSharedMaxAge(60);

        return $response;
    }

    public function apiAction(Weekend $weekend)
    {
        $response = new JsonResponse([
            'text'       => $weekend->getText(),
            'subtext'    => $weekend->getSubText(),
            'is_weekend' => $weekend->isWeekend(),
        ]);

        $response->setPublic();
        $response->setSharedMaxAge(60);

        return $response;
    }
}
