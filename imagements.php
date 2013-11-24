<?php

/**
* Plugin Name: imagements
* Plugin URI: http://williewonka.site50.net/?page_id=9
* Description: lets your users put images in comments.
* Version: 1.3.4
* Author: williewonka
* Copyright 2012-2013  williewonka  (email : williewonka341@gmail.com)
*/

register_activation_hook(__file__, "imagements_init");
register_deactivation_hook(__file__, 'imagements_deac');
register_uninstall_hook(__file__, 'imagements_uninstall');
add_action('wp_enqueue_scripts', 'imagement_add_form_tag');
add_action('wp_enqueue_scripts', 'imagements_add_scripts');
add_filter('comment_text', 'imagements_input_image_in_comment');
add_action('comment_form_logged_in_after', 'imagements_additional_fields');
add_action('comment_form_after_fields', 'imagements_additional_fields');
add_filter('preprocess_comment', 'imagements_verify_uploading_info');
add_action('admin_menu', 'imagements_admin_add_page');
add_action('admin_init', 'imagements_admin_init');
add_action('init', 'imagements_check_report_form_input');
add_action('admin_init', 'imagements_act_on_admin_input');
add_action('init', 'imagements_version_check');
add_action('pre_comment_on_post', 'imagements_edit_comment');

define('PLUGIN_PATH', dirname(__file__));
$folder_info = wp_upload_dir();
define('UPLOAD_PATH', $folder_info['baseurl'] . '/');
define('UPLOAD_PATH_ABS', $folder_info['basedir']);

require PLUGIN_PATH . "/img_resize_function.php";
require PLUGIN_PATH . "/options.php";
//require __DIR__ . '/img_resize_function.php';
//require __DIR__ . '/options.php';

define("VERSION", "1.3.4");
function imagements_version_check() //this function checks if the database is up to date with the latest format
{
    if (!(get_option('version') == VERSION))
    {
        imagements_init();
        update_option('version', VERSION);
    }
}
function imagements_add_scripts()
{
    wp_enqueue_script('add_morefields_script', plugins_url('/js/more_fields.js', __file__));
    //wp_enqueue_script('add_jquery', plugins_url('/js/jquery-1.7.2.min.js', __file__));
    wp_enqueue_script('add_lightboxscriptjquery', plugins_url('/js/jquery-1.10.2.min.js', __file__));
    wp_enqueue_script('add_lightboxscript', plugins_url('/js/lightbox-2.6.min.js', __file__));
    wp_enqueue_style('add_lightboxstyle', plugins_url('/css/lightbox.css', __file__));
}
function imagements_check_report_form_input() //this function checks if there has been input from the report button and if so handles it
{
    if (isset($_POST['image_name']) && isset($_POST['comment_id']))
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "imagements";
        if (current_user_can('moderate_comments'))
        {
            global $wpdb;
            $table_name = $wpdb->prefix . "imagements";
            $image_name = $_POST['image_name'];
            $sql = "UPDATE $table_name
            SET status='blocked'
            WHERE naam = %s
            ";
            $sql = $wpdb->prepare($sql, $image_name);
            $wpdb->query($sql);
            wp_die(__('image blocked. Go <a href="javascript:history.back()">back</a> where you came from. If you dont see the block, please refresh the page.<br>Remember: the image is not yet deleted from the server, go to dashboard->imagements reports to remove the image. You can also unblock the image there.'));
        } else
        {
            global $wpdb;
            $table_name = $wpdb->prefix . "imagements_reports";
            $sql = "
            SELECT id
            FROM $table_name
            WHERE image_name = %s
            ";
            $sql = $wpdb->prepare($sql,$_POST['image_name']);
            $result = $wpdb->get_var($sql);
            if ($result == null)
            {
                $sql = "INSERT INTO $table_name
                VALUES (NULL, %s, %s
                );";
                $sql = $wpdb->prepare($sql, $_POST['image_name'], $_POST['comment_id']);
                $wpdb->query($sql);
                wp_die(__('image reported, an admin will look at the case as soon as possible. Go <a href="javascript:history.back()">back</a> where you came from.'));
            } else
            {
                wp_die(__('image already reported, an admin will look at it as soon as possible. Go <a href="javascript:history.back()">back</a> where you came from.'));
            }
        }
    }
}
function imagement_add_form_tag() //this function adds a tag to the comment form to enable file uploading
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-form');
    wp_enqueue_script('add_form_tag', plugins_url('/js/form_tag.js', __file__));
}
function imagements_deac() //this function clears some options when the plugin is deactivated
{
    delete_option('version');
}
function imagements_edit_comment() //this function adds a tag automaticly if it is set in options
{
    $option = get_option('tag_use');
    $tag = get_option('tag');
    $count = count($_FILES['image']['name']);
    if ($option == 'auto' && $_FILES['image']['name'][0] != null)
    {
        for ($i = 0; $i < $count; $i++)
        {
            if($_FILES['image']['name'][$i] != '')
            {
                $_POST['comment'] = $_POST['comment'] . '<br>[' . $tag . '=' . $_FILES['image']['name'][$i] . ']';
            }
        }
    }
}
function imagements_verify_uploading_info($commentdata) //this function checks if the input in the comment form is valid for this plugin
{
    $option = get_option('tag_use');
    $count_images = count($_FILES['image']['name']);
    if ($_FILES['image']['name'][0] != null)
    {
        for ($index = 0; $index < $count_images; $index++)
        {
            if($_FILES['image']['type'][$index] == '')
            {
                continue;
            }
            $number = $index + 1;
            if ($_POST['naam'][$index] == null && $option == 'user')
            {
                wp_die(__("no name was specified for image $number. Hit the Back button and try again."));
            } else
            {
                if ($option == 'auto')
                {
                    $_POST['naam'][$index] = $_FILES['image']['name'][$index];
                }
                if ($_FILES["file"]["error"][$i] > 0)
                {
                    wp_die(__("file Error: " . $_FILES['image']['error'][$index] . " for image $number Hit the Back button on your Web browser and try again"));
                } else
                {
                    if (!($_FILES['image']['type'][$index] == 'image/x-png' || $_FILES['image']['type'][$index] == 'image/pjpeg' || $_FILES['image']['type'][$index] == 'image/jpeg' || $_FILES['image']['type'][$index] == 'image/jpg' || $_FILES['image']['type'][$index] == 'image/png'))
                    {
                        wp_die(__("this file is no image for file $number. Hit the Back button on your Web browser and try again"));
                    } else
                    {
                        global $wpdb;
                        $table_name = $wpdb->prefix . "imagements";
                        $sql = "SELECT naam
                                FROM $table_name
                                WHERE naam = %s
                                ";
                        $sql = $wpdb->prepare($sql, $_POST['naam'][$index]);
                        $result = $wpdb->get_var($sql);
                        if ($result != null && get_option('tag_use') == 'user')
                        {
                            wp_die(__("the name for image $number is already in use. Hit the Back button and choose a new unique one"));
                        } else
                        {
                            $dir = UPLOAD_PATH . get_option('upload_folder') . '/';
                            if (file_exists($dir . $_FILES["image"]["name"][$index]))
                            {
                                wp_die(__("the filename for image $number already exists on our server. please rename the file and try againg"));
                            } else
                            {
                                $sql = "SELECT hash,naam
                                        FROM $table_name
                                        ";
                                $hash = $wpdb->get_col($sql, 0);
                                $names = $wpdb->get_col(null, 1);
                                $count = count($hash);
                                $tmp = $_FILES['image']['tmp_name'][$index];
                                $hash_target = hash_file("md5", $tmp);
                                for ($i = 0; $i < $count; $i++)
                                {
                                    if ($hash[$i] == $hash_target)
                                    {
                                        $tag = get_option('tag');
                                        wp_die(__("File number $number already exists on our server. You can use [$tag=$names[$i]] to use it in your comment<br>If you uploaded this image twice in the same comment the image is already on our server. Please use the tag to use it."));
                                    }
                                }
                                imagements_uploading($index);
                            }
                        }
                    }
                }
            }
        }
    }
    return $commentdata;
}
function imagements_uploading($index) //handles the actual uploading
{
    $name = str_replace(' ', '_', $_FILES['image']['name'][$index]);
    //die($name);
    $hash = hash_file("md5", $_FILES['image']['tmp_name'][$index]);
    $dir = UPLOAD_PATH_ABS . '/' . get_option('upload_folder') . '/';
    move_uploaded_file($_FILES["image"]["tmp_name"][$index], $dir . $name);
    if (!imagements_resize_image($dir . $name, 0, 1, get_option('max_width'), get_option('max_height')))
    {
        wp_die(__('error in resizing image, hit the back button and try again. <br> if problem persist contact site admin'));
    }
    global $wpdb;
    $table_name = $wpdb->prefix . 'imagements';
    $sql = "INSERT INTO $table_name
            VALUES (NULL, %s, %s, 'waiting', '$hash') 
            ";
    $sql = $wpdb->prepare($sql, $_POST['naam'][$index], $name);
    $wpdb->query($sql);
}
function imagements_additional_fields() //this function adds fields to the comment form
{
    if ((get_option('members_only') == 'yes' && current_user_can('read')) || get_option('members_only') == 'no')
    {
        $option = get_option('tag_use');
        $limit = get_option('uploadlimit');
        if ($option == 'user')
        {
            $fields = 1;
            echo '<p><label for="naam">' . __('name image') . '</label><input id="name_image" name="naam[]" type="text"/></p>';
        } else
        {
            $fields = 0;
        }
        echo '<p><label for="image">' . __('file image') . '</label><input id="image" name="image[]" type="file"/></p>';
        echo "<p><input type='button' id='extra_field' value='extra image upload field' onClick=extend('$fields','$limit')></p>";
    }
}
function imagements_input_image_in_comment() //this function check every comment to see if an image has to be inserted and if so handles this
{
    global $wpdb;
    global $comment;
    $table_name = $wpdb->prefix . "imagements";
    $tag = get_option('tag');
    $start = "[$tag=";
    $end = "]";
    $pos = 0;
    $comment_content = $comment->comment_content;
    while ($pos = stripos($comment_content, $start, $pos))
    {
        global $wpdb;
        $str = substr($comment_content, $pos);
        $str_two = substr($str, strlen($start));
        $second_pos = stripos($str_two, $end);
        $str_three = substr($str_two, 0, $second_pos);
        $keyword = $str_three;
        $sql = "
        SELECT path, status
        FROM $table_name
        WHERE naam = %s
        ";
        $sql = $wpdb->prepare($sql, $keyword);
        $result = $wpdb->get_row($sql);
        $path = UPLOAD_PATH . get_option('upload_folder') . '/';
        if ($result->path == null)
        {
            $path = plugins_url('/img/notfound.gif', __file__);
            $w = getimagesize($path)[0];
            $h = getimagesize($path)[1];

        } elseif ($result->status == 'blocked')
        {
            $path = plugins_url('/img/blocked.gif', __file__);
            $w = getimagesize($path)[0];
            $h = getimagesize($path)[1];

        } elseif ($result->status == 'waiting' && get_option('admin_system') == 'admin')
        {
            $path = plugins_url('/img/waiting.png', __file__);
            $w = getimagesize($path)[0];
            $h = getimagesize($path)[1];

        } else
        {
            $path = $path . $result->path;
            $data = imagements_calculate_size($path, 0, 1, get_option('max_width_thumb'), get_option('max_height_thumb'));
            $w = $data['w'];
            $h = $data['h'];
        }
        //$replace = '<a href="' . $path . '" rel="lightbox"><img src="' . $path . '" width="' . $w . '" height="' . $h . '"></a>';
        $imgtag = '<img src="' . $path . '" width="' . $w . '" height="' . $h . '">';
        if(get_option('lightbox_on'))
        {
            $replace = '<a href="' . $path . '" data-lightbox="images">' . $imgtag . '</a>';
        }else
        {
            $replace = $imgtag;
        }
        if (!($result->status == 'blocked' || ($result->status == 'waiting' && get_option('admin_system') == 'admin')))
        {
            if (!current_user_can('moderate_comments'))
            {
                $table_name = $wpdb->prefix . "imagements_reports";
                $sql = "
                SELECT id
                FROM $table_name
                WHERE image_name = '$keyword'
                ";
                $result = $wpdb->get_var($sql);
                if ($result == null)
                {
                    global $wpdb;
                    $table_name = $wpdb->prefix . 'imagements';
                    $sql = "
                    SELECT status
                    FROM $table_name
                    WHERE naam = '$keyword'
                    ";
                    $status = $wpdb->get_var($sql);
                    if ($status == 'approved')
                    {
                        $replace = $replace . __('<br>image approved by admin');
                    } else
                    {
                        if ($path != 'blocked.gif')
                        {
                            $replace = $replace . '<br><form method="post" action="' . '' . '"><input type="hidden" name="image_name" value="' . $keyword . '"><input type="hidden" name="comment_id" value="' . $comment->comment_ID . '"><input type="submit" value="' . __('report image') . '"></form>';
                        }
                    }
                } else
                {
                    $replace = $replace . __('<br>image reported');
                }
            } else
            {
                $replace = $replace . '<br><form method="post" action="' . '' . '"><input type="hidden" name="image_name" value="' . $keyword . '"><input type="hidden" name="comment_id" value="' . $comment->comment_ID . '"><input type="submit" value="' . __('block image') . '"></form>';
            }
        }
        $search = "[$tag=" . $keyword . ']';
        $comment_content = str_replace($search, $replace, $comment_content);
    }
    return $comment_content;
}
function imagements_init() //this functions sets the options and creates tables in the database when the plugin is activated
{
    add_option('max_height', 1000);
    add_option('max_width', 1000);
    add_option('tag', 'image');
    add_option('version', VERSION);
    add_option('max_height_thumb', 300);
    add_option('max_width_thumb', 300);
    add_option('tag_use', 'auto');
    add_option('uploadlimit', 5);
    add_option('admin_system', 'free'); //free=image is published immediately, admin=image has to be approved first
    add_option('members_only', 'no');
    add_option('upload_folder', 'images');
    add_option('lightbox_on', 1);
    global $wpdb;
    $table_name = $wpdb->prefix . "imagements";
    $sql = "CREATE TABLE $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    naam text NOT NULL,
    path text NOT NULL,
    status text NOT NULL,
    hash text NOT NULL,
    UNIQUE KEY id (id)
    );";
    require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    $table_name = $wpdb->prefix . "imagements_reports";
    $sql = "CREATE TABLE $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    image_name text NOT NULL,
    comment_id int NOT NULL,
    UNIQUE KEY id (id)
    );";
    require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
function imagements_uninstall() //this function cleans everyting up when the plugin is deinstalled
{
    global $wpdb;
    $table_name = $wpdb->prefix . "imagements";
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
    $table_name = $wpdb->prefix . "imagements_reports";
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
    delete_option('max_height');
    delete_option('max_width');
    delete_option('tag');
    delete_option('max_width_thumb');
    delete_option('max_height_thumb');
    delete_option('tag_use');
    delete_option('version');
    delete_option('uploadlimit');
    delete_option('admin_system');
    delete_option('members_only');
    delete_option('upload_folder');
    delete_option('lightbox_on');
}

?>