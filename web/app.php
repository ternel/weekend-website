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
));

// Weekend, where the magic happens.
$app['weekend'] = function () {
    return new \Ternel\Weekend();
};


// Website
$app->get('/', function () use ($app) {
    return new Response($app['twig']->render('index.html.twig', [
            'text'    => $app['weekend']->getText(),
            'subtext' => $app['weekend']->getSubText(),
        ]),
        200,
        ['Cache-Control' => 's-maxage=60']
    );
});

// Api
$app->get('/api', function () use ($app) {
    return new JsonResponse([
            'text'    => $app['weekend']->getText(),
            'subtext' => $app['weekend']->getSubText(),
        ],
        200,
        ['Cache-Control' => 'maxage=60']
    );
});

$app['http_cache']->run();
