<?php

namespace App\Controller;

use App\Weekend;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, Weekend $weekend): Response
    {
        /**
         * Quickfix - @todo: avoid user-agent, use the accept-encoding header
         * if(!preg_match('/AppleWebKit|Chrome|Edge|Gecko|Opera|Trident/i', $request->headers->get('User-Agent'))) {
         * // We didn't detect a browser that has a rendering engine,
         * // so let's return the JSON response instead.
         * return $this->api($weekend);
         * }.
         */
        $response = $this->render('index.html.twig', [
            'text' => $weekend->getRichText(),
            'subtext' => $weekend->getRichSubText(),
        ]);

        $response->setPublic();
        $response->setSharedMaxAge(60);

        return $response;
    }

    #[Route('/api', name: 'api')]
    public function api(Weekend $weekend): JsonResponse
    {
        $response = new JsonResponse([
            'text' => $weekend->getText(),
            'subtext' => $weekend->getSubText(),
            'is_weekend' => $weekend->isWeekend(),
        ]);

        $response->setPublic();
        $response->setSharedMaxAge(60);

        return $response;
    }
}
