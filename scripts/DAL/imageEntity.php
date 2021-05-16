<?php
namespace toolmarr\WebSlideshow\DAL;

use toolmarr\WebSlideshow\DAL\iEntity;

class ImageEntity extends BaseEntity implements iEntity
{
    // properties
    public $imageID;
    public $fullFilePath;
    public $fileName;
    public $originalFileName;
    public $width;
    public $height;
    public $secure;
    
    
    
    // methods
    public function get($image)
    {
        throw new \Exception("Function has not been implemented");
    }
    
    public function insert() : int
    {
        // set up the query
        $db = $this->getDB();
        if ($this->getUseSPROCs()) {
            $sproc = $this->getSPROCs()["insert"]["image"];
            $sql = "EXEC [$sproc] :id, :fullFilePath, :fileName, :originalFileName, :width, :height, :secure";
            $insertStatement = $db->prepare($sql);
            $insertStatement->bindParam(":id", $newID, \PDO::PARAM_INT, 10);
            $insertStatement->bindParam(":fullFilePath", $this->fullFilePath);
            $insertStatement->bindParam(":fileName", $this->fileName);
            $insertStatement->bindParam(":originalFileName", $this->originalFileName);
            $insertStatement->bindParam(":width", $this->width);
            $insertStatement->bindParam(":height", $this->height);
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