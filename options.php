<?php

/**
 * @author Frank
 * @copyright 2012
 */


function imagements_admin_add_page()
{
    add_options_page(__('imagements general options menu'), __('imagements general'), 'moderate_comments', 'imagements', 'imagements_general_menu_options');
    add_comments_page(__('imagements reports'), __('imagements reports'), 'manage_options', 'imagements_reports', 'imagements_reports_form');
}

function imagements_admin_init()
{
    add_settings_section('imagements_main', 'imagements', 'imagements_general_section_text', 'imagements');
    register_setting('imagements_options', 'tag', 'imagements_tag_replacement');
    register_setting('imagements_options', 'max_width', 'imagements_general_options_validate');
    register_setting('imagements_options', 'max_height', 'imagements_general_options_validate');
    register_setting('imagements_options', 'max_width_thumb', 'imagements_general_options_validate');
    register_setting('imagements_options', 'max_height_thumb', 'imagements_general_options_validate');
    register_setting('imagements_options', 'tag_use');
    add_settings_field('tag', __('the tag used to insert an image: '), 'imagements_option_tag', 'imagements', 'imagements_main');
    add_settings_field('max_width', __('maximum width in pixels: '), 'imagements_option_width', 'imagements', 'imagements_main');
    add_settings_field('max_height', __('maximum height in pixels: '), 'imagements_option_height', 'imagements', 'imagements_main');
    add_settings_field('max_width_thumb', __('maximum width for thumbnails in pixels: '), 'imagements_option_width_thumb', 'imagements', 'imagements_main');
    add_settings_field('max_height_thumb', __('maximum height for thumbnails in pixels: '), 'imagements_option_height_thumb', 'imagements', 'imagements_main');
    add_settings_field('tag_use', __('How the tag is used, by user or automaticly: '), 'imagements_option_tag_use', 'imagements', 'imagements_main');
}

function imagements_tag_replacement($input){
    global $wpdb;
    $table_name = $wpdb->prefix . 'comments';
    $tag = get_option('tag');
    $sql =  "SELECT comment_content, comment_ID
            FROM $table_name
            WHERE comment_content LIKE '%[$tag=%'
            ";
    $data = $wpdb->get_results($sql);
    foreach($data as $comment){
        $new_comment = str_replace("[$tag=","[$input=",$comment->comment_content);
    $sql =  "UPDATE $table_name
            SET comment_content='$new_comment'
            WHERE comment_ID = '$comment->comment_ID'
            ";
    $wpdb->query($sql);
    }
    return $input;
}

function imagements_check_admin_reports_form_input()
{
    if (current_user_can('moderate_comments'))
    {
        if (isset($_POST['action_1']))
        {
            $action = $_POST['action_1'];
            foreach ($_POST as $id)
            {
                if (is_numeric($id))
                {
                    global $wpdb;
                    $table_name = $wpdb->prefix . 'imagements_reports';
                    $id = $wpdb->prepare($id);
                    $sql = "
                        SELECT image_name
                        FROM $table_name
                        WHERE id = '$id'
                        ";
                    $image_name = $wpdb->get_var($sql);
                    $sql = "
                        DELETE FROM $table_name
                        WHERE id = '$id'
                        ";
                    $wpdb->query($sql);
                    $table_name = $wpdb->prefix . 'imagements';
                    if ($action == 'block')
                    {
                        global $wpdb;
                        $sql = "
                        UPDATE $table_name
                        SET blocked = 'yes'
                        WHERE naam = '$image_name'
                        ";
                        $wpdb->query($sql);
                    } elseif ($action == 'approve')
                    {
                        global $wpdb;
                        $sql = "
                        UPDATE $table_name
                        SET blocked = 'approved'
                        WHERE naam = '$image_name'
                        ";
                        $wpdb->query($sql);
                    } elseif ($action == 'delete')
                    {
                        global $wpdb;
                        $table_name = $wpdb->prefix . 'imagements';
                        $sql = "
                        SELECT path
                        FROM $table_name
                        WHERE naam = '$image_name'
                        ";
                        $path = $wpdb->get_var($sql);
                        $path = __dir__ . '/images/' . $path;
                        $sql = "
                        DELETE FROM $table_name
                        WHERE naam = '$image_name'
                        ";
                        $wpdb->query($sql);
                        unlink($path);
                    }
                }
            }
        } elseif (isset($_POST['action_2']))
        {
            $action = $_POST['action_2'];
            foreach ($_POST as $id)
            {
                if (is_numeric($id))
                {
                    if ($action == 'unblock')
                    {
                        global $wpdb;
                        $table_name = $wpdb->prefix . 'imagements';
                        $sql = "
                        UPDATE $table_name
                        SET blocked = 'no'
                        WHERE id = '$id'
                        ";
                        $wpdb->query($sql);
                    } elseif ($action == 'approve')
                    {
                        global $wpdb;
                        $table_name = $wpdb->prefix . 'imagements';
                        $sql = "
                        UPDATE $table_name
                        SET blocked = 'approved'
                        WHERE id = '$id'
                        ";
                        $wpdb->query($sql);
                    } elseif ($action == 'delete')
                    {
                        global $wpdb;
                        $table_name = $wpdb->prefix . 'imagements';
                        $sql = "
                        SELECT path
                        FROM $table_name
                        WHERE id = '$id'
                        ";
                        $path = $wpdb->get_var($sql);
                        $path = __dir__ . '/images/' . $path;
                        $sql = "
                        DELETE FROM $table_name
                        WHERE id = '$id'
                        ";
                        $wpdb->query($sql);
                        unlink($path);
                    }
                }
            }
        }
    }
}

function imagements_reports_form()
{

?>
<div class="wrap">
<h2><?php

    echo __('imagements reports');

?></h2>
<?php
    global $wpdb;
    $table_name = $wpdb->prefix . 'imagements_reports';
    $sql = "
    SELECT *
    FROM $table_name
    ";
    $results = $wpdb->get_results($sql);
    if($results == NULL){
        echo __('No reports to show!');
    }else{
?>
<form method="post" action="">
<table border="4">
<tr><td><b>checkbox</b></td><td><b>comment</b></td><td><b>image</b></td><td><b>author</b></td></tr>
<?php
    foreach ($results as $data)
    {
        $naam = $data->image_name;
        $comment_ID = $data->comment_id;
        $id = $data->id;
        global $wpdb;
        $table_name = $wpdb->prefix . 'imagements';
        $sql = "
        SELECT path
        FROM $table_name
        WHERE naam = '$naam'
    ";
        $path = $wpdb->get_var($sql);
        $path = plugin_dir_url(__file__) . 'images/' . $path;
        $table_name = $wpdb->prefix . 'comments';
        $sql = "
    SELECT comment_author, comment_content
    FROM $table_name
    WHERE comment_ID = '$comment_ID'
    ";
        $comment_info = $wpdb->get_row($sql);
        $author = $comment_info->comment_author;
        $content = $comment_info->comment_content;
        if (get_option('max_width_thumb') < get_option('max_width') || get_option('max_height_thumb') < get_option('max_height'))
        {
            $size = imagements_calculate_size($path, 0, 1, get_option('max_width_thumb'), get_option('max_height_thumb'));
            $w = $size['w'];
            $h = $size['h'];
        } else
        {
            $w = get_option('max_width');
            $h = get_option('max_height');
        }
        echo '<tr><td>';
        echo '<input type="checkbox" name="' . $id . '" value="' . $id . '">';
        echo '</td><td>';
        echo $content;
        echo '</td><td>';
        echo '<img src="' . $path . '" height="' . $h . '" width="' . $w . '">';
        echo '</td><td>';
        echo $author;
        echo '</td></tr>';
    }

?> 
<tr><td>
<select name="action_1">
<option value="block"><?php

    echo __('block image');

?></option>
<option value="approve"><?php

    echo __('approve image');

?></option>
<option value="delete"><?php

    echo __('delete image');

?></option>
</select>
</td><td>
<input name="Submit" type="submit" value="<?php

    $buttontext = __('Apply');
    esc_attr_e($buttontext);

?>" /></td></tr>	
</table>
</form>
<?php } ?>
<h2><?php

    echo __('blocked images');

?></h2>
<?php
    global $wpdb;
    $table_name = $wpdb->prefix . 'imagements';
    $sql = "
    SELECT id, naam, path
    FROM $table_name
    WHERE blocked = 'yes'
    ";
    $result = $wpdb->get_results($sql);
    if($result == NULL){
        echo __('No blocked images to show!');
    }else{
?>
</div>
<form method="post" action="">
<table border="3">
<tr><td><b><?php

    echo __('checkbox');

?></b></td><td><b><?php

    echo __('name blocked image');

?></b></td><td><b><?php

    echo __('blocked image');

?></b></td></tr>
<?php
    foreach ($result as $data)
    {
        $path = plugin_dir_url(__file__) . 'images/' . $data->path;
        if (get_option('max_width_thumb') < get_option('max_width') || get_option('max_height_thumb') < get_option('max_height'))
        {
            $size = imagements_calculate_size($path, 0, 1, get_option('max_width_thumb'), get_option('max_height_thumb'));
            $w = $size['w'];
            $h = $size['h'];
        } else
        {
            $w = get_option('max_width');
            $h = get_option('max_height');
        }
        echo '<tr><td>';
        echo '<input type="checkbox" name="' . $data->id . '" value="' . $data->id . '">';
        echo '</td><td>';
        echo $data->naam;
        echo '</td><td>';
        echo '<img src="' . $path . '" height = "' . $h . '" width = "' . $w . '">';
        echo '</td></tr>';
    }

?>
<tr><td>
<select name="action_2">
<option value="unblock"><?php

    echo __('unblock image');

?></option>
<option value="approve"><?php

    echo __('approve image');

?></option>
<option value="delete"><?php

    echo __('delete image');

?></option>
</select>
</td><td>
<input name="Submit" type="submit" value="<?php

    $buttontext = __('Apply');
    esc_attr_e($buttontext);

?>" />
</td><td></td></tr>
</table>
</form>
<?php
    }
}

function imagements_general_options_validate($input)
{
    if (!(is_numeric($input)))
    {
        $input = 300;
    }
    return $input;
}

function imagements_option_tag_use()
{
    $option = get_option('tag_use');
    if ($option == 'auto')
    {
        echo "<select id='tag_use' name = 'tag_use'>
        <option value='auto'>auto</option>
        <option value='user'>user</option>
        </select>
        ";
    } elseif ($option == 'user')
    {
        echo "<select id='tag_use' name = 'tag_use'>
        <option value='user'>user</option>
        <option value='auto'>auto</option>
        </select>
        ";
    }
}

function imagements_option_width_thumb()
{
    $option = get_option('max_width_thumb');
    echo "<input id='max_width_thumb' name='max_width_thumb' size='40' type='text' value='$option'/>";
}

function imagements_option_height_thumb()
{
    $option = get_option('max_height_thumb');
    echo "<input id='max_width_thumb' name='max_height_thumb' size='40' type='text' value='$option'/>";
}

function imagements_option_width()
{
    $option = get_option('max_width');
    echo "<input id='max_width' name='max_width' size='40' type='text' value='$option' />";
}

function imagements_option_height()
{
    $option = get_option('max_height');
    echo "<input id='max_height' name='max_height' size='40' type='text' value='$option' />";

}

function imagements_option_tag()
{
    $option = get_option('tag');
    echo "<input id='tag' name='tag' size='40' type='text' value='$option' />";
}

function imagements_general_section_text()
{
    echo __('<p>here you can set the settings of the imagements plugin</p>');
}

function imagements_general_menu_options()
{

?>
<div class="wrap">
<h2>imagements options</h2>
<form method="post" action="options.php">
<?php

    settings_fields('imagements_options');
    do_settings_sections('imagements');

?> 
	<input name="Submit" type="submit" value="<?php

    $buttontext = __('Save Changes');
    esc_attr_e($buttontext);

?>" />
</form>
</div>
<?php

}

?>