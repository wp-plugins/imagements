=== Imagements ===
Contributors: williewonka, dc5ala
Tags: image, in, comments, images, comment
Requires at least: 3.0.0
Tested up to: 3.7.1
Stable tag: 1.2.5
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Let users use images in the comment section!

== Description ==

This plugin will:
<ul>
<li>let users upload and put in comments images. Multiple images at the same time is supported</li>
<li>let users report images wich they think is inappropriate.</li>
<li>let admins moderate images and block them.</li>
<li>offer 2 systems to put insert the images in a comment. (auto = image will be automaticly inserted at end of comment, user = user will have to insert the tag for image anywhere he/she likes in the comment)</li>
<li>is fully localized and ready for translation.</li>
</ul>
If you want to see a certain function in this plugin, please email me(the author) at williewonka341@gmail.com or open an issue at my github: github.com/williewonka/imagements

<b>latest news:<b>
version 1.3.4 is now available! This version marks the merge of the free and (intended but never published) premium version. This means a ton of new features which include multiple file uploads at the same time, lightbox effects, extensive moderator options and much more.
WARNING IF YOU ARE UPDATING FROM VERSION 1.2.5 THEN FIRST BACKUP THE UPLOADED IMAGES AND MOVE THEM TO THE WP_CONTENT/IMAGES/ FOLDER. THIS FOLDER NEEDS TO BE CREATED FIRST, if you fail to do this you WILL LOSE ALL YOUR IMAGES



if you find any bugs, please mail me at williewonka341@gmail.com

== Installation ==
1. upload the imagements folder to wp-content/plugins
2. create an images folder in the wp_content/uploads/ folder
3. activate the plugin through the dashboard under 'plugins'
4. set the settings on the dashboard under settings->imagements options to your desired choice. This is very important because the default values will not suit everyone!
5. let your users know how to use it (very important!)

== Frequently Asked Questions ==

= Where can I moderate images? =

Go to the dashboard and under comments->images in comments you can moderate images

= How did you get the funny name? =

image and commments put together = imagements

= I have found a bug/have a problem with the plugin, what should i do? =

email me at williewonka341@gmail.com and i wil try to fix it as soon as possible

= I would like to see feature x in this plugin/ i have this great idea y for this plugin =

email me the idea at williewonka341@gmail.com and i will see if i can implement it.

= how do I use this plugin? =

Their are 2 ways a user can use this plugin. The first method is when the tag use is set to user. In this method the user specifies a file and name for the image and then inserts the tag (default [image=~name~]) anywhere in the comment to use the image.
The second method is when the tag use is set to auto. In this method the users only specifies a file and the image is automaticly inserted at the end of the comment.

= I have multiple errors when i try to use the plugin = 

Try to deactivate and reactivate the plugin. If this doesn't fix it, then email me the errors to williewonka341@gmail.com

== Screenshots ==

1. example of images in comments
2. the commentform with the upload image field
3. the options menu on the dashboard

== Changelog ==

= 1.3.4 =
* WARNING IF YOU ARE UPDATING FROM VERSION 1.2.5 THEN FIRST BACKUP THE UPLOADED IMAGES AND MOVE THEM TO THE WP_CONTENT/IMAGES/ FOLDER. THIS FOLDER NEEDS TO BE CREATED FIRST, if you fail to do this you WILL LOSE ALL YOUR IMAGES
* ADDED: lightbox effects on images, can be switched off in options
* ADDED: free and premium version are merged, the plugin will be continued as one free project and will also be published on github
* FIXED: images displayed will now of the size specified for the thumbnail and the lightbox image (and on file) will be the size specified under normal size
* FIXED: fixed a bug where  images uploaded with a space in them would break the plugin
* FIXED: several security issues (that will not be disclosed)
* FIXED: update the code to the standard of wordpress version 3.7
* FIXED: fixed a bug where empty upload fields were not ignored but instead threw an error

= 1.3.3 =
* ADDED: option to specify the uploadfolder
* ADDED: changed upload to wp_content instead of plugin folder, this way the images arent destroyed when updating
* FIXED: when comment is empty and tag system is set to auto, the tag is entered and wordpress doesnt reject the comment as empty
* FIXED: multiple small bugs
* FIXED: bug wich occured on servers with certain settings wich would break the plugin.

= 1.3.2 =
* ADDED: option to only let members upload images
* FIXED: when changing tag, comments change too

= 1.3.1 =
* ADDED: new moderatortools, moderators can now block or delete images

= 1.3.0 =
* ADDED: users can now upload multiple images in one go up to a limit wich can be set in the option menu.
* FIXED: when imagename is not found in database, it is no longer possible to report image.

= 1.2.2 =
* FIXED: when there is no text in comment, plugin will insert the word 'image' so that the image is still displayed
* FIXED: some small security things and better flow of code.
* FIXED: updated screenshots and FAQ

= 1.2.1 =
* ADDED: option to skip the tag, plugin will no automaticly input the image tag at the end of a comment
* FIXED: the tag can now be chosen by admin, default is 'image'
* FIXED: no longer possible to upload the same image twice if it has a different filename

= 1.2.0 =
* ADDED: report system for images
* ADDED: several changes to the structure of the plugin so it works better, also preparation for future updates
* FIXED: new update detection system, the plugin will now automaticly update the database structure and there is no need to deactivate/reactivate the plugin anymore.
* FIXED: some small bugs

= 1.1.0 =
* ADDED: automatic resize
* ADDED: automatic conversion to jpeg
* ADDED: option menu added under settings on the dashboard (currently only option for the automatic resize)
* FIXED: database structure updated, also for future updates

= 1.0.0 =
* first version

== Upgrade Notice ==

= 1.3.4 =
* merge of free and premium version, please see changelof for changes
* WARNING IF YOU ARE UPDATING FROM VERSION 1.2.5 THEN FIRST BACKUP THE UPLOADED IMAGES AND MOVE THEM TO THE WP_CONTENT/IMAGES/ FOLDER. THIS FOLDER NEEDS TO BE CREATED FIRST, if you fail to do this you WILL LOSE ALL YOUR IMAGES

= 1.2.2 =
* please upgrade to this version to clear out some bugs and security isues

= 1.2.1 =
* please read the readme for the latest change and choose if you want automaticly or user tag input.

= 1.2.0 =

* Email notification is not yet added for the report system, you will have to manually check every now and then for new reports.
* reactivation is no longer needed when you upgrade the plugin, the plugin will handle the database structure update now self.

= 1.1.0 =
* Please deactivate and reactive the plugin to update the database structure.
* Please note that images already on the server wont be resized.
* The resize process is irreversible so please set the settings right as soon as possible.
* If you put words in the setting fields rather then numbers, the size will by resetted to 300 by 300 pixels. This is to avoid errors.

= 1.0.0 =
* first version