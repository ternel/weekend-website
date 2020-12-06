<?php

namespace App\Controller;

use App\Weekend;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function index(Request $request, Weekend $weekend)
    {
        if(!preg_match('/AppleWebKit|Chrome|Edge|Gecko|Opera|Trident/i', $request->headers->get('User-Agent'))) {
            // We didn't detect a browser that has a rendering engine,
            // so let's return the JSON response instead.
            return $this->api($weekend);
        }

        $response = $this->render('index.html.twig', [
            'text'    => $weekend->getRichText(),
            'subtext' => $weekend->getRichSubText(),
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
