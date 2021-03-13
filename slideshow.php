<?php
    use toolmarr\WebSlideshow\WebSlideshow;

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
        require_once('scripts/mainConfig.php');
        
        // instantiate the Slideshow
        $slideshow = new WebSlideshow($maxHeight);
    }
?>
<!DOCTYPE html>
<html lang="en" xml:lang="en">
    <head>
        <title>JavaScript Slideshow</title>
        <meta charset="utf-8">
        <link href="styles/main.css" media="all" rel="Stylesheet" type="text/css" />
        <script type="text/javascript" language="javascript" src="scripts/slideshowFunctions.js"></script>
    </head>
    <body>
        <div class="slideshowOptions">
            <span class="slideshowSelection">
                <?php $slideshow->renderSlideshowDropdown($configuration, $chosenSlideshow) ?>
            </span>
            <span class="randomizeToggle">
                <input type="checkbox" id="randomizeToggle" name="randomizeToggle" value="randomize" onclick="randomize_change(this)" />
                <label for="randomizeToggle">Randomize!</label>
            </span>
            <span class="slideshowSpeed">
                <label for="slideshowSpeed">Slideshow Speed: </label>
                <span class="currentSlideshowSpeed">
                    <output id="currentSlideshowSpeed" name="currentSlideshowSpeed">5</output><span> seconds</span>
                </span>
                <input type="range" id="slideshowSpeed" name="slideshowSpeed" min="1" max="120" step="1" value="5"
                    oninput="currentSlideshowSpeed.value = slideshowSpeed.value" />
                <span>
                    <input type="checkbox" id="haltSlideshow" name="haltSlideshowToggle" value="halt" onclick="haltSlideshow(this)" />
                    <label for="randomizeToggle">Halt!</label>
                </span>

            </span>
        </div>
        <!-- Slideshow container -->
        <div class="slideshow-container">
            <!-- Full-width images with number and caption text -->   
            <?php $slideshow->renderSlideShow($configuration, $chosenSlideshow) ?>

            <!-- Next and previous buttons -->
            <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
            <a class="next" onclick="plusSlides(1)">&#10095;</a>
        </div>        
        <script type="text/javascript" language="javascript">showSlides();</script>
    </body>
</html>