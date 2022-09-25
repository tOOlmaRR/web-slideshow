<?php
use toolmarr\WebSlideshow\DbWebSlideshow;

require_once('../vendor/autoload.php');
require_once('../scripts/dbMainConfig.php');

$alSlides = [];
if (isset($_POST['maxHeight']) && isset($_POST['chosenTags'])) {
    // gather inputs from the request
    $privateAccessGranted = $_GET['in'] ?? false;
    $maxHeight = $_POST['maxHeight'];
    $chosenTags = explode(",", $_POST['chosenTags']);
    $tagsToOmit =  isset($_POST['tagsToOmit']) ? explode(",", $_POST['tagsToOmit']) : array();

    // instantiate the slidehow and build the slide data
    $dbSlideshow = new DbWebSlideshow($maxHeight);
    $dbSlideshow->privateAcessGranted = $privateAccessGranted == 'true' ? true : false;
    $allSlides = $dbSlideshow->retrieveTagSlideshowData($configuration, $chosenTags, $tagsToOmit);
}

// prepare the response
header('Content-Type: application/json; charset=utf-8');
$jsonResponse = json_encode($allSlides);
echo $jsonResponse;
exit();