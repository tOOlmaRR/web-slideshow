<?php

$allSlideshows = Array();

$allSlideshows["Honeymoon"] = [
    "name" => "Honeymoon",
    "public" => false,
    "physicalPath" => "Honeymoon\\",
];

$allSlideshows["Wedding"] = [
    "name" => "Wedding",
    "public" => true,
    "physicalPath" => "Wedding\\",
];



// determine chosen slideshow - use the first valid available slidehow by default
$currentHourAndMinutes = date('Gi');
if (isset($_GET) && isset($_GET["in"]) && ($_GET["in"] >= $currentHourAndMinutes - 1) && ($_GET["in"] <= $currentHourAndMinutes + 1)) {
    foreach ($allSlideshows as $slideshow) {
        if ($slideshow["public"] == false) {
            $chosenSlideshow = $slideshow;
            break;
        }
    }
} else {
    foreach ($allSlideshows as $slideshow) {
        if ($slideshow["public"] == true) {
            $chosenSlideshow = $slideshow;
            break;
        }
    }
}

if (isset($_POST) && isset($_POST["chosenSlideshow"])) {
    $chosenSlideshow = $allSlideshows[$_POST["chosenSlideshow"]];
}