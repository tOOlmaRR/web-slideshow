<?php
namespace toolmarr\WebSlideshow\DAL;

use toolmarr\WebSlideshow\DAL\iEntity;

class ManufacturerEntity extends BaseEntity implements iEntity
{
    // private members
    private $fullFilePath;
    private $fileName;
    private $originalFileName;
    private $width;
    private $height;
    private $orientation;
    private $secure;
    
    
    
    //  methods
    public function get($image)
    {
        throw new \Exception("Function has not been implemented");
    }
    
    public function insert($manufacturer)
    {
        return 1;
    }
}