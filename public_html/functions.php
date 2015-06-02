<?php

function getBestImage($width, $height, $person)
{
    // print '<strong>Looking for image: width: '.$width.' and height: '.$height.' and person: ' . $person . '</strong><br /><br />';

    if ( is_null($person) ) {
        $dir = 'img/';
    } else {
        $dir = 'img/' . $person . '/';
    }

    $files = scandir($dir);

    $bestWDiff = PHP_INT_MAX;
    $bestHDiff = PHP_INT_MAX;

    foreach($files as $file) {
        if ( is_file($dir . $file) && $file !== '.DS_Store') {
            $info = getimagesize($dir . $file);
            $fileWidth = $info[0];
            $fileHeight = $info[1];
            // print 'file: ' . $file . ' | '. $fileWidth .' x '. $fileHeight . '<br /><br />';

            if ( abs($fileWidth - $width) < $bestWDiff ) {
                $bestWDiff = abs($fileWidth - $width);
                $bestForW = $file;
            }
            if ( abs($fileHeight - $height) < $bestHDiff ) {
                $bestHDiff = abs($fileHeight - $height);
                $bestForH = $file;
            }
        }
    }

    // Detect request orientation
    if ($width > $height) {

        // Choose landscape picture
        return $dir . $bestForW;

    } elseif ($width < $height) {

        // Portrait
        return $dir . $bestForH;

    } else {
        //Return for square
        return $dir . $bestForW;
    }
}

function serve($width, $height, $person)
{
    $app = \Slim\Slim::getInstance();

    $response = $app->response();
    $response['Content-Type'] = 'image/jpeg';

    $img = new abeautifulsite\SimpleImage( getBestImage($width, $height, $person) );

    $img->crop(0, 0, $width, $height)
        ->output();
}
