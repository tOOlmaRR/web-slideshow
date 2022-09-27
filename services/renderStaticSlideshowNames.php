<?php
use toolmarr\WebSlideshow\DbWebSlideshow;

require_once('../vendor/autoload.php');
require_once('../scripts/dbMainConfig.php');

$staticSlideshowOptionsHTML = '';
if (isset($_POST['staticSlideshows'])) {
    $staticSlideshows = json_decode($_POST['staticSlideshows'], true);
    
    // Build dropdown list of static slideshows to choose from
    $slideshowDropdownHtml = "<form id=\"staticSlideshowForm\" action=\"\" method=\"POST\">";
    $staticSlideshowOptionsHTML .= "  <div class=\"staticSlideshowSelection\">";

    // Build the dropdown
    $staticSlideshowOptionsHTML .= "  <label for=\"staticSlideshowDropdown\">";
    $staticSlideshowOptionsHTML .= "  <select name=\"staticSlideshowSelection\" id=\"staticSlideshowDropdown\">";

    foreach ($staticSlideshows as $sSlideshow) {
        // build dropdown options
        $sSlideshowName = $sSlideshow["staticSlideshowName"];
        $sSlideshowID = $sSlideshow["staticSlideshowID"];
        $staticSlideshowOptionsHTML .= "    <option value=\"$sSlideshowID\">$sSlideshowName</option>";
    }
    
    // close off the drop down
    $staticSlideshowOptionsHTML .= "  </select>";
    $staticSlideshowOptionsHTML .= "  <div id=\"staticSlideshowSubmit\"><input type=\"Submit\" value=\"Begin\"></div>";
    $staticSlideshowOptionsHTML .= "</form>";
}

// prepare the response
header('Content-Type: application/json; charset=utf-8');
$responseObject = (object)['HTML' => $staticSlideshowOptionsHTML];
$jsonResponse = json_encode($responseObject);
echo $jsonResponse;
exit();