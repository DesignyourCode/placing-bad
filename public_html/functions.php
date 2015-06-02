<?php

function getBestImage($width, $height) {
    print 'Looking for image: width:'.$width.' and height:'.$height.'<br /><br />';

    $dir = 'img/';
    $files = scandir($dir);

    foreach($files as $file) {
        if ($file != '.' && $file != '..') {
            $info = getimagesize($dir . $file);
            $fileWidth = $info[0];
            $fileHeight = $info[1];
            print 'file: ' . $file . ' | '. $fileWidth .' x '. $fileHeight . '<br /><br />';
        }
    }

}

function getPlaceBad($person)
{
    if ( is_null($person) ) {
        $imgDir = glob('img/*.*');
    } else {
        $imgDir = glob('img/' . $person . '/*.*');
    }

    $img = array_rand($imgDir);
    return $imgDir[$img];

}

function serve($width, $height, $placeBad)
{
    $app = \Slim\Slim::getInstance();

    // $response = $app->response();
    // $response['Content-Type'] = 'image/jpeg';

    getBestImage($width, $height);

    // $img = new abeautifulsite\SimpleImage($placeBad);

    // if ($width > $height) {

    //     $img->fit_to_width($width)
    //         ->crop(0, 0, $width, $height)
    //         ->output();

    // } elseif ($width < $height) {

    //     $img->fit_to_height($height)
    //         ->crop(0, 0,$width, $height)
    //         ->output();

    // } else {
    //     $img->best_fit($width, $height)
    //         ->crop(0, 0, $width, $height)
    //         ->output();
    // }
}
