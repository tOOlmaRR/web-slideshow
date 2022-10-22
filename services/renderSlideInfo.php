<?php
$slideInfoHtml = '';
if (isset($_POST['slide'])) {
    // gather inputs from the request
    $slide = json_decode($_POST['slide'], true);
    $slideCheckedAttribute = $slide['secured'] ? "checked" : "";
    $filePath = dirname($slide['fullpath']);
            
    $slideInfoHtml .= "<fieldset>";
    $slideInfoHtml .= "<legend>Basic Info:</legend>";
    $slideInfoHtml .= "    <div class=\"slideBasicInfo\">";
    $slideInfoHtml .= "        <div>";
    $slideInfoHtml .= "            <span class=\"title\">Filename: </span><br />";
    $slideInfoHtml .= "            <input class=\"slide-filename\" type=\"text\" disabled=\"disabled\" name=\"filename\" value=\"" . $slide['filename'] . "\" />";
    $slideInfoHtml .= "        </div>";
    $slideInfoHtml .= "        <div>";
    $slideInfoHtml .= "            <span class=\"title\">File Path: </span><br />";
    $slideInfoHtml .= "            <input class=\"slide-filepath\" type=\"text\" disabled=\"disabled\" name=\"filepath\" value=\"" . $filePath . "\" />";
    $slideInfoHtml .= "        </div>";
    $slideInfoHtml .= "        <div>";
    $slideInfoHtml .= "            <span class=\"title\">Original Size: </span><br />";
    $slideInfoHtml .= "            <input class=\"slide-o-size\" type=\"text\" disabled=\"disabled\" value=\"" .
        $slide['originalWidth'] . "x" . $slide['originalHeight'] . "\" />";
    $slideInfoHtml .= "        </div>";
    $slideInfoHtml .= "        <div>";
    $slideInfoHtml .= "            <span class=\"title\">Resized To: </span><br />";
    $slideInfoHtml .= "            <input class=\"slide-n-size\" type=\"text\" disabled=\"disabled\" value=\"" . $slide['width'] . "x" . $slide['height'] . "\" />";
    $slideInfoHtml .= "        </div>";
    $slideInfoHtml .= "        <div>";
    $slideInfoHtml .= "            <input class=\"slide-secured\" type=\"checkbox\" disabled=\"disabled\" name=\"secureImage\" $slideCheckedAttribute/>" .
        "<label for=\"secureImage\">Secured Image</label>";
    $slideInfoHtml .= "        </div>";
    $slideInfoHtml .= "    </div>";
    $slideInfoHtml .= "</fieldset>";
}

// prepare the response
header('Content-Type: application/json; charset=utf-8');
$responseObject = (object)['HTML' => $slideInfoHtml];
$jsonResponse = json_encode($responseObject);
echo $jsonResponse;
exit();
