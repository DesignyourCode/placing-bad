<?php

use abeautifulsite\SimpleImage;
use Slim\Http\Request;

function getBestImage($width, $height, $person)
{

    $app = \Slim\Slim::getInstance();

    if ( is_null($person) || $person == 'all') {
        $dir = $_SERVER["DOCUMENT_ROOT"].'/img/';
    } else {
        $dir = $_SERVER["DOCUMENT_ROOT"].'/img/' . $person . '/';
    }

    if(!is_dir($dir)) handle404();

    if($person == 'all'){
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    } else {
        $files = scandir($dir);
    }

    $requestedAspect = $width/$height;
    $allowed_file_types = array("jpg", "png");
    $files_with_difference = array();

    foreach($files as $file) {
        if(in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), $allowed_file_types)){
            if (strpos($file, $dir) === false) $file = $dir . $file;
            if (is_file($file)) {
                $dimensions = getimagesize($file);
                $aspect = $dimensions[0] / $dimensions[1];
                $files_with_difference[] = array(
                    'file' => $file,
                    'aspect_difference' => abs($requestedAspect - $aspect)
                );
           }
        }
    }

    function sort_array($a, $b){
        if ($a['aspect_difference'] == $b['aspect_difference']) {
            return 0;
        }
        return ($a['aspect_difference'] < $b['aspect_difference']) ? -1 : 1;
    }
    usort($files_with_difference, 'sort_array');

    $possibilities = array();
    $random = $app->request()->params('random');
    $randomisation_range = (isset($random) ? 5 : 1);
    $randomised = 0;
    foreach($files_with_difference as $file){
        $possibilities[] = $file['file'];
        $randomised++;
        if($randomised == $randomisation_range) break;
    }

    $match = $possibilities[array_rand($possibilities)];

    if(!is_file($match)) handle404();

    return $match;

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

    $request = $app->request();
    $canRequestBeCached = canRequestBeCached($request, $person);

    $response = $app->response();
    $response['Content-Type'] = 'image/jpeg';

    $cacheKey = getCacheKey($width, $height, $person, $request);
    if ($canRequestBeCached && isFileCached($cacheKey)) {
        $img = new SimpleImage(getCacheFile($cacheKey));
    } else {
        $img = new SimpleImage(getBestImage($width, $height, $person));
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
    }

    if ($canRequestBeCached) {
        cacheImage($cacheKey, $img);
    }

    $img->output();
}

function canRequestBeCached(Request $request, $person)
{
    if (!$person || $person === 'all') {
        return false;
    }

    if ($request->params('random') !== null) {
        return false;
    }

    return true;
}

function getCacheKey($width, $height, $person, Request $request)
{
    return "$width-$height-$person-" . md5(serialize($request->params())) . ".jpg";
}

function isFileCached($cacheKey)
{
    return file_exists(getCacheFile($cacheKey));
}

function getCacheFile($cacheKey)
{
    return __DIR__ . '/cache/' . $cacheKey;
}

function cacheImage($cacheKey, SimpleImage $image)
{
    if (!is_dir('cache')) {
        mkdir('cache');
    }
    $image->save(getCacheFile($cacheKey));
}
