let allSlides;
let slideIndex = 0;
let slideShowIntervalID;
let slides;
let slideInfoPanels;
let slideIndexes;

// Do this once the DOM has loaded
window.addEventListener('DOMContentLoaded', function() {
    console.log('DOM has loaded');     
    const slideshowForm = document.getElementById("slideshowForm");
    if (slideshowForm !== null) {
        slideshowForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Retrieve slideshow data from database');
    
            // get checked tags
            var inputElements = slideshowForm.getElementsByTagName('input');
            var chosenTags = [];
            for (var i=0; inputElements[i]; ++i) {
                if (inputElements[i].checked) {
                    chosenTags.push(inputElements[i].value);
                }
            }
            loadSlideshowFromDb(chosenTags);
        });
    }
})



function loadSlideshowFromDb(chosenTags) {
    console.log('Retrieving slideshow data from database');
    
    // halt existing slideshow and reset some info    
    clearInterval(slideShowIntervalID);
    slideIndex = 0;
    slideIndexes = undefined;
    allSlides = null;
    const randomizeCheckbox = document.getElementById("randomizeToggle");
    randomizeCheckbox.checked = false;
    
    // determine maximum height based on the client
    let maxHeight = Math.max(document.documentElement.clientHeight || 0, window.innerHeight || 0);
    
    // retrieve slide data from the database
    var httpRequest = new XMLHttpRequest();
    httpRequest.open('POST', 'services/loadSlides.php');
    httpRequest.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    data = {
        'maxHeight': maxHeight,
        'chosenTags': chosenTags
    }
    var params = Object.keys(data).map(
        function(k){ return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]) }
    ).join('&');

    httpRequest.send(params);
    httpRequest.onload = function() {
        console.log('Received response from Load Slides service');
        allSlides = JSON.parse(httpRequest.responseText);
        // if we have slides start the slideshow from the beginning
        if (allSlides != null && allSlides.length > 0) {
            slideIndex = 0;
            showSlides(0);
        }
        // if we don't, clear the slide placeholder
        else {
            slidePlaceholder = document.getElementById('slideContainer');
            slidePlaceholder.innerHTML = '';
        }
    }
}

// Next/previous controls
function plusSlides(n)
{
    if (slideShowIntervalID !== 'undefined') {
        clearInterval(slideShowIntervalID);
    }
    if (allSlides != null && allSlides.length > 0) {
        console.log('show next slide from data');
    }
    showSlides(slideIndex += n);
}

function showSlides(n)
{
    const configuredIntervalText = document.getElementById("currentSlideshowSpeed").innerText;
    const configuredInterval = +configuredIntervalText * 1000;

    // Handle DB-driven slideshow
    if (allSlides != null && allSlides.length > 0) {
        // load up initial list of indexes (used for randomization)
        if (slideIndexes === undefined) {
            slideIndexes = new Array();
            for (let i = 0; i < allSlides.length; i++) {
                slideIndexes.push(i);
            }
        }
        // render slides based on the allSlides array instead of hiding/showing pre-rendered HTML
        renderSlideFromData(configuredInterval, n);
        return;
    }
    
    // retrieve all slides from the source HTML if not already retrieved
    if (slides === undefined) {
        slides = document.getElementsByClassName("mySlides");        
    }

    // retrieve all slide info panels from the source HTML
    if (slideInfoPanels === undefined) {
        slideInfoPanels = document.getElementsByClassName("mySlideInfo");
    }

    // early exit if there are no slides to display
    if (slides.length === 0)
        return;
    
    // not asked to display a specific slide (continue the slideshow)
    if (n === undefined) {
        // load up initial list of indexes (used for randomization)
        if (slideIndexes === undefined) {
            slideIndexes = new Array();
            for (let i = 0; i < slides.length; i++) {
                slideIndexes.push(i);
            }
        }
        
        // hide all slides and info panels
        for (let i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
            if (slideInfoPanels.length > 0) {
                slideInfoPanels[i].style.display = "none"
            }
        }

        // move forward one slide
        slideIndex++;
        
        // if we're at the end, start back at the beginning
        if (slideIndex > slides.length) {
            slideIndex = 1
        }
        
        // show only the current slide and it's info panel
        slides[slideIndexes[slideIndex-1]].style.display = "block";
        if (slideInfoPanels.length > 0) {
            slideInfoPanels[slideIndexes[slideIndex-1]].style.display = "block";
        }

        // continue the slideshow after the configured pause
        slideShowIntervalID = setTimeout(showSlides, configuredInterval);
    
    // display a specified slide
    } else {
        // if we're at the end, start back at the beginning
        if (n > slides.length) {
            slideIndex = 1
        }
        
        // if we're at the beginning, start back at the end
        if (n < 1) {
            slideIndex = slides.length
        }
        
        // hide all slides and info panels
        for (let i = 0; i < slides.length; i++) {
            slides[slideIndexes[i]].style.display = "none";
            if (slideInfoPanels.length > 0) {
                slideInfoPanels[slideIndexes[i]].style.display = "none";
            }
        }
        
        // show only the current slide and it's info panel
        slides[slideIndexes[slideIndex-1]].style.display = "block";
        if (slideInfoPanels.length > 0) {
            slideInfoPanels[slideIndexes[slideIndex-1]].style.display = "block";
        }
        
        // continue the slideshow after the configured pause
        slideShowIntervalID = setTimeout(showSlides, configuredInterval);
    }
}

function renderSlideFromData(configuredInterval, n)
{
    console.log('render slide from data');
    determineNextSlideIndex(n)    
    console.log('render slide index ' + slideIndexes[slideIndex] + '(' + slideIndex + ') from data');

    // retrieve current slide
    var httpRequest = new XMLHttpRequest();
    httpRequest.open('POST', 'services/renderSlide.php');
    httpRequest.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    data = {
        'slide': JSON.stringify(allSlides[slideIndexes[slideIndex]]),
        'slideCount': allSlides.length,
        'slideIndex': slideIndexes[slideIndex]
    }
    var params = Object.keys(data).map(
        function(k){ return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]) }
    ).join('&');

    httpRequest.send(params);
    httpRequest.onload = function() {
        console.log('Received response from Render Slide service');
        jsonResponse = JSON.parse(httpRequest.responseText);
        slideHTML = jsonResponse['HTML'];
        slidePlaceholder = document.getElementById('slideContainer');
        slidePlaceholder.innerHTML = slideHTML; // replace all content of the placeholder
    }
    
    // continue the slideshow after the configured pause
    slideShowIntervalID = setTimeout(showSlides, configuredInterval);
}

function determineNextSlideIndex(n)
{
    // Request is for a specific slide #
    if (n !== undefined) {
        // if we're at the end, start back at the beginning
        if (slideIndex > allSlides.length - 1) {
            slideIndex = 0
        }
        
        // if we're at the beginning, start back at the end
        if (slideIndex < 0) {
            slideIndex = allSlides.length - 1
        }
    }
    
    // Request is for the next slide in the slideshow
    else {
        // move forward one slide
        slideIndex++;
        
        // if we're at the end, start back at the beginning
        if (slideIndex > allSlides.length - 1) {
            slideIndex = 0
        }
    }
}

function randomize_change(checkbox)
{
    clearInterval(slideShowIntervalID);
    if (checkbox.checked) {
        shuffleArray(slideIndexes);
    } else {
        slideIndex = slideIndexes[slideIndex-1];
        slides = undefined;
        slideInfoPanels = undefined;
        slideIndexes = undefined;
    }
    showSlides();
}

function haltSlideshow(checkbox) {
    if (checkbox.checked) {
        clearInterval(slideShowIntervalID);
    } else {
        const configuredIntervalText = document.getElementById("currentSlideshowSpeed").innerText;
        const configuredInterval = +configuredIntervalText * 1000;
        slideShowIntervalID = setTimeout(showSlides, configuredInterval);
    }
}

function updateTags(imageID, tagID, tag, checkbox) {
    console.log("update Tag: " + tag);
    var msgDiv = document.getElementById('slideTagsSubmitMessages');

    // halt the slideshow if in progress
    var haltSlideshowCheckbox = document.getElementById('haltSlideshow');
    var slideshowInProgress = !haltSlideshowCheckbox.checked;
    if (slideshowInProgress) {
        clearInterval(slideShowIntervalID);
        haltSlideshowCheckbox.checked = true;
        var haltedMsgDiv = document.createElement("div");
        haltedMsgDiv.className = 'inProgress';
        haltedMsgDiv.innerText = 'Slideshow HALTED';
        msgDiv.appendChild(haltedMsgDiv);
    }

    // display operation to be performed and indicate operation is in progress
    var newMsgDiv = document.createElement("div");
    newMsgDiv.className = 'inProgress';
    var newOperation = checkbox.checked === true ? 'adding' : "removing";
    var newMsg = newOperation + ' "' + tag + '"...';
    newMsgDiv.innerText = newMsg;
    msgDiv.appendChild(newMsgDiv);
    
    // perform the operation
    console.log("update DB");
    var httpRequest = new XMLHttpRequest();

    if (!httpRequest) {
        newMsgDiv.className = 'failure';
        newMsgDiv.innerText += "FAILED!"
        return false;
    }

    httpRequest.open('POST', 'services/taggedimage.php');
    httpRequest.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    var params = "imageID=" + imageID + "&tagID=" + tagID + "&operation=" + newOperation;
    httpRequest.send(params);
    
    httpRequest.onload = function() {
        // Do whatever with response
        if (httpRequest.responseText != 'success') {
            newMsgDiv.className = 'failure';
            newMsgDiv.innerText += "FAIL!"
        } else {
            newMsgDiv.className = 'success';
            newMsgDiv.innerText += "DONE!"
        }
    }
}

/* Randomize array in-place using Durstenfeld shuffle algorithm */
/* Source: https://stackoverflow.com/questions/2450954/how-to-randomize-shuffle-a-javascript-array */
function shuffleArray(array) {
    for (var i = array.length - 1; i > 0; i--) {
        var j = Math.floor(Math.random() * (i + 1));
        var temp = array[i];
        array[i] = array[j];
        array[j] = temp;
    }
}