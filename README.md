# Web Slideshow
## Summary
This is a web application that can display custom slideshows in a browser for locally-stored photos.

## Features
1. **Manual Controls** - at any time you can move forward or backward in the current slideshow. This does not halt the slideshow in progress, but it does restart the timer.
1. **Slideshow Speed** - a slider control in the UI controls how long each slide will be displayed for. Changes take effect after transitioning to the next slide.
1. **Randomize Option** - allows you to randomize the slides in the current slideshow. Changes take effect immediately.
1. **Halt** - allows you to stop the slidehow on the current slide and pick up where you left off afterwards.
1. **Slideshow Selection** - if multiple slideshows have been configured, you can choose which slideshow to watch via a dropdown selection.
1. **Private Slideshows** - via slideshow configuration, you can limit access to selected slideshows. These slideshows only appear in the dropdown if you include a special query string parameter and value in your web request. Private slideshows appear in a red font, whereas public slideshows appear in green.
1. **Multiple Folders** - via slideshow configuration, you can specify either a single folder or mutliple folders to include in a slideshow. In each case, the assumption is that there are only valid images in those folders
1. **Recursive Folders** - via slideshow configuration, you can also choose to include all subfolders of each folder configured for a slideshow.

## Slideshow Configuration
Configuration of available slideshows is defined in the */mainConfig.php* file. For now, it is a separate PHP file that includes associative arrays for the configuration elements of each slideshow and also includes some logic to determine the default slideshow to run if there are multiple slideshows defined and available, and determines the currently chosen slideshow - either the desired default or the chosen one from the dropdown and subsequent form submission.

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

Each configuration element is documented below (keep in mind that the keys are case-sensitive):
- **array index**: This value uniquely idenfities the slideshow and must be a unique value.
- **name**: This is the visible name of the slideshow and appears in the UI.
- **public**: This is a boolean value that indicates if the slideshow is public (*true*) or private (*false*).
- **physicalPath**: This is the physical path, relative to the root folder, including the folder in which the images are located. This path is appended to the currently hardcoded *$rootFolder* value to build the full physical path to the folder being included in the slideshow. Use this key if you want to include only one folder of images, or if the other folders in your slideshow are subfolders of a single parent folder.
- **physicalPaths**: This is a separate key that you can use to define multiple folders for your slideshow. Instead of including a single value (path), you can define an array of values (paths). This key-value pair overrides the older physicalPath element.
- **includeSubfolders**: This is a boolean element that defines whether subfolders should be included in the slideshow (*true*) or not (*false*).

## Currently Known Bugs and Limitations
### Root Folders and Virtual Paths are Hardcoded
While this web application does support separate root folders and virtual paths for public and private slideshows, those values are hardcoded in the PHP Slideshow Control. Current settings are:
- **Public**
    - Virtual Path: /myphotos/private/
    - Physical Path: E:\\MyPhotos\\
- **Private**
    - Virtual Path: /myphotos/
    - Physical Path: E:\\MyPhotos\\Private\\

This means that you'll need to bind these virtual folders to their associated physical paths in your web server configuration. For example, in Apache (httpd.config):

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

### Slideshows Include All Files Found
The Slideshow Control doesn't restrict itself to only images. If the folder(s) in your slideshow include other file types, it will try to include those as images in the slideshow.

### No Exclusion Option
In the case that you've configured a slideshow to include subfolders, you cannot exclude certain folders from the resulting directory tree.

## History

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