<?php
use toolmarr\WebSlideshow\DbWebSlideshow;

require_once('../vendor/autoload.php');
require_once('../scripts/dbMainConfig.php');

$allStaticSlideShowSlides = [];
// gather inputs from the request
$privateAccessGranted = $_GET['in'] ?? false;
$staticSlideshowIDString = $_POST['staticSlideshowID'] ?? 0;
$staticSlideshowID = intval($staticSlideshowIDString);
$maxHeight = $_POST['maxHeight'] ?? 300;

// instantiate the slidehow, determine if private access is granted and get slideshow slides
$dbSlideshow = new DbWebSlideshow($maxHeight);
$dbSlideshow->privateAcessGranted = $privateAccessGranted == 'true' ? true : false;
$allStaticSlideShowSlides = $dbSlideshow->getStaticSlideshowSlides($configuration, $staticSlideshowID);

// prepare the response
header('Content-Type: application/json; charset=utf-8');
$jsonResponse = json_encode($allStaticSlideShowSlides);
echo $jsonResponse;
exit();
