=== Imagements ===
Contributors: williewonka, dc5ala
Tags: image, in, comments, images, comment
Requires at least: 3.4.1
Tested up to: 3.4.1
Stable tag: 1.2.0
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Let users use images in the comment section!

== Description ==

This plugin lets users put images in comments. The user can upload the image through the comment form to your server and choose a easy name for it.
Then it puts the [afbeelding=<name>] tag in the comment and the image will appear!
The development is still going strong but most of the features I thought off are now in the plugin, if you have ideas of features email me at williewonka341@gmail.com !

WARNING: due to technical reasons the tag to use in the comments is currently only avaible in dutch, so you need to use [afbeelding=insert choosen image name here] for the moment.
im working on this problem, but its a bit weird coding thing. this will be fixed as soon as possible in a future update.

newest updates:

update 1.2.0 is now out! Now users can report an image with the report button and admins can now block and delete images.
Also new in the dashboard page is the section where admins can look at the reported images and decide to block them or not. You can also unblock and permantly delete images here. Go to dashboard->comments->imagements reports to see the new menu.
Also improved is the update system. Now the plugin will automaticly update the database structure, so reactivating the plugin is no longer needed.

update 1.1.0 is live! automatic resize is now added. Go to the settings on the dashboard and under imagements(under settings on the dashboard) you can choose the maximum size in pixels.
The image is automatically converted to jpeg to conserver space. The actual file on the server is resized so its not just setting property settings in the img tag. This is also to conservers diskspace.
The plugin will keep the aspect ratio of the image.

upcoming updates:
<ul>
<li>configurable options such as:</li>
<ol>
<li>the tag users use in comments</li>
<li>what types of images are allowed</li>
<li>the folder where the images will be put on your server (it now uses the plugin folder as default)</li>
</ol>
<li>more languages (im dutch so that one will come shortly)</li>
<li>fixes for the known bugs</li>
<li>and much more if someone requests it</li>
</ul>

known bugs:
<ul>
<li>the tag used in the comments is in dutch, you have to use [afbeelding=name here] for now. this will be fixed in the next version</li>
<li>no email notification yet for the report image system</li>
<li>if you rename a image, you can upload the same image twice, but with different name</li>
</ul>
<b>if you find any bugs, please mail me at williewonka341@gmail.com

== Installation ==
1. upload the imagements folder to wp-content/plugins
2. activate the plugin through the dashboard under 'plugins'
3. let your users know how to use it (very important!)

== Frequently Asked Questions ==

= How did you get the funny name? =

image and commments put together = imagements

= I have found a bug/have a problem with the plugin, what should i do? =

email me at williewonka341@gmail.com and i wil try to fix it as soon as possible

= how do I use this plugin? =

a user has to tick the checkbox in the comment form and choose a name for the image and choose a file. i can then use the tag [afbeelding=insert choosen image name here] in the comment. The plugin will automaticly insert the proper image html tag in the comment.

= I have multiple errors when i try to use the plugin = 

Try to deactivate and reactivate the plugin. If this doesn't fix it, then email me the errors to williewonka341@gmail.com

== Screenshots ==

1. screen showing the upload form
2. screen showing the image in a comment

== Changelog ==

= 1.2.0 =
* ADDED: report system for images
* ADDED: several changes to the structure of the plugin so it works better, also preparation for future updates
* FIXED: new update detection system, the plugin will now automaticly update the database structure and there is no need to deactivate/reactivate the plugin anymore.
* FIXED: some small bugs

= 1.1.0 =
* automatic resize added
* automatic conversion to jpeg added
* database structure updated, also for future updates
* option menu added under settings on the dashboard (currently only option for the automatic resize)

= 1.0.0 =
* first version

== Upgrade Notice ==

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