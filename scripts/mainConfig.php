<?php

$allSlideshows = array();

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

$allSlideshows["WeddingAll"] = [
    "name" => "Wedding - ALL",
    "public" => true,
    "physicalPaths" => [
        "Wedding\\Disc1\\",
        "Wedding\\Disc2\\"
    ],
    "includeSubfolders" => true
];



// determine chosen slideshow - use the first valid available slidehow by default
$currentHourAndMinutes = date('Gi');
if (isset($_GET) && isset($_GET["in"]) && ($_GET["in"] >= $currentHourAndMinutes - 1) && ($_GET["in"] <= $currentHourAndMinutes + 1)) {
    foreach ($allSlideshows as $slideshow) {
        if (!$slideshow["public"]) {
            $chosenSlideshow = $slideshow;
            break;
        }
    }
} else {
    foreach ($allSlideshows as $slideshow) {
        if ($slideshow["public"]) {
            $chosenSlideshow = $slideshow;
            break;
        }
    }
}

if (isset($_POST) && isset($_POST["chosenSlideshow"])) {
    $chosenSlideshow = $allSlideshows[$_POST["chosenSlideshow"]];
}
