<?php
$configuration = array();

// Configure database connection
$database = array();
$database['type'] = 'mssql';
$database['host'] = "MARR2\\GMARRMSSQL1";
$database['name'] = "WebSlideshow-DEV";
$database['user'] = "Urgele1";
$database['password'] = "goldmOOn78!";
$database['useSPROCS'] = true;

    // Configure SPROC names
    $database['SPROCS'] = array();
    $database['SPROCS']['insert'] = array();
    $database['SPROCS']['insert']['image'] = 'Image.Insert';
    $database['SPROCS']['insert']['tag'] = 'Tag.Insert';
    $database['SPROCS']['insert']['taggedImage'] = 'TaggedImage.Insert';

    $database['SPROCS']['select']['image'] = 'Image.Select';
    $database['SPROCS']['select']['tag'] = 'Tag.Select';

$configuration["database"] = $database;

// Configure public and private root paths
$virtualRoots = array();
$virtualRoots["public"] = "/myphotos/";
$virtualRoots["private"] = "/myphotos/private/";
$configuration["virtualRoots"] = $virtualRoots;

$physicalRoots = array();
$physicalRoots["public"] = "E:\\MyPhotos\\";
$physicalRoots["private"] = "E:\\MyPhotos\\Private\\";
$configuration["physicalRoots"] = $physicalRoots;


// Configure All Slideshows
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
$configuration["allSlideshows"] = $allSlideshows;



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
