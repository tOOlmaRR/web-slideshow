<?php
function renderSlideShow()
{
    // security
    $currentHourAndMinutes = date('Gi');
    if (isset($_GET) && $_GET["in"] === $currentHourAndMinutes) {
        $rootFolder = "E:\\MyPhotos\\Private\\";
        $physicalPath = "Honeymoon\\";
    } else {
        $rootFolder = "E:\\MyPhotos\\";
        $physicalPath = "Wedding\\";
    }

    // build physical and virtual locations
    $physicalFolderLocation = $rootFolder . $physicalPath;
    if ($rootFolder == "E:\\MyPhotos\\Private\\") {
        $virtualRoot = "/myphotos/private/";
    } else {
        $virtualRoot = "/myphotos/";
    }
    $virualFolderLocation = $virtualRoot . str_replace("\\", "/", $physicalPath);

    // get all photos in provided folder
    $allPhotos = scandir($physicalFolderLocation);

    // determine which to display (for now, weed out directories)
    for ($i = 0; $i < count($allPhotos); $i++) {
        if (is_dir($allPhotos[$i])) {
            continue;
        } else {
            $photosToDisplay[] = $allPhotos[$i];
        }
    }

    // render the output
    foreach ($photosToDisplay as $number => $filename) {
        $filePath = $virualFolderLocation . $filename;
        $slidehowHtml = "";
        $slidehowHtml = $slidehowHtml . "            <div class=\"mySlides fade c" . $number . "\">";
        $slidehowHtml = $slidehowHtml . "                <div class=\"numbertext\">" . ($number + 1) . " / " . count($photosToDisplay) . "</div>";
        $slidehowHtml = $slidehowHtml . "                <img src=\"" . $filePath . "\">";
        $slidehowHtml = $slidehowHtml . "                <div class=\"text\"><span class=\"filename\">" . $filename . "</span></div>";
        $slidehowHtml = $slidehowHtml . "            </div>";
        echo $slidehowHtml;
    }
}
