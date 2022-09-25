<?php
namespace toolmarr\WebSlideshow\DAL;

use toolmarr\WebSlideshow\DAL\IEntity;

class StaticSlideshowsEntity extends BaseEntity implements IEntity
{
    // properties - inputs
    public $includeSecureSlideshows = 0;
    
    // properties - outputs
    public $staticSlideshows = [];



    // methods
    public function get()
    {
        // set up the query
        $db = $this->getDB();
        if ($this->getUseSPROCs()) {
            $sproc = $this->getSPROCs()["select"]["staticSlideshows"];
            $sql = "EXEC [$sproc] @secureSlideshows=:secure";
            $sqlParams = [
                ":secure" => $this->includeSecureSlideshows
            ];
        } else {
            throw new \Exception("This application only supports the use of SPROCs for database queries!");
        }
        $getStatement = $db->prepare($sql);
        
        // perform the select and retrieve the data
        $getStatement->execute($sqlParams);
        $rows = $getStatement->fetchAll();
    
        // build/return a list of business objects based on the returned data
        foreach ($rows as $row) {
            $slideshow = new StaticSlideshowEntity($this->getDB(), true);
            $slideshow->staticSlideshowID = $row["StaticSlideshowID"];
            $slideshow->staticSlideshowName = $row["Name"];
            $slideshow->includeSecureImages = $row["Secure"];
            $this->staticSlideshows[$row["Name"]] = $slideshow;
            return true;
        }    
    }

    public function insert() : int
    {
        throw new \Exception("This has not been implemented yet");
    }
}