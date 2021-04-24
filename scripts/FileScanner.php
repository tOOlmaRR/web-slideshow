<?php
namespace toolmarr\WebSlideshow;

class FileScanner
{
    public string $scanLog;

    public function __construct()
    {
        $this->scanLog = "Please paste in the full folder path to scan above and click SUBMIT to scan all images into the database";
    }

    public function scanFolders($inputs)
    {
        // pull data from the inputs
        $inputScanFolder = $inputs['folder'];
        $recurse = $inputs['recurse'];
        $tags = $inputs['tags'];

        $this->scanLog = "You requested to scan the following folder: " . $_POST['folder'];
        
        // if target folder doesn't exist (is not a directory), return
        if (!is_dir($inputScanFolder)) {
            $this->scanLog .= PHP_EOL . "ERROR: Requested folder does not exist or is not a folder!";
            return;
        }
        
        $this->scanLog .= PHP_EOL . "Requested folder has been found!";
        
        // Recursive folder scan?
        $recurse ? $this->scanLog .= PHP_EOL . "You requested to scan this folder and it's sub-folders." 
            : $this->scanLog .= PHP_EOL . "You requested to scan only this folder.";
        
        // Tag images?
        !empty($tags) ? $this->scanLog .= PHP_EOL . "You requested to add the following tags to all scanned images: " . $tags 
            : $this->scanLog .= PHP_EOL . "You did not request to add tags to any scanned images: ";
        
        if ($recurse) {
            $this->scanFolderAndSubFolders($inputs);
        } else {
            $this->scanSingleFolder($inputs);
        }
    }

    public function scanFolderAndSubFolders(array $inputs)
    {
        $this->scanLog .= PHP_EOL . "Beginning Recursive Scan...";
        $objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($inputs['folder']), \RecursiveIteratorIterator::SELF_FIRST);
        $filesToProcess = array();
        foreach ($objects as $name => $object) {
            // determine current image properties; ignore anything that doesn't appear to be an image, but also handle test images for unit testing
            if ($object->getFilename() == WebSlideshow::TEST_PUBLIC_PHOTO || @list($width, $height) = getimagesize($name)) {
                $filesToProcess[] = $name;
                $this->scanLog .= PHP_EOL . "Processing: " . $name;
            } else {
                $this->scanLog .= PHP_EOL . "Ignoring: " . $name;
            }
        }
    }

    public function scanSingleFolder(array $inputs)
    {
        $this->scanLog .= PHP_EOL . "Beginning Single Folder Scan...";
        $allPhotos = scandir($inputs['folder']);
        for ($i = 0; $i < count($allPhotos); $i++) {
            // determine current image properties; ignore anything that doesn't appear to be an image, but also handle test images for unit testing
            $filename = $allPhotos[$i];
            $fullFilePath = $inputs['folder'] . '\\' . $filename;
            if ($fullFilePath == WebSlideshow::TEST_PUBLIC_PHOTO || @list($width, $height) = getimagesize($fullFilePath)) {
                $filesToProcess[] = $fullFilePath;
                $this->scanLog .= PHP_EOL . "Processing: " . $fullFilePath;
            } else {
                $this->scanLog .= PHP_EOL . "Ignoring: " . $fullFilePath;
            }
        }
    }
}