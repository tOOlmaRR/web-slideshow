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
        <script type="text/javascript" language="javascript" src="scripts/slideshowFunctions.js"></script>
    </head>
    <body>
        <!-- Left Pane -->
        <div id="leftPane">
            <!-- Options re: the Slideshow -->
            <div id="slideshowOptions">
                <fieldset>
                    <legend class="title">Slideshow Options:</legend>
                    <fieldset>
                        <legend>Tags to Include in Slideshow:</legend>
                            <div id="slideshowTagSelection"></div>
                        </legend>
                    </fieldset>
                    <div class="randomizeToggle">
                        <?php echo $dbSlideshow->buildRandomizeToggleHtml() ?>
                    </div>
                    <div class="slideshowSpeed">
                        <?php echo $dbSlideshow->buildSlideshowSpeedHtml() ?>
                    </div>
                </fieldset>
            </div>
        </div>
        
        <!-- Slideshow Container -->
        <div class="slideshow-container">
            <fieldset>
                <legend>Slide:</legend>
                <div id="slideContainer"></div>
                <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
                <a class="next" onclick="plusSlides(1)">&#10095;</a>
            </fieldset>
        </div>

        <div id="rightPane">
            <!-- Information re: the Current Slide -->
            <div id="slideInfo">
                <fieldset>
                    <legend class="title">Slide Information:</legend>
                    <div id="slideInfoContainer"></div>
                        <fieldset>
                            <legend>Tags Associated to this Slide:</legend>
                            <div id="slideInfoTagsContainer"></div>
                        </fieldset>
                </fieldset>
            </div>    
        </div>

        <script type="text/javascript" language="javascript">showSlides();</script>
    </body>
</html>