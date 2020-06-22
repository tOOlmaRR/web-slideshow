<?php  declare(strict_types=1);
namespace toolmarr\WebSlideshowTests;

trait TestHelpers
{
    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     * @see https://jtreminio.com/blog/unit-testing-tutorial-part-iii-testing-protected-private-methods-coverage-reports-and-crap/
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    public function createTestFilesAndFolders(array $testFolders, array $testPhotos = []) : void
    {
        foreach ($testFolders as $testFolder) {
            echo PHP_EOL;
            echo 'Creating the following folder: ' . $testFolder . PHP_EOL;

            if (!\is_dir($testFolder)) {
                mkdir($testFolder);
            }
        }

        foreach ($testPhotos as $testPhoto) {
            echo PHP_EOL;
            echo 'Creating the following file: ' . $testPhoto . PHP_EOL;

            if (!\file_exists($testPhoto)) {
                fopen($testPhoto, "w");
            }
        }
    }

    public function destroyTestFilesAndFolders(array $testFolders, array $testPhotos = []) : void
    {
        foreach ($testPhotos as $testPhoto) {
            echo PHP_EOL;
            echo 'Destroying the following file: ' . $testPhoto . PHP_EOL;
            
            if (\file_exists($testPhoto)) {
                unlink($testPhoto);
            }
        }

        foreach ($testFolders as $testFolder) {
            echo PHP_EOL;
            echo 'Destroying the following folder: ' . $testFolder . PHP_EOL;

            if (\is_dir($testFolder)) {
                rmdir($testFolder);
            }
        }
    }
}
