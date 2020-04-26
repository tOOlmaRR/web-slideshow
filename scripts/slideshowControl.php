<?php
function renderSlideshowDropdown($allSlideshows, $selectedSlideshow) {
    // security
    $includeSecureConfigurationOptions = false;
    $currentHourAndMinutes = date('Gi');
    if (isset($_GET) && isset($_GET["in"]) && ($_GET["in"] >= $currentHourAndMinutes - 1) && ($_GET["in"] <= $currentHourAndMinutes + 1)) {
        $includeSecureConfigurationOptions = true;
    }

    // initial form and dropdown rendering
    $slidehowDropdownHtml = "<form action=\"\" method=\"POST\">";
    $slidehowDropdownHtml = $slidehowDropdownHtml . "<label for \"Slideshow\">Choose a Slideshow to start: </label>";
    $slidehowDropdownHtml = $slidehowDropdownHtml . "<select id=\"slideshowSelection\" name=\"chosenSlideshow\">";

    // Build list items for each available slideshow
    foreach ($allSlideshows as $key => $slideshow) {
        $color = "green";
        $selected = "";
        if ($selectedSlideshow["name"] == $slideshow["name"]) {
            $selected = " selected ";
        }

        if ($slideshow["public"] == true || $includeSecureConfigurationOptions == true) {
            if ($slideshow["public"] == false) $color = "red";
            $slidehowDropdownHtml = $slidehowDropdownHtml . "    <option style=\"color:" . $color . ";\" value=\"" . $key . "\" " . $selected . ">" . $slideshow["name"] . "</option>";
        }
    }

    // close off the drop down and render the button
    $slidehowDropdownHtml = $slidehowDropdownHtml . "</select>";
    $slidehowDropdownHtml = $slidehowDropdownHtml . "<input type=\"submit\" value=\"GO!\">";
    $slidehowDropdownHtml = $slidehowDropdownHtml . "</form>";

    // display the built HTML to the page
    echo $slidehowDropdownHtml;
}

function renderSlideShow($chosenSlideshow)
{
    // determine physical and virtual locations of chosen slideshow
    $physicalPath = $chosenSlideshow["physicalPath"];
    if ($chosenSlideshow["public"] == false) {
        $virtualRoot = "/myphotos/private/";
        $rootFolder = "E:\\MyPhotos\\Private\\";
    } else {
        $virtualRoot = "/myphotos/";
        $rootFolder = "E:\\MyPhotos\\";
    }

    // build physical and virtual locations
    $physicalFolderLocation = $rootFolder . $physicalPath;
    $physicalFolderLocation = str_replace("\ ", "%20", $physicalFolderLocation);
    $virtualFolderLocation = $virtualRoot . str_replace("\\", "/", $physicalPath);
    
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
        $filePath = $virtualFolderLocation . $filename;
        $slidehowHtml = "";
        $slidehowHtml = $slidehowHtml . "            <div class=\"mySlides fade c" . $number . "\">";
        $slidehowHtml = $slidehowHtml . "                <div class=\"numbertext\">" . ($number + 1) . " / " . count($photosToDisplay) . "</div>";
        $slidehowHtml = $slidehowHtml . "                <img src=\"" . $filePath . "\">";
        $slidehowHtml = $slidehowHtml . "                <div class=\"text\"><span class=\"filename\">" . $filename . "</span></div>";
        $slidehowHtml = $slidehowHtml . "            </div>";
        echo $slidehowHtml;
    }
}
