<?php
    require '../vendor/autoload.php';

    $loader = new Twig_Loader_Filesystem('../templates');
    $twig = new Twig_Environment($loader, array('debug' => true));

    $app = new \Slim\Slim();

    require 'functions.php';

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

        $currentURL = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $template = $twig->loadTemplate('homeTemplate.php');
        echo $template->render(array(
            'currenturl' => $currentURL,
            'people' => $people
        ));

    });

    $app->get('/:width', function($width) use($app) {
        //just redirect them to the width & height route
        $app->response()->redirect("/$width/$width", 303);
    });

    $app->get('/:width/:height', function($width, $height) use($app) {
        if($width > 1500 || $height > 1500) {
            echo "Woah now...do you really want to serve an image that size?";
            die();
        }

        $placeBad = getPlaceBad();

        $app = \Slim\Slim::getInstance();
        $response = $app->response();
        $response['Content-Type'] = 'image/jpeg';

        $img = new abeautifulsite\SimpleImage($placeBad);

        if ($width > $height) {

            $img->fit_to_height($height)
                ->crop(0, 0, $width, $height)
                ->output();

        } elseif ($width < $height) {

            $img->fit_to_height($height)
                ->crop(0, 0,$width, $height)
                ->output();

        } else {
            $img->resize($width, $height)
                ->crop(0, 0, $width, $height)
                ->output();
        }


    });

$app->run();
