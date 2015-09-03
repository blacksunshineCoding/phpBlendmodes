<?php

include 'Blendmodes.class.php';

$blendmodes = new Blendmodes();
$baseImage = imagecreatefrompng('base.png');
$topImage = imagecreatefrompng('top.png');
$mode = 'multiply';
$blendedImage = $blendmodes->blend($baseImage, $topImage, $mode);

header('Content-Type: image/png');
imagepng($blendedImage);
imagedestroy($baseImage);
imagedestroy($topImage);
imagedestroy($blendedImage);