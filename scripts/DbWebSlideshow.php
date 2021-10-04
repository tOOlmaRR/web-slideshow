<?php
namespace toolmarr\WebSlideshow;

use toolmarr\WebSlideshow\DAL\EntityFactory;
use toolmarr\WebSlideshow\DAL\TagsEntity;
use toolmarr\WebSlideshow\DAL\TagEntity;

class DbWebSlideshow
{
    const SLIDE_FILENAME_KEY = "filename";
    const SLIDE_FULLPATH_KEY = "fullpath";
    const SLIDE_VIRTUAL_LOCATION_KEY = "virtualLocation";

    public int $maxHeight;
    public bool $privateAcessGranted = false;

    public function __construct(int $viewportHeight)
    {
        $this->maxHeight = $viewportHeight - 100;
        $this->privateAcessGranted = $this->isPrivateAccessGranted();
    }

    public function getAvailableTags($config)
    {
        $entityFactory = new EntityFactory($config['database']);
        $tagsEntity = $entityFactory->getEntity("tags");
        if ($tagsEntity->get())
        {
            $allTags = $tagsEntity->tags;
        } else {
            $allTags = [];
            // TODO: consider outputting an error to the UI
        }

        $tagsToDisplay = [];
        foreach ($allTags as $tag) {
            // add secure tags only if private access has been granted
            if ($tag->secure && $this->privateAcessGranted) {
                $tagsToDisplay[] = $tag;
            }

            // add all public tags
            else if (!$tag->secure) {
                $tagsToDisplay[] = $tag;
            }
        }
        return $tagsToDisplay;
    }

    public function renderSlideshowTags($tags)
    {
        // initial form and fieldset rendering
        $slidehowTagsHtml = "<form action=\"\" method=\"POST\">";
        $slidehowTagsHtml = $slidehowTagsHtml . "<fieldset>";
        $slidehowTagsHtml = $slidehowTagsHtml . "<legend>Tags to Include in the Slideshow:</legend>";
        $slidehowTagsHtml = $slidehowTagsHtml . "<div id=\"tagSelection\">";
        
        // Build list of tags to render
        foreach ($tags as $tag) {
            $cssClass = $tag->secure ? 'privateOption' : 'publicOption';
            $slidehowTagsHtml = $slidehowTagsHtml . "<span>";
            $slidehowTagsHtml = $slidehowTagsHtml . "<input type=\"checkbox\" name=\"chosenSlideshowTags[]\" value=\"" . $tag->tag . "\" id=\"" . $tag->tag . "\">";
            $slidehowTagsHtml = $slidehowTagsHtml . "<label class=\"" . $cssClass . "\" for=\"" . $tag->tag . "\">" . $tag->tag . "</label>";
            $slidehowTagsHtml = $slidehowTagsHtml . "</span>";
        }

        // close off the drop down and render the button
        $slidehowTagsHtml = $slidehowTagsHtml . "</div>";
        $slidehowTagsHtml = $slidehowTagsHtml . "<div id=\"tagSelectionSubmit\"><input type=\"submit\" value=\"GO!\"></div>";
        $slidehowTagsHtml = $slidehowTagsHtml . "</form>";
        $slidehowTagsHtml = $slidehowTagsHtml . "</fieldset>";

        // display the built HTML to the page
        echo $slidehowTagsHtml;
    }

    public function renderRandomizeToggle()
    {
        $slideshowRandomizeToggleHtml = "<fieldset>";
        $slideshowRandomizeToggleHtml = $slideshowRandomizeToggleHtml . "<legend>Randomize:</legend>";
        $slideshowRandomizeToggleHtml = $slideshowRandomizeToggleHtml . "<input type=\"checkbox\" id=\"randomizeToggle\" name=\"randomizeToggle\" value=\"randomize\" onclick=\"randomize_change(this)\" />";
        $slideshowRandomizeToggleHtml = $slideshowRandomizeToggleHtml . "<label for=\"randomizeToggle\">Randomize!</label>";
        $slideshowRandomizeToggleHtml = $slideshowRandomizeToggleHtml . "</fieldset>";

        // display the built HTML to the page
        echo $slideshowRandomizeToggleHtml;
    }

    public function renderSlideshowSpeed()
    {
        $slideshowSpeedHtml = "<fieldset>";
        $slideshowSpeedHtml = $slideshowSpeedHtml . "<legend>Slideshow Speed:</legend>";
        $slideshowSpeedHtml = $slideshowSpeedHtml . "<span class=\"currentSlideshowSpeed\">";
            $slideshowSpeedHtml = $slideshowSpeedHtml . "<output id=\"currentSlideshowSpeed\" name=\"currentSlideshowSpeed\">30</output><span> seconds</span>";
        $slideshowSpeedHtml = $slideshowSpeedHtml . "</span>";
        $slideshowSpeedHtml = $slideshowSpeedHtml . "<input type=\"range\" id=\"slideshowSpeed\" name=\"slideshowSpeed\" min=\"5\" max=\"120\" step=\"5\" value=\"30\"
        oninput=\"currentSlideshowSpeed.value = slideshowSpeed.value\" /><br/>";
        $slideshowSpeedHtml = $slideshowSpeedHtml . "</span>";
            $slideshowSpeedHtml = $slideshowSpeedHtml . "<input type=\"checkbox\" id=\"haltSlideshow\" name=\"haltSlideshowToggle\" value=\"halt\" onclick=\"haltSlideshow(this)\" />";
            $slideshowSpeedHtml = $slideshowSpeedHtml . "<label for=\"randomizeToggle\">Halt!</label>";
        $slideshowSpeedHtml = $slideshowSpeedHtml . "</span>";
        $slideshowSpeedHtml  = $slideshowSpeedHtml . "</fieldset>";

        // display the built HTML to the page
        echo $slideshowSpeedHtml;
    }

    public function renderSlideShow($configuration)
    {
        $allImages = [];
        foreach ($configuration['chosenTags'] as $tag) {
            $entityFactory = new EntityFactory($configuration['database']);
            $imagesEntity = $entityFactory->getEntity("images");
            $imagesEntity->tag = $tag;
            $imagesEntity->includeSecureImages = $this->privateAcessGranted ? 1 : 0;
            if ($imagesEntity->get())
            {
                $newImages = $imagesEntity->images;
                foreach ($newImages as $image) {
                    if (!array_key_exists($image->imageID, $allImages)) {
                        $allImages[$image->imageID] = $image;
                    }
                }
            }
        }

        $photosToDisplay = array();
        foreach ($allImages as $image) {

            // determine physical and virtual roots based on configuration
            $virtualRoot = $image->secure ? $configuration["virtualRoots"]["private"] : $configuration["virtualRoots"]["public"];
            $rootFolder = $image->secure ? $configuration["physicalRoots"]["private"] : $configuration["physicalRoots"]["public"];

            // proportionally resize the image's dimensions
            $newImageDimensions = $this->optimizePhotoSize($image->width, $image->height);

            // build the photo object and add it to the list
            $photoToDisplay[DbWebSlideshow::SLIDE_FILENAME_KEY] = $image->fileName;
            $photoToDisplay[DbWebSlideshow::SLIDE_FULLPATH_KEY] = $image->fullFilePath;
            $photoToDisplay['originalWidth'] = $image->width;
            $photoToDisplay['originalHeight'] = $image->height;
            $photoToDisplay['width'] = $newImageDimensions['width'];
            $photoToDisplay['height'] = $newImageDimensions['height'];
            
            /* get the path */
            // take the full physical path and trim off the root folder
            $path =  substr($image->fullFilePath, strlen($rootFolder));

            // trim off the filename
            $path = substr($path, 0, strpos($path, $image->fileName));

            // append remainder to the virtualRoot
            $virtualLocation = $virtualRoot . $path;

            // replace the \ with a /
            $virtualLocation = str_replace("\\", "/", $virtualLocation);

            // append the filename
            $virtualFullPath = $virtualLocation . $image->fileName;
            
            $photoToDisplay[DbWebSlideshow::SLIDE_VIRTUAL_LOCATION_KEY] = $virtualFullPath;
            $photosToDisplay[] = $photoToDisplay;
        }

        /* Render the slides */
        $number = 0;
        $slideshowHtml = '';
        foreach ($photosToDisplay as $photoToDisplay) {
            $slideshowHtml = $slideshowHtml . "            <div class=\"mySlides fade c" . $number . "\" style=\"height: " . (intval($photoToDisplay['height'])+55) . "px;\">";
            $slideshowHtml = $slideshowHtml . "                <div class=\"numbertext\">" . ($number + 1) . " / " . count($photosToDisplay) . "</div>";
            $slideshowHtml = $slideshowHtml . "                <img width=\"$photoToDisplay[width]\" height=\"$photoToDisplay[height]\" src=\"" . $photoToDisplay[DbWebSlideshow::SLIDE_VIRTUAL_LOCATION_KEY] . "\">";
            $slideshowHtml = $slideshowHtml . "                <div class=\"text\"><span class=\"filename\">" . $photoToDisplay[DbWebSlideshow::SLIDE_FILENAME_KEY] . "</span><span class=\"dimensions\">$photoToDisplay[originalWidth]x$photoToDisplay[originalHeight] resized to $photoToDisplay[width]x$photoToDisplay[height]<span></div>";
            $slideshowHtml = $slideshowHtml . "            </div>";
            echo $slideshowHtml;
            $number++;
        }
    }



    private function optimizePhotoSize($width, $height) : array
    {
        $newImageDimensions = array();
        $newImageDimensions['width'] = intval(ceil(($this->maxHeight * $width) / $height));
        $newImageDimensions['height'] = $this->maxHeight;
        return $newImageDimensions;
    }

    private function isPrivateAccessGranted()
    {
        $currentHourAndMinutes = date('Gi');
        if (isset($_GET) && isset($_GET["in"]) && ($_GET["in"] >= $currentHourAndMinutes - 1) && ($_GET["in"] <= $currentHourAndMinutes + 1)) {
            return true;
        } else {
            return false;
        }
    }
}