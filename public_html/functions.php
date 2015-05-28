<?php

function getPlaceBad($dir = 'img') {

    $imgDir = glob($dir . '/*.*');
    $img = array_rand($imgDir);
    return $imgDir[$img];

}
