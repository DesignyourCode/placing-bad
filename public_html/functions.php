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

    foreach($files as $file) {
        if ( is_file($dir . $file) && $file !== '.DS_Store') {
            $info = getimagesize($dir . $file);
            $aspect = $info[0]/$info[1];
            $diff = $requestedAspect - $aspect;
            if(abs($diff)<$best){
                $best = abs($diff);
                $match = $file;
            }
        }
    }

    return $dir . $match;
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

    $img->output();
}
