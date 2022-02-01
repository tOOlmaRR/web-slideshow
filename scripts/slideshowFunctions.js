let secretKey = "tOOlmaRR";
let allTags;
let allSlides;
let slideIndex = 0;
let slideShowIntervalID;
let slides;
let slideInfoPanels;
let slideIndexes;

// Do this once the DOM has loaded
window.addEventListener('DOMContentLoaded', function() {
    console.log('DOM has loaded');

    // This following DIV should only exist in old file-system-based slideshow. Do nothing if this DIV is found.
    const slideshowTagsSelectionDiv = document.getElementById("slideshowTagSelection");
    if (slideshowTagsSelectionDiv !== null) {
        // load available tags
        loadAvailableTagsFromDb();

        // render the tags available for the slideshow
        renderSlideshowTagsSelection();
        
        // listen for, and handle, slideshow generation requests
        const slideshowForm = document.getElementById("slideshowForm");
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

// Loads all available tags from the database via AJAX call to a service
function loadAvailableTagsFromDb() {
    console.log('Retrieving tags from database');
   
    let currentURL = window.location;
    let queryString = new URLSearchParams(currentURL.search);
    let secretValue = queryString.get('in');
    let allowPrivate = isPrivateAccessGranted(secretValue)
    let url = 'services/loadTags.php?in=' + allowPrivate;

    var httpRequest = new XMLHttpRequest();
    httpRequest.open('GET', url, false);
    httpRequest.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    httpRequest.send();
    console.log('Received response from Load Tags service');
    allTags = JSON.parse(httpRequest.responseText);
    
    // if we made this call asynchronousely, we'd need to handle the completion with an event handler like this:
    // httpRequest.onload = function() {
    //     console.log('Received response from Load Tags service');
    //     allTags = JSON.parse(httpRequest.responseText);
    // }
    // But rendering the tags must happen only after we have handled the AJAX response
}

// Load all slides for the chosen tags via AJAX call to a service, then start the slidehow if slides have been loaded
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

// Move forward or backward in the slideshow by the specified number of slides
function plusSlides(n)
{
    clearInterval(slideShowIntervalID);
    if (allSlides != null && allSlides.length > 0) {
        console.log('show next slide from data');
    }
    showSlides(slideIndex += n);
}

// Show a slide and it's info panels: either the next one in the current slideshow, or a specific slide if requested
function showSlides(n)
{
    // Handle DB-driven slideshow
    if (allSlides != null && allSlides.length > 0) {
        // load up initial list of indexes (used for randomization)
        if (slideIndexes === undefined) {
            slideIndexes = new Array();
            for (let i = 0; i < allSlides.length; i++) {
                slideIndexes.push(i);
            }
        }
        // render slides and slide info panels based on the allSlides array instead of hiding/showing pre-rendered HTML
        determineNextSlideIndex(n);
        renderSlideFromData();
        renderSlideInfoFromData();
        renderSlideTagInfoFromData()
        
        // Ensure previous interval is cleared first, then reset the interval based on the UI and continue the slideshow
        clearInterval(slideShowIntervalID);
        const configuredIntervalText = document.getElementById("currentSlideshowSpeed").innerText;
        const configuredInterval = +configuredIntervalText * 1000;
        slideShowIntervalID = setTimeout(showSlides, configuredInterval);
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

        // Ensure previous interval is cleared first, then reset the interval based on the UI and continue the slideshow
        clearInterval(slideShowIntervalID);
        const configuredIntervalText = document.getElementById("currentSlideshowSpeed").innerText;
        const configuredInterval = +configuredIntervalText * 1000;
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
        
        // Ensure previous interval is cleared first, then reset the interval based on the UI and continue the slideshow
        clearInterval(slideShowIntervalID);
        const configuredIntervalText = document.getElementById("currentSlideshowSpeed").innerText;
        const configuredInterval = +configuredIntervalText * 1000;
        slideShowIntervalID = setTimeout(showSlides, configuredInterval);
    }
}

// render all tags available for generating a slideshow
function renderSlideshowTagsSelection()
{
    console.log('render slideshow tag selection from data');

    var httpRequest = new XMLHttpRequest();
    httpRequest.open('POST', 'services/renderTags.php', false);
    httpRequest.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    data = {
        'allTags' : JSON.stringify(allTags),
    }
    var params = Object.keys(data).map(
        function(k){ return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]) }
    ).join('&');

    httpRequest.send(params);
    console.log('Received response from Render Tags service');

    jsonResponse = JSON.parse(httpRequest.responseText);
    slideInfoHTML = jsonResponse['HTML'];
    slideInfoPlaceholder = document.getElementById('slideshowTagSelection');
    slideInfoPlaceholder.innerHTML = slideInfoHTML; // replace all content of the placeholder
    
    // if we made this call asynchronousely, we'd need to handle the completion with an event handler like this:
    // httpRequest.onload = function() {
    //     console.log('Received response from Render Tags service');
    //     jsonResponse = JSON.parse(httpRequest.responseText);
    //     slideInfoHTML = jsonResponse['HTML'];
    //     slideInfoPlaceholder = document.getElementById('slideshowTagSelection');
    //     slideInfoPlaceholder.innerHTML = slideInfoHTML; // replace all content of the placeholder
    // }
    // But we need to wait until this response is handled in order to attach the event handle to the submit button
}


// render the HTML needed to display a slide via AJAX call to a service
function renderSlideFromData()
{
    console.log('render slide with index ' + slideIndexes[slideIndex] + '(' + slideIndex + ') from data');

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
}

// render the HTML needed to display a slide's info panels via AJAX call to a service
function renderSlideInfoFromData()
{
    console.log('render slide info with index ' + slideIndexes[slideIndex] + '(' + slideIndex + ') from data');

    // retrieve current slide
    var httpRequest = new XMLHttpRequest();
    httpRequest.open('POST', 'services/renderSlideInfo.php');
    httpRequest.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    data = {
        'slide': JSON.stringify(allSlides[slideIndexes[slideIndex]]),
        'allTags' : JSON.stringify(allTags),
    }
    var params = Object.keys(data).map(
        function(k){ return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]) }
    ).join('&');

    httpRequest.send(params);
    httpRequest.onload = function() {
        console.log('Received response from Render Slide Info service');
        jsonResponse = JSON.parse(httpRequest.responseText);
        slideInfoHTML = jsonResponse['HTML'];
        slideInfoPlaceholder = document.getElementById('slideInfoContainer');
        slideInfoPlaceholder.innerHTML = slideInfoHTML; // replace all content of the placeholder
    }
}

// render the HTML needed to display a slide's info panels via AJAX call to a service
function renderSlideTagInfoFromData()
{
    console.log('render slide tag info with index ' + slideIndexes[slideIndex] + '(' + slideIndex + ') from data');

    // retrieve current slide
    var httpRequest = new XMLHttpRequest();
    httpRequest.open('POST', 'services/renderTags.php');
    httpRequest.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    data = {
        'slide': JSON.stringify(allSlides[slideIndexes[slideIndex]]),
        'allTags' : JSON.stringify(allTags),
    }
    var params = Object.keys(data).map(
        function(k){ return encodeURIComponent(k) + '=' + encodeURIComponent(data[k]) }
    ).join('&');

    httpRequest.send(params);
    httpRequest.onload = function() {
        console.log('Received response from Render Tags service');
        jsonResponse = JSON.parse(httpRequest.responseText);
        slideInfoHTML = jsonResponse['HTML'];
        slideInfoPlaceholder = document.getElementById('slideInfoTagsContainer');
        slideInfoPlaceholder.innerHTML = slideInfoHTML; // replace all content of the placeholder
    }
}

// Helper method to determine which slide to show
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

// Event handler for the checkbox that enables or disables the randomize feature
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

// Event handler for the checkbox that halts or resumes the current slideshow
function haltSlideshow(checkbox) {
    clearInterval(slideShowIntervalID);
    if (!checkbox.checked) {
        // Reset the interval based on the UI and continue the slideshow
        const configuredIntervalText = document.getElementById("currentSlideshowSpeed").innerText;
        const configuredInterval = +configuredIntervalText * 1000;
        slideShowIntervalID = setTimeout(showSlides, configuredInterval);
    }
}

// Event handler to update a tag for the current slideshow (to either add or remove the tag based on the state of a checkbox)
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

// Determine if the current request has been granted private access
function isPrivateAccessGranted(secretValue)
{
    return secretValue == secretKey ? true : false;   
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

/**
 * Adds time to a date. Modelled after MySQL DATE_ADD function.
 * Example: dateAdd(new Date(), 'minute', 30)  //returns 30 minutes from now.
 * https://stackoverflow.com/a/1214753/18511
 * 
 * @param date  Date to start with
 * @param interval  One of: year, quarter, month, week, day, hour, minute, second
 * @param units  Number of units of the given interval to add.
 */
 function dateAdd(date, interval, units) {
    if(!(date instanceof Date))
      return undefined;
    var ret = new Date(date); //don't change original date
    var checkRollover = function() { if(ret.getDate() != date.getDate()) ret.setDate(0);};
    switch(String(interval).toLowerCase()) {
      case 'year'   :  ret.setFullYear(ret.getFullYear() + units); checkRollover();  break;
      case 'quarter':  ret.setMonth(ret.getMonth() + 3*units); checkRollover();  break;
      case 'month'  :  ret.setMonth(ret.getMonth() + units); checkRollover();  break;
      case 'week'   :  ret.setDate(ret.getDate() + 7*units);  break;
      case 'day'    :  ret.setDate(ret.getDate() + units);  break;
      case 'hour'   :  ret.setTime(ret.getTime() + units*3600000);  break;
      case 'minute' :  ret.setTime(ret.getTime() + units*60000);  break;
      case 'second' :  ret.setTime(ret.getTime() + units*1000);  break;
      default       :  ret = undefined;  break;
    }
    return ret;
  }