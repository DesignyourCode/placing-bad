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

    $centreX = round($img->get_width() / 2);
    $centreY = round($img->get_height() / 2);

    $x1 = $centreX - $width / 2;
    $y1 = $centreY - $height / 2;

    $x2 = $centreX + $width / 2;
    $y2 = $centreY + $height / 2;

    $img->crop($x1, $y1, $x2, $y2);
    $img->output();
}
