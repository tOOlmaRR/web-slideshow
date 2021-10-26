<?php
namespace toolmarr\WebSlideshow\DAL;

use toolmarr\WebSlideshow\DAL\IEntity;

class ImagesEntity extends BaseEntity implements IEntity
{
    // properties - inputs
    public $tag;
    public $includeSecureImages = 0;
    
    // properties - output
    public $images = [];
    

    
    // methods
    public function get()
    {
        // set up the query
        $db = $this->getDB();
        if ($this->getUseSPROCs()) {
            $sproc = $this->getSPROCs()["select"]["images"];
            $sql = "EXEC [$sproc] @tag=:tag, @secureImages=:secure";
            $sqlParams = [
                ":tag" => $this->tag,
                ":secure" => $this->includeSecureImages
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
            $image = new ImageEntity($this->getDB(), true);
            $image->imageID = $row["ImageID"];
            $image->fullFilePath = $row["FullFilePath"];
            $image->fileName = $row["FileName"];
            $image->width = $row["width"];
            $image->height = $row["height"];
            $image->secure = $row["Secure"] === '1' ? true : false;
            $this->images[$row["ImageID"]] = $image;
        }
        return true;
    }
    
    public function insert() : int
    {
        throw new \Exception("This has not been implemneted yet");
    }
}