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
    } else {
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
        <!-- Slideshow Options -->
        <div id="leftPane">
            <div id="slideshowOptions">
                <fieldset>
                    <legend class="title">
                        <span>
                            <a id="slideshowOptionsPaneToggle" class="show_collapse" title="Slideshow Options" onclick="toggleOptionsPane(this);">&laquo;&laquo;&laquo;</a>
                        </span>
                        <span id="show_collapsible_label" class="legendLabel">Slideshow Options:</span>
                    </legend>
                    
                    <!-- Collapsible: Full Left Menu -->
                    <div id="show_collapsible_div">
                        
                        <div id="slideshowTypeSelection">
                            <input type="radio" name="slideshowType" value="tags" id="tagSlideshowSelection" checked onclick="toggleSlideshowTypeOptionsPane();">
                            <label for="tagSlideshowSelection">Tag-Based Slideshows</label>
                            <br />
                            <input type="radio" name="slideshowType" value="static" id="staticSlideshowSelection" onclick="toggleSlideshowTypeOptionsPane();">
                            <label for="staticSlideshowSelection">Static Slideshows</label>
                        </div>

                        <!-- Collapsible: Tag Slideshow Options -->
                        <div id="tagSlideshowOptions">
                            <fieldset>
                                <legend>Tag Slideshow Options:</legend>
                                <form id="tagSlideshowForm" action="" method="POST">
                                    <fieldset>
                                        <legend>Tags to Include:</legend>
                                        <!-- render the options dynamically into the following container -->
                                        <div id="slideshowTagSelection"></div>
                                        <div id="tagSelectionSubmit">
                                            <input type="submit" value="Generate Slideshow">
                                        </div>
                                    </fieldset>
                                    <div class="randomizeToggle">
                                        <?php echo $dbSlideshow->buildRandomizeToggleHtml() ?>
                                    </div>
                                </form>
                            </fieldset>
                        </div>

                        <!-- Collapsible: Static Slideshow Options -->
                        <div id="staticSlideshowOptions">
                            <fieldset>
                                <legend>Static Slideshow Options:</legend>
                                <form id="staticSlideshowForm" action="" method="POST">
                                    <!-- render the options dynamically into the following container -->
                                    <div id="staticSlideshowOptionsContainer"></div>
                                    <div id="staticSlideshowSubmit">
                                        <input type="Submit" value="Begin">
                                    </div>
                                </form>
                            </fieldset>
                        </div>

                        <div class="slideshowSpeed">
                            <?php echo $dbSlideshow->buildSlideshowSpeedHtml() ?>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
        
        <!-- Slide Info -->
        <div id="rightPane">
            <div id="slideInfo">
                <fieldset>
                    <legend class="title">
                        <span><a id="slideInfoPaneToggle" class="info_collapse" title="Slide Information" onclick="toggleInfoPane(this);">&laquo;&laquo;&laquo;</a></span>
                        <span id="info_collapsible_label" class="legendLabel">Slide Information:</span>
                    </legend>

                    <div id="info_collapsible_div">
                        <div id="slideInfoContainer"></div>
                        <fieldset>
                            <legend>Tags:</legend>
                            <div id="slideInfoTagsContainer"></div>
                        </fieldset>
                    <div>
                </fieldset>
            </div>
        </div>

        <!-- Slideshow Container -->
        <div id="slideshowContainer">
            <fieldset>
                <legend class="title">Slide:</legend>
                <div id="slideContainer"></div>
                <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
                <a class="next" onclick="plusSlides(1)">&#10095;</a>
            </fieldset>
        </div>

        <script type="text/javascript" language="javascript">showSlides();</script>
    </body>
</html>
