<?php

namespace App\Controller;

use App\Weekend;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    public function index(Weekend $weekend)
    {
        if(!preg_match('/AppleWebKit|Chrome|Edge|Gecko|Opera|Trident/i', $_SERVER['HTTP_USER_AGENT'])) {
            // We didn't detect a browser that has a rendering engine,
            // so let's return the JSON response instead.
            return $this->api($weekend);
        }

        $response = $this->render('index.html.twig', [
            'text'    => $weekend->getText(),
            'subtext' => $weekend->getSubText(),
        ]);

        $response->setPublic();
        $response->setSharedMaxAge(60);

        return $response;
    }

    public function api(Weekend $weekend)
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
