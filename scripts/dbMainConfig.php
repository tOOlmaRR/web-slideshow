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

    $database['SPROCS']['select']['images'] = 'Images.Select';
    $database['SPROCS']['select']['image'] = 'Image.Select';
    $database['SPROCS']['select']['tags'] = 'Tags.Select';
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

/* Determine Slideshow Configuration - Chosen Tags */
if (isset($_POST) && isset($_POST["chosenSlideshowTags"])) {
    $configuration["chosenTags"] = $_POST["chosenSlideshowTags"];
} else {
    $configuration["chosenTags"] = [];
}