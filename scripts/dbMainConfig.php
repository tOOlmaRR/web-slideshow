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

    $database['SPROCS']['delete']['taggedImage'] = 'TaggedImage.Delete';

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

/* Determine Slideshow Configuration */

// tags chosen for the slideshow
if (isset($_POST) && isset($_POST["chosenTags"])) {
    $configuration["chosenTags"] = $_POST["chosenTags"];
} else {
    $configuration["chosenTags"] = [];
}

// what slideshow mode are we in?
//   normal: show both slideshow options and slide info panes and display the slideshow to the right
//   tagging: hide the slideshow options pane, increase the width of the slide info pane, and only include slides that are not 'fully tagged'
//   maximize: hide bot slideshow options and slide info panes, increase the allowable width, go into full screen mode, and remove as much 'chrome' as possible
if (isset($_POST) && isset($_POST["slideshowMode"])) {
    $configuration["slideshowMode"] = $_POST["slideshowMode"];
} else {
    $configuration["slideshowMode"] = [];
}