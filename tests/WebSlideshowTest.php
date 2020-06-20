<?php declare(strict_types=1);
namespace toolmarr\WebSlideshowTests;

use PHPUnit\Framework\TestCase;
use toolmarr\WebSlideshow\WebSlideshow;

final class WebSlideshowTest extends TestCase
{
    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

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
