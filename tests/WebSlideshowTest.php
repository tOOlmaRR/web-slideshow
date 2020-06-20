<?php declare(strict_types=1);
namespace toolmarr\WebSlideshowTests;

use PHPUnit\Framework\TestCase;
use toolmarr\WebSlideshow\WebSlideshow;
use toolmarr\WebSlideshowTests\TestHelpers;

final class WebSlideshowTest extends TestCase
{
    use TestHelpers;

    public function test_constructor_noParametersCreatesAnObject(): void
    {
        // assert that the constructor without any parameters will instantiate the object
        $this->assertIsObject(new WebSlideshow);
    }

    public function test_constructor_noParametersCreatesAWebSlideshowObject(): void
    {
        // assert that the constructor without any parameters will instantiate a WebSlideshow object
        $this->assertInstanceOf(WebSlideshow::class, new WebSlideshow);
    }
    
    public function test_buildSlidesHtml_emptyPhotosArray(): void
    {
        // instantiate a slideshow
        $slideshow = new WebSlideshow;
        
        // create an empty list of photos to include in the slideshow
        $photosToDisplay = array();

        // assert that this will return no HTML
        $htmlReturned = $this->invokeMethod($slideshow, "buildSlidesHtml", [$photosToDisplay]);
        $this->assertEmpty($htmlReturned);
    }
}
