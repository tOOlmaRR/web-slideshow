<?php
use toolmarr\WebSlideshow\DbWebSlideshow;

require_once('../vendor/autoload.php');
require_once('../scripts/dbMainConfig.php');

$staticSlideshowOptionsHTML = '';
if (isset($_POST['staticSlideshows'])) {
    $staticSlideshows = json_decode($_POST['staticSlideshows'], true);
    
    // Build dropdown list of static slideshows to choose from
    $staticSlideshowOptionsHTML .= "<div class=\"staticSlideshowSelection\">";
    $staticSlideshowOptionsHTML .= "  <label for=\"staticSlideshowDropdown\">";
    $staticSlideshowOptionsHTML .= "  <select name=\"staticSlideshowSelection\" id=\"staticSlideshowDropdown\">";

    foreach ($staticSlideshows as $sSlideshow) {
        // build dropdown options
        $sSlideshowName = $sSlideshow["staticSlideshowName"];
        $sSlideshowID = $sSlideshow["staticSlideshowID"];
        $staticSlideshowOptionsHTML .= "    <option value=\"$sSlideshowID\">$sSlideshowName</option>";
    }
    
    // close off the drop down and enclosing div
    $staticSlideshowOptionsHTML .= "  </select>";
    $staticSlideshowOptionsHTML .= "</div>";
}

// prepare the response
header('Content-Type: application/json; charset=utf-8');
$responseObject = (object)['HTML' => $staticSlideshowOptionsHTML];
$jsonResponse = json_encode($responseObject);
echo $jsonResponse;
exit();
