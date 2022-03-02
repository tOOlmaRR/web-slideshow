<?php
namespace toolmarr\WebSlideshow;

use toolmarr\WebSlideshow\DAL\EntityFactory;
use toolmarr\WebSlideshow\DAL\ImageEntity;
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
        $this->maxHeight = $viewportHeight - 65;
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

    public function retrieveSlideshowData($configuration, $chosenTags, array $omitTags) : array
    {
        /* Retrieve images from database and build the slides */
        $entityFactory = new EntityFactory($configuration['database']);
        $allImages = $this->getAllImagesWithChosenTags($chosenTags, $entityFactory);
        
        $photosToDisplay = [];
        foreach ($allImages as $image) {
            // build the slide object and add it to the list
            $photoToDisplay = $this->buildSlide($image, $entityFactory);

            // apply filters to remove the photo if it contains a tag in the omit list
            if ($this->filterPhoto($photoToDisplay, $omitTags)) {
                continue;
            }

            // build the image's virtual location based on it's path and the configured physical and virtual roots
            $physicalRoot = $image->secure ? $configuration["physicalRoots"]["private"] : $configuration["physicalRoots"]["public"];
            $virtualRoot = $image->secure ? $configuration["virtualRoots"]["private"] : $configuration["virtualRoots"]["public"];
            $photoToDisplay[DbWebSlideshow::SLIDE_VIRTUAL_LOCATION_KEY] = $this->buildImageVirtualLocation($physicalRoot, $virtualRoot, $image);
            
            // add it to the collection
            $photosToDisplay[] = $photoToDisplay;
        }
        return $photosToDisplay;
    }

    private function getAllImagesWithChosenTags(array $chosenTags, EntityFactory $entityFactory) : array
    {
        $allImages = [];        
        foreach ($chosenTags as $tag) {
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
        return $allImages;
    }

    private function buildSlide(ImageEntity $image, EntityFactory $entityFactory) : array
    {
        // proportionally resize the image's dimensions
        $newImageDimensions = $this->optimizePhotoSize($image->width, $image->height);

        // build and return the slide
        $slide = [];
        $slide['ID'] = $image->imageID;
        $slide[DbWebSlideshow::SLIDE_FILENAME_KEY] = $image->fileName;
        $slide[DbWebSlideshow::SLIDE_FULLPATH_KEY] = $image->fullFilePath;
        $slide['originalWidth'] = $image->width;
        $slide['originalHeight'] = $image->height;
        $slide['width'] = $newImageDimensions['width'];
        $slide['height'] = $newImageDimensions['height'];
        $slide['secured'] = $image->secure;
        
        // retrieve all tags for the current image and add them to the slide
        $tagsEntity = $entityFactory->getEntity("tags");
        $tagsEntity->imageID = $slide['ID'];
        $tagsEntity->includeSecureTags = $this->privateAcessGranted ? 1 : 0;
        if ($tagsEntity->get())
        {
            $imageTags = $tagsEntity->tags;
            foreach ($imageTags as $tag) {
                $slide['tags'][$tag->tag] = $tag;
            }
        }
        return $slide;
    }

    private function filterPhoto(array $photo, array $omitTags) : bool
    {
        if ($omitTags == null) {
            return false;
        } else {
            foreach ($omitTags as $tagToOmit) {
                if (array_key_exists($tagToOmit, $photo['tags'])) {
                    return true;
                }
            }
        }
        return false;
    }
    
    private function buildImageVirtualLocation(string $physicalRoot, string $virtualRoot, ImageEntity $image) : string
    {
        // take the full physical path and trim off the root folder
        $path = substr($image->fullFilePath, strlen($physicalRoot));

        // trim off the filename
        $path = substr($path, 0, strpos($path, $image->fileName));

        // append remainder to the virtualRoot
        $virtualLocation = $virtualRoot . $path;

        // replace the \ with a /
        $virtualLocation = str_replace("\\", "/", $virtualLocation);

        // append the filename and return
        return $virtualLocation . $image->fileName;
    }
    
    private function optimizePhotoSize($width, $height) : array
    {
        $newImageDimensions = array();
        $newImageDimensions['width'] = intval(ceil(($this->maxHeight * $width) / $height));
        $newImageDimensions['height'] = $this->maxHeight;
        return $newImageDimensions;
    }

    private function isPrivateAccessGranted() : bool
    {
        $currentHourAndMinutes = date('Gi');
        return isset($_GET) && isset($_GET['in']) && ($_GET['in'] >= $currentHourAndMinutes - 1) && ($_GET['in'] <= $currentHourAndMinutes + 1);
    }
}