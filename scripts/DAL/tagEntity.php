<?php
namespace toolmarr\WebSlideshow\DAL;

use toolmarr\WebSlideshow\DAL\IEntity;

class TagEntity extends BaseEntity implements IEntity
{
    // properties
    public $tagID;
    public $tag;
    public bool $secure;
    
    
    
    // methods
    public function get()
    {
        // set up the query
        $db = $this->getDB();
        if ($this->getUseSPROCs()) {
            $sproc = $this->getSPROCs()["select"]["tag"];
            $sql = "EXEC [$sproc] @ID=:id, @tag=:tag";
            $sqlParams = [
                ":id" => $this->tagID >= 0 ? $this->tagID : null,
                ":tag" => !empty($this->tag) ? $this->tag : null
            ];
        } else {
            throw new \Exception("This application only supports the use of SPROCs for database queries!");
        }
        $getStatement = $db->prepare($sql);
        
        // perform the select and retrieve the data
        $getStatement->execute($sqlParams);
        $row = $getStatement->fetch();
        
        // build/return a business object based on the returned data
        if ($row) {
            $this->tagID = $row["TagID"];
            $this->tag = $row["Tag"];
            $this->secure = $row["Secure"] === '1' ? true : false;
            return true;
        } else {
            return false;
        }
    }
    
    public function insert() : int
    {
        // set up the query
        $db = $this->getDB();
        if ($this->getUseSPROCs()) {
            $sproc = $this->getSPROCs()["insert"]["tag"];
            $sql = "EXEC [$sproc] :id, :tag, :secure";
            $insertStatement = $db->prepare($sql);
            $insertStatement->bindParam(":id", $newID, \PDO::PARAM_INT, 10);
            $insertStatement->bindParam(":tag", $this->tag);
            $insertStatement->bindParam(":secure", $this->secure);
        } else {
            throw new \Exception("This application only supports the use of SPROCs for database queries!");
        }
        
        // perform the insert
        $insertStatement->execute();
        
        // capture and return the new rows autoincremented ID
        if (!$this->getUseSPROCs()) {
            $newID = $db->lastInsertId();
        }
        if ($newID == 0) {
            $newID = null;
        }
        return $newID;
    }
}