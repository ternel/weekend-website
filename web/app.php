<?php

require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

$app = new Silex\Application();
//$app['debug'] = true;

// Twig
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));

// Cache
$app->register(new Silex\Provider\HttpCacheServiceProvider(), array(
    'http_cache.cache_dir' => __DIR__.'/../cache/',
    'http_cache.esi'       => null,
));

// Weekend, where the magic happens.
$app['weekend'] = function () {
    return new \Ternel\Weekend();
};


// Website
$app->get('/', function () use ($app) {
    $response = new Response($app['twig']->render('index.html.twig', [
            'text'    => $app['weekend']->getText(),
            'subtext' => $app['weekend']->getSubText(),
        ]),
        200
    );

    $response->setPublic();
    $response->setSharedMaxAge(60);

    return $response;
});

// Api
$app->get('/api', function () use ($app) {
    $response = new JsonResponse([
            'text'       => $app['weekend']->getText(),
            'subtext'    => $app['weekend']->getSubText(),
            'is_weekend' => $app['weekend']->isWeekend(),
        ],
        200
    );

    $response->setPublic();
    $response->setSharedMaxAge(60);

    return $response;
});

$app['http_cache']->run();
