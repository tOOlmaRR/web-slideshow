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
        $allSuccess = true;
        foreach ($testFolders as $testFolder) {
            if (!is_dir($testFolder)) {
                $success = mkdir($testFolder);
            }
            if ($allSuccess) $allSuccess = $success;
        }

        foreach ($testPhotos as $testPhoto) {
            if (!file_exists($testPhoto)) {
                $success = fopen($testPhoto, "w");
            }
            if ($allSuccess && !$success) $allSuccess = false;
        }
        
        // TODO: catch when these operations fail and return a false
        return true;
    }

    public function destroyTestFilesAndFolders(array $testFolders, array $testPhotos = []) : bool
    {
        $allSuccess = true;
        foreach ($testPhotos as $testPhoto) {
            if (file_exists($testPhoto)) {
                $success = unlink($testPhoto);
                if ($allSuccess) $allSuccess = $success;
            }
        }

        foreach ($testFolders as $testFolder) {
            if (is_dir($testFolder)) {
                $success = rmdir($testFolder);
                if ($allSuccess) $allSuccess = $success;
            }
        }
        return $allSuccess;
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
