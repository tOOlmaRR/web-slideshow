<?php
namespace toolmarr\WebSlideshow\DAL;

use toolmarr\WebSlideshow\DAL\iEntity;

class ImageEntity extends BaseEntity implements iEntity
{
    // private members
    public $imageID;
    public $fullFilePath;
    public $fileName;
    public $originalFileName;
    public $width;
    public $height;
    public $orientation;
    public $secure;
    
    
    
    //  methods
    public function get($image)
    {
        throw new \Exception("Function has not been implemented");
    }
    
    public function insert()
    {
        return 1;
    }
}