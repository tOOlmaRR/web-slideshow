<?php
namespace toolmarr\WebSlideshow\DAL;

interface iEntity
{
    public function get($objectToFind);
    
    public function insert($objectToInsert);
}