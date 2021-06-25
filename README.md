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

### Memory Consumption Issues for Large Slideshows
A block of HTML is added to the webpage for every single file within each of the configured folders for a slideshow. This means that the page source can grow uncontrollably if your slideshow simply contains too many images. In addition, all images are loaded in at load time, compounding the issue. In addition, length and width of images aren't specificed in the HTML, so all images are loaded in their original form.<br>
So how many images are too many you ask? A test run containing 470 images lower resolution images (most were under 1MB) loaded in 23 seconds and loaded 141MB. Another test run with 693 higher quality images (averaging about 5MB per photo) from multiple folders was not so fun, loading 1614MB in just under 5 minutes, and only about half of the images were loaded in memory at this point. So it's safe to say that size matters!

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


## History

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
