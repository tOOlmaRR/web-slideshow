<?php
$slideHtml = '';
if (isset($_POST['slide']) && isset($_POST['slideCount']) && isset($_POST['slideIndex'])) {
    // gather inputs from the request
    $slideToRender = json_decode($_POST['slide']);
    $slideCount = $_POST['slideCount'];
    $slideIndex = $_POST['slideIndex'];
    
    // build the HTML
    $slideHtml = '';
    $slideHtml .= "<div class=\"mySlides fade\" style=\"height: " . intval($slideToRender->height + 25) . "px;\">";
    $slideHtml .= "    <div class=\"numbertext\">" . ($slideIndex + 1) . " / " . $slideCount . "</div>";
    $slideHtml .= "    <img width=\"$slideToRender->width\" height=\"$slideToRender->height\" src=\"" . $slideToRender->virtualLocation . "\">";
    $slideHtml .= "</div>";
}

// prepare the response
header('Content-Type: application/json; charset=utf-8');
$responseObject = (object)['HTML' => $slideHtml];
$jsonResponse = json_encode($responseObject);
echo $jsonResponse;
exit();