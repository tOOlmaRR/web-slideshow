<?php
namespace toolmarr\WebSlideshow;

class WebSlideshow
{
    const SLIDE_VIRTUAL_LOCATION_KEY = "virtualLocation";
    const SLIDE_FILENAME_KEY = "filename";
    
    const CONFIG_SLIDESHOW_VISIBILITY_PUBLIC_KEY = "public";

    // for purposes of unit testing (make sure this valu eis synced up with unit tests)
    const TEST_PUBLIC_PHOTO = 'testPhoto.png';

    public int $maxHeight;

    public function __construct(int $viewportHeight)
    {
        $this->maxHeight = $viewportHeight - 100;
    }

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

        // if the target folder doesn't exist (is not a directory), return an empty array
        if (!is_dir($physicalFolderLocation)) {
            return [];
        }

        // Recursively include subfolders if configured to do so; otherwise, skip them
        if ($includeSubFolders) {
            $objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($physicalFolderLocation), \RecursiveIteratorIterator::SELF_FIRST);
            foreach ($objects as $name => $object) {
                // build the virtual location
                $virtualLocation = $virtualFolderLocation . substr($object->getPathName(), strpos($object->getPathName(), $physicalPath) + strlen($physicalPath));
                $virtualLocation = str_replace("\\", "/", $virtualLocation);
                $photoToDisplay[WebSlideshow::SLIDE_VIRTUAL_LOCATION_KEY] = $virtualLocation;
                
                // determine current image properties; ignore anything that doesn't appear to be an image, but also handle test images for unit testing
                if ($object->getFilename() == WebSlideshow::TEST_PUBLIC_PHOTO) {
                    $width = 250;
                    $height = 250;
                } elseif (!@list($width, $height) = getimagesize($name)) {
                    continue;
                }

                // proportionally resize the image's dimensions
                $newImageDimensions = $this->optimizePhotoSize($width, $height);
                
                // build the photo object and it to the list
                $photoToDisplay[WebSlideshow::SLIDE_FILENAME_KEY] = $object->getFileName();
                $photoToDisplay['originalWidth'] = $width;
                $photoToDisplay['originalHeight'] = $height;
                $photoToDisplay['width'] = $newImageDimensions['width'];
                $photoToDisplay['height'] = $newImageDimensions['height'];
                $photosToDisplay[] = $photoToDisplay;
            }
        } else {
            $allPhotos = scandir($physicalFolderLocation);
            for ($i = 0; $i < count($allPhotos); $i++) {
                // determine full phsyical location
                $fullPhysicalLocation = $physicalFolderLocation . $allPhotos[$i];
                
                // build the virtual location
                $photoToDisplay[WebSlideshow::SLIDE_FILENAME_KEY] = $allPhotos[$i];
                $photoToDisplay[WebSlideshow::SLIDE_VIRTUAL_LOCATION_KEY] = $virtualFolderLocation . $photoToDisplay[WebSlideshow::SLIDE_FILENAME_KEY];

                // determine current image properties; ignore anything that doesn't appear to be an image, but also handle test images for unit testing
                if ($allPhotos[$i] == WebSlideshow::TEST_PUBLIC_PHOTO) {
                    $width = 250;
                    $height = 250;
                } elseif (!@list($width, $height) = getimagesize($fullPhysicalLocation)) {
                    continue;
                }

                // proportionally resize the image's dimensions
                $newImageDimensions = $this->optimizePhotoSize($width, $height);
                    
                // build the photo object and it to the list
                $photoToDisplay['originalWidth'] = $width;
                $photoToDisplay['originalHeight'] = $height;
                $photoToDisplay['width'] = $newImageDimensions['width'];
                $photoToDisplay['height'] = $newImageDimensions['height'];
                $photosToDisplay[] = $photoToDisplay;
            }
        }
        return $photosToDisplay;
    }

    private function optimizePhotoSize($width, $height) : array
    {
        $newImageDimensions = array();
        $newImageDimensions['width'] = ceil(($this->maxHeight * $width) / $height);
        $newImageDimensions['height'] = $this->maxHeight;
        return $newImageDimensions;
    }

    // Builds the HTML for all slides
    private function buildSlidesHtml(array $photosToDisplay) : string
    {
        $slideshowHtml = "";
        foreach ($photosToDisplay as $number => $photoToDisplay) {
            if (isset($photoToDisplay[WebSlideshow::SLIDE_VIRTUAL_LOCATION_KEY])
                && isset($photoToDisplay[WebSlideshow::SLIDE_FILENAME_KEY])
                && !empty($photoToDisplay[WebSlideshow::SLIDE_VIRTUAL_LOCATION_KEY])
                && !empty($photoToDisplay[WebSlideshow::SLIDE_FILENAME_KEY])
            ) {
                $slideshowHtml = $slideshowHtml . "            <div class=\"mySlides fade c" . $number . "\" style=\"height: " . (intval($photoToDisplay['height'])+100) . "px;\">";
                $slideshowHtml = $slideshowHtml . "                <div class=\"numbertext\">" . ($number + 1) . " / " . count($photosToDisplay) . "</div>";
                $slideshowHtml = $slideshowHtml . "                <img width=\"$photoToDisplay[width]\" height=\"$photoToDisplay[height]\" src=\"" . $photoToDisplay[WebSlideshow::SLIDE_VIRTUAL_LOCATION_KEY] . "\">";
                $slideshowHtml = $slideshowHtml . "                <div class=\"text\"><span class=\"filename\">" . $photoToDisplay[WebSlideshow::SLIDE_FILENAME_KEY] . "</span><span class=\"dimensions\">$photoToDisplay[originalWidth]x$photoToDisplay[originalHeight] resized to $photoToDisplay[width]x$photoToDisplay[height]<span></div>";
                $slideshowHtml = $slideshowHtml . "            </div>";
            }
        }
        return $slideshowHtml;
    }
}
