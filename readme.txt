=== Omni Secure Files ===
Contributors: jsapara,omnilogic
Tags: files, upload, secure, private
Requires at least: 3.0.0
Tested up to: 3.3.1
Stable tag: 0.1.15

Provides a back end secure area to upload and share files. Perfect for a simple intranet file sharing site. Supports large file uploads.

== Description ==

Provides a back end secure area to upload and share files. Perfect for a simple intranet file sharing site. Supports large file uploads.

Features:

* Rename files *NEW*
* Set max file upload size *NEW*
*	Can be configured to store files outside of your web root directory
*	Simple permission system based on roles
*	Supports uploading large files

== Installation ==

1.	Upload zip package through WordPress backend and activate plugin.
1.	Configure secure paths to store files

== Screenshots ==
1.	File browser
1.	Upload file
1.	Setup

== Frequently Asked Questions ==

= I cannot upload files =

You may have a theme or plugin that re-registered jquery. Review your theme for any calls to: wp_deregister_script().

If found, make sure the add_action that calls this code uses: wp_enqueue_scripts/admin_enqueue_scripts

If you find a plugin that does this, contact the author and encourage them to properly override jquery.

= I cannot rename/delete files =

Your web provider likely has mod_security enabled for your site. You should be able to contact them and have it disabled
or updated to support applications which make use of AJAX posting.

= I get duplicate files when I upload =

Make sure you are running the lates version of Omni Secure Files.

= My downloads are corrupt =

You probably have WP_DEBUG enabled, which in some cases can output extra text into downloads.

== Upgrade Notice ==

= 0.1.14 =
Important security update

= 0.1.12 =
Minor update for some issues with order of script loading in some rare cases

= 0.1.10 =
Adjusted order of script loading to fix some issues related to IE 8/9.

== Changelog ==

= 0.1.12 =
Added a jQuery enqueue to resolve some order of loading issues in some cases.

= 0.1.11 =
* Minor update to CSS includes

= 0.1.10 =
* Fixed order of scripts loading issue that affected some installations

= 0.1.9 =
* Added additional warnings about read/write permissions for upload directories
* Added warning about WP_DEBUG possibly causing download issues
* Fixed error in menu load callback
* Added more error detection on download processor. More descriptive error messages.

= 0.1.8 =
* Fixed order scripts are loaded to further prevent conflicts with plUploader

= 0.1.7 =
* Fixed conflict with WordPress 3.2+ and plUploader

= 0.1.6 =
* Updated support information
* New ownership, development starts again soon!

= 0.1.4 =
* Fixed bug with max upload size not being set

= 0.1.3 =
* Fixed UI glitch in WordPress 3.1
* Added renaming of folders and files
* Added ability to set max file upload size

= 0.1.2 =
* Fixed meta mark up

= 0.1.1 =
* Inital public release

