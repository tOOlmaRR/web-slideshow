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

    public function createTestFilesAndFolders(array $testFolders, array $testPhotos = []) : bool
    {
        foreach ($testFolders as $testFolder) {
            if (!\is_dir($testFolder)) {
                mkdir($testFolder);
            }
        }

        foreach ($testPhotos as $testPhoto) {
            if (!\file_exists($testPhoto)) {
                fopen($testPhoto, "w");
            }
        }
        
        // TODO: catch when these operations fail and return a false
        return true;
    }

    public function destroyTestFilesAndFolders(array $testFolders, array $testPhotos = []) : void
    {
        foreach ($testPhotos as $testPhoto) {
            if (\file_exists($testPhoto)) {
                unlink($testPhoto);
            }
        }

        foreach ($testFolders as $testFolder) {
            if (\is_dir($testFolder)) {
                rmdir($testFolder);
            }
        }
    }

    public function destroyFolders(array $testFolders) : bool
    {
        $success = true;
        foreach ($testFolders as $testFolder) {
            
            if (!is_dir($testFolder)) {
                $success = false; // Not a directory
                break;
            }

            $testFiles = array_diff(scandir($testFolder), ['.', '..']); // Exclude '.' and '..'
            foreach ($testFiles as $testFile) {
                $testFileWithPath = $testFolder . DIRECTORY_SEPARATOR . $testFile;
                is_dir($testFileWithPath) ? $this->destroyFolders([$testFileWithPath]) : unlink($testFileWithPath); // Recursively delete
            }

            $success = rmdir($testFolder); // Remove the now-empty directory
        }
        return $success;
    }
}
