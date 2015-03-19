<?php
    require '../vendor/autoload.php';

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
        resizeAndServe($placeBad, $width, $height);
    });

    function resizeAndServe($imagePath, $newWidth, $newHeight) {
        //Get original image and dimensions
        $sourceImage = imagecreatefromjpeg($imagePath);
        $sourceX = imagesx($sourceImage);
        $sourceY = imagesy($sourceImage);

        $destImage = imagecreatetruecolor($newWidth, $newHeight);

        //imagecopyresampled will cut a rectangle out of the source image
        imagecopyresampled($destImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $sourceX, $sourceY);

        //send the image header
        $app = \Slim\Slim::getInstance();
        $response = $app->response();
        $response['Content-Type'] = 'image/jpeg';

        //send out the file contents to the browser
        imagejpeg($destImage);
    }

    function getPlaceBad($dir = 'img') {
        $imgDir = glob($dir . '/*.*');
        $img = array_rand($imgDir);
        return $imgDir[$img];
    };

$app->run();
