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
    if (isset($_POST['slide'])) {
        $slide = json_decode($_POST['slide'], true);
        $mode = "slideTags";
    }
    $availableTags = json_decode($_POST['allTags'], true);
    
    // Build list of tags to render
    $slideInfoHtml .= "    <div class=\"tagSelection\">";
    foreach ($availableTags as $tag) {
        // determine the CSS class to set for the tag itself
        $cssClass = $tag['secure'] ? 'privateOption' : 'publicOption';

        // determine if the tag's checkbox should be checked or not
        $tagCheckedAttribute = "";
        if ($mode == "slideshowTags") {
            $tagCheckedAttribute = in_array($tag['tag'], $configuration['chosenTags']) ? 'checked' : '';
        } elseif ($mode == "slideTags" && array_key_exists($tag['tag'], $slide['tags'])) {
            $tagCheckedAttribute = "checked";
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
    if ($mode == "slideTags") {
        $slideInfoHtml .= "    <div id=\"slideTagsSubmitMessages\"></div>";
    } elseif ($mode == "slideshowTags") {
        $slideInfoHtml .= "    <div id=\"modeSelection\">";
        $slideInfoHtml .= "        <input type=\"radio\" name=\"slideshowMode\" value=\"normal\" id=\"normal\" checked>";
        $slideInfoHtml .= "        <label for=\"normal\">normal</label>";
        $slideInfoHtml .= "        <input type=\"radio\" name=\"slideshowMode\" value=\"tagging\" id=\"tagging\">";
        $slideInfoHtml .= "        <label for=\"tagging\">tagging</label>";
        $slideInfoHtml .= "        <input type=\"radio\" name=\"slideshowMode\" value=\"maximize\" id=\"maximize\">";
        $slideInfoHtml .= "        <label for=\"maximize\">maximize</label>";
        $slideInfoHtml .= "    </div>";
    }
}

// prepare the response
header('Content-Type: application/json; charset=utf-8');
$responseObject = (object)['HTML' => $slideInfoHtml];
$jsonResponse = json_encode($responseObject);
echo $jsonResponse;
exit();
