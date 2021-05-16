<?php
namespace toolmarr\WebSlideshow\DAL;

use toolmarr\WebSlideshow\DAL\iEntity;

class TaggedImageEntity extends BaseEntity implements iEntity
{
    // private members
    public $imageID;
    public $tagID;
    
    
    
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