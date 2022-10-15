<?php declare(strict_types=1);
namespace toolmarr\WebSlideshowTests;

use PHPUnit\Framework\TestCase;
use toolmarr\WebSlideshow\WebSlideshow;
use toolmarr\WebSlideshowTests\TestHelpers;

/**
 * @testdox A WebSlideshow object
 */
final class WebSlideshowTest extends TestCase
{
    use TestHelpers;

    const FUNCTION_NAME_BUILDSLIDESHTML = 'buildSlidesHtml';
    const FUNCTION_NAME_DETERMINEPHOTOSTODISPLAYFORPATH = 'determinePhotosToDisplayForPath';

    const TEST_PUBLIC_FOLDER = 'publicPhotosTestFolder' . DIRECTORY_SEPARATOR;
    const TEST_PRIVATE_FOLDER = 'privatePhotosTestFolder' . DIRECTORY_SEPARATOR;
    const TEST_PUBLIC_SUBFOLDER = 'publicSubFolder' . DIRECTORY_SEPARATOR;
    const TEST_PUBLIC_PHOTO = 'testPhoto.png';

    /**
     * @test
     * @group Constructor
     * @testdox Constructor should return an object that is an instance of the WebSlideshow class
     */
    public function constructor_noParametersCreatesAnObject(): void
    {
        // assert that the constructor without any parameters will instantiate an object
        $this->assertIsObject(new WebSlideshow(500));

        // assert that the constructor without any parameters will instantiate a WebSlideshow object
        $this->assertInstanceOf(WebSlideshow::class, new WebSlideshow(500));
    }

    /**
     * @test
     * @group determinePhotosToDisplayForPath
     * @testdox When there are no valid photos in the specified location (ie. public folder), 
     *      the determinePhotosToDisplayForPath method should return an empty array
     * @testWith ["/myPhotos/", false]
     */
    public function determinePhotosToDisplayForPath_noRecurse_noPhotos(string $virtualRoot, bool $includeSubFolders): void
    {
        // instantiate a slideshow
        $slideshow = new WebSlideshow(500);

        // create test folders
        $testPublicFolder_fullPath = __DIR__ . DIRECTORY_SEPARATOR . WebSlideshowTest::TEST_PUBLIC_FOLDER;
        $testPrivateFolder_fullPath = __DIR__ . DIRECTORY_SEPARATOR . WebSlideshowTest::TEST_PRIVATE_FOLDER;
        $this->createTestFilesAndFolders([$testPublicFolder_fullPath, $testPrivateFolder_fullPath]);
        
        // set up inputs
        $slideshowPath = WebSlideshowTest::TEST_PUBLIC_FOLDER;
        $rootFolder = __DIR__ . DIRECTORY_SEPARATOR;
        $inputs = [$slideshowPath, $rootFolder, $virtualRoot, $includeSubFolders];

        // invoke the function
        $photosReturned = $this->invokeMethod($slideshow, WebSlideshowTest::FUNCTION_NAME_DETERMINEPHOTOSTODISPLAYFORPATH, $inputs);
        $this->assertEmpty($photosReturned);

        // destroy test folders
        $this->destroyTestFilesAndFolders([$testPublicFolder_fullPath, $testPrivateFolder_fullPath]);
    }

    /**
     * @test
     * @group determinePhotosToDisplayForPath
     * @testdox When there is a valid photo in the specified location (ie. public folder), 
     *      the determinePhotosToDisplayForPath method should return a non-empty array
     * @testWith ["/myPhotos/", false]
     */
    public function determinePhotosToDisplayForPath_noRecurse_onePhoto(string $virtualRoot, bool $includeSubFolders): void
    {
        // instantiate a slideshow
        $slideshow = new WebSlideshow(500);

        // create test folders and file
        $testPublicFolder_fullPath = __DIR__ . DIRECTORY_SEPARATOR . WebSlideshowTest::TEST_PUBLIC_FOLDER;
        $testPrivateFolder_fullPath = __DIR__ . DIRECTORY_SEPARATOR . WebSlideshowTest::TEST_PRIVATE_FOLDER;
        $testPhoto_fullPath = __DIR__ . DIRECTORY_SEPARATOR . WebSlideshowTest::TEST_PUBLIC_FOLDER . DIRECTORY_SEPARATOR . WebSlideshowTest::TEST_PUBLIC_PHOTO;
        $this->createTestFilesAndFolders([$testPublicFolder_fullPath, $testPrivateFolder_fullPath], [$testPhoto_fullPath]);

        // set up inputs
        $slideshowPath = WebSlideshowTest::TEST_PUBLIC_FOLDER;
        $rootFolder = __DIR__ . DIRECTORY_SEPARATOR;
        $inputs = [$slideshowPath, $rootFolder, $virtualRoot, $includeSubFolders];

        // invoke the function
        $photosReturned = $this->invokeMethod($slideshow, WebSlideshowTest::FUNCTION_NAME_DETERMINEPHOTOSTODISPLAYFORPATH, $inputs);
        $this->assertNotEmpty($photosReturned);

        // destroy test folders
        $this->destroyTestFilesAndFolders([$testPublicFolder_fullPath, $testPrivateFolder_fullPath], [$testPhoto_fullPath]);
    }


    
    /**
     * @test
     * @group buildSlidesHtml
     * @testdox When an empty array (ie. there are no photos to build HTML for) is received by the buildSlidesHtml method, 
     *      an empty string should be returned
     * @testWith [[]]
     */
    public function buildSlidesHtml_emptyPhotosArray(?array $photosToDisplay): void
    {
        // instantiate a slideshow
        $slideshow = new WebSlideshow(500);
        
        // assert that this will return no HTML
        $htmlReturned = $this->invokeMethod($slideshow, WebSlideshowTest::FUNCTION_NAME_BUILDSLIDESHTML, [$photosToDisplay]);
        $this->assertEmpty($htmlReturned);
    }

    /**
     * @test
     * @group buildSlidesHtml
     * @testdox When a NULL input is received by the buildSlidesHtml method,
     *      a TypeError should be raised
     * @testWith [null]
     */
    public function buildSlidesHtml_nullPhotosArray(?array $photosToDisplay): void
    {
        // instantiate a slideshow
        $slideshow = new WebSlideshow(500);

        // assert that this will raise an TypeError
        $this->expectException(\TypeError::class);
        $this->invokeMethod($slideshow, WebSlideshowTest::FUNCTION_NAME_BUILDSLIDESHTML, [$photosToDisplay]);
    }

    /**
     * @test
     * @group buildSlidesHtml
     * @testdox When a single valid photo is received by the buildSlidesHtml method, 
     *      a non-empty string should be returned, 
     *      and it should contain an HTML image tag with the specified virtual path
     * @testWith [[{"filepath":"/some/filepath/", "filename":"someFilename.jpg", "virtualLocation":"/some/virtual/location", "height":"500", "width":"500", "originalHeight":"250", "originalWidth":"250"}]]
     */
    public function buildSlidesHtml_singleValidPhoto(array $photosToDisplay): void
    {
        // instantiate a slideshow
        $slideshow = new WebSlideshow(500);

        // assert that this will return a non-empty string
        $htmlReturned = $this->invokeMethod($slideshow, WebSlideshowTest::FUNCTION_NAME_BUILDSLIDESHTML, [$photosToDisplay]);
        $this->assertNotEmpty($htmlReturned);
        $this->assertIsString($htmlReturned);

        // assert that the HTML that is being built contains an image tag with the specified virtual location
        $this->assertStringContainsString("src=\"" . $photosToDisplay[0][WebSlideshow::SLIDE_VIRTUAL_LOCATION_KEY] . "\"", $htmlReturned);
    }

    /**
     * @test
     * @group buildSlidesHtml
     * @testdox When the buildSlidesHtml method receives an array that does not contain the 'virtualLocation' index, 
     *      an empty string should be returned
     * @testWith [[{"filename":"someFilename.jpg"}]]
     */
    public function buildSlidesHtml_missingVirtualLocationIndex(array $photosToDisplay): void
    {
        // instantiate a slideshow
        $slideshow = new WebSlideshow(500);

        // assert that this will return an empty string
        $htmlReturned = $this->invokeMethod($slideshow, WebSlideshowTest::FUNCTION_NAME_BUILDSLIDESHTML, [$photosToDisplay]);
        $this->assertEmpty($htmlReturned);
    }

    /**
     * @test
     * @group buildSlidesHtml
     * @testdox When the buildSlidesHtml method receives an array that does not contain the 'filename' index, 
     *      an empty string should be returned
     * @testWith [[{"virtualLocation":"/some/virtual/location"}]]
     */
    public function buildSlidesHtml_missingFilenameIndex(array $photosToDisplay): void
    {
        // instantiate a slideshow
        $slideshow = new WebSlideshow(500);

        // assert that this will return an empty string
        $htmlReturned = $this->invokeMethod($slideshow, WebSlideshowTest::FUNCTION_NAME_BUILDSLIDESHTML, [$photosToDisplay]);
        $this->assertEmpty($htmlReturned);
    }

    /**
     * @test
     * @group buildSlidesHtml
     * @testDox When the buildSlidesHtml method receives a properly formed array, 
     *      but the 'virtualLocation' index is empty,
     *      an empty string should be returned
     * @testWith [[{"filename":"someFilename.jpg", "virtualLocation":""}]]
     */
    public function buildSlidesHtml_missingVirtualLocationData(array $photosToDisplay): void
    {
        // instantiate a slideshow
        $slideshow = new WebSlideshow(500);

        // assert that this will return an empty string
        $htmlReturned = $this->invokeMethod($slideshow, WebSlideshowTest::FUNCTION_NAME_BUILDSLIDESHTML, [$photosToDisplay]);
        $this->assertEmpty($htmlReturned);
    }

    /**
     * @test
     * @group buildSlidesHtml
     * @testDox When the buildSlidesHtml method receives a properly formed array,
     *      but the 'filename' index is empty,
     *      an empty string should be returned
     * @testWith [[{"filename":"", "virtualLocation":"/some/virtual/location"}]]
     */
    public function buildSlidesHtml_missingFilenameData(array $photosToDisplay): void
    {
        // instantiate a slideshow
        $slideshow = new WebSlideshow(500);

        // assert that this will return an empty string
        $htmlReturned = $this->invokeMethod($slideshow, WebSlideshowTest::FUNCTION_NAME_BUILDSLIDESHTML, [$photosToDisplay]);
        $this->assertEmpty($htmlReturned);
    }

    /**
     * @test
     * @group buildSlidesHtml
     * @testDox When the buildSlidesHtml method receives an invalid slide before a valid one,
     *      it should still continue processing after the invalid slide
     *      and return HTML for the valid one
     * @testWith [[{"filepath":"", "filename":"someFilename.jpg", "virtualLocation":""}, {"filepath":"/some/filepath/", "filename":"someFilename.jpg", "virtualLocation":"/some/virtual/location", "height":"500", "width":"500", "originalHeight":"250", "originalWidth":"250"}]]
     */
    public function buildSlidesHtml_invalidSlideDoesNotAbortHtmlBuilding(array $photosToDisplay): void
    {
        // instantiate a slideshow
        $slideshow = new WebSlideshow(500);

        // assert that this will still return some HTML
        $htmlReturned = $this->invokeMethod($slideshow, WebSlideshowTest::FUNCTION_NAME_BUILDSLIDESHTML, [$photosToDisplay]);
        $this->assertNotEmpty($htmlReturned);

        // assert that the HTML that is being built contains an image tag with the specified virtual location from the second (the valid) slide
        $this->assertStringContainsString("src=\"" . $photosToDisplay[1][WebSlideshow::SLIDE_VIRTUAL_LOCATION_KEY] . "\"", $htmlReturned);
    }
}
