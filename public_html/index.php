<?php
    require '../vendor/autoload.php';
    require 'functions.php';

    $loader = new Twig_Loader_Filesystem('../templates');
    $twig = new Twig_Environment($loader, array('debug' => true));
    $app = new \Slim\Slim();

    //Add an application-wide condition to width/height parameters
    \Slim\Route::setDefaultConditions(array(
        'width'=>'[\d]*',
        'height'=>'[\d]*'
    ));

    // Homepage
    $app->get('/', function() use($app, $twig) {

        $people = array();

        $dir = new DirectoryIterator('img/');
        foreach ($dir as $fileinfo) {
          if ($fileinfo->isDir() && !$fileinfo->isDot()) {
            array_push($people, $fileinfo->getFilename());
          }
        }

        $currentURL = (isset($_SERVER['HTTPS']) ? "https" : "http") . '://' . $_SERVER['HTTP_HOST'] . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        $template = $twig->loadTemplate('views/home.html.twig');
        echo $template->render(array(
            'currenturl' => $currentURL,
            'people' => $people
        ));

    });

    $app->get('/:width/:height', function($width, $height) use($app) {
        if($width > 3000 || $height > 3000) {
            echo "Sorry but this size is not available. Max is 3000 x 3000.";
            die();
        }
        serve($width, $height, '');
    });

    $app->get('/:width', function($width) use($app) {
        $app->response()->redirect("/$width/$width", 303);
    });

    $app->get('/:width/:height/:person', function($width, $height, $person) use($app) {
        if($width > 3000 || $height > 3000) {
            echo "Sorry but this size is not available. Max is 3000 x 3000.";
            die();
        }
        serve($width, $height, $person);
    });

    // Attribution
    $app->get('/attribution', function() use($app, $twig) {
        $template = $twig->loadTemplate('views/attribution.html.twig');
        echo $template->render(array());
    });

    // Releases
    $app->get('/releases', function() use($app, $twig) {
        $template = $twig->loadTemplate('views/releases.html.twig');
        echo $template->render(array());
    });

    // Releases

    $app->get('/phpinfo', function() use($app, $twig) {
        $phpinfo = phpinfo();
        $template = $twig->loadTemplate('phpinfo.html.twig');
        echo $template->render(array('phpinfo' => $phpinfo));
    });

    // 404
    $app->notFound(function () use ($app, $twig) {
        $template = $twig->loadTemplate('views/404.html.twig');
        echo $template->render(array());
    });

$app->run();
