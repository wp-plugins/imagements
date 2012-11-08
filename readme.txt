=== Imagements ===
Contributors: williewonka, dc5ala
Tags: image, in, comments, images, comment
Requires at least: 3.4.1
Tested up to: 3.4.2
Stable tag: 1.2.1
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Let users use images in the comment section!

== Description ==

This plugin will:
* let users upload and put in comments images.
* let users report images wich they think is inappropriate.
* let admins moderate images and block them.
* offer 2 systems to put insert the images in a comment. (auto = image will be automaticly inserted at end of comment, user = user will have to insert the tag for image anywhere he/she likes in the comment)
* is fully localized and ready for translation.
* be updated reguraly with new features

If you want to see a certain function in this plugin, please email me(the author) at williewonka341@gmail.com

<b>newest update:</b>

update 1.2.1 is done! Now the admin can choose to skip the tag part, if the tag use setting is set to auto the plugin will automaticly insert the image at the end of the comment if the users uploads one.
When this setting is activated the user will no longer be able to give a name to the image, it will just insert the filename as name.
Furthermore the plugin will now check the hashes of images so that it is no longer possible to upload the same image twice if it has a different filename.
And last but not least the bug with the tag is finally resolved! you can now choose in the imagements general options menu the tag you want the plugin to use. Please note that the plugin will not automaticly update the comments to the new tag.

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
<li>the option to upload and insert multiple images at the same time</li>
<li>system that images need to be seen by admin first before they are seen publicly</li>
</ul>

known bugs:
<ul>
<li>when the tag is changed the comments are not automaticly updated</li>
<li>no email notification yet for the report image system</li>
</ul>
<b>if you find any bugs, please mail me at williewonka341@gmail.com

== Installation ==
1. upload the imagements folder to wp-content/plugins
2. activate the plugin through the dashboard under 'plugins'
3. choose a tag you want to use in the imagements general options under settings on the dashboards, the default one is 'image'
4. let your users know how to use it (very important!)

== Frequently Asked Questions ==

= How did you get the funny name? =

image and commments put together = imagements

= I have found a bug/have a problem with the plugin, what should i do? =

email me at williewonka341@gmail.com and i wil try to fix it as soon as possible

= how do I use this plugin? =

a user has to tick the checkbox in the comment form and choose a name for the image and choose a file. you can then use the tag [image=insert choosen image name here] in the comment or if you have set the automatic system in the options the plugin will automaticly insert the image at the end of a comment.
The plugin will automaticly insert the proper image html tag in the comment when someone views the comments.

= I have multiple errors when i try to use the plugin = 

Try to deactivate and reactivate the plugin. If this doesn't fix it, then email me the errors to williewonka341@gmail.com

== Screenshots ==

1. screen showing the upload form
2. screen showing the image in a comment

== Changelog ==

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