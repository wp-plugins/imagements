<?php

/*
Plugin Name: imagements
Description: this plugin lets your users put images in comments.
Version: 1.2.0
Author: williewonka
Author URI: http://www.deweblogvanhelmond.nl
License: GPL2
*/

/*  Copyright 2012  williewonka  (email : williewonka341@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

register_activation_hook(__file__, "imagements_init");
register_deactivation_hook(__file__, 'imagements_deac');
register_uninstall_hook(__file__, 'imagements_uninstall');
add_action('wp_enqueue_scripts', 'imagement_add_form_tag');
add_filter('comment_text', 'imagements_comment_check');
add_action('comment_form_logged_in_after', 'imagements_additional_fields');
add_action('comment_form_after_fields', 'imagements_additional_fields');
add_action('comment_post', 'imagements_formverwerking');
add_filter('preprocess_comment', 'imagements_verify_post_data');
add_action('admin_menu', 'imagements_admin_add_page');
add_action('admin_init', 'imagements_admin_init');
add_action('init', 'imagements_check_report_form_input');
add_action('init', 'imagements_check_admin_reports_form_input');
add_action('init', 'imagements_version_check');

require __DIR__ . '/img_resize_function.php';
require __DIR__ . '/options.php';

function imagements_version_check()
{
    if (!(get_option('version') == '1.2.0'))
    {
        $table_name = $wpdb->prefix . "imagements_reports";
        $sql = "CREATE TABLE $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    image_name text NOT NULL,
    comment_id int NOT NULL,
    UNIQUE KEY id (id)
    );";

        require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        add_option('version', '1.2.0');
        update_option('version', '1.2.0');
        add_option('max_height_thumb', 300);
        add_option('max_width_thumb', 300);
        add_option('tag', 'afbeelding');
    }
}

function imagements_check_report_form_input()
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
            SET blocked='yes'
            WHERE naam = '$image_name'
            ";
            $wpdb->query($sql);
            wp_die(__('image blocked. Go <a href="javascript:history.back()">back</a> where you came from. If you dont see the block, please refresh the page.<br>Remember: the image is not yet deleted from the server, go to dashboard->imagements reports to remove the image. You can also unblock the image there.'));
        } else
        {
            global $wpdb;
            $table_name = $wpdb->prefix . "imagements_reports";
            $sql = "
            SELECT id
            FROM $table_name
            WHERE image_name='" . $wpdb->prepare($_POST['image_name']) . "'
            ";
            $result = $wpdb->get_var($sql);
            if ($result == null)
            {
                $sql = "INSERT INTO $table_name
        VALUES (NULL, '" . $wpdb->prepare($_POST['image_name']) . "', '" . $wpdb->prepare($_POST['comment_id']) . "'
        );";
                $wpdb->query($sql);
                wp_die(__('image reported, an admin will look at the case as soon as possible. Go <a href="javascript:history.back()">back</a> where you came from.'));
            } else
            {
                wp_die(__('image already reported, an admin will look at it as soon as possible. Go <a href="javascript:history.back()">back</a> where you came from.'));
            }
        }
    }
}

function imagement_add_form_tag()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-form');
    wp_enqueue_script('add_form_tag', plugins_url('/js/form_tag.js', __file__));
}

function imagements_deac()
{
    delete_option('version');
}

function imagements_formverwerking()
{
    if (isset($_POST['checkbox']))
    {
        $name = $_FILES['image']['name'];
        move_uploaded_file($_FILES["image"]["tmp_name"], __DIR__ . '/images/' . $name);
        if (!imagements_resize_image(__DIR__ . '/images/' . $name, 0, 1, get_option('max_width'), get_option('max_height')))
        {
            wp_die(__('error in resizing image, hit the back button and try again. <br> if problem persist contact site admin'));
        }
        global $wpdb;
        $table_name = $wpdb->prefix . 'imagements';
        $sql = "INSERT INTO $table_name
        VALUES (NULL, '" . $wpdb->prepare($_POST['naam']) . "', '" . $wpdb->prepare($_FILES['image']['name']) . "', 'no') 
        ";
        $wpdb->query($sql);

    }
}


function imagements_verify_post_data($commentdata)
{
    if (isset($_POST["checkbox"]))
    {
        if ($_FILES['image']['name'] == null)
        {
            wp_die(__('no file was specified. Hit the Back button on your Web browser and try agian'));
        } else
        {
            if ($_POST['naam'] == null)
            {
                wp_die(__('no name was specified. Hit the Back button and try agian.'));
            } else
            {
                if ($_FILES["file"]["error"] > 0)
                {
                    wp_die(__('file Error: ' . $_FILES['image']['error'] . ' Hit the Back button on your Web browser and try again'));
                } else
                {
                    if (!($_FILES['image']['type'] == 'image/x-png' || $_FILES['image']['type'] == 'image/pjpeg' || $_FILES['image']['type'] == 'image/jpeg' || $_FILES['image']['type'] == 'image/jpg' || $_FILES['image']['type'] == 'image/png'))
                    {
                        wp_die(__('this file is no image. Hit the Back button on your Web browser and try again'));
                    } else
                    {
                        global $wpdb;
                        $table_name = $wpdb->prefix . "imagements";

                        $sql = "SELECT naam
                FROM $table_name
                WHERE naam = '" . $wpdb->prepare($_POST['naam']) . "'
                ";
                        $result = $wpdb->get_var($sql);
                        if ($result != null)
                        {
                            wp_die(__('this name is already in use. Hit the Back button and choose a new unique one'));
                        } else
                        {
                            $dir = plugin_dir_url(__file__) . '/images/';
                            if (file_exists($dir . $_FILES["image"]["name"]))
                            {
                                wp_die(__('this filename already exists on our server. please rename the file and try againg'));
                            }
                        }

                    }
                }
            }
        }
    }
    return $commentdata;
}

function imagements_additional_fields()
{
    echo '<p>' . '<label for="checkbox">' . __('upload image') . '</label>' . '<input id="checkbox" name="checkbox" type="checkbox" value ="yes"/></p>';
    echo '<p>' . '<label for="naam">' . __('name image') . '</label>' . '<input id="naam" name="naam" type="text"/></p>';

    echo '<p>' . '<label for="image">' . __('file image') . '</label>' . '<input id="image" name="image" type="file"/></p>';
}

function imagements_comment_check()
{

    global $wpdb;
    global $comment;

    $table_name = $wpdb->prefix . "imagements";

    $start = "[afbeelding=";
    $end = "]";
    $pos = 0;
    $comment_content = $comment->comment_content;

    while ($pos = stripos($comment_content, $start, $pos))
    {
        global $wpdb;
        $str = substr($comment->comment_content, $pos);
        $str_two = substr($str, strlen($start));
        $second_pos = stripos($str_two, $end);
        $str_three = substr($str_two, 0, $second_pos);
        $keyword = $str_three;
        $sql = "
        SELECT path, blocked
        FROM $table_name
        WHERE naam = '$keyword'
        ";
        $result = $wpdb->get_row($sql);
        if ($result->path == null)
        {
            $path = 'notfound.gif';
        } elseif ($result->blocked == 'yes')
        {
            $path = 'blocked.gif';
        } else
        {
            $path = $result->path;
        }
        $path = plugin_dir_url(__file__) . 'images/' . $path;
        $replace = '<img src="' . $path . '">';

        if (!($result->blocked == 'yes'))
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
                    SELECT blocked
                    FROM $table_name
                    WHERE naam = '$keyword'
                    ";
                    $status = $wpdb->get_var($sql);
                    if ($status == 'approved')
                    {
                        $replace = $replace . __('<br>image approved by admin');
                    } else
                    {
                        $replace = $replace . '<br><form method="post" action="' . '' . '"><input type="hidden" name="image_name" value="' . $keyword . '"><input type="hidden" name="comment_id" value="' . $comment->comment_ID . '"><input type="submit" value="' . __('report image') . '"></form>';
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
        $search = '[afbeelding=' . $keyword . ']';
        $comment_content = str_replace($search, $replace, $comment_content);
    }
    return $comment_content;
}

function imagements_init()
{
    add_option('max_height', 300);
    add_option('max_width', 300);
    add_option('tag', 'afbeelding');
    add_option('version', '1.2.0');
    add_option('max_height_thumb', 300);
    add_option('max_width_thumb', 300);

    global $wpdb;
    $table_name = $wpdb->prefix . "imagements";

    $sql = "CREATE TABLE $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    naam text NOT NULL,
    path text NOT NULL,
    blocked text NOT NULL,
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

function imagements_uninstall()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "imagements";
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
    $table_name = $wpdb->prefix . "imagements_reports";
    $wpdb->query("DROP TABLE IF EXISTS $tablename");
    delete_option('max_height');
    delete_option('max_width');
    delete_option('tag');
}

?>