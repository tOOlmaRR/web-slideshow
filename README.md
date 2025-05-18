# Web Slideshow
## Introduction
This web application can display pre-defined or dynamic slideshows in a browser for locally-stored photos. There are two different slideshow applications on 2 separate pages: a *file system slideshows* page that displays images directly from the file system configured using folder paths, and a *data-driven slideshows* page which includes database-driven slideshows, including statically defined slideshows as well as dynamic slideshows based on tag selection. There is also an admin page for the database-driven slideshows used to import photos into the database. Each page's features are listed below.

## File-Based Slideshows
### Summary
This is available on the **/slideshow.php** page. These slideshows has no dependency on a database, so they are the easiest to set up and get running; however, many features going forward will not be developed for this page.
### Features
1. **Slideshow Selection** - if multiple slideshows have been configured, you can choose which slideshow to watch via a dropdown selection.
1. **Photo Resizing** - automatically resizes photos and slides based on the client's current viewport height. This is accomplished by a redirect and query string paramaters.
1. **Manual Controls** - at any time you can move forward or backward in the current slideshow. This does not halt the slideshow in progress, but it does restart the timer.
1. **Slideshow Speed** - a slider control in the UI controls how long each slide will be displayed for. Changes take effect after transitioning to the next slide.
1. **Randomize Option** - allows you to randomize the slides in the current slideshow. Changes take effect immediately. Halting randomization will stop at the current slide.
1. **Halt** - allows you to stop the slidehow on the current slide and pick up where you left off afterwards.
1. **Private Slideshows** - via slideshow configuration, you can limit access to selected slideshows. These slideshows only appear in the dropdown if you include a special query string parameter and value in your web request. Private slideshows appear in a red font, whereas public slideshows appear in green.
1. **Multiple Folders** - via slideshow configuration, you can specify either a single folder or mutliple folders to include in a slideshow. In each case, the assumption is that there are only valid images in those folders
1. **Recursive Folders** - via slideshow configuration, you can also choose to include all subfolders of each folder configured for a slideshow.

### Configuration
All configuration elements are defined in the *scripts/mainConfig.php* file. This builds an overlying *$configuration* array which is designed to contain all configuration elements. Within that, there are separate arrays that contain all slideshow configurations as well as the physical and virtual root folders to use for both private and public slideshows.

This file also contains some logic to determine the default slideshow to run if there are multiple slideshows defined and available, and determines the currently chosen slideshow - either the desired default or the chosen one from the dropdown and subsequent form submission.

A sample is included in the repo, but here's a couple of slideshow configuration elements:
1. Single Folder Private Slideshow

    ```php
    $allSlideshows["Honeymoon"] = [
        "name" => "Honeymoon",
        "public" => false,
        "physicalPath" => "Honeymoon\\",
    ];
    ```

1. Multiple Folder Public Slideshow
    ```php
    $allSlideshows["WeddingAll"] = [
        "name" => "Wedding - ALL",
        "public" => true,
        "physicalPaths" => [
            "Wedding\\Disc1\\",
            "Wedding\\Disc2\\"
        ],
        "includeSubfolders" => true
    ];
    ```

Each slideshow configuration element is documented below (keep in mind that the keys are case-sensitive):
- **array index**: Uniquely idenfities the slideshow and must be a unique value.
- **name**: The name of the slideshow that appears in the dropdown selection on the page.
- **public**: A value that indicates if the slideshow is public (*true*) or private (*false*).
- **physicalPath**: The physical path, relative to the root folder, including the folder in which the images are located. This path is appended to the hardcoded *$rootFolder* value to build the full physical path. Use this key if you want to include only one folder of images, or if the other folders in your slideshow are subfolders of a single parent folder.
- **physicalPaths**: Overrides the older *physicalPath* element and is used to define multiple folders for your slideshow using an array of values (paths). 
- **includeSubfolders**: Defines whether subfolders should be included in the slideshow (*true*) or not (*false*).

Also, here's an example of how to define the virtual and private roots to use:
```php
$virtualRoots = array();
$virtualRoots["public"] = "/myphotos/";
$virtualRoots["private"] = "/myphotos/private/";
$configuration["virtualRoots"] = $virtualRoots;

$physicalRoots = array();
$physicalRoots["public"] = "E:\\MyPhotos\\";
$physicalRoots["private"] = "E:\\MyPhotos\\Private\\";
$configuration["physicalRoots"] = $physicalRoots;
```

### Technical Details
#### Summary
File-based slideshows will look at the configured folder(s), retrieve a list of images, and build an HTML DIV for each image in the slideshow. The slideshow engine works by loading all DIVs into an array, hiding all slides by default, and only showing the current slide. This means that the page load times can be long because the page will get large with all of the HTML code, and all of the images are rendered on page load.
#### /slideshow.php
- the webpage that displays the file-based slideshow
- includes some javascript to detect the screen height and redirect to itself with the height in the query string, which allows PHP on the backend to resize the slides
#### /scripts/mainConfig.php
- configuration file including paths to the images
- includes a security feature to only allow access to private slideshows if you add an "in" query string parameter with a value that matches the current hour and minute in 24 hour time plus-or-minus 1 minute (which no longer appears to work)
- determines the currently chosen slideshow based on selection and submission from the webpage
#### /scripts/slideshowFunctions.js
 - note that this includes a script that runs once the basic page has loaded
 - includes the original javascript slideshow library that this web app was built from and essentially acts as the 'engine' of the slideshow
 - uses methods shared between the file-based and DB-driven slideshows:
    - **showSlides** - the entry point into the slideshow engine used by both file-based and DB-driven slideshows, but there is a fairly clear divide between the two
    - **plusSlides** - hides the current slide and shows the slide with the given index
    - **haltSlideshow** - an event handler that toggles the timer that controls the slideshow, controlled by the checkbox in the UI (ie. stops the timer if checked and restarts it if unchecked)
    - **randomizeChange** - an event handler that toggles the randomization of the slideshow. When checked, the index values of the array of slides is shuffled, and when it is not, they are returned to the order in which they are retrieved from the database
#### /scripts/WebSlideshow.php
- server-side class that represents a file-based slideshow
- builds many of the UI elements such as the slideshow drop-down and the slides themselves, and retrieves the images from the file system
#### /styles/main.css
- includes all CSS styling for the file-based slideshows

### Known Bugs and Limitations
1. **Memory Consumption Issues for Large Slideshows**
A block of HTML is added to the webpage for every single file within each of the configured folders for a slideshow. This means that the page source can grow uncontrollably if your slideshow contains too many images. All images are retrieved at load time, and image dimaensions aren't specificed in the HTML, so all images are loaded in their original form, which compounds the issue.<br />
So how many images are too many you ask? A test run containing 470 lower resolution images (most were under 1MB) loaded in 23 seconds and loaded 141 MB. Another test run with 693 higher quality images (averaging about 5 MB per photo) from multiple folders was not so fun, loading 1614 MB in just under 5 minutes, and only about half of the images were loaded in memory at this point. So it's safe to say that size matters!
1. **No Exclusion Option**
In the case that you've configured a slideshow to include subfolders, you cannot exclude certain folders from the resulting directory tree.
1. **Private Slideshows Not Available**
At some point, private slideshows broke so they are currently not available
<hr />



## Data-Driven Slideshows
### Summary
This is found on the **/slideshow-db.php** page. This page has similar features to the file-based slideshow page, except that it is driven off of a database rather than physical folders of images in the file system, though the images still have to live on a local storage device. The UI has also been redesigned. Going forward, most features will be built for this page. This page has the following features:

### Features
#### Static Slideshows
This feature is still in **BETA**. These slideshows are pre-defined in the database. The slides are displayed in a specific order. These slideshows have the following features:
- **Slideshow Selection** - if multiple slideshows have been configured, you can choose which slideshow to watch via a dropdown selection.
- **Photo Resizing** - automatically resizes photos and slides based on the client's current viewport height. This is accomplished by a redirect and query string paramaters.
- **Manual Controls** - at any time you can move forward or backward in the current slideshow. This does not halt the slideshow in progress, but it does restart the timer.
- **Slideshow Speed** - a slider control in the UI controls how long each slide will be displayed for. Changes take effect after transitioning to the next slide.
- **Halt** - allows you to stop the slidehow on the current slide and pick up where you left off afterwards.
- **Private Slideshows** - via DB configuration, you can limit access to selected static slideshows. These slideshows only appear in the dropdown if you include a special query string parameter and value in your web request.
- **Private Images** - you can limit access to specific images via DB configuration. These images will only be included in slideshows if you are authorized to view them, even if the slideshow is a publicly available one.
- **See Slide Details** - see details of the slide including size and filename.
- **Update a Slide's Tags** - see and update which tags are associated to the current slide. Note that the slideshow interval is reset after adding/removing tags.

#### Tag-Based Slideshows
This feature is mature. These slideshows are based off of tags, and thus, are dynamic in nature. These slideshows include the following features:
- **Photo Resizing** - automatically resizes photos and slides based on the client's current viewport height. This is accomplished by a redirect and query string paramaters.
- **Manual Controls** - at any time you can move forward or backward in the current slideshow. This does not halt the slideshow in progress, but it does restart the timer.
- **Slideshow Speed** - a slider control in the UI controls how long each slide will be displayed for. Changes take effect after transitioning to the next slide.
- **Randomize Option** - allows you to randomize the slides in the current slideshow. Changes take effect immediately. Halting randomization will stop at the current slide.
- **Halt** - allows you to stop the slidehow on the current slide and pick up where you left off afterwards.
- **Private Images** - you can limit access to specific images via DB configuration. These images will only be included in slideshows if you are authorized to view them, even if the slideshow is a publicly available one.
- **Available Tags** - determines all tags that are available to you and allows you to create a slideshow by combining all photos in the database associated to the chosen tags. Photos will only be added once if a photo has more than one of the chosen tags.
- **Private Tags** - via DB configuration, tags can be marked as private. These tags will only be displayed if you are authorized to view them.
- **See Slide Details** - see details of the slide including size and filename.
- **Update a Slide's Tags** - see and update which tags are associated to the current slide. Note that the slideshow interval is reset after adding/removing tags.
- **Slideshow Modes** - presets for viewing a slideshow including:
    - **Normal** - standard UI and slideshow settings.
    - **Tagging** - only display slides that are not tagged with the "fully tagged" tag, minimize the slideshow settings pane at the far left, and increases the width of the slideshow info pane to make tagging slides a bit easier.
    - **Maximize** - puts the focus on the slideshow by minimizing the other two panes.

### Configuration
All configuration elements are defined in the *scripts/dbMainConfig.php* file. This builds an overlying *$configuration* array which is designed to contain all configuration elements including:
- a *$database* array that database connection configuration and the names of stored procedures in the configured DB used by this web application
- *$virtualRoots* and *$physicalRoots* arrays that define the root paths, both public and private, in the URLs and in the file-system respectively, for displaying and loading images
- hooks to retrieve the list of chosen tags and the selected slideshow mode from the query string

### Technical Details
#### Summary
Database-driven slideshows are a bit more intelligent that the file-based slideshows.
#### /slideshow-db.php
- the webpage that displays the database-driven slideshows including tag-based and statically-configured slideshows
- includes some javascript to detect the screen height and redirect to itself with the height in the query string, which allows PHP on the backend to resize the slides
#### /scripts/dbMainConfig.php
- configuration file including database connection details including stored procedure names, and virtual and physical file paths
- for the tag-driven slideshows, this file also includes wiring for the user-defined configuration for the current slideshow including chosen tags and the display mode
#### /scripts/slideshowFunctions.js
 - note that this includes a script that runs once the basic page has loaded
 - includes the original javascript slideshow library that this web app was built from and essentially acts as the 'engine' of the slideshow
 - uses methods shared between the file-based and DB-driven slideshows
    - **showSlides** - the entry point into the slideshow engine used by both file-based and DB-driven slideshows, but there is a fairly clear divide between the two
    - **plusSlides** - hides the current slide and shows the slide with the given index
    - **haltSlideshow** - an event handler that toggles the timer that controls the slideshow, controlled by the checkbox in the UI (ie. stops the timer if checked and restarts it if unchecked)
    - **randomizeChange** - an event handler that toggles the randomization of the slideshow. When checked, the index values of the array of slides is shuffled, and when it is not, they are returned to the order in which they are retrieved from the database
 - includes additional methods and services to add tags to the current slide, retrieve slides to display, load dynamic elements such as tags to be displayed, etc. Many of these are triggered on page load or when a slideshow generation request comes in from the UI
    - **loadAvailableTagsFromDb** - loads all available tags from the database via an API endpoint (loadTags.php). Includes logic to only retrieve private tags if authorized to do so. Returns JSON
    - **renderSlideshowTagsSelection** - receives JSON containing all tags and calls an API endpoint (renderTags.php) to build and return HTML to add to the page in order to render the tags. This service is used to populate the tag selection in the options pane as well as the slide info pane. An API parameter is used to add JS event handlers to the HTML to trigger the updateTags method when tags are added or removed
    - **loadAvailableStaticSlideshowNames** - loads all static slideshow names from the database via an API endpoint (loadStaticSlideshows.php). Includes logic to only retrieve names if authorized to do so. Returns JSON
    - **renderStaticSlideshowSelection** - receives JSON containing all static slideshow names and calls an API endpoint (renderStaticSlideshowNames.php) to build and return HTML to add to the page. This service is used to populate a list of slideshow names in the options pane
    - **determineSlideshowMode** - looks at the 'slideshowMode' radio button element in the UI and sets the visual 'mode' for the slideshow based on the value chosen. This function is called during the initial page load and is later applied via the applySlideshowModeToUI function when a slideshow starts
    - **applySlideshowModeToUI** - applies the chosen slideshow 'visual mode' to the UI. This value is used to determine how the UI is going to appear, and in the case of 'tagging' mode, will also filter out slides that have the 'fully tagged' tag
    - **loadTagSlideshowFromDb** - one of the main functions called when a slideshow is triggered from the UI. This function halts an existing slideshow, applies some customizations based on the chosen 'visual mode', determines if the user has private access, and then calls an API endpoint (loadSlides.php) to retrieve the relevant slide data based on the selections in the UI. If slides were loaded, this function will then trigger the slideshow to start by calling the showSlides function
- also includes some methods to control some behavioural components of the UI such as hiding/showing info panes etc
    - **toggleOptionsPane** - collapses or expands the entire options pane that displays all of the slideshow options. When collapsed, a small link is left behind to allow the user to expand it
    - **toggleSlideshowTypeOptionsPane** - toggles between the options panes for the tag-based slideshow and the static slideshows, hiding the current and showing the other
    - **toggleInfoPane** - collapses or expands the entire slide information pane that displays information on the current slide including tags. When collapsed, a small link is left behind to allow the user to expand it

#### /scripts/DbWebSlideshow.php
- server-side class that represents a tag-based slideshow
builds many of the UI elements such as the slideshow drop-down and the slides themselves, and retrieves the images from the file system
#### /styles/main-db.css
- includes all CSS styling for the file-based slideshows

### Known Bugs and Limitations
1. **Private Tags Available for Public Images**
When a public image is displayed during a private slideshow, private tags appear as options for adding tags and there is nothing stopping you from assigning private tags to a public image.
1. **Public Static Slideshows Can Include Private Images**
If you accidentally map up a private image to a public static slideshow, it will be included and displayed.
1. **Slide Info Only Loaded at the Start of a Slideshow**
When you start a slideshow, all slides and all info for each slide is loaded as well. Slide information is not reloaded each time a slide is displayed, which means that, if you set tags for a slide, and the slide appears later during the slideshow, the previous updates are not visible.
1. **No Constraints Resizing Smaller Images**
If an image is very small, it may be blown up to a point where it becomes distorted.
1. **Resizing of Larger Images May Cause Wrapping**
For images that get resized, if it's too wide to fit in the display area, it'll 'wrap over' and appear underneath the rest of the site instead of fitting nicely into the frame of the site.
1. **Cannot Add a New Tag**
There is currently no way to add a new tag from the webpage. You need to manually add it into the database.
1. **Cannot Easily Include All Images in a Slideshow**
There is no easy way to include all available images in a slideshow. You need to manually select every single tag.
1. **Video is Not Supported, but Animated GIFs will Render**
There is no support for including videos in a slideshow.
1. **Cannot Remove an Image from the Database**
There is no support for removing an image from the database. Once it's in there, you can only remove it by manually removing entries from the database, and you need to be careful to unmap associated tags as well.
1. **Cannot Remap an Image File**
There is no support for fixing an image in the database if it physically moves to a different location. If you move or rename a folder or image in the file system, the affected images end up broken and you cannot update the physical image location from within the web page.
1. **No Broken Image Detection**
The slideshow does not attempt to detect broken image links, so if images have been physically moved or deleted, the slideshow still tries to load and display those images.
1. **Tags Cannot be Nested or Grouped**
There is no existing concept of grouping tags together, or creating parent/child relationships between tags. The best you can do at the moment is to set up a naming convention. For example, if you want to tag images based on the location where they were taken, you can prefix those tags with "Location: ".
<hr />



## Image Scanner
### Summary
This is available on the **/scan.php** page. This page allows you to scan the images within a folder into the database. You can also choose to scan all subfolders as well. Data retrieved and added includes:
- full file path
- file name
- the dimensions (width and height) of the image
- a boolean field indicating whether or not the image should be considered secured
- tags you would like associated to the images being loaded from the requested folder
    - if a tag doesn't exist, it'll be created
    - for multiple tags, separate each tag with a comma - just be careful to not include commas within the tags themselves
    - tags can also be marked as secure

The scan process generates and displays a log file to the screen to detail the files found, which were added to the database (and which were not), and some details re: the parameters and results of the scan.

### Configuration
All configuration elements are defined in the *scripts/mainConfig.php* file. This builds an overlying *$configuration* array which is designed to contain all configuration elements, including database connection details and physical and virtual file roots for images.

### Technical Details
### Summary
The image scanner is a simple form that sumbits the parameters for the scan and population of image metadata to the server. Using those parameers, the FileScanner class in PHP takes those parameters, looks for all files in the designated location, retrieves and sets the appropriate metadata, and inserts records into the database to keep track of where the physical images are, what the paths will be, and what metadata is associated to each file.
#### /scan.php
- the webpage that presents the form to the user to collect the image scan parameters and metadata to add to the images, and displays the results after the scan has completed. The form posts back to itself, instantiates a FileScanner object in PHP, gathers the inputs from the form, and passes them, along with the nested array of configuration details generated by the main config file, into a function call
#### /scripts/FileScanner.php
- server-side class that provides the file-scanning feature.
- one main function to perform the scan and two separate helper functions to scan a single folder with and without subfolders
- uses built-in PHP classes (RecursiveDirectoryIterator and RecursiveIteratorIterator) to recursively iterate through the subfolders within the provided folder
- builds a list of tags to add to each image assuming that the input is a comma-delimited list
- retrieves some metadata for each image including its filename and its dimensions
- includes logic and a test photo for unit tests
- skips any physical images that already exist in the database (based on full physical file path)
- associates each image to all tags submitted from the UI, creates any that do not already exist, and inserts this data into the database
- populates a scan log variable during the process which the UI reads and displays to the user



### Known Bugs and Limitations
1. **Cannot Select Existing Tags**
There is no pre-populated list of tags to select from when adding tags to the images being imported; you need to know which tags currently exist and, if you add a tag that does not exist, a new tag is created.
1. **Cannot Define Tags For Individual Images or Group of Images**
When you define tags for the images you are importing, they are applied to all images; you cannot apply them to a single image or a group of images.
1. **All Tags Are Either Secured or Not**
When you tag images being scanned into the database, you cannot specify a mix of public and private tags; they are all either secured or not secured
<hr />



## Known Bugs and Limitations (Application Level)
1. **Root Folders and Virtual Paths are Hardcoded**
This web application supports separate root folders and virtual paths for public and private slideshows. These values are defined in the config files, and out-of-the-box are set to:
- Public
    - Virtual Path: /myphotos/private/
    - Physical Path: E:\\MyPhotos\\
- Private
    - Virtual Path: /myphotos/
    - Physical Path: E:\\MyPhotos\\Private\\

You will need to bind these virtual folders to their associated physical paths in your web server configuration. For example, in Apache (httpd.conf):

```html
    #-E-drive folders for the TEST Web-Slideshow Web App
    Alias "/myphotos" "E:\MyPhotos"
    <Directory "E:\Photos">
        Require all granted
    </Directory>

    Alias "/private_photos" "E:\MyPhotos\Private"
    <Directory "E:\MyPhotos">
        Require all granted
    </Directory>
```
<hr />



## Technical Notes
### Running Unit Tests
In the terminal / command window, navigate to the root folder and type the following command to run all unit tests:
```
vendor/bin/phpunit tests --configuration ./tests --coverage-clover ./tests/results/coverage.xml --debug --log-junit ./tests/results/testResults.xml --verbose
```
You can also run a single test, without generating a coverage report, using the following command:
```
vendor/bin/phpunit tests --filter buildSlidesHtml_singleValidPhoto ./tests
```

### Setting up Apache Web Server to Allow Requests from LAN
1. Set the "ServerName" value in the Apache httpd.conf file to your IP (if you connect to the LAN using DHCP, this will change from time to time) on port 80.
    ```
    ServerName 192.168.0.29:80
    ```
1. Set the "Listen" value to all IP addresses on 80:
    ```
    Listen *:80
    ```
1. Find your "DocumentRoot" setting and it's accompanying Directory node and set up "Require" statements for each IP address or IP range you want to serve content to:
    ```
        Require host localhost
        Require ip 127.0.0.1
        Require ip 192.168 
    ```
1. Open up your firewall to allow internal incoming requests on port 80 for Apache
    - In Windows 10, you will likely need to navigate to Update & Security > Windows Security > Firewall & Network Protection, and click on the Advanced Settings link near the bottom.
    - You then need to go to the Inbound rules, find Apache Web Server, and either change an existing rule or set up a new rule to allow local port 80 and local IP addresses of your choosing (potentially, 192.168.0.0 to 192.168.0.255). You may need to set this for one profile or another (public or private - I needed public apparently).
1. Consider setting up a static IP on your internet connection. You can do this in Windows by:
    - opening your Network & Internet Settings
    - clicking on Properties to view your current connection details
    - finding the IP Settings section and clicking on Edit
    - editing your IP settings (likely IP v4) to manually specify your IP address. You'll likely want your subnet prefix length to be set to 32.
    
**Note**: Be careful when setting your static IP address. Log into your router and ensure that the address is not in the range that your router will use will assigning local IP addresses. If you don't, you may run into weird network issues in cases when the DHCP server assigns your IP address to another device on the network.
<hr />



## History
### v5.3.0
- Significant improvements and additions to the readme documentation including technical details and better separation of documentation between products
- UI improvements related to the slideshow duration (file-based and DB-based slideshows) and image dimensions (file-based slideshows)
- (Coming Soon) Technical: enhancements to automated tests
- (Coming Soon) Technical: connect test and coverage results to SonarCloud

### v5.2.0
- Added static slideshows, including UI enhancements to allow you to choose either static or tag-based slideshows and hide anything that does not apply to your select.
- UX Enhancement : display entire image path instead of just the filename. In the DB-based slideshow, this means adding an extra field in the slide info pane

### v5.1.1
- UX enhancement : instead of halting the slideshow when adding tags to, or removing tags from, the current slide, the slideshow interval is now restarted.

### v5.1
- bug fix: calculate the dimensions of images based on the adjusted height value in the query string rather than just the originally calculated height. This was preventing manual adjustments via the query string
- UI enhancements
    - remove the slideshow info in the footer of slides and reclaim that usused space to increase the overall size of the slide
    - move the slideshow info pane to the left of the slideshow itself
    - add links to allow the slideshow settings and slide info panes to be hidden and restored
- added 3 slideshow modes (UI presets) that can be chosen when generating a slideshow

### v5.0.1
- bug fix: tags not being rendered in alphabetical order
- minor styling changes
    - render tags in a smaller font size
    - reduce the width of the left and right panes

### v5.0
- architectural improvements to DB-driven slideshow to render HTML for one slide at a time
    - required creation of PHP server-side services that are called from the client via AJAX calls to:
        - load and render slides to begin a slideshow
        - load info/metadata for the current slide
        - load tags available for slideshow generation
        - render tags avaialble for slideshow generation or tags that are associated to the current slide
    - required some changes/improvements to the HTML structure

### v4.2
- improvements to the Database-driven Slideshow
    - add right column to display information on individual slides, including tags
    - add/remove tags associated to a slide by checking/unchecking them in the UI

### v4.1
- improved randomization feature in both the static and dynamic (DB-driven) slideshows:
    - fixed a bug that caused the randomized slideshow to not include all slides, and include some slides multiple times.
    - when you halt a randomized slideshow, the slideshow now continues from the current slide instead of restarting from the first slide.
- ui improvements
    - contrain the slideshow options pane at the left to specific width (300px)
    - reduce width of the slide footer (file name and dimensions) so that text doesn't overflow the boundaries
    - reduce padding in fieldsets in the left pane

### v4.0
- add new slideshow-db.php page to show slideshows based on image metadata in a database rather than physical folders. This includes:
    - listing all tags available to be included in a slideshow, including private tags only if authorized
    - compile and include images based on selected tags, including private images only if authorized
    - same features as the original slidehow page including randomization, halting the slidehow, manual navigation, and adjusting slideshow speed

### v3.2
- add new scan.php page to scan and load image metadata into the database. Includes options to:
    - scan all subfolders or just the contents of the requested folder
    - set images as secured or not secured
    - add tags to all images being scanned
    - set each tag as secured or not secured

### v3.1
- detect current viewport height, redirect, and use that value to proportionally resize slides and images, taking the 'chrome' into account
- include original and resized dimensions in the slide
- ignore all non-image files
- adjust default slideshow speed to 30 seconds and increment to 5 seconds

### v3.0
- transformed the slideshowControl file into a class and updated the application as needed
- added unit tests for the new WebSlideshow class
- added a couple of GitHub workflows
    - PHP Composer - to build/validate Composer dependencies
    - PHP Unit Tests - to run PHPUnit tests and generate test and coverage reports

### v2.1
- added composer
- added phpunit and symfony/yaml packages

### v2.0
- configuration restructuring to include all config elements in their own array
- updates to the main slideshow page and the underlying PHP control to pass the full configuration from the front-end to the back-end when populating the slideshows dropdown and rendering the slideshow
- updates to make the root folders configurable

### v1.2
- addressed code smells and bugs resulting from SonarCloud scans
- addressed some PSR-2 compliance issues

### v1.1
- added feature to configuratively allow images in subfolders for a slideshow, through a new configuration element
- fixed a bug that causes an attempt to render folders in the slideshow

### v1.0
- first commits to GitHub, and thus, public availability
- support for defining multiple folders for slideshows via configuration while keeping the original element for single folders

### v0.5
- created a configuration file and wired that up to the rest of the application to get away from hard-coded virtual and physical paths/locations
- added support for multiple configurable slideshows via configuration
- included support to configure slideshows as public or private
- added the Halt slideshow feature
- bug fix: slideshows with a single photo caused an error to occur

### v0.4
- added Slideshow Speed feature
- bug fix: not specifying the special query string parameter no longer raises an error
- removed extraneous slideshow page that is no longer being used (all logic is now linked to a single page)

### v0.3
- added the Randomize Slideshow feature
- abstracted much of the PHP logic for scanning files and building HTML into it's own control/file

### v0.2
- Creation of a separate slideshow page which includes a security feature used to display the private slideshow instead of the public one (folders and paths still hard-coded at this point)
- Support for file-system paths via virtual paths configured on the web server
- Added filename as a caption on each slide

### v0.1
- Initial slideshow creation based on a couple of tutorials in W3Schools ([Slideshow](https://www.w3schools.com/howto/howto_js_slideshow.asp) and [Slideshow Gallery](https://www.w3schools.com/howto/howto_js_slideshow_gallery.asp)) but heavily customized to:
    - handle next and previous links to cycle through the slides
    - PHP to scan and build HTML elements for each file in a hardcoded folder/location
    - styling to better fit the photo on the display (floating width based on set height)
