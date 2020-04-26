<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>JavaScript Slideshow - L</title>
        <meta charset="utf-8">
        <link href="styles/main.css" media="all" rel="Stylesheet" type="text/css" />
        <script type="text/javascript" language="javascript" src="scripts/slideshowFunctions.js"></script>
    </head>
    <body>
        <!-- Slideshow container -->
        <div class="slideshow-container">
        
            <!-- Full-width images with number and caption text -->
            <?php
                // security
                $currentHourAndMinutes = date('Gi');
                if (isset($_GET) && $_GET["in"] === $currentHourAndMinutes) {
                    $rootFolder = "E:\\MyPhotos\\Private\\";
                    $physicalPath = "honeymoon\\";
                } else {
                    $rootFolder = "E:\\MyPhotos\\";
                    $physicalPath = "wedding\\";
                }
                
                // build physical and virtual locations
                $physicalFolderLocation = $rootFolder . $physicalPath;
                if ($rootFolder == "E:\\MyPhotos\\Private\\") {
                    $virtualRoot = "/private_photos/";
                } else {
                    $virtualRoot = "/myphotos/";
                }
                $virualFolderLocation = $virtualRoot . str_replace("\\", "/", $physicalPath);
                
                // get all photos in provided folder
                $allPhotos = scandir($physicalFolderLocation);
                
                // determine which to display (for now, weed out directories)
                $photosToDisplay = array();
                for ($i = 0; $i < count($allPhotos); $i++) {
                    if (is_dir($allPhotos[$i])) {
                        continue;
                    } else {
                        $photosToDisplay[] = $allPhotos[$i];
                    }
                }
                
                foreach ($photosToDisplay as $number => $filename) {
                    $filePath = $virualFolderLocation . $filename;
            ?>
            
            <div class="mySlides fade">
                <div class="numbertext"><?=$number + 1 ?> / <?=count($photosToDisplay) ?></div>
                    <img src="<?=$filePath?>">
                <div class="text"><span class="filename"><?=$filename?></span></div>
            </div>
            
            <?php } ?>
            
            <!-- Next and previous buttons -->
            <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
            <a class="next" onclick="plusSlides(1)">&#10095;</a>
        </div>
        <br>

        <!-- The dots/circles -->
        <!--<div style="text-align:center">
            <span class="dot" onclick="currentSlide(1)"></span>
            <span class="dot" onclick="currentSlide(2)"></span>
            <span class="dot" onclick="currentSlide(3)"></span>
        </div>-->
        <script type="text/javascript" language="javascript">showSlides();</script> 
    </body>
</html>