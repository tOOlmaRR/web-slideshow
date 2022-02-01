<?php
use toolmarr\WebSlideshow\DbWebSlideshow;

require_once('../vendor/autoload.php');
require_once('../scripts/dbMainConfig.php');

$alSlides = [];
if (isset($_POST['maxHeight']) && isset($_POST['chosenTags'])) {
    // gather inputs from the request
    $maxHeight = $_POST['maxHeight'];
    $chosenTags = explode(",", $_POST['chosenTags']);

    // instantiate the slidehow and build the slide data
    $dbSlideshow = new DbWebSlideshow($maxHeight);
    $allSlides = $dbSlideshow->retrieveSlideshowData($configuration, $chosenTags);
}

// prepare the response
header('Content-Type: application/json; charset=utf-8');
$jsonResponse = json_encode($allSlides);
echo $jsonResponse;
exit();