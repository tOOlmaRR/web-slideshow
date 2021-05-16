<?php
namespace toolmarr\WebSlideshow\DAL;

use toolmarr\WebSlideshow\DAL\iEntity;

class TagEntity extends BaseEntity implements iEntity
{
    // private members
    public $tagID;
    public $tag;
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