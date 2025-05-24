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

    const TEMP_TEST_FILES_FOLDER = 'TempTestFiles' . DIRECTORY_SEPARATOR;
    const TEMP_TEST_FILES_PATH = __DIR__ . DIRECTORY_SEPARATOR . WebSlideshowTest::TEMP_TEST_FILES_FOLDER;
    const TEST_PUBLIC_FOLDER = 'publicPhotosTestFolder' . DIRECTORY_SEPARATOR;
    const TEST_PRIVATE_FOLDER = 'privatePhotosTestFolder' . DIRECTORY_SEPARATOR;
    const TEST_PUBLIC_SUBFOLDER = 'publicSubFolder' . DIRECTORY_SEPARATOR;
    const TEST_PUBLIC_PHOTO1 = 'testPhoto1.png';
    const TEST_PUBLIC_PHOTO2 = 'testPhoto2.png';



    /**
     * Summary.
     * Execute startup scripts before every test
     * 
     * Description.
     * Creates a test folder for all tests to use
     */
    protected function setUp(): void
    {
        // the test folder should not exist... if it does, we might have junk test data, so skip test
        $testFolder = __DIR__ . DIRECTORY_SEPARATOR . WebSlideshowTest::TEMP_TEST_FILES_FOLDER;
        if (is_dir($testFolder)) {
            $this->markTestSkipped(
              'Test folder already exists... Aborting test to ensure testing is clean'
            );
        }

        $success = $this->createTestFilesAndFolders([$testFolder]);
        if (!$success) {
            $this->markTestSkipped(
              'Failed to create test folder... Cannot run this test without it'
            );
        }
    }
    
    /**
     * Summary.
     * Execute clean up scripts after every test
     * 
     * Description.
     * Removes the test folder, along with all of it's contents, after every test, leaving a clean slate for the next
     */
    protected function tearDown(): void
    {
        $success = $this->destroyFolders([WebSlideshowTest::TEMP_TEST_FILES_PATH]);
        if (!$success) {
            $this->markTestIncomplete(
              'Failed to delete test folder and/or files within it... Proceeding tests may fail'
            );
        }
    }
    


/********** Constructor Tests **********/

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


/********** determinePhotosToDisplayForPath Tests **********/

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
        $testPublicFolder_fullPath = WebSlideshowTest::TEMP_TEST_FILES_PATH . WebSlideshowTest::TEST_PUBLIC_FOLDER;
        $testPrivateFolder_fullPath = WebSlideshowTest::TEMP_TEST_FILES_PATH . WebSlideshowTest::TEST_PRIVATE_FOLDER;
        $this->createTestFilesAndFolders([$testPublicFolder_fullPath, $testPrivateFolder_fullPath]);
        
        // set up inputs
        $slideshowPath = WebSlideshowTest::TEST_PUBLIC_FOLDER;
        $rootFolder = WebSlideshowTest::TEMP_TEST_FILES_PATH;
        $inputs = [$slideshowPath, $rootFolder, $virtualRoot, $includeSubFolders];

        // invoke the function and test assertions
        $photosReturned = $this->invokeMethod($slideshow, WebSlideshowTest::FUNCTION_NAME_DETERMINEPHOTOSTODISPLAYFORPATH, $inputs);
        $this->assertDirectoryExists($rootFolder.$slideshowPath);
        $this->assertEmpty($photosReturned);
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
        $testPublicFolder_fullPath = WebSlideshowTest::TEMP_TEST_FILES_PATH . WebSlideshowTest::TEST_PUBLIC_FOLDER;
        $testPrivateFolder_fullPath = WebSlideshowTest::TEMP_TEST_FILES_PATH . WebSlideshowTest::TEST_PRIVATE_FOLDER;
        $testPhoto1_fullPath = WebSlideshowTest::TEMP_TEST_FILES_PATH . WebSlideshowTest::TEST_PUBLIC_FOLDER . DIRECTORY_SEPARATOR . WebSlideshowTest::TEST_PUBLIC_PHOTO1;
        $this->createTestFilesAndFolders([$testPublicFolder_fullPath, $testPrivateFolder_fullPath], [$testPhoto1_fullPath]);

        // set up inputs
        $slideshowPath = WebSlideshowTest::TEST_PUBLIC_FOLDER;
        $rootFolder = WebSlideshowTest::TEMP_TEST_FILES_PATH;
        $inputs = [$slideshowPath, $rootFolder, $virtualRoot, $includeSubFolders];

        // invoke the function and test assertions
        $photosReturned = $this->invokeMethod($slideshow, WebSlideshowTest::FUNCTION_NAME_DETERMINEPHOTOSTODISPLAYFORPATH, $inputs);
        $this->assertNotEmpty($photosReturned);
    }

    /**
     * @test
     * @group determinePhotosToDisplayForPath
     * @testdox When there is are 2 valid photos in the specified location (ie. public folder),
     *      the determinePhotosToDisplayForPath method should return an array with 2 different elements
     * @testWith ["/myPhotos/", false]
     */
    public function determinePhotosToDisplayForPath_noRecurse_twoPhotos(string $virtualRoot, bool $includeSubFolders): void
    {
        // instantiate a slideshow
        $slideshow = new WebSlideshow(500);

        // create test folders and file
        $testPublicFolder_fullPath = WebSlideshowTest::TEMP_TEST_FILES_PATH . WebSlideshowTest::TEST_PUBLIC_FOLDER;
        $testPrivateFolder_fullPath = WebSlideshowTest::TEMP_TEST_FILES_PATH . WebSlideshowTest::TEST_PRIVATE_FOLDER;
        $testPhoto1_fullPath = WebSlideshowTest::TEMP_TEST_FILES_PATH . WebSlideshowTest::TEST_PUBLIC_FOLDER . DIRECTORY_SEPARATOR . WebSlideshowTest::TEST_PUBLIC_PHOTO1;
        $testPhoto2_fullPath = WebSlideshowTest::TEMP_TEST_FILES_PATH . WebSlideshowTest::TEST_PUBLIC_FOLDER . DIRECTORY_SEPARATOR . WebSlideshowTest::TEST_PUBLIC_PHOTO2;
        $this->createTestFilesAndFolders([$testPublicFolder_fullPath, $testPrivateFolder_fullPath], [$testPhoto1_fullPath, $testPhoto2_fullPath]);

        // set up inputs
        $slideshowPath = WebSlideshowTest::TEST_PUBLIC_FOLDER;
        $rootFolder = WebSlideshowTest::TEMP_TEST_FILES_PATH;
        $inputs = [$slideshowPath, $rootFolder, $virtualRoot, $includeSubFolders];

        // invoke the function
        $photosReturned = $this->invokeMethod($slideshow, WebSlideshowTest::FUNCTION_NAME_DETERMINEPHOTOSTODISPLAYFORPATH, $inputs);
        $this->assertNotEmpty($photosReturned);
        $this->assertCount(2, $photosReturned);
    }

    /**
     * @test
     * @group determinePhotosToDisplayForPath
     * @testdox When a slideshow is configured for a folder that doesn't exist, not photos are returned, but no other errors happen either
     * @testWith ["/myPhotos/", false]
     */
    public function determinePhotosToDisplayForPath_noRecurse_invalidFolder(string $virtualRoot, bool $includeSubFolders): void
    {
        // instantiate a slideshow
        $slideshow = new WebSlideshow(500);

        // create test folders and file
        $testPublicFolder_fullPath = WebSlideshowTest::TEMP_TEST_FILES_PATH . WebSlideshowTest::TEST_PUBLIC_FOLDER;
        $testPrivateFolder_fullPath = WebSlideshowTest::TEMP_TEST_FILES_PATH . WebSlideshowTest::TEST_PRIVATE_FOLDER;
        $testPhoto1_fullPath = WebSlideshowTest::TEMP_TEST_FILES_PATH . WebSlideshowTest::TEST_PUBLIC_FOLDER . DIRECTORY_SEPARATOR . WebSlideshowTest::TEST_PUBLIC_PHOTO1;
        $testPhoto2_fullPath = WebSlideshowTest::TEMP_TEST_FILES_PATH . WebSlideshowTest::TEST_PUBLIC_FOLDER . DIRECTORY_SEPARATOR . WebSlideshowTest::TEST_PUBLIC_PHOTO2;
        $this->createTestFilesAndFolders([$testPublicFolder_fullPath, $testPrivateFolder_fullPath], [$testPhoto1_fullPath, $testPhoto2_fullPath]);

        // set up inputs
        $slideshowPath = WebSlideshowTest::TEST_PUBLIC_FOLDER . "ZZZ";
        $rootFolder = WebSlideshowTest::TEMP_TEST_FILES_PATH;
        $inputs = [$slideshowPath, $rootFolder, $virtualRoot, $includeSubFolders];

        // invoke the function
        $photosReturned = $this->invokeMethod($slideshow, WebSlideshowTest::FUNCTION_NAME_DETERMINEPHOTOSTODISPLAYFORPATH, $inputs);
        $this->assertDirectoryDoesNotExist($rootFolder.$slideshowPath);
        $this->assertEmpty($photosReturned);        
    }

    /**
     * @test
     * @group determinePhotosToDisplayForPath
     * @testdox When a slideshow is configured to not recursively scan subfolders, it shouldn't pick up any images within subfolders
     * @testWith ["/myPhotos/", false]
     */
    public function determinePhotosToDisplayForPath_noRecurse_doesNotRecurse(string $virtualRoot, bool $includeSubFolders): void
    {
        // instantiate a slideshow
        $slideshow = new WebSlideshow(500);

        // create test folders and file
        $testPublicFolder_fullPath = WebSlideshowTest::TEMP_TEST_FILES_PATH . WebSlideshowTest::TEST_PUBLIC_FOLDER;
        $testPublicSubFolder_fullPath = WebSlideshowTest::TEMP_TEST_FILES_PATH . WebSlideshowTest::TEST_PUBLIC_FOLDER . WebSlideshowTest::TEST_PUBLIC_SUBFOLDER;
        $testPrivateFolder_fullPath = WebSlideshowTest::TEMP_TEST_FILES_PATH . WebSlideshowTest::TEST_PRIVATE_FOLDER;
        $testPhoto1_fullPath = WebSlideshowTest::TEMP_TEST_FILES_PATH . WebSlideshowTest::TEST_PUBLIC_FOLDER . WebSlideshowTest::TEST_PUBLIC_SUBFOLDER . WebSlideshowTest::TEST_PUBLIC_PHOTO1;

        $this->createTestFilesAndFolders([$testPublicFolder_fullPath, $testPublicSubFolder_fullPath, $testPrivateFolder_fullPath], [$testPhoto1_fullPath]);

        // set up inputs
        $slideshowPath = WebSlideshowTest::TEST_PUBLIC_FOLDER;
        $rootFolder = WebSlideshowTest::TEMP_TEST_FILES_PATH;
        $inputs = [$slideshowPath, $rootFolder, $virtualRoot, $includeSubFolders];

        // invoke the function
        $photosReturned = $this->invokeMethod($slideshow, WebSlideshowTest::FUNCTION_NAME_DETERMINEPHOTOSTODISPLAYFORPATH, $inputs);
        $this->assertDirectoryExists($rootFolder.$slideshowPath);
        $this->assertEmpty($photosReturned);
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
