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
    $slideshowPaths = array();
    if (array_key_exists("physicalPaths", $chosenSlideshow)) {
        $slideshowPaths = $chosenSlideshow["physicalPaths"];
    } else {
        $slideshowPaths[] = $chosenSlideshow["physicalPath"];
    }

    // determine physical and virtual root folders based on security settings (use the first folder)
    if ($chosenSlideshow["public"] == false) {
        $virtualRoot = "/myphotos/private/";
        $rootFolder = "E:\\MyPhotos\\Private\\";
    } else {
        $virtualRoot = "/myphotos/";
        $rootFolder = "E:\\MyPhotos\\";
    }

    // gather a collection of all relevant photos, including all data needed to render them in the webpage
    $photosToDisplay = array();
    foreach ($slideshowPaths as $slideshowPath) {
        // build physical and virtual locations
        $physicalPath = $slideshowPath;
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
                $photoToDisplay["filename"] = $allPhotos[$i];
                $photoToDisplay["virtualFolderLocation"] = $virtualFolderLocation;
                $photosToDisplay[] = $photoToDisplay;
            }
        }
    }

    // render the output for all valid phptos
    foreach ($photosToDisplay as $number => $photoToDisplay) {
        $filePath = $photoToDisplay["virtualFolderLocation"] . $photoToDisplay["filename"];
        $slidehowHtml = "";
        $slidehowHtml = $slidehowHtml . "            <div class=\"mySlides fade c" . $number . "\">";
        $slidehowHtml = $slidehowHtml . "                <div class=\"numbertext\">" . ($number + 1) . " / " . count($photosToDisplay) . "</div>";
        $slidehowHtml = $slidehowHtml . "                <img src=\"" . $filePath . "\">";
        $slidehowHtml = $slidehowHtml . "                <div class=\"text\"><span class=\"filename\">" . $photoToDisplay["filename"] . "</span></div>";
        $slidehowHtml = $slidehowHtml . "            </div>";
        echo $slidehowHtml;
    }
}