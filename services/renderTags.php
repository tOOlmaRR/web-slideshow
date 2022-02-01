<?php
use toolmarr\WebSlideshow\DbWebSlideshow;

require_once('../vendor/autoload.php');
require_once('../scripts/dbMainConfig.php');

// "slideshowTags" : render tag selection for generating a slideshow
// "slideTags" : render tags associated to a specific slide
$mode = "slideshowTags";

$slideInfoHtml = '';
if (isset($_POST['allTags'])) {
    // gather inputs from the request and choose which 'mode' we are in
    if (isset($_POST['slide']))
    {
        $slide = json_decode($_POST['slide'], true);
        $mode = "slideTags";
    }
    $availableTags = json_decode($_POST['allTags'], true);
    
    // Build list of tags to render
    if ($mode == "slideshowTags") {
        $slideInfoHtml .= "<form id=\"slideshowForm\" action=\"\" method=\"POST\">";
    }
    $slideInfoHtml .= "    <div class=\"tagSelection\">";
    foreach ($availableTags as $tag) {
        // determine the CSS class to set for the tag itself
        $cssClass = $tag['secure'] ? 'privateOption' : 'publicOption';

        // determine if the tag's checkbox should be checked or not
        $tagCheckedAttribute = "";
        if ($mode == "slideshowTags") {
            $tagCheckedAttribute = in_array($tag['tag'], $configuration['chosenTags']) ? 'checked' : '';
        } else if ($mode == "slideTags") {            
            if (array_key_exists($tag['tagID'], $slide['tags'])) {
                $tagCheckedAttribute = "checked";
            }
        }

        // build the tags themselves
        $slideInfoHtml .= "    <span>";
        $slideInfoHtml .= "        <input type=\"checkbox\" name=\"chosenTags[]\" value=\"" . $tag['tag'] . "\" id=\"" . $tag['tag'] . "\" $tagCheckedAttribute ";
        // only trigger the onclick event handler in 'slideTags' mode: in 'slideshowTags' mode, there is a form and a server-side postback
        if ($mode == "slideTags") {            
            $slideInfoHtml .= "onclick=\"updateTags(" . $slide['ID'] . ", " . $tag['tagID'] . ", '" . $tag['tag'] . "', this);\"";
        }
        $slideInfoHtml .= "/>";
        $slideInfoHtml .= "        <label class=\"$cssClass\" for=\"" . $tag['tag'] . "\">" . $tag['tag'] . "</label>";
        $slideInfoHtml .= "    </span>";
    }
    $slideInfoHtml .= "    </div>";

    // in 'slideTags' mode: show messages area to indicate what is going on
    // in 'slideshowTags' mode: show submit button
    if ($mode == "slideTags") {            
        $slideInfoHtml .= "    <div id=\"slideTagsSubmitMessages\"></div>";
    } else if ($mode == "slideshowTags") {
        $slideInfoHtml .= "    <div id=\"tagSelectionSubmit\"><input type=\"submit\" value=\"Generate Slideshow\"></div>";
    }
}

// prepare the response
header('Content-Type: application/json; charset=utf-8');
$responseObject = (object)['HTML' => $slideInfoHtml];
$jsonResponse = json_encode($responseObject);
echo $jsonResponse;
exit();