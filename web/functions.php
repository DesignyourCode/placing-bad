<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use abeautifulsite\SimpleImage;

class Images
{
    private $app;

    public function __construct(Application $a)
    {
        $this->app = $a;
    }

    public function serve($width, $height, $person, Request $request)
    {
        $canRequestBeCached = $this->canRequestBeCached($request, $person);

        $cacheKey = $this->getCacheKey($width, $height, $person, $request);
        if ($canRequestBeCached && $this->isFileCached($cacheKey)) {
            $img = new SimpleImage($this->getCacheFile($cacheKey));
        } else {
            $img = new SimpleImage($this->getBestImage($width, $height, $person, $request));
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

            $img = $this->applyFilters($img, $request);
        }

        if ($canRequestBeCached) {
            $this->cacheImage($cacheKey, $img);
        }

        $this->app->stream($img->output(), 200, ['Content-Type' => 'image/jpeg']);
    }

    public function getBestImage($width, $height, $person, Request $request)
    {
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
        $random = $request->query->get('random');
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

    public function applyFilters($img, Request $request)
    {
        // Default filter value if no parameter value given
        $defaults = array(
            'desaturate' => 100,
            'blur' => 10,
            'brightness' => 50,
            'color' => 'FF0000',
            'pixelate' => 8
        );

        $desaturate = $request->query->get('desaturate');
        if(isset($desaturate)) {
            if(empty($desaturate)){
                $img->desaturate($defaults['desaturate']);
            } else {
                $img->desaturate($desaturate);
            }
        }

        $blur = $request->query->get('blur');
        if(isset($blur)) {
            if(empty($blur)){
                $img->blur('gaussian', $defaults['blur']);
            } else {
                $img->blur('gaussian', $blur);
            }
        }

        $brightness = $request->query->get('brightness');
        if(isset($brightness)) {
            if(empty($brightness)){
                $img->brightness($defaults['brightness']);
            } else {
                $img->brightness($brightness);
            }
        }

        $color = $request->query->get('color');
        if(isset($color)) {
            $img->desaturate();
            if(empty($color)){
                $img->colorize($defaults['color'], null);
            } else {
                $img->colorize('#'.$color, null);
            }
        }

        $pixelate = $request->query->get('pixelate');
        if(isset($pixelate)) {
            if(empty($pixelate)){
                $img->pixelate($defaults['pixelate']);
            } else {
                $img->pixelate($pixelate);
            }
        }

        $sepia = $request->query->get('sepia');
        if(isset($sepia)) {
            $img->sepia();
        }

        return $img;
    }

    public function canRequestBeCached(Request $request, $person)
    {
        if (!$person || $person === 'all') {
            return false;
        }

        if ($request->query->get('random') !== null) {
            return false;
        }

        return true;
    }

    public function getCacheKey($width, $height, $person, Request $request)
    {
        return "$width-$height-$person-" . md5(serialize($request->query->all())) . ".jpg";
    }

    public function isFileCached($cacheKey)
    {
        return file_exists($this->getCacheFile($cacheKey));
    }

    public function getCacheFile($cacheKey)
    {
        return __DIR__ . '/cache/' . $cacheKey;
    }

    public function cacheImage($cacheKey, SimpleImage $image)
    {
        if (!is_dir('cache')) {
            mkdir('cache');
        }
        $image->save($this->getCacheFile($cacheKey));
    }
}
