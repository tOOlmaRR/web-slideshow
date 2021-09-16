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
            $slidehowTagsHtml = $slidehowTagsHtml . "<input type=\"checkbox\" name=\"chosenSlideshowTags\" value=\"" . $tag->tag . "\" id=\"" . $tag->tag . "" . $tag->tag . "\">";
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