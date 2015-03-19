<?php

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
