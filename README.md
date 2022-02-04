# Web Slideshow
## Summary
This is a web application that can display custom slideshows in a browser for locally-stored photos.

## Features
1. **Photo Resizing** - automatically resizes photos and slides based on the client's current viewport height. This is accomplished by a redirect and via query string paramaters.
1. **Manual Controls** - at any time you can move forward or backward in the current slideshow. This does not halt the slideshow in progress, but it does restart the timer.
1. **Slideshow Speed** - a slider control in the UI controls how long each slide will be displayed for. Changes take effect after transitioning to the next slide.
1. **Randomize Option** - allows you to randomize the slides in the current slideshow. Changes take effect immediately.
1. **Halt** - allows you to stop the slidehow on the current slide and pick up where you left off afterwards.
1. **Slideshow Selection** - if multiple slideshows have been configured, you can choose which slideshow to watch via a dropdown selection.
1. **Private Slideshows** - via slideshow configuration, you can limit access to selected slideshows. These slideshows only appear in the dropdown if you include a special query string parameter and value in your web request. Private slideshows appear in a red font, whereas public slideshows appear in green.
1. **Multiple Folders** - via slideshow configuration, you can specify either a single folder or mutliple folders to include in a slideshow. In each case, the assumption is that there are only valid images in those folders
1. **Recursive Folders** - via slideshow configuration, you can also choose to include all subfolders of each folder configured for a slideshow.
### Features Under Development
1. **Folder Scanner** - via the /scan.php page. This page allows you to scan a folder, with or without it's subfolders, into a database. Data retrieved and added includes the full file path, the file name, the width and height of images, and a bit indicating whether or not the image should be considered secured (to be used for private slideshows). This scanner also includes the option to create and associate tags (comma-delimited set) to each image scanned. Tags are associated to images and tags can also be marked as secured. **Note**: The slideshow page does not load images from the database yet, and there are no admin options to modify the data in the web UI yet either.
1. **Database-Driven Slideshow** - via the /slideshow-db.php page. This page has very similar features to the main slideshow.php page, except that it is driven off of the database and by tags rather than physical folders of photos/images. The UI has also been redesigned slightly. For the moment, this page has the following features, but should be considered as a beta version for the moment:   
   - **Available Tags** - determines all tags that are available to you and allows you to create a slideshow by combining all photos in the database associated to the chosen tags. Photos will only be added once if a photo has more than one of the chosen tags.
   - **Private Tags** - tags can be defined as 'secure' in the database, and secure tags will only be displayed if you are authorized to view them.
   - **Private Images** - images stored in the database can also be marked as 'secure'. These images will only be included in slideshows if you are authorized to view them.
   - **Manual Controls** - at any time you can move forward or backward in the current slideshow. This does not halt the slideshow in progress, but it does restart the timer.
   - **Slideshow Speed** - a slider control in the UI controls how long each slide will be displayed for. Changes take effect after transitioning to the next slide.
   - **Randomize Option** - allows you to randomize the slides in the current slideshow. Changes take effect immediately. Halting randomization will stop at the current slide.
   - **Halt** - allows you to stop the slidehow on the current slide and pick up where you left off afterwards.
   - **See Slide Details** - see details of the slide including size and filename
   - **Update a Slide's Tags** - see and update which tags are associated to the current slide, and halt the slideshow on the first change

## Slideshow Configuration
All configuration elements are defined in the */mainConfig.php* file. There is an overlying $configuration array which is designed to contain all configuration elements, and then within, there are separate arrays that contain all slideshow configurations as well as the root physical and virtual folders to use for both private and public slideshows.

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
- **array index**: This value uniquely idenfities the slideshow and must be a unique value.
- **name**: This is the visible name of the slideshow and appears in the UI.
- **public**: This is a boolean value that indicates if the slideshow is public (*true*) or private (*false*).
- **physicalPath**: This is the physical path, relative to the root folder, including the folder in which the images are located. This path is appended to the currently hardcoded *$rootFolder* value to build the full physical path to the folder being included in the slideshow. Use this key if you want to include only one folder of images, or if the other folders in your slideshow are subfolders of a single parent folder.
- **physicalPaths**: This is a separate key that you can use to define multiple folders for your slideshow. Instead of including a single value (path), you can define an array of values (paths). This key-value pair overrides the older physicalPath element.
- **includeSubfolders**: This is a boolean element that defines whether subfolders should be included in the slideshow (*true*) or not (*false*).

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

## Currently Known Bugs and Limitations
### Root Folders and Virtual Paths are Hardcoded
This web application supports separate root folders and virtual paths for public and private slideshows. These values are defined in the mainConfig.php file, and out-of-the-box are set to:
- **Public**
    - Virtual Path: /myphotos/private/
    - Physical Path: E:\\MyPhotos\\
- **Private**
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

### Memory Consumption Issues for Large Slideshows in slideshow.php
A block of HTML is added to the webpage for every single file within each of the configured folders for a slideshow. This means that the page source can grow uncontrollably if your slideshow simply contains too many images. In addition, all images are loaded in at load time, compounding the issue. In addition, length and width of images aren't specificed in the HTML, so all images are loaded in their original form.<br>
So how many images are too many you ask? A test run containing 470 images lower resolution images (most were under 1MB) loaded in 23 seconds and loaded 141MB. Another test run with 693 higher quality images (averaging about 5MB per photo) from multiple folders was not so fun, loading 1614MB in just under 5 minutes, and only about half of the images were loaded in memory at this point. So it's safe to say that size matters!

**Note: This no longer applies to the DB-driven slideshow (slideshow-db.php)!** This page will only render one slide at a time, along with it's metadata, using AJAX calls to server-side services.

### No Exclusion Option
In the case that you've configured a slideshow to include subfolders, you cannot exclude certain folders from the resulting directory tree.

## Technical Notes
### Running Unit Tests
In the terminal / command window, navigate to the root folder and type the following command to run all unit tests:
```
vendor/bin/phpunit tests --configuration ./tests --coverage-clover ./tests/results/coverage.xml --debug --log-junit ./tests/results/testResults.xml --verbose
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


## History

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
