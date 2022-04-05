=== ezCache ===
Contributors: upress, ilanf
Tags: upress,hosting,cache,speed,boost
Requires PHP: 5.6
Requires at least: 4.6
Tested up to: 5.7
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

EzCache is an easy and innovative cache plugin that will help you significantly improve your site speed.

== Description ==

EzCache is an easy and innovative cache plugin that will help you significantly improve your site speed.
The plugin comes in a simple and easy installation, without the need for advanced technical knowledge, offers you the opportunity to make your site much faster in a few simple steps, cache pages on your site, automatically optimize images using WebP format to reduce the size of your site's images by tens of percent and save You need the extra image minimization plugin.

In addition, the plugin allows you to minimize advanced HTML files, JAVA SCRIPT files
And CSS files
In the advanced settings of the extension, you can easily save advanced settings, such as:
Configure caching by page type, set cached links,
Exclude certain user types.
And of course, you can always view statistics that will always keep you updated on your site's caching performance.

We created ezCash to take the new decade's speed experience and bring it to your WordPress sites easily and quickly

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/ezcache` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress

== Screenshots ==

1. Plugin main screen

== Changelog ==
= 1.5.1 =
- Fix error when wp_query is not available
- Fix misspelling in plugin description
- Fix REST API namespace warning
- Fix REST API endpoint URL
- Uninstall will not delete settings
- Added reset settings button

= 1.5 =
- Rewritten the WebP converter functionality to less strain the system while running
- Fixed WPML support
- Fixed WebP converter duplicating .webp extension when replacing images in specific circumstances
- Fixed Activation/Deactivation hooks not properly running
- Added more cache lifetime options
- Added option to clear homepage cache when updating a post
- Removed session usage to prevent interfering with WordPress' loopback

= 1.4.1 =
- FIX: Reverted minified CSS position
- FIX: Translation will now load correctly
- FIX: CSS minification optimization
- ADD: WebP now supports <style> tags

= 1.4 =
- FIX: Clearing cache will delete cache for all languages on a multi language or multisite website
- FIX: CSS Combiner not combining files in multi language websites
- FIX: UI fixes and improvements
- ADD: Separated option to not serve cached content to comment authors from the logged in users
- ADD: Added additional detections for images. WebP Conversion should take effect on images with srcset and images set as background images in inline CSS
- ADD: ezCache will now automatically fix installation problems
- ADD: When choosing to cache pages for logged in users the Admin Bar will be automatically hidden
- ADD: Added option to combine CSS files to the footer
- ADD: Added option to insert Critical CSS - use with "Combine CSS in footer" to eliminate render blocking CSS
