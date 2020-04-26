<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>JavaScript Slideshow - L</title>
        <meta charset="utf-8">
        <link href="styles/main.css" media="all" rel="Stylesheet" type="text/css" />
        <script type="text/javascript" language="javascript" src="scripts/slideshowFunctions.js"></script>
        <?php include "scripts/slideshowControl.php" ?>
    </head>
    <body>
        <div class="slideshowOptions">
            <span class="randomizeToggle">
                <input type="checkbox" id="randomizeToggle" name="randomizeToggle" value="randomize" onclick="randomize_change(this)" />
                <label for="randomizeToggle">Randomize!</label>
            </span>
            <span class="slideshowSpeed">
                <label for="slideshowSpeed">Slideshow Speed: </label>
                <span class="currentSlideshowSpeed">
                    <output id="currentSlideshowSpeed" name="currentSlideshowSpeed">10</output><span> seconds</span
                </span>
                <input type="range" id="slideshowSpeed" name="slideshowSpeed" min="0" max="120" step="5" value="10" oninput="currentSlideshowSpeed.value = slideshowSpeed.value" />

            </span>
        </div>
        <!-- Slideshow container -->
        <div class="slideshow-container">
            <!-- Full-width images with number and caption text -->        
            <?php renderSlideShow() ?>

            <!-- Next and previous buttons -->
            <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
            <a class="next" onclick="plusSlides(1)">&#10095;</a>
        </div>        
        <script type="text/javascript" language="javascript">showSlides();</script>
    </body>
</html>