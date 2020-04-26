var slideIndex = 0;
var slideShowIntervalID;

// Next/previous controls
function plusSlides(n)
{
    if (slideShowIntervalID !== 'undefined') {
        clearInterval(slideShowIntervalID);
    }
    showSlides(slideIndex += n);
}

// Thumbnail image controls
/*function currentSlide(n)
{
    if (slideShowIntervalID !== 'undefined') {
        clearInterval(slideShowIntervalID);
    }
    showSlides(slideIndex = n);
}*/

function showSlides(n)
{
    if (n === undefined) {
        var i;
        var slides = document.getElementsByClassName("mySlides");
        for (i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
        }
        slideIndex++;
        if (slideIndex > slides.length) {
            slideIndex = 1}
        slides[slideIndex-1].style.display = "block";
        slideShowIntervalID = setTimeout(showSlides, 10000);
    } else {
        var i;
        var slides = document.getElementsByClassName("mySlides");
        //var dots = document.getElementsByClassName("dot");
        if (n > slides.length) {
            slideIndex = 1}
        if (n < 1) {
            slideIndex = slides.length}
        for (i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
        }
        /*for (i = 0; i < dots.length; i++) {
            dots[i].className = dots[i].className.replace(" active", "");
        }*/
        slides[slideIndex-1].style.display = "block";
        //dots[slideIndex-1].className += " active";
        slideShowIntervalID = setTimeout(showSlides, 10000);
    }    
}
