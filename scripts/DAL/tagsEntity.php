<?php
namespace toolmarr\WebSlideshow\DAL;

use toolmarr\WebSlideshow\DAL\IEntity;
use toolmarr\WebSlideshow\DAL\TagEntity;

class TagsEntity extends BaseEntity implements IEntity
{
    // properties - inputs    
    public $imageID;
    public $includeSecureTags;
    
    // properties - output
    public $tags = [];
    
    
    
    // methods
    public function get()
    {
        // set up the query
        $db = $this->getDB();
        if ($this->getUseSPROCs()) {
            $sproc = $this->getSPROCs()["select"]["tags"];
            $sql = "EXEC [$sproc] @imageID=:imageID, @secureTags=:secure";
            $sqlParams = [
                ":imageID" => $this->imageID,
                ":secure" => $this->includeSecureTags
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
            $tag = new TagEntity($this->getDB(), true);
            $tag->tagID = $row["TagID"];
            $tag->tag = $row["Tag"];
            $tag->secure = $row["Secure"] === '1' ? true : false;
            $this->tags[$row["Tag"]] = $tag;
        }
        return true;
    }
    
    public function insert() : int
    {
        throw new \Exception("This has not been implemneted yet");
    }
}