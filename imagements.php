<?php

/*
Plugin Name: imagements
Description: this plugin lets your users put images in comments.
Version: 1.1.0
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
register_deactivation_hook(__FILE__, 'imagements_deac');
register_uninstall_hook(__file__, 'imagements_uninstall');
add_action('wp_enqueue_scripts', 'imagement_add_form_tag');
add_filter('comment_text', 'imagements_comment_check');
add_action('comment_form_logged_in_after', 'imagements_additional_fields');
add_action('comment_form_after_fields', 'imagements_additional_fields');
add_action('comment_post', 'imagements_formverwerking');
add_filter('preprocess_comment', 'imagements_verify_post_data');
add_action('admin_menu', 'imagements_admin_add_page');
add_action('admin_init', 'imagements_admin_init');
add_action('plugins_loaded', 'imagements_version_check');

require __DIR__ . '\img_resize_function.php';
require __DIR__ . '\options.php';

function imagement_add_form_tag()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('jquery-form');
    wp_enqueue_script('add_form_tag', plugins_url('/js/form_tag.js', __file__));
}

function imagements_version_check(){
    if(! (get_option('version') == '1.1.0') and ! (get_option('warning') == 'yes')){
        add_option('warning', 'yes');
        wp_die(__('imagements update detected, please deactivate the plugin and then reactivate it to update the database structure. refresh this page to let this warning disappear. it will not appear twice.'));
    }
}

function imagements_deac(){
    delete_option('version');
}

function imagements_formverwerking()
{
    if (isset($_POST['checkbox']))
    {
        $name = $_FILES['image']['name'];
        move_uploaded_file($_FILES["image"]["tmp_name"], __DIR__ . '/images/' . $name);
        if (!imagements_resize_image(__DIR__ . '/images/' . $name, 0, 1, get_option('max_width'),
            get_option('max_height')))
        {
            wp_die(__('error in resizing image, hit the back button and try again. <br> if problem persist contact site admin'));
        }
        global $wpdb;
        $table_name = $wpdb->prefix . 'imagements';
        $sql = "INSERT INTO $table_name
        VALUES (NULL, '" . $wpdb->prepare($_POST['naam']) . "', '" . $wpdb->
            prepare($_FILES['image']['name']) . "', 'no') 
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
                    wp_die(__('file Error: ' . $_FILES['image']['error'] .
                        ' Hit the Back button on your Web browser and try again'));
                } else
                {
                    if (!($_FILES['image']['type'] == 'image/x-png' || $_FILES['image']['type'] ==
                        'image/pjpeg' || $_FILES['image']['type'] == 'image/jpeg' || $_FILES['image']['type'] ==
                        'image/jpg' || $_FILES['image']['type'] == 'image/png'))
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
    echo '<p>' . '<label for="checkbox">' . __('upload image') . '</label>' .
        '<input id="checkbox" name="checkbox" type="checkbox" value ="yes"/></p>';
    echo '<p>' . '<label for="naam">' . __('name image') . '</label>' .
        '<input id="naam" name="naam" type="text"/></p>';

    echo '<p>' . '<label for="image">' . __('file image') . '</label>' .
        '<input id="image" name="image" type="file"/></p>';
}

function imagements_comment_check($comment)
{

    global $wpdb;
    $table_name = $wpdb->prefix . "imagements";

    $start = "[afbeelding=";
    $end = "]";
    $pos = 0;

    while ($pos = stripos($comment, $start, $pos))
    {
        $str = substr($comment, $pos);
        $str_two = substr($str, strlen($start));
        $second_pos = stripos($str_two, $end);
        $str_three = substr($str_two, 0, $second_pos);
        // $keyword = trim($str_three); // remove whitespaces
        $keyword = $str_three;
        $sql = "
        SELECT path
        FROM $table_name
        WHERE naam = '$keyword'
        ";
        $path = $wpdb->get_var($sql);
        if ($path == null)
        {
            $path = 'notfound.gif';
        }
        $path = plugin_dir_url(__file__) . 'images/' . $path;
        $replace = '<img src="' . $path . '">';
        $search = '[afbeelding=' . $keyword . ']';
        $comment = str_replace($search, $replace, $comment);
    }
    return $comment;
}

function imagements_init()
{
    add_option('max_height', 300);
    add_option('max_width', 300);
    add_option('version', '1.1.0');
    add_option('tag', 'afbeelding');
    
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
}


function imagements_uninstall()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "imagements";
    $wpdb->query("DROP TABLE IF EXISTS $table_name");
    delete_option('max_height');
    delete_option('max_width');
}

?>