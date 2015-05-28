<?php

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
    $response = $app->response();
    $response['Content-Type'] = 'image/jpeg';

    $img = new abeautifulsite\SimpleImage($placeBad);

    if ($width > $height) {

        $img->fit_to_width($width)
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
}
