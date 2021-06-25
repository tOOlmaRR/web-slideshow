<?php
namespace toolmarr\WebSlideshow\DAL;

use toolmarr\WebSlideshow\DAL\iEntity;

class TaggedImageEntity extends BaseEntity implements iEntity
{
    // properties
    public $imageID;
    public $tagID;
    
    
    
    // methods
    public function get()
    {
        throw new \Exception("Function has not been implemented");
    }
    
    public function insert()
    {
        // set up the query
        $db = $this->getDB();
        if ($this->getUseSPROCs()) {
            $sproc = $this->getSPROCs()["insert"]["taggedImage"];
            $sql = "EXEC [$sproc] :imageID, :tagID";
            $insertStatement = $db->prepare($sql);
            $insertStatement->bindParam(":imageID", $this->imageID);
            $insertStatement->bindParam(":tagID", $this->tagID);
        } else {
            throw new \Exception("This application only supports the use of SPROCs for database queries!");
        }
        
        // perform the insert
        return $insertStatement->execute();
    }
}