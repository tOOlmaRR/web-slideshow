let slideIndex = 0;
let slideShowIntervalID;
var slides;
var slideIndexes;

// Next/previous controls
function plusSlides(n)
{
    if (slideShowIntervalID !== 'undefined') {
        clearInterval(slideShowIntervalID);
    }
    showSlides(slideIndex += n);
}

function showSlides(n)
{
    if (n === undefined) {
        // get all slides from the source HTML
        let i;
        if (slides === undefined) {
            slides = document.getElementsByClassName("mySlides");
        }
        
        // load up initial list of indexes (used for randomization)
        if (slideIndexes === undefined) {
            slideIndexes = new Array();
            for (let i = 0; i < slides.length; i++) {
                slideIndexes.push(i);
            }
        }
        
        // hide all slides
        for (let i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
        }
        
        // move forward one slide
        slideIndex++;
        
        // if we're at the end, start back at the beginning
        if (slideIndexes[slideIndex] > slides.length) {
            slideIndex = 1
        }
        
        // show only the current slide
        slides[slideIndexes[slideIndex-1]].style.display = "block";
        
        // continue the slideshow after the configured pause
        slideShowIntervalID = setTimeout(showSlides, 8000);
    } else {
        // get all slides from the source HTML
        if (slides === undefined) {
            slides = document.getElementsByClassName("mySlides");
        }

        // if we're at the end, start back at the beginning
        if (n > slides.length) {
            slideIndex = 1
        }
        
        // if we're at the beginning, start back at the end
        if (n < 1) {
            slideIndex = slides.length
        }
        
        // hide all slides
        for (let i = 0; i < slides.length; i++) {
            slides[slideIndexes[i]].style.display = "none";
        }
        
        // show only the current slide
        slides[slideIndexes[slideIndex-1]].style.display = "block";
        
        // continue the slideshow after the configured pause
        slideShowIntervalID = setTimeout(showSlides, 8000);
    }
}

function randomize_change(checkbox)
{
    clearInterval(slideShowIntervalID);
    if (checkbox.checked) {
        slideIndexes = new Array();
        for (let i = 0; i < slides.length; i++) {
            randomIndex = Math.floor(Math.random() * slides.length);
            slideIndexes.push(randomIndex);
        }
    } else {
        slides = undefined;
        slideIndexes = undefined;
        slideIndex = 0;
    }
    showSlides();
}