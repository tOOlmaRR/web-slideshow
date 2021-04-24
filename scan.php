<?php
    use toolmarr\WebSlideshow\FileScanner;
    require_once('vendor/autoload.php');

    // instantiate the Scanner and, if a scan request has been submitted, run a scan
    $scanner = new FileScanner();
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['folder'])) {
        // gather inputs from the POST object
        $inputScanFolder = isset($_POST['folder']) ? $_POST['folder'] : "";
        $recurse = isset($_POST['recurse']) ? $_POST['recurse'] : false;
        $tags = isset($_POST['tags']) ? $_POST['tags'] : "";
        $inputs = array(
            "folder" => $inputScanFolder,
            "recurse" => $recurse,
            "tags" => $tags,
        );
        $scanLog = $scanner->scanFolders($inputs);
    }
?>
<!DOCTYPE html>
<html lang="en" xml:lang="en">
    <head>
        <title>JavaScript Slideshow</title>
        <meta charset="utf-8">
        <link href="styles/scan.css" media="all" rel="Stylesheet" type="text/css" />
    </head>
    <body>
        <div id="scanForm">
            <form action="" method="POST" enctype="multipart/form-data">
                <label for="folder">Folder Path: </label><input type="text" name="folder" />
                <label for="tags">Tag all images: </label><input type="text" name="tags" title="comma-separate multiple tags" />
                <input type="checkbox" name="recurse" checked=checked/><label for="folder">Scan Sub-folders</label>
                <input type="submit" value="SCAN" />
            </form>
            <br />
            <textarea id="scanlog" rows="25" cols="175" name="scanlog"><?=$scanner->scanLog ?></textarea>
        </div>
    </body>
</html>