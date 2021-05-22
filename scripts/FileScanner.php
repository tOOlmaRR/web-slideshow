<?php
namespace toolmarr\WebSlideshow;

use toolmarr\WebSlideshow\DAL\EntityFactory;

class FileScanner
{
    public string $scanLog;

    public function __construct()
    {
        $this->scanLog = "Please paste in the full folder path to scan above and click SUBMIT to scan all images into the database";
    }

    public function scanFolders(array $inputs, array $config)
    {
        // pull data from the inputs
        $inputScanFolder = $inputs['folder'];
        $secureImages = $inputs['secureImages'];
        $recurse = $inputs['recurse'];
        $tags = $inputs['tags'];
        $secureTags = $inputs['secureTags'];

        $this->scanLog = "You requested to scan the following folder: " . $_POST['folder'];
        
        // if target folder doesn't exist (is not a directory), return
        if (!is_dir($inputScanFolder)) {
            $this->scanLog .= PHP_EOL . "ERROR: Requested folder does not exist or is not a folder!";
            return;
        }
        $this->scanLog .= PHP_EOL . "Requested folder has been found!";

        // build tag list from input
        $rawTagList = explode(',', $tags);
        foreach ($rawTagList as $rawTag) {
            $tagList[] = trim($rawTag, ' ');
        }
        $inputs['tags'] = $tagList;

        // reinterpret the checkboxes and radio buttons as bits
        $inputs['secureImages'] = $inputs['secureImages'] == 'on' ? 1 : 0;
        $inputs['secureTags'] = $inputs['secureTags'] == 'on' ? 1 : 0;
        
        // Recursive folder scan?
        $recurse ? $this->scanLog .= PHP_EOL . "You requested to scan this folder and it's sub-folders." 
            : $this->scanLog .= PHP_EOL . "You requested to scan only this folder.";

        // Secure Images?
        $secureImages ? $this->scanLog .= PHP_EOL . "You requested to keep the images secured." 
            : $this->scanLog .= PHP_EOL . "Images will be public.";
        
        // Tag images?
        !empty($tagList) ? $this->scanLog .= PHP_EOL . "You requested to add the following tags to all scanned images: " . implode(',', $tagList) 
            : $this->scanLog .= PHP_EOL . "You did not request to add tags to any scanned images: ";
        
        // Secure Tags?
        $secureTags ? $this->scanLog .= PHP_EOL . "You requested to keep the tags secured." 
            : $this->scanLog .= PHP_EOL . "Tags will be public.";
        
        if ($recurse) {
            $this->scanFolderAndSubFolders($inputs, $config);
        } else {
            $this->scanSingleFolder($inputs, $config);
        }
    }

    public function scanFolderAndSubFolders(array $inputs, array $config)
    {
        $this->scanLog .= PHP_EOL . "Beginning Recursive Scan...";
        $objects = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($inputs['folder']), \RecursiveIteratorIterator::SELF_FIRST);
        $filesToProcess = array();
        $entityFactory = new EntityFactory($config['database']);
        $db = $entityFactory->getDatabaseConnection();
        foreach ($objects as $name => $object) {
            // determine current image properties; ignore anything that doesn't appear to be an image, but also handle test images for unit testing
            if ($object->getFilename() == WebSlideshow::TEST_PUBLIC_PHOTO || @list($width, $height) = getimagesize($name)) {
                $this->scanLog .= PHP_EOL . "Processing: " . $name;
                //$db->beginTransaction();
                
                // build image
                $imageEntity = $entityFactory->getEntity('image');
                $imageEntity->fullFilePath = $name;
                $imageEntity->fileName = $object->getFilename();
                $imageEntity->originalFileName = $object->getFilename();
                $imageEntity->width = $width;
                $imageEntity->height = $height;
                $imageEntity->secure = $inputs['secureImages'];
                $newImageID = $imageEntity->insert();

                // build tag and mappings
                foreach ($inputs['tags'] as $tag) {
                    // get the tag if it already exists, or insert a new tag if it doesn't
                    $tagEntity = $entityFactory->getEntity('tag');
                    $tagEntity->tag = $tag;
                    $tagEntity->secure = $inputs['secureTags'];
                    $tagID = null;
                    if ($tagEntity->get()) {
                        $tagID = $tagEntity->tagID;
                    } else {
                        $tagID = $tagEntity->insert();
                    }

                    // associate the tag to the image
                    if ($tagID != null) {
                        $taggedImageEntity = $entityFactory->getEntity('taggedImage');
                        $taggedImageEntity->imageID = $newImageID;
                        $taggedImageEntity->tagID = $tagID;
                        $taggedImageEntity->insert();
                    }
                }
                //$db->commit();
            } else {
                $this->scanLog .= PHP_EOL . "Ignoring: " . $name;
            }
        }
    }

    public function scanSingleFolder(array $inputs, array $config)
    {
        $this->scanLog .= PHP_EOL . "Beginning Single Folder Scan...";
        $allPhotos = scandir($inputs['folder']);

        $entityFactory = new EntityFactory($config['database']);
        $db = $entityFactory->getDatabaseConnection();

        for ($i = 0; $i < count($allPhotos); $i++) {
            // determine current image properties; ignore anything that doesn't appear to be an image, but also handle test images for unit testing
            $filename = $allPhotos[$i];
            $fullFilePath = $inputs['folder'] . '\\' . $filename;
            if ($fullFilePath == WebSlideshow::TEST_PUBLIC_PHOTO || @list($width, $height) = getimagesize($fullFilePath)) {
                $this->scanLog .= PHP_EOL . "Processing: " . $fullFilePath;
                //$db->beginTransaction();
                
                // build image
                $imageEntity = $entityFactory->getEntity('image');
                $imageEntity->fullFilePath = $fullFilePath;
                $imageEntity->fileName = $filename;
                $imageEntity->originalFileName = $filename;
                $imageEntity->width = $width;
                $imageEntity->height = $height;
                $imageEntity->secure = $inputs['secureImages'];
                $newImageID = $imageEntity->insert();
                
                // build tag and mappings
                foreach ($inputs['tags'] as $tag) {
                    // get the tag if it already exists, or insert a new tag if it doesn't
                    $tagEntity = $entityFactory->getEntity('tag');
                    $tagEntity->tag = $tag;
                    $tagEntity->secure = $inputs['secureTags'];
                    $tagID = null;
                    if ($tagEntity->get()) {
                        $tagID = $tagEntity->tagID;
                    } else {
                        $tagID = $tagEntity->insert();
                    }

                    // associate the tag to the image
                    if ($tagID != null) {
                        $taggedImageEntity = $entityFactory->getEntity('taggedImage');
                        $taggedImageEntity->imageID = $newImageID;
                        $taggedImageEntity->tagID = $tagID;
                        $taggedImageEntity->insert();
                    }
                }
                //$db->commit();
            } else {
                $this->scanLog .= PHP_EOL . "Ignoring: " . $fullFilePath;
            }
        }
    }
}