<?php
namespace toolmarr\WebSlideshow;

class WebSlideshow
{
    const SLIDE_VIRTUAL_LOCATION_KEY = "virtualLocation";
    const SLIDE_FILENAME_KEY = "filename";
    
    const CONFIG_SLIDESHOW_VISIBILITY_PUBLIC_KEY = "public";

    public function renderSlideshowDropdown($config, $selectedSlideshow)
    {
        $allSlideshows = $config["allSlideshows"];
        
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

        // Build list of items for each available slideshow
        foreach ($allSlideshows as $key => $slideshow) {
            $color = "green";
            $selected = "";
            $slideshowIsPublic = $slideshow[WebSlideshow::CONFIG_SLIDESHOW_VISIBILITY_PUBLIC_KEY];
            if ($selectedSlideshow["name"] == $slideshow["name"]) {
                $selected = " selected ";
            }

            if ($slideshowIsPublic || $includeSecureConfigurationOptions) {
                if (!$slideshowIsPublic) {
                    $color = "red";
                }
                $slidehowDropdownHtml = $slidehowDropdownHtml . "    <option style=\"color:" . $color . ";\" value=\"" .
                    $key . "\" " . $selected . ">" . $slideshow["name"] . "</option>";
            }
        }

        // close off the drop down and render the button
        $slidehowDropdownHtml = $slidehowDropdownHtml . "</select>";
        $slidehowDropdownHtml = $slidehowDropdownHtml . "<input type=\"submit\" value=\"GO!\">";
        $slidehowDropdownHtml = $slidehowDropdownHtml . "</form>";

        // display the built HTML to the page
        echo $slidehowDropdownHtml;
    }

    public function renderSlideShow($config, $chosenSlideshow)
    {
        $slideshowPaths = array();
        if (array_key_exists("physicalPaths", $chosenSlideshow)) {
            $slideshowPaths = $chosenSlideshow["physicalPaths"];
        } else {
            $slideshowPaths[] = $chosenSlideshow["physicalPath"];
        }

        // determine physical and virtual root folders based on security settings (use the first folder)
        $isPublicSlideshow = $chosenSlideshow[WebSlideshow::CONFIG_SLIDESHOW_VISIBILITY_PUBLIC_KEY];
        $virtualRoot = $isPublicSlideshow ? $config["virtualRoots"]["public"] : $config["virtualRoots"]["private"];
        $rootFolder = $isPublicSlideshow ? $config["physicalRoots"]["public"] : $config["physicalRoots"]["private"];

        // Do we want to include subfolders?
        $includeSubFolders = isset($chosenSlideshow["includeSubfolders"]) && $chosenSlideshow["includeSubfolders"];

        // gather a collection of all relevant photos, including all data needed to render them in the webpage
        $imagesToDisplay = array();
        foreach ($slideshowPaths as $slideshowPath) {
            $imagesToDisplayForThisPath = $this->determinePhotosToDisplayForPath($slideshowPath, $rootFolder, $virtualRoot, $includeSubFolders);
            $imagesToDisplay = array_merge($imagesToDisplay, $imagesToDisplayForThisPath);
        }

        // render the output for all valid photos
        $slidesHtml = $this->buildSlidesHtml($imagesToDisplay);
        echo $slidesHtml;
    }



    // iterates through all configured slideshow paths and builds a list of images to display
    // - omits the directory objects from the slideshow
    // - recursively includes images within subfolders if configured to do so
    private function determinePhotosToDisplayForPath($slideshowPath, $rootFolder, $virtualRoot, $includeSubFolders)
    {
        $photosToDisplay = array();
        
        // build physical and virtual locations
        $physicalPath = $slideshowPath;
        $physicalFolderLocation = $rootFolder . $physicalPath;
        $physicalFolderLocation = str_replace("\ ", "%20", $physicalFolderLocation);
        $virtualFolderLocation = $virtualRoot . str_replace("\\", "/", $physicalPath);

        // Recursively include subfolders if configured to do so; otherwise, skip them
        if ($includeSubFolders) {
            $objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($physicalFolderLocation), \RecursiveIteratorIterator::SELF_FIRST);
            foreach ($objects as $name => $object) {
                // weed out directories
                if (!is_dir($name)) {
                    $photoToDisplay[WebSlideshow::SLIDE_FILENAME_KEY] = $object->getFileName();
                    // build the virtual location
                    $virtualLocation = $virtualFolderLocation . substr($object->getPathName(), strpos($object->getPathName(), $physicalPath) + strlen($physicalPath));
                    $virtualLocation = str_replace("\\", "/", $virtualLocation);
                    $photoToDisplay[WebSlideshow::SLIDE_VIRTUAL_LOCATION_KEY] = $virtualLocation;
                    $photosToDisplay[] = $photoToDisplay;
                }
            }
        } else {
            $allPhotos = scandir($physicalFolderLocation);
            for ($i = 0; $i < count($allPhotos); $i++) {
                $fullPhysicalLocation = $physicalFolderLocation . $allPhotos[$i];
                // weed out directories
                if (!is_dir($fullPhysicalLocation)) {
                    // this is a file... assume it's a photo and add it to the collection of photos to be displayed
                    $photoToDisplay[WebSlideshow::SLIDE_FILENAME_KEY] = $allPhotos[$i];
                    $photoToDisplay[WebSlideshow::SLIDE_VIRTUAL_LOCATION_KEY] = $virtualFolderLocation . $photoToDisplay[WebSlideshow::SLIDE_FILENAME_KEY];
                    $photosToDisplay[] = $photoToDisplay;
                }
            }
        }
        return $photosToDisplay;
    }



    // Builds the HTML for all slides
    private function buildSlidesHtml(array $photosToDisplay) : string
    {
        $slideshowHtml = "";
        foreach ($photosToDisplay as $number => $photoToDisplay) {
            $slideshowHtml = $slideshowHtml . "            <div class=\"mySlides fade c" . $number . "\">";
            $slideshowHtml = $slideshowHtml . "                <div class=\"numbertext\">" . ($number + 1) . " / " . count($photosToDisplay) . "</div>";
            $slideshowHtml = $slideshowHtml . "                <img src=\"" . $photoToDisplay[WebSlideshow::SLIDE_VIRTUAL_LOCATION_KEY] . "\">";
            $slideshowHtml = $slideshowHtml . "                <div class=\"text\"><span class=\"filename\">" . $photoToDisplay[WebSlideshow::SLIDE_FILENAME_KEY] . "</span></div>";
            $slideshowHtml = $slideshowHtml . "            </div>";
        }
        return $slideshowHtml;
    }
}
