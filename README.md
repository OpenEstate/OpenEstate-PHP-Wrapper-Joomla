OpenEstate-PHP-Wrapper for Joomla 0.5-SNAPSHOT
==============================================

This component integrates [*OpenEstate-PHP-Export*](https://github.com/OpenEstate/OpenEstate-PHP-Export) 
(in **version 1.x**) into a *Joomla* based website. 


Description
-----------

### English

The [*OpenEstate* project](https://openestate.org) provides a freeware software - called *OpenEstate-ImmoTool* - for 
small and medium sized real estate agencies all over the world.

As one certain feature of this software, the managed properties can be exported to any website that supports *PHP*. 
Together with this component the exported properties can be easily integrated into a *Joomla* based website without any 
frames.

**Please notice:** This component version does only work with PHP exports in version 1.x!

### Deutsch

Im Rahmen des [*OpenEstate*-Projekts](https://openestate.org) wird unter anderem eine kostenlose Immobiliensoftware 
unter dem Namen *OpenEstate-ImmoTool* entwickelt. Dies ist eine Softwarelösung für kleine bis mittelgroße 
Immobilienunternehmen.

Unter anderem können die im *OpenEstate-ImmoTool* verwalteten Immobilien als *PHP*-Skripte auf die eigene Webseite 
exportiert werden. Mit Hilfe dieser Komponente kann der *PHP*-Export unkompliziert in eine auf *Joomla* basierende 
Webseite integriert werden.

**Bitte beachten:** Diese Version der Komponente kann nur mit PHP-Exporten in Version 1.x genutzt werden!


Changelog
---------

### 0.5

*not released yet*

### 0.4.1

-   dropped support for *Joomla* 1.x & 2.x
-   fixed an error with PHP 7.1+
-   minor fixes and improvements on the codebase
-   switched license to GPLv2 or later
-   tested against the latest version of *Joomla* (3.8.13)

### 0.4

-   use the same version numbering for the different Joomla packages
-   less strict validation of the script URL in the component configuration
-   translated any source code comments into English
-   made some syntax fixes
-   tested against *Joomla* 1.5.26 / 2.5.27 / 3.3.6

### 0.1.8 / 0.2.8 / 0.3.8

-   Version 0.3.8 is the first public release for *Joomla* 3.0 / Platform 12.1.
-   Some smaller fixes

### 0.1.7 / 0.2.7

-   make use of meta elements, that were specified for the menu entry
-   put the configured `$MetaDescriptionTexts` from `myconfig.php` into the meta `description` element of property 
    listing pages

### 0.1.6 / 0.2.6

-   Some smaller fixes

### 0.1.5 / 0.2.5

-   Predefined filters / orderings are handled incorrectly under certain circumstances.
    (see [forum](http://board.openestate.org/viewtopic.php?f=7&t=8698))
-   Show all available ordering-options within administration dashboard.
    (see [forum](http://board.openestate.org/viewtopic.php?f=7&t=8763#p12562))

### 0.1.4 / 0.2.4

-   some compatibility fixes for *Joomla* 2.5
-   put selected language as meta `language` element into the page
-   put the title of the selected property as `<title>`element  into the page
-   put the short description of the selected property meta `description` element into the page
-   put the keywords of the selected property meta `keywords` element into the page
-   put general stylesheet (`style.php`) and additional stylesheets into the `<head>` section of the page

### 0.1.3 / 0.2.3

-   Reset filter selection, if a property page is accessed for the first time or if the website visitor jumps between 
    multiple property pages. 
    (see [Forum](http://board.openestate.org/viewtopic.php?f=7&t=3329))
-   Show a **notice** instead of an **error** message on the website, if *OpenEstate-ImmoTool* is currently exporting 
    properties to the webspace.
    (see [Forum](http://board.openestate.org/viewtopic.php?f=6&t=3208))
-   Fixed problem with SEO URL's (URL-Rewrite), that occurred solely on 1&1 webspaces.

### 0.1.2 / 0.2.2

-   Show an error message on the website, if *OpenEstate-ImmoTool* is currently exporting properties to the webspace.
    (see [Bug-Tracker #594](http://tracker.openestate.org/view.php?id=594))
-   Improved handling of SEO URL's for enabled and disabled `mod_rewrite`.
    (see [Joomla documentation](http://docs.joomla.org/How_do_you_implement_Search_Engine_Friendly_URLs_%28SEF%29%3F))
-   Enter a text or HTML code in the administration of a menu entry, that is displayed before / after the wrapped 
    content.
-   Property search fails in multilingual installations of *Joomla* 1.6 / 1.7 / 2.5.
    (see [Forum](http://board.openestate.org/viewtopic.php?f=16&p=3929#p3870))

### 0.1.1 / 0.2.1

-   Fixed compatibility issue with *Internet Explorer*.
    (see [Forum](http://board.openestate.org/viewtopic.php?f=7&t=1949))

### 0.2

-   First public release for *Joomla* 1.6 / 1.7 / 2.5.

### 0.1

-   First public release for *Joomla* 1.5.


License
-------

[GNU General Public License 2](http://www.gnu.org/licenses/gpl-2.0.html) or later
