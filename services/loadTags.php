<?php
use toolmarr\WebSlideshow\DbWebSlideshow;

require_once('../vendor/autoload.php');
require_once('../scripts/dbMainConfig.php');

// gather inputs from the request
$privateAccessGranted = $_GET['in'] ?? false;

// instantiate the slidehow, determine if private access is granted and get all available tags
$dbSlideshow = new DbWebSlideshow(0);
$dbSlideshow->privateAcessGranted = $privateAccessGranted == 'true' ? true : false;
$allTags = $dbSlideshow->getAvailableTags($configuration);

// prepare the response
header('Content-Type: application/json; charset=utf-8');
$jsonResponse = json_encode($allTags);
echo $jsonResponse;
exit();
