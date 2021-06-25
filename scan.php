<?php
    use toolmarr\WebSlideshow\FileScanner;
    require_once('vendor/autoload.php');
    require_once('scripts/mainConfig.php');

    // instantiate the Scanner and, if a scan request has been submitted, run a scan
    $scanner = new FileScanner();
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['folder'])) {
        // gather inputs from the POST object
        $inputScanFolder = isset($_POST['folder']) ? $_POST['folder'] : "";
        $secureImages = isset($_POST['secureImages']) ? $_POST['secureImages'] : false;
        $recurse = isset($_POST['recurse']) ? $_POST['recurse'] : false;
        $tags = isset($_POST['tags']) ? $_POST['tags'] : "";
        $secureTags = isset($_POST['secureTags']) ? $_POST['secureTags'] : false;
        $inputs = array(
            "folder" => $inputScanFolder,
            "secureImages" => $secureImages,
            "recurse" => $recurse,
            "tags" => $tags,
            "secureTags" => $secureTags,
        );
        $scanLog = $scanner->scanFolders($inputs, $configuration);
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
                <table>
                    <tr>
                        <td><label for="folder">Folder Path: </label></td>
                        <td><input type="text" name="folder" /></td>
                        <td><label for="tags">Tag all images: </label></td>
                        <td><input type="text" name="tags" title="comma-separate multiple tags" /></td>                        
                    </tr>
                    <tr>
                        <td></td>
                        <td><input type="checkbox" name="secureImages" checked=checked/><label for="secureImages">Secured Images</label></td>
                        <td></td>
                        <td><input type="checkbox" name="secureTags" checked=checked/><label for="secureTags">Secured Tag</label></td>
                        <td></td>
                    </tr>
                    <tr>
                    <td></td>
                        <td><input type="checkbox" name="recurse" checked=checked/><label for="recurse">Scan Sub-folders</label></td>
                        <td></td>
                        <td></td>
                        <td><input type="submit" value="SCAN" /></td>
                    </tr>
                </table>
                
            </form>
            <br />
            <textarea id="scanlog" rows="25" cols="175" name="scanlog"><?=$scanner->scanLog ?></textarea>
        </div>
    </body>
</html>