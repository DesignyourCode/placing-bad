<?php

require_once __DIR__ . '/../vendor/autoload.php';
require 'functions.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// Config
$app = new Silex\Application();

$app->register(new Silex\Provider\TwigServiceProvider(), [
    'twig.path' => __DIR__.'/../templates'
]);

$currentURL = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$app['twig']->addGlobal('currenturl', $currentURL);

$app['images'] = function () use ($app) {
    return new Images($app);
};

// Homepage
$app->get('/', function() use($app) {

    $people = array();

    $dir = new DirectoryIterator('img/');
    foreach ($dir as $fileinfo) {
      if ($fileinfo->isDir() && !$fileinfo->isDot()) {
        array_push($people, $fileinfo->getFilename());
      }
    }

    return $app['twig']->render('views/home.html.twig', [
        'people' => $people
    ]);

});

$app->get('/{width}/{height}', function($width, $height, Request $request) use($app) {
    if($width > 3000 || $height > 3000) {
        echo "Sorry but this size is not available. Max is 3000 x 3000.";
        die();
    }
    $app['images']->serve($width, $height, '', $request);
});

$app->get('/{width}', function($width) use($app) {
    $app->redirect("/$width/$width", 303);
})
->assert('width', '\d+');

$app->get('/{width}/{height}/{person}', function($width, $height, $person, Request $request) use($app) {
    if($width > 3000 || $height > 3000) {
        echo "Sorry but this size is not available. Max is 3000 x 3000.";
        die();
    }
    $app['images']->serve($width, $height, $person, $request);
})
->assert('width', '[\d]*')
->assert('height', '[\d]*');

// Attribution
$app->get('/attribution', function() use($app) {
    return $app['twig']->render('views/attribution.html.twig');
});

// Releases
$app->get('/releases', function() use($app) {
    return $app['twig']->render('views/releases.html.twig');
});

// 404
$app->error(function () use ($app) {
    return $app['twig']->render('views/404.html.twig');
});

$app->run();
