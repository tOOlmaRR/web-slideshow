<?php
use toolmarr\WebSlideshow\DbWebSlideshow;

require_once('../vendor/autoload.php');
require_once('../scripts/dbMainConfig.php');

$allStaticSlideShowSlides = [];
// gather inputs from the request
$privateAccessGranted = $_GET['in'] ?? false;
$staticSlideshowID = $_GET['ssID'] ?? false;

// instantiate the slidehow, determine if private access is granted and get slideshow slides
$dbSlideshow = new DbWebSlideshow(0);
$dbSlideshow->privateAcessGranted = $privateAccessGranted == 'true' ? true : false;
$allStaticSlideShowSlides = $dbSlideshow->getStaticSlideshowSlides($configuration);

// prepare the response
header('Content-Type: application/json; charset=utf-8');
$jsonResponse = json_encode($allStaticSlideShowSlides);
echo $jsonResponse;
exit();