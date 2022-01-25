<?php
$slideInfoHtml = '';
if (isset($_POST['slide']) && isset($_POST['allTags'])) {
    // gather inputs from the request
    $slide = json_decode($_POST['slide'], true);
    $availableTags = json_decode($_POST['allTags'], true);
    
    // initial form and fieldset rendering
    $slideInfoHtml .= "    <fieldset>";
    $slideInfoHtml .= "        <legend>Tags Associated to this Slide:</legend>";
    $slideInfoHtml .= "        <div id=\"tagSelection\">";

    // Build list of tags to render
    foreach ($availableTags as $tag) {
        $tagCheckedAttribute = "";
        if (array_key_exists($tag['tagID'], $slide['tags'])) {
            $tagCheckedAttribute = "checked";
        }
        $cssClass = $tag['secure'] ? 'privateOption' : 'publicOption';
        $slideInfoHtml .= "            <span>";
        $slideInfoHtml .= "                <input type=\"checkbox\" name=\"slideTags[]\" value=\"" . $tag['tag'] . "\" id=\"" . $tag['tag'] . "\" $tagCheckedAttribute onclick=\"updateTags(" . $slide['ID'] . ", " . $tag['tagID'] . ", '" . $tag['tag'] . "', this);\"/>";
        $slideInfoHtml .= "                <label class=\"$cssClass\" for=\"" . $tag['tag'] . "\">" . $tag['tag'] . "</label>";
        $slideInfoHtml .= "            </span>";
    }

    // close off the drop down and render the button
    $slideInfoHtml .= "        </div>";
    $slideInfoHtml .= "        <div id=\"slideTagsSubmitMessages\"></div>";
    $slideInfoHtml .= "    </fieldset>";
}

// prepare the response
header('Content-Type: application/json; charset=utf-8');
$responseObject = (object)['HTML' => $slideInfoHtml];
$jsonResponse = json_encode($responseObject);
echo $jsonResponse;
exit();