<?php declare(strict_types=1);
namespace toolmarr\WebSlideshowTests;

use PHPUnit\Framework\TestCase;
use toolmarr\WebSlideshow\WebSlideshow;
use toolmarr\WebSlideshowTests\TestHelpers;

final class WebSlideshowTest extends TestCase
{
    use TestHelpers;

    const SLIDE_VIRTUAL_LOCATION_KEY = "virtualLocation";
    const SLIDE_FILENAME_KEY = "filename";

    const FUNCTION_NAME_BUILDSLIDESHTML = 'buildSlidesHtml';

    // construct tests
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

    

    // buildSlidesHtml Tests
    public function test_buildSlidesHtml_emptyPhotosArray(): void
    {
        // instantiate a slideshow
        $slideshow = new WebSlideshow;
        
        // create an empty list of photos to include in the slideshow
        $photosToDisplay = array();

        // assert that this will return no HTML
        $htmlReturned = $this->invokeMethod($slideshow, WebSlideshowTest::FUNCTION_NAME_BUILDSLIDESHTML, [$photosToDisplay]);
        $this->assertEmpty($htmlReturned);
    }

    public function test_buildSlidesHtml_nullPhotosArray(): void
    {
        // instantiate a slideshow
        $slideshow = new WebSlideshow;

        // create an empty array of slides
        $photosToDisplay = null;

        // assert that this will raise an TypeError
        $this->expectException(\TypeError::class);
        $this->invokeMethod($slideshow, WebSlideshowTest::FUNCTION_NAME_BUILDSLIDESHTML, [$photosToDisplay]);
    }

    public function test_buildSlidesHtml_singleValidPhoto(): void
    {
        // instantiate a slideshow
        $slideshow = new WebSlideshow;

        // create an array of slides containing a single 'valid' slide
        $photosToDisplay = [
            [
                WebSlideshow::SLIDE_FILENAME_KEY => 'someFilename.jpg',
                WebSlideshow::SLIDE_VIRTUAL_LOCATION_KEY => '/some/virtual/location'
            ]
        ];

        // assert that this will return a non-empty string
        $htmlReturned = $this->invokeMethod($slideshow, WebSlideshowTest::FUNCTION_NAME_BUILDSLIDESHTML, [$photosToDisplay]);
        $this->assertNotEmpty($htmlReturned);
        $this->assertIsString($htmlReturned);

        // assert that the HTML that is being built contains an image tag with the specified virtual location
        $this->assertStringContainsString("img src=\"" . $photosToDisplay[0][WebSlideshow::SLIDE_VIRTUAL_LOCATION_KEY] . "\"", $htmlReturned);
    }

    public function test_buildSlidesHtml_missingVirtualLocation(): void
    {
        // instantiate a slideshow
        $slideshow = new WebSlideshow;

        // create a array of slides with a single slide that is missing the 'virtualLocation' index
        $photosToDisplay = [
            [
                WebSlideshow::SLIDE_FILENAME_KEY => 'someFilename.jpg'
            ]
        ];

        // assert that this will return an empty string
        $htmlReturned = $this->invokeMethod($slideshow, WebSlideshowTest::FUNCTION_NAME_BUILDSLIDESHTML, [$photosToDisplay]);
        $this->assertEmpty($htmlReturned);
    }

    public function test_buildSlidesHtml_missingFilename(): void
    {
        // instantiate a slideshow
        $slideshow = new WebSlideshow;

        // create a array of slides with a single slide that is missing the 'filename' index
        $photosToDisplay = [
            [
                WebSlideshow::SLIDE_VIRTUAL_LOCATION_KEY => '/some/virtual/location'
            ]
        ];

        // assert that this will return an empty string
        $htmlReturned = $this->invokeMethod($slideshow, WebSlideshowTest::FUNCTION_NAME_BUILDSLIDESHTML, [$photosToDisplay]);
        $this->assertEmpty($htmlReturned);
    }
}
