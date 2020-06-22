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
    const FUNCTION_NAME_DETERMINEPHOTOSTODISPLAYFORPATH = 'determinePhotosToDisplayForPath';

    const TEST_PUBLIC_FOLDER = 'publicPhotosTestFolder';
    const TEST_PRIVATE_FOLDER = 'privatePhotosTestFolder';
    const TEST_PUBLIC_SUBFOLDER = 'publicSubFolder';
    const TEST_PUBLIC_PHOTO = 'testPhoto.png';

    // construct())
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



    // determinePhotosToDisplayForPath()
    public function test_determinePhotosToDisplayForPath_noRecurse_noPhotos(): void
    {
        // instantiate a slideshow
        $slideshow = new WebSlideshow;

        // create test folders
        $testPublicFolder_fullPath = __DIR__ . DIRECTORY_SEPARATOR . WebSlideshowTest::TEST_PUBLIC_FOLDER;
        $testPrivateFolder_fullPath = __DIR__ . DIRECTORY_SEPARATOR . WebSlideshowTest::TEST_PRIVATE_FOLDER;
        $this->createTestFilesAndFolders([$testPublicFolder_fullPath, $testPrivateFolder_fullPath]);
        
        // set up inputs
        $slideshowPath = WebSlideshowTest::TEST_PUBLIC_FOLDER;
        $rootFolder = __DIR__ . DIRECTORY_SEPARATOR;
        $virtualRoot = '/myPhotos/';
        $includeSubFolders = false;
        $inputs = [$slideshowPath, $rootFolder, $virtualRoot, $includeSubFolders];
        // var_dump($inputs);

        // invoke the function
        $photosReturned = $this->invokeMethod($slideshow, WebSlideshowTest::FUNCTION_NAME_DETERMINEPHOTOSTODISPLAYFORPATH, $inputs);
        $this->assertEmpty($photosReturned);
        foreach($photosReturned as $key => $photo) {
            print_r($photo);
            foreach ($photo as $key => $value) {
                echo $key . ' => ' . $value . PHP_EOL;
            }
        }

        // destroy test folders
        $this->destroyTestFilesAndFolders([$testPublicFolder_fullPath, $testPrivateFolder_fullPath]);
    }

    // determinePhotosToDisplayForPath()
    // public function test_determinePhotosToDisplayForPath_noRecurse_onePhoto(): void
    // {
    //     // instantiate a slideshow
    //     $slideshow = new WebSlideshow;

    //     // create test folders and file
    //     $testPublicFolder_fullPath = __DIR__ . DIRECTORY_SEPARATOR . WebSlideshowTest::TEST_PUBLIC_FOLDER;
    //     $testPrivateFolder_fullPath = __DIR__ . DIRECTORY_SEPARATOR . WebSlideshowTest::TEST_PRIVATE_FOLDER;
    //     $testPhoto_fullPath = __DIR__ . DIRECTORY_SEPARATOR . WebSlideshowTest::TEST_PUBLIC_FOLDER . DIRECTORY_SEPARATOR . WebSlideshowTest::TEST_PUBLIC_PHOTO;
    //     $this->createTestFilesAndFolders([$testPublicFolder_fullPath, $testPrivateFolder_fullPath], [$testPhoto_fullPath]);

    //     // set up inputs
    //     $slideshowPath = WebSlideshowTest::TEST_PUBLIC_FOLDER;
    //     $rootFolder = __DIR__ . DIRECTORY_SEPARATOR;
    //     $virtualRoot = '/myPhotos/';
    //     $includeSubFolders = false;
    //     $inputs = [$slideshowPath, $rootFolder, $virtualRoot, $includeSubFolders];
    //     // var_dump($inputs);

    //     // invoke the function
    //     $photosReturned = $this->invokeMethod($slideshow, WebSlideshowTest::FUNCTION_NAME_DETERMINEPHOTOSTODISPLAYFORPATH, $inputs);
    //     $this->assertNotEmpty($photosReturned);
    //     foreach($photosReturned as $key => $photo) {
    //         print_r($photo);
    //         foreach ($photo as $key => $value) {
    //             echo $key . ' => ' . $value . PHP_EOL;
    //         }
    //     }

    //     // destroy test folders
    //     $this->destroyTestFilesAndFolders([$testPublicFolder_fullPath, $testPrivateFolder_fullPath], [$testPhoto_fullPath]);
    // }


    

    
    // buildSlidesHtml()
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

    public function test_buildSlidesHtml_missingVirtualLocationIndex(): void
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

    public function test_buildSlidesHtml_missingFilenameIndex(): void
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

    public function test_buildSlidesHtml_missingVirtualLocationData(): void
    {
        // instantiate a slideshow
        $slideshow = new WebSlideshow;

        // create a array of slides with a single slide that is missing the 'virtualLocation' index
        $photosToDisplay = [
            [
                WebSlideshow::SLIDE_VIRTUAL_LOCATION_KEY => '',
                WebSlideshow::SLIDE_FILENAME_KEY => 'someFilename.jpg'
            ]
        ];

        // assert that this will return an empty string
        $htmlReturned = $this->invokeMethod($slideshow, WebSlideshowTest::FUNCTION_NAME_BUILDSLIDESHTML, [$photosToDisplay]);
        $this->assertEmpty($htmlReturned);
    }

    public function test_buildSlidesHtml_missingFilenameData(): void
    {
        // instantiate a slideshow
        $slideshow = new WebSlideshow;

        // create a array of slides with a single slide that is missing the 'filename' index
        $photosToDisplay = [
            [
                WebSlideshow::SLIDE_VIRTUAL_LOCATION_KEY => '/some/virtual/location',
                WebSlideshow::SLIDE_FILENAME_KEY => ''
            ]
        ];

        // assert that this will return an empty string
        $htmlReturned = $this->invokeMethod($slideshow, WebSlideshowTest::FUNCTION_NAME_BUILDSLIDESHTML, [$photosToDisplay]);
        $this->assertEmpty($htmlReturned);
    }

    public function test_buildSlidesHtml_invalidSlideDoesNotAbortHtmlBuilding(): void
    {
        // instantiate a slideshow
        $slideshow = new WebSlideshow;

        // create a array of slides with a single slide that is missing the 'filename' index
        $photosToDisplay = [
            [
                WebSlideshow::SLIDE_VIRTUAL_LOCATION_KEY => '',
                WebSlideshow::SLIDE_FILENAME_KEY => 'someFilename.jpg'
            ],
            [
                WebSlideshow::SLIDE_VIRTUAL_LOCATION_KEY => '/some/virtual/location',
                WebSlideshow::SLIDE_FILENAME_KEY => 'someFilename.jpg'
            ],
        ];

        // assert that this will still return some HTML
        $htmlReturned = $this->invokeMethod($slideshow, WebSlideshowTest::FUNCTION_NAME_BUILDSLIDESHTML, [$photosToDisplay]);
        $this->assertNotEmpty($htmlReturned);

        // assert that the HTML that is being built contains an image tag with the specified virtual location from the second (the valid) slide
        $this->assertStringContainsString("img src=\"" . $photosToDisplay[1][WebSlideshow::SLIDE_VIRTUAL_LOCATION_KEY] . "\"", $htmlReturned);
    }
}
