<?php
namespace toolmarr\WebSlideshow\DAL;

use toolmarr\WebSlideshow\DAL\IEntity;

class ImageEntity extends BaseEntity implements IEntity
{
    // properties
    public $imageID;
    public $fullFilePath;
    public $fileName;
    public $originalFileName;
    public $width;
    public $height;
    public $secure;
    public $displayOrder;
    
    
    
    // methods
    public function get()
    {
        // set up the query
        $db = $this->getDB();
        if ($this->getUseSPROCs()) {
            $sproc = $this->getSPROCs()["select"]["image"];
            $sql = "EXEC [$sproc] @ID=:id, @fullFilePath=:path";
            $sqlParams = [
                ":id" => $this->imageID >= 0 ? $this->imageID : null,
                ":path" => !empty($this->fullFilePath) ? $this->fullFilePath : null
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
            $this->imageID = $row["ImageID"];
            $this->fullFilePath = $row["FullFilePath"];
            $this->fileName = $row["FileName"];
            $this->originalFileName = $row["OriginalFileName"];
            $this->width = $row["Width"];
            $this->height = $row["Height"];
            $this->secure = $row["Secure"];
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
            $sproc = $this->getSPROCs()["insert"]["image"];
            $sql = "EXEC [$sproc] :id, :fullFilePath, :fileName, :originalFileName, :width, :height, :secure";
            $insertStatement = $db->prepare($sql);
            $insertStatement->bindParam(":id", $newID, \PDO::PARAM_INT, 10);
            $insertStatement->bindParam(":fullFilePath", $this->fullFilePath);
            $insertStatement->bindParam(":fileName", $this->fileName);
            $insertStatement->bindParam(":originalFileName", $this->originalFileName);
            $insertStatement->bindParam(":width", $this->width);
            $insertStatement->bindParam(":height", $this->height);
            $secure = $this->secure ? '1' : '0';
            $insertStatement->bindParam(":secure", $secure);
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