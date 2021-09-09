<?php
namespace toolmarr\WebSlideshow;

class DbWebSlideshow
{
    public int $maxHeight;

    public function __construct(int $viewportHeight)
    {
        $this->maxHeight = $viewportHeight - 100;
    }

    public function renderSlideshowTags($config)
    {
        //$allSlideshows = $config["allSlideshows"];
        
        // security
        $includeSecureConfigurationOptions = false;
        $currentHourAndMinutes = date('Gi');
        if (isset($_GET) && isset($_GET["in"]) && ($_GET["in"] >= $currentHourAndMinutes - 1) && ($_GET["in"] <= $currentHourAndMinutes + 1)) {
            $includeSecureConfigurationOptions = true;
        }

        // initial form and fieldset rendering
        $slidehowTagsHtml = "<form action=\"\" method=\"POST\">";
        $slidehowTagsHtml = $slidehowTagsHtml . "<fieldset>";
        $slidehowTagsHtml = $slidehowTagsHtml . "<legend>Tags to Include in the Slideshow:</legend>";
        
        // Build list of tags to include in the slideshow
        $slidehowTagsHtml = $slidehowTagsHtml . "<input type=\"checkbox\" name=\"chosenSlideshowTags\" value=\"Tag1\" id=\"Tag1\"><label for=\"Tag1\">Tag2</label><br>";
        $slidehowTagsHtml = $slidehowTagsHtml . "<input type=\"checkbox\" name=\"chosenSlideshowTags\" value=\"Tag2\" id=\"Tag2\"><label for=\"Tag2\">Tag2</label><br>";
        

        // Build list of items for each available slideshow
/*        foreach ($allSlideshows as $key => $slideshow) {
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
        }*/

        // close off the drop down and render the button
        $slidehowTagsHtml = $slidehowTagsHtml . "<input type=\"submit\" value=\"GO!\">";
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
}