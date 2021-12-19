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
    public array $allSlides;

    public function __construct(int $viewportHeight)
    {
        $this->maxHeight = $viewportHeight - 100;
        $this->privateAcessGranted = $this->isPrivateAccessGranted();
    }

    public function getAvailableTags($config)
    {
        $entityFactory = new EntityFactory($config['database']);
        $tagsEntity = $entityFactory->getEntity("tags");
        $tagsEntity->imageID = null;
        $tagsEntity->includeSecureTags = $this->privateAcessGranted ? 1 : 0;

        if ($tagsEntity->get())
        {
            $allTags = $tagsEntity->tags;
        } else {
            $allTags = [];
            // TODO: consider outputting an error to the UI
        }
        return $allTags;
    }

    public function buildSlideshowTagsHtml($tags, $config) : string
    {
        // initial form and fieldset rendering
        $slideshowTagsHtml = "<form action=\"\" method=\"POST\">";
        $slideshowTagsHtml = $slideshowTagsHtml . "<fieldset>";
        $slideshowTagsHtml = $slideshowTagsHtml . "<legend>Tags to Include in Slideshow:</legend>";
        $slideshowTagsHtml = $slideshowTagsHtml . "<div id=\"tagSelection\">";
        
        // Build list of tags to render
        foreach ($tags as $tag) {
            $cssClass = $tag->secure ? 'privateOption' : 'publicOption';
            $checkedAttribute = in_array($tag->tag, $config['chosenTags']) ? 'checked' : '';

            $slideshowTagsHtml = $slideshowTagsHtml . "<span>";
            $slideshowTagsHtml = $slideshowTagsHtml . "<input type=\"checkbox\" name=\"chosenSlideshowTags[]\" value=\"$tag->tag\" id=\"$tag->tag\" $checkedAttribute />";
            $slideshowTagsHtml = $slideshowTagsHtml . "<label class=\"$cssClass\" for=\"$tag->tag\">$tag->tag</label>";
            $slideshowTagsHtml = $slideshowTagsHtml . "</span>";
        }

        // close off the drop down and render the button
        $slideshowTagsHtml = $slideshowTagsHtml . "</div>";
        $slideshowTagsHtml = $slideshowTagsHtml . "<div id=\"tagSelectionSubmit\"><input type=\"submit\" value=\"Generate Slideshow\"></div>";
        $slideshowTagsHtml = $slideshowTagsHtml . "</form>";
        $slideshowTagsHtml = $slideshowTagsHtml . "</fieldset>";

        // display the built HTML to the page
        return $slideshowTagsHtml;
    }

    public function buildRandomizeToggleHtml() : string
    {
        $slideshowRandomizeToggleHtml = "<fieldset>";
        $slideshowRandomizeToggleHtml = $slideshowRandomizeToggleHtml . "<legend>Randomize:</legend>";
        $slideshowRandomizeToggleHtml = $slideshowRandomizeToggleHtml . "<input type=\"checkbox\" id=\"randomizeToggle\" name=\"randomizeToggle\" value=\"randomize\" onclick=\"randomize_change(this)\" />";
        $slideshowRandomizeToggleHtml = $slideshowRandomizeToggleHtml . "<label for=\"randomizeToggle\">Randomize!</label>";
        $slideshowRandomizeToggleHtml = $slideshowRandomizeToggleHtml . "</fieldset>";

        // display the built HTML to the page
        return $slideshowRandomizeToggleHtml;
    }

    public function buildSlideshowSpeedHtml() : string
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
        return $slideshowSpeedHtml;
    }

    public function renderSlideShow($configuration) : string
    {
        $entityFactory = new EntityFactory($configuration['database']);
        $allImages = [];
        foreach ($configuration['chosenTags'] as $tag) {
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
            $photoToDisplay = [];
            
            // determine physical and virtual roots based on configuration
            $virtualRoot = $image->secure ? $configuration["virtualRoots"]["private"] : $configuration["virtualRoots"]["public"];
            $rootFolder = $image->secure ? $configuration["physicalRoots"]["private"] : $configuration["physicalRoots"]["public"];

            // proportionally resize the image's dimensions
            $newImageDimensions = $this->optimizePhotoSize($image->width, $image->height);

            // build the photo object and add it to the list
            $photoToDisplay['ID'] = $image->imageID;
            $photoToDisplay[DbWebSlideshow::SLIDE_FILENAME_KEY] = $image->fileName;
            $photoToDisplay[DbWebSlideshow::SLIDE_FULLPATH_KEY] = $image->fullFilePath;
            $photoToDisplay['originalWidth'] = $image->width;
            $photoToDisplay['originalHeight'] = $image->height;
            $photoToDisplay['width'] = $newImageDimensions['width'];
            $photoToDisplay['height'] = $newImageDimensions['height'];
            $photoToDisplay['secured'] = $image->secure;
            
            /* get the path */
            // take the full physical path and trim off the root folder
            $path = substr($image->fullFilePath, strlen($rootFolder));

            // trim off the filename
            $path = substr($path, 0, strpos($path, $image->fileName));

            // append remainder to the virtualRoot
            $virtualLocation = $virtualRoot . $path;

            // replace the \ with a /
            $virtualLocation = str_replace("\\", "/", $virtualLocation);

            // append the filename
            $virtualFullPath = $virtualLocation . $image->fileName;
            $photoToDisplay[DbWebSlideshow::SLIDE_VIRTUAL_LOCATION_KEY] = $virtualFullPath;

            // retrieve all tags for the current image
            $tagsEntity = $entityFactory->getEntity("tags");
            $tagsEntity->imageID = $image->imageID;
            $tagsEntity->includeSecureTags = $this->privateAcessGranted ? 1 : 0;
            if ($tagsEntity->get())
            {
                $imageTags = $tagsEntity->tags;
                foreach ($imageTags as $tag) {
                    $photoToDisplay['tags'][$tag->tagID] = $tag;
                }
            }
            $photosToDisplay[] = $photoToDisplay;
        }

        /* Render the slides */
        $number = 0;
        $slideshowHtml = '';
        foreach ($photosToDisplay as $photoToDisplay) {
            $slideshowHtml = $slideshowHtml . "<div class=\"mySlides fade c" . $number . "\" style=\"height: " . (intval($photoToDisplay['height'])+55) . "px;\">";
            $slideshowHtml = $slideshowHtml . "    <div class=\"numbertext\">" . ($number + 1) . " / " . count($photosToDisplay) . "</div>";
            $slideshowHtml = $slideshowHtml . "    <img width=\"$photoToDisplay[width]\" height=\"$photoToDisplay[height]\" src=\"" . $photoToDisplay[DbWebSlideshow::SLIDE_VIRTUAL_LOCATION_KEY] . "\">";
            $slideshowHtml = $slideshowHtml . "    <div class=\"text\"><span class=\"filename\">" . $photoToDisplay[DbWebSlideshow::SLIDE_FILENAME_KEY] . "</span><span class=\"dimensions\">$photoToDisplay[originalWidth]x$photoToDisplay[originalHeight] resized to $photoToDisplay[width]x$photoToDisplay[height]<span></div>";
            $slideshowHtml = $slideshowHtml . "</div>";
            $number++;
        }

        $this->allSlides = $photosToDisplay;
        return $slideshowHtml;
    }

    public function buildSlideInfoHtml($allSlides, $tags) : string
    {
        $slideInfoHtml = '';
        $number = 0;
        foreach ($allSlides as $slide)
        {
            $slideInfoHtml .= "<div class=\"mySlideInfo c" . $number . "\">";
            $slideInfoHtml .= $this->buildSlideBasicInfoHtml($slide);
            $slideInfoHtml .= $this->buildSlideTagsHtml($slide, $tags);
            $slideInfoHtml .= "</div>";
            $number++;
        }
        $slideInfoHtml .= "<div id=\"slideTagsSubmitMessages\"></div>";
        return $slideInfoHtml;
    }



    private function buildSlideBasicInfoHtml($slide) : string
    {
        $checkedAttribute = $slide['secured']? "checked" : "";
        
        $slideBasicInfoHtml = "<fieldset>";
        $slideBasicInfoHtml .= "<legend>Basic Info:</legend>";
        
        $slideBasicInfoHtml .= "    <div class=\"slideBasicInfo\">";
        $slideBasicInfoHtml .= "        <div>";
        $slideBasicInfoHtml .= "            <span class=\"title\">Filename: </span><br />";
        $slideBasicInfoHtml .= "            <input class=\"slide-filename\" type=\"text\" disabled=\"disabled\" name=\"filename\" value=\"" . $slide[DbWebSlideshow::SLIDE_FILENAME_KEY] . "\" />";
        $slideBasicInfoHtml .= "        </div>";
        $slideBasicInfoHtml .= "        <div>";
        $slideBasicInfoHtml .= "            <span class=\"title\">Original Size: </span><br />";
        $slideBasicInfoHtml .= "            <input class=\"slide-o-size\" type=\"text\" disabled=\"disabled\" value=\"$slide[originalWidth]x$slide[originalHeight]\" />";
        $slideBasicInfoHtml .= "        </div>";
        $slideBasicInfoHtml .= "        <div>";
        $slideBasicInfoHtml .= "            <span class=\"title\">Resized To: </span><br />";
        $slideBasicInfoHtml .= "            <input class=\"slide-n-size\" type=\"text\" disabled=\"disabled\" value=\"$slide[width]x$slide[height]\" />";
        $slideBasicInfoHtml .= "        </div>";
        $slideBasicInfoHtml .= "        <div>";
        $slideBasicInfoHtml .= "            <input class=\"slide-secured\" type=\"checkbox\" disabled=\"disabled\" name=\"secureImage\" $checkedAttribute/><label for=\"secureImage\">Secured Image</label>";
        $slideBasicInfoHtml .= "        </div>";
        $slideBasicInfoHtml .= "    </div>";
        $slideBasicInfoHtml .= "</fieldset>";
        return $slideBasicInfoHtml;
    }

    private function buildSlideTagsHtml($slide, $tags) : string
    {
        // initial form and fieldset rendering
        $slideTagsHtml = "<div class=\"slideTagInfo\">";
        $slideTagsHtml .= "    <form action=\"\" method=\"POST\">";
        $slideTagsHtml .= "        <fieldset>";
        $slideTagsHtml .= "            <legend>Tags Associated to this Slide:</legend>";
        $slideTagsHtml .= "            <div id=\"tagSelection\">";
        
        // Build list of tags to render
        foreach ($tags as $tag) {
            $checkedAttribute = "";
            if (array_key_exists($tag->tagID, $slide['tags'])) {
                $checkedAttribute = "checked";
            }
            $cssClass = $tag->secure ? 'privateOption' : 'publicOption';
            $slideTagsHtml .= "                <span>";
            $slideTagsHtml .= "                    <input type=\"checkbox\" name=\"slideTags[]\" value=\"$tag->tag\" id=\"$tag->tag\" $checkedAttribute onclick=\"updateTags(" . $slide['ID'] . ", $tag->tagID, '$tag->tag', this);\"/>";
            $slideTagsHtml .= "                    <label class=\"$cssClass\" for=\"$tag->tag\">$tag->tag</label>";
            $slideTagsHtml .= "                </span>";
        }

        // close off the drop down and render the button
        $slideTagsHtml .= "            </div>";
        $slideTagsHtml .= "        </fieldset>";
        $slideTagsHtml .= "    </form>";
        $slideTagsHtml .= "</div>";

        // display the built HTML to the page
        return $slideTagsHtml;
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
        if (isset($_GET) && isset($_GET['in']) && ($_GET['in'] >= $currentHourAndMinutes - 1) && ($_GET['in'] <= $currentHourAndMinutes + 1)) {
            return true;
        } else {
            return false;
        }
    }
}