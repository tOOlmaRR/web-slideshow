let slideIndex = 0;
let slideShowIntervalID;
let slides;
let slideIndexes;

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
    const configuredIntervalText = document.getElementById("currentSlideshowSpeed").innerText;
    const configuredInterval = +configuredIntervalText * 1000;

    if (slides === undefined) {
        slides = document.getElementsByClassName("mySlides");
    }
    
    if (n === undefined) {
        // get all slides from the source HTML
        let i;
        
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
        if (slideIndex > slides.length) {
            slideIndex = 1
        }
        
        // show only the current slide
        slides[slideIndexes[slideIndex-1]].style.display = "block";
        
        // continue the slideshow after the configured pause
        slideShowIntervalID = setTimeout(showSlides, configuredInterval);
    } else {
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
        slideShowIntervalID = setTimeout(showSlides, configuredInterval);
    }
}

function randomize_change(checkbox)
{
    clearInterval(slideShowIntervalID);
    if (checkbox.checked) {
        slideIndexes = new Array();
        for (let i = 0; i < slides.length; i++) {
            const randomIndex = Math.floor(Math.random() * slides.length);
            slideIndexes.push(randomIndex);
        }
    } else {
        slides = undefined;
        slideIndexes = undefined;
        slideIndex = 0;
    }
    showSlides();
}

function haltSlideshow(checkbox) {
    if (checkbox.checked) {
        clearInterval(slideShowIntervalID);
    } else {
        configuredIntervalText = document.getElementById("currentSlideshowSpeed").innerText;
        configuredInterval = +configuredIntervalText * 1000;
        slideShowIntervalID = setTimeout(showSlides, configuredInterval);
    }
}