<?php
use toolmarr\WebSlideshow\DbWebSlideshow;

require_once('../vendor/autoload.php');
require_once('../scripts/dbMainConfig.php');

$slideInfoHtml = '';
if (isset($_POST['slideshows'])) {
    $staticSlideshows = json_decode($_POST['slideshows'], true);
    
    // Build dropdown list of static slideshows to choose from
    $slideshowDropdownHtml = "<form id=\"staticSlideshowForm\" action=\"\" method=\"POST\">";
    $slideInfoHtml .= "  <div class=\"staticSlideshowSelection\">";

    // Build the dropdown
    $slideInfoHtml .= "  <label for=\"staticSlideshowDropdown\">";
    $slideInfoHtml .= "  <select name=\"staticSlideshowSelection\" id=\"staticSlideshowDropdown\">";

    foreach ($staticSlideshows as $sSlideshow) {
        // build dropdown options
        $sSlideshowName = $sSlideshow["Name"];
        $sSlideshowID = $sSlideshow["ID"];
        $slideInfoHtml .= "    <option value=\"$sSlideshowID\">$sSlideshowName<option>";
    }
    
    // close off the drop down
    $slideInfoHtml .= "  </select>";
    $slideInfoHtml .= "  <input type=\"Submit\" value=\"Begin\">";
    $slideInfoHtml .= "</form>";
}

// prepare the response
header('Content-Type: application/json; charset=utf-8');
$responseObject = (object)['HTML' => $slideInfoHtml];
$jsonResponse = json_encode($responseObject);
echo $jsonResponse;
exit();