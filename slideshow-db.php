<?php
    use toolmarr\WebSlideshow\DbWebSlideshow;

    $maxHeight = 1000;
    if (!isset($_GET['r'])) {
        echo "<script language=\"JavaScript\">
        <!--
        let maxHeight = Math.max(document.documentElement.clientHeight || 0, window.innerHeight || 0);
        document.location=\"$_SERVER[PHP_SELF]?r=1&height=\" + maxHeight;
        //-->
        </script>";
    }
    else {
        // Code to be displayed if resolution is detected
        if (isset($_GET['height'])) {
            // Resolution detected
            $maxHeight = $_GET['height'];
        }
        
        require_once('vendor/autoload.php');
        require_once('scripts/dbMainConfig.php');
        
        // instantiate the Slideshow
        $dbSlideshow = new DbWebSlideshow($maxHeight);

        // load available tags
        $availableTags = $dbSlideshow->getAvailableTags($configuration);
    }
?>
<!DOCTYPE html>
<html lang="en" xml:lang="en">
    <head>
        <title>JavaScript Slideshow v2</title>
        <meta charset="utf-8">
        <link href="styles/main-db.css" media="all" rel="Stylesheet" type="text/css" />
        <!--<script type="text/javascript" language="javascript" src="scripts/slideshowFunctions.js"></script>-->
    </head>
    <body>
        <div id="slideshowOptions">
            <fieldset>
                <legend>Slideshow Options:</legend>
                <div class="slideshowSelection">             
                    <?php $dbSlideshow->renderSlideshowTags($availableTags) ?>
                </div>
                
                <div class="randomizeToggle">
                    <?php $dbSlideshow->renderRandomizeToggle() ?>
                </div>

                <div class="slideshowSpeed">
                    <?php $dbSlideshow->renderSlideshowSpeed() ?>
                </div>
            </fieldset>
        </div>
        <!-- Slideshow container -->
        <div class="slideshow-container">
            <fieldset>
                <legend>Slide:</legend>
            </fieldset>
            <!-- Full-width images with number and caption text -->
            <?php $dbSlideshow->renderSlideShow($configuration) ?>

            <!-- Next and previous buttons -->
            <!--<a class="prev" onclick="plusSlides(-1)">&#10094;</a>
            <a class="next" onclick="plusSlides(1)">&#10095;</a>-->            
        </div>        
        <!--script type="text/javascript" language="javascript">showSlides();</script>-->
    </body>
</html>