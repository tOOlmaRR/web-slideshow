<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>JavaScript Slideshow - Test 1</title>
        <meta charset="utf-8">
        <link href="styles/main.css" media="all" rel="Stylesheet" type="text/css" />
        <script type="text/javascript" language="javascript" src="scripts/slideshowFunctions.js"></script>
    </head>
    <body>
        <!-- Slideshow container -->
        <div class="slideshow-container">
        
            <!-- Full-width images with number and caption text -->
            <?php
                $allPhotos = scandir(dirname(__FILE__) . '/photos/');
                $photosToDisplay = array();
                for ($i = 0; $i < count($allPhotos); $i++) {
                    if (is_dir($allPhotos[$i])) {
                        continue;
                    } else {
                        $photosToDisplay[] = $allPhotos[$i];
                    }
                }
                
                foreach ($photosToDisplay as $number => $filename) {
            ?>
            <div class="mySlides fade" style="height: 1000px; width: 100%; background-color: black; text-align: center;">
                <div class="numbertext"><?=$number + 1 ?> / <?=count($photosToDisplay) ?></div>
                <img src="photos/<?=$filename ?>" style="max-width: 100%; max-height: 100%; object-fit:contain; position: relative; top: 50%; transform: translateY(-50%);">
                <div class="text"><?=$filename?></div>
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