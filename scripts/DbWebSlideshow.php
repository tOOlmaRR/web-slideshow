<?php
namespace toolmarr\WebSlideshow;

use toolmarr\WebSlideshow\DAL\EntityFactory;
use toolmarr\WebSlideshow\DAL\TagsEntity;
use toolmarr\WebSlideshow\DAL\TagEntity;

class DbWebSlideshow
{
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

        // determine physical and virtual root folders based on security settings (use the first folder)
        $number = 0;
        $slideshowHtml = '';
        foreach ($allImages as $image) {
            $virtualRoot = $image->secure ? $configuration["virtualRoots"]["public"] : $configuration["virtualRoots"]["private"];
            $rootFolder = $image->secure ? $configuration["physicalRoots"]["public"] : $configuration["physicalRoots"]["private"];
            
            $slideshowHtml = $slideshowHtml . "<div>" . $image->fileName . "(" . $image->width . "x" . $image->height . ")</div>";
            /*$slideshowHtml = $slideshowHtml . "            <div class=\"mySlides fade c" . $number . "\" style=\"height: " . intval($image->height+100) . "px;\">";
                $slideshowHtml = $slideshowHtml . "                <div class=\"numbertext\">" . ($number + 1) . " / " . count($allImages) . "</div>";
                $slideshowHtml = $slideshowHtml . "                <img width=\"$image->width\" height=\"$image->height\" src=\"" . $virtualRoot . $image->fileName . "\">";
                $slideshowHtml = $slideshowHtml . "                <div class=\"text\"><span class=\"filename\">" . $image->fileName . "</span>";
                $slideshowHtml = $slideshowHtml . "            </div>";*/

            $number++;
        }
        echo $slideshowHtml;



        /*$slideshowPaths = array();
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
        echo $slidesHtml;*/
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