<?php

function getBestImage($width, $height, $person)
{
    if ( is_null($person) ) {
        $dir = 'img/';
    } else {
        $dir = 'img/' . $person . '/';
    }

    $files = scandir($dir);
    $best = PHP_INT_MAX;
    $match = $files[2];
    $requestedAspect = $width/$height;

    $possibles = array();
    $maximumPossibilities = 10; // Change this to increase randomisation of images

    foreach($files as $file) {
        if (is_file($dir . $file) && $file !== '.DS_Store') {
            $info = getimagesize($dir . $file);
            $aspect = $info[0]/$info[1];
            $diff = $requestedAspect - $aspect;
            if(abs($diff)<$best){
                if(count($possibles) > $maximumPossibilities){
                    array_shift($possibles);
                }
                $possibles[] = $file;
                $best = abs($diff);
            }
        }
    }

    $match = $possibles[array_rand($possibles)];

    return $dir . $match;
}

function applyFilters($img)
{

    $app = \Slim\Slim::getInstance();

    // Default filter value if no parameter value given
    $defaults = array(
        'desaturate' => 100,
        'blur' => 10,
        'brightness' => 50,
        'color' => 'FF0000',
        'pixelate' => 8
    );

    $desaturate = $app->request()->params('desaturate');
    if(isset($desaturate)) {
        if(empty($desaturate)){
            $img->desaturate($defaults['desaturate']);
        } else {
            $img->desaturate($desaturate);
        }
    }

    $blur = $app->request()->params('blur');
    if(isset($blur)) {
        if(empty($blur)){
            $img->blur('gaussian', $defaults['blur']);
        } else {
            $img->blur('gaussian', $blur);
        }
    }

    $brightness = $app->request()->params('brightness');
    if(isset($brightness)) {
        if(empty($brightness)){
            $img->brightness($defaults['brightness']);
        } else {
            $img->brightness($brightness);
        }
    }

    $color = $app->request()->params('color');
    if(isset($color)) {
        $img->desaturate();
        if(empty($color)){
            $img->colorize($defaults['color'], null);
        } else {
            $img->colorize('#'.$color, null);
        }
    }

    $pixelate = $app->request()->params('pixelate');
    if(isset($pixelate)) {
        if(empty($pixelate)){
            $img->pixelate($defaults['pixelate']);
        } else {
            $img->pixelate($pixelate);
        }
    }

    $sepia = $app->request()->params('sepia');
    if(isset($sepia)) {
        $img->sepia();
    }
    
    return $img;
}

function serve($width, $height, $person)
{
    $app = \Slim\Slim::getInstance();

    $response = $app->response();
    $response['Content-Type'] = 'image/jpeg';

    $img = new abeautifulsite\SimpleImage( getBestImage($width, $height, $person) );

    if($img->get_width()/$img->get_height() >= $width/$height){
        $img->fit_to_height($height);
        $centre = round($img->get_width() / 2);
        $x1 = $centre - ($width / 2);
        $x2 = $centre + ($width / 2);
        $img->crop($x1, 0, $x2, $height);
    } else {
        $img->fit_to_width($width);
        $centre = round($img->get_height() / 2);
        $y1 = $centre - ($height / 2);
        $y2 = $centre + ($height / 2);
        $img->crop(0, $y1, $width, $y2);
    }

    $img = applyFilters($img);

    $img->output();
}
