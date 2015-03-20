<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

$app['weekend'] = function () {
    return new \Ternel\Weekend();
};

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));


$app->get('/', function () use ($app) {
    return $app['twig']->render('index.html.twig', array(
        'text'    => $app['weekend']->getText(),
        'subtext' => $app['weekend']->getSubText(),
    ));
});

$app->get('/api', function () use ($app) {
    return $app->json([
        'text'    => $app['weekend']->getText(),
        'subtext' => $app['weekend']->getSubText(),
    ]);
});
$app->run();
