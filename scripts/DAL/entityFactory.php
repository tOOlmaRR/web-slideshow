<?php
namespace toolmarr\WebSlideshow\DAL;

use PDO;

class EntityFactory
{
    // private members
    private $dbInfo;
    private $dbType;
    private $dbDSN;
    private $dbUser;
    private $dbPassword;
    private $databaseConnection;
    private $useSPROCs;
    private $SPROCS;
    
    
    
    // constructor(s)
    public function __construct(array $databaseInfo)
    {
        $this->dbInfo = $databaseInfo;
        $this->dbType = $databaseInfo['type'];
        $this->dbUser = $databaseInfo['user'];
        $this->dbPassword = $databaseInfo['password'];
        $this->getDatabaseConnection();
        $useSPROCs = isset($databaseInfo['useSPROCS']) ? $databaseInfo['useSPROCS'] : false;
        $this->useSPROCs = $useSPROCs;
        $this->SPROCS = [];
        if ($useSPROCs) {
            $this->SPROCS = $databaseInfo["SPROCS"];
        }
    }
    
    
    //  methods
    public function getDatabaseConnection() : PDO
    {
        if (is_null($this->databaseConnection)) {
            // Determine DSN based on DB Type, Host, and DB Name
            if ($this->dbType == "mysql") {
                $this->dbDSN = "mysql:host=" . $this->dbInfo['host'] . ";dbname=" . $this->dbInfo['name'];
            } elseif ($this->dbType == "mssql") {
                $this->dbDSN = "odbc:Driver={SQL Server Native Client 11.0};Server=" . $this->dbInfo['host'] . ";Database=" . $this->dbInfo['name'];
            } else {
                throw new \Exception("Database Type Configuration Error");
            }
            
            // Get a connection to the database if we don't already have one
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            try {
                $db = new \PDO($this->dbDSN, $this->dbUser, $this->dbPassword, $options);
                if ($db != null) {
                    $this->databaseConnection = $db;
                }
            } catch (\PDOException $e) {
                 throw new \PDOException($e->getMessage(), (int)$e->getCode());
            }
        }
        return $this->databaseConnection;
    }
    
    public function getEntity(string $entityType)
    {
        switch ($entityType)
        {
            case "image":
                return new ImageEntity($this->getDatabaseConnection(), $this->useSPROCs, $this->SPROCS);

            case "images":
                return new ImagesEntity($this->getDatabaseConnection(), $this->useSPROCs, $this->SPROCS);
                
            case "tag":
                return new TagEntity($this->getDatabaseConnection(), $this->useSPROCs, $this->SPROCS);

            case "tags":
                return new TagsEntity($this->getDatabaseConnection(), $this->useSPROCs, $this->SPROCS);
                
            case "taggedImage":
                return new TaggedImageEntity($this->getDatabaseConnection(), $this->useSPROCs, $this->SPROCS);

            case "staticSlideshow":
                return new StaticSlideshowEntity($this->getDatabaseConnection(), $this->useSPROCs, $this->SPROCS);

            case "staticSlideshows":
                return new StaticSlideshowsEntity($this->getDatabaseConnection(), $this->useSPROCs, $this->SPROCS);

            default:
                return null;
        }
    }
}
