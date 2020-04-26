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
        <!-- Slideshow container -->
        <div class="slideshowOptions">
            <label for="randomizeToggle">Randomize!</label>
            <input type="checkbox" id="randomizeToggle" name="randomizeToggle" value="randomize" onclick="randomize_change(this)" />    
        </div>
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