<?php

/**
 * @author Williewonka
 * @copyright 2012
 */


function imagements_admin_add_page()
{
    add_options_page(__('imagements options'), __('imagements options'), 'manage_options', 'imagements', 'imagements_general_menu_options');
    add_comments_page(__('imagements images'), __('images in comments'), 'moderate_comments', 'imagements_images', 'imagements_view_images');
}

function imagements_admin_init()
{
    add_settings_section('imagements_main', 'imagements', 'imagements_general_section_text', 'imagements');
    register_setting('imagements_options', 'upload_folder');
    register_setting('imagements_options', 'tag', 'imagements_tag_replacement');
    register_setting('imagements_options', 'max_width', 'imagements_general_options_validate');
    register_setting('imagements_options', 'max_height', 'imagements_general_options_validate');
    register_setting('imagements_options', 'max_width_thumb', 'imagements_general_options_validate');
    register_setting('imagements_options', 'max_height_thumb', 'imagements_general_options_validate');
    register_setting('imagements_options', 'tag_use');
    register_setting('imagements_options', 'uploadlimit', 'imagements_general_options_validate');
    register_setting('imagements_options', 'admin_system');
    register_setting('imagements_options', 'members_only');
    register_setting('imagements_options', 'lightbox_on');
    add_settings_field('upload_folder', __('uploadfolder for images, this folder always needs to be in wp-content/uploads. please note that this folder is not made by the plugin if you change this setting. you will need to create it yourself'), 'imagements_option_folder', 'imagements', 'imagements_main');
    add_settings_field('tag', __('the tag used to insert an image: '), 'imagements_option_tag', 'imagements', 'imagements_main');
    add_settings_field('max_width', __('maximum width in pixels: '), 'imagements_option_width', 'imagements', 'imagements_main');
    add_settings_field('max_height', __('maximum height in pixels: '), 'imagements_option_height', 'imagements', 'imagements_main');
    add_settings_field('max_width_thumb', __('maximum width for thumbnails in pixels: '), 'imagements_option_width_thumb', 'imagements', 'imagements_main');
    add_settings_field('max_height_thumb', __('maximum height for thumbnails in pixels: '), 'imagements_option_height_thumb', 'imagements', 'imagements_main');
    add_settings_field('tag_use', __('How the tag is used, by user or automaticly: '), 'imagements_option_tag_use', 'imagements', 'imagements_main');
    add_settings_field('uploadlimit', __('Limit for ammount of images users can upload per comment: '), 'imagements_option_uploadlimit', 'imagements', 'imagements_main');
    add_settings_field('admin_system', __('Aproval system<br> free=images is published without approval from admin.<br> admin=images have to be approved by admin first before publishing.'), 'imagements_option_admin_system', 'imagements', 'imagements_main');
    add_settings_field('members_only', __('Only members can upload images: '), 'imagements_option_member_only', 'imagements', 'imagements_main');
    add_settings_field('lightbox_on', __('lightbox setting: '), 'imagements_option_lightbox_on', 'imagements', 'imagements_main');
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

function imagements_act_on_admin_input()
{
    if (current_user_can('moderate_comments'))
    {
        if (isset($_POST['action']))
        {
            foreach ($_POST as $id)
            {
                if (is_numeric($id))
                {
                    global $wpdb;
                    $table_name = $wpdb->prefix . 'imagements';
                    switch ($_POST['action'])
                    {
                        case 'approve':
                        case 'unblock':
                            imagements_change_status('id',$id,'approved');
                            break;
                        case 'block':
                            imagements_change_status('id',$id,'blocked');
                            break;
                        case 'delete':
                            $sql =  "SELECT path
                                    FROM $table_name
                                    WHERE id = %s
                                    ";
                            $sql = $wpdb->prepare($sql, $id);
                            $path = $wpdb->get_var($sql);
                            $sql =  "DELETE FROM $table_name
                                    WHERE id = %s
                                    ";
                            $sql = $wpdb->prepare($sql, $id);
                            $wpdb->query($sql);
                            //$path = __dir__ . '\\images\\' . $path;
                            $dir = UPLOAD_PATH_ABS . '/' . get_option('upload_folder') . '/';
                            $path = $dir . $path;
                            unlink($path);
                            break;
                    }
                }
            }
        } elseif (isset($_POST['action_reports']))
        {
            foreach ($_POST as $id)
            {
                if (is_numeric($id))
                {
                    global $wpdb;
                    $table_name = $wpdb->prefix . 'imagements_reports';
                    $sql = "
                        SELECT image_name
                        FROM $table_name
                        WHERE id = %s
                        ";
                    $sql = $wpdb->prepare($sql, $id);
                    $image_name = $wpdb->get_var($sql);
                    $sql = "
                        DELETE FROM $table_name
                        WHERE id = %s
                        ";
                    $sql = $wpdb->prepare($sql, $id);
                    $wpdb->query($sql);
                    $table_name = $wpdb->prefix . 'imagements';
                    switch ($_POST['action_reports'])
                    {
                        case 'approve':
                            imagements_change_status('naam',$image_name,'approved');
                            break;
                        case 'block':
                            imagements_change_status('naam',$image_name,'blocked');
                            break;
                        case 'delete':
                            $sql =  "SELECT path
                                    FROM $table_name
                                    WHERE naam='$image_name'
                                    ";
                            $path = $wpdb->get_var($sql);
                            $sql =  "DELETE FROM $table_name
                                    WHERE naam = %s
                                    ";
                            $sql = $wpdb->prepare($sql, $image_name);
                            $wpdb->query($sql);
                            $path = __dir__ . '/images/' . $path;
                            unlink($path);
                            break;
                    }
                }
            }
        }
    }
}
function imagements_change_status($target_column,$target, $status)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'imagements';
    $sql = "UPDATE $table_name
            SET status = '$status'
            WHERE $target_column = %s
            ";
    $sql = $wpdb->prepare($sql, $target);
    $wpdb->query($sql);
}

function imagements_view_images()
{
    if (!isset($_POST['choice']))
    {

?>
    <h2><?php

        echo __('Please choose');

?></h2>
    <?php

        echo __('Please make a choice what images you want to see in the menu below')

?>
    <form action="#" method="post">
    <select name="choice">
        <option value="all"><?php

        echo __('all images');

?></option>
        <?php

        if (get_option('admin_system') == 'free')
        {

?><option value="reports"><?php

            echo __('reported images');

?></option><?php

        }

?>
        <?php

        if (get_option('admin_system') == 'admin')
        {

?><option value="approval"><?php

            echo __('images waiting for approval');

?></option><?php

        }

?>
        <option value="blocked"><?php

        echo __('blocked images');

?></option>
    </select>
    <input type="submit" value="<?php

        echo __('choose');

?>">
    </form>
    <?php

    } else
    {
        $choice = $_POST['choice'];

        switch ($choice)
        {
            case 'all':
                imagements_uploaded_images_viewsystem();
                break;
            case 'reports':
                imagements_view_reported_images();
                break;
            case 'approval':
                imagements_view_waiting_images();
                break;
            case 'blocked':
                imagements_view_blocked_images();
                break;
        }
    }
}

function imagements_uploaded_images_viewsystem()
{

?>
<div class="wrap">
<h2>
<?php

    echo __('all uploaded images')

?>
</h2>
<form method="post" action="#">
<input type="hidden" name="choice" value="<?php echo $_POST['choice']; ?>">
<table border="3">
<tr><td><b><?php

    echo __('checkbox');

?></b></td><td><b><?php

    echo __('image');

?></b></td><td><b><?php

    echo __('status');

?></b></td></tr>
<?php

    global $wpdb;
    $table_name = $wpdb->prefix . "imagements";
    $sql = "SELECT id, naam, path, status
            FROM $table_name
            ";
    $result = $wpdb->get_results($sql);
    foreach ($result as $data)
    {
        echo '<tr><td>';
        echo '<input type="checkbox" name="' . $data->id . '" value="' . $data->id . '"/>';
        echo '</td><td>';
        $path = UPLOAD_PATH . get_option('upload_folder') . '/' . $data->path;
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
        echo '<img src="' . $path . '" height = "' . $h . '" width="' . $w . '">';
        echo '</td><td>';
        switch ($data->status)
        {
            case 'blocked':
                echo __('image blocked');
                break;
            case 'approved':
                if (get_option('admin_system') == 'admin')
                {
                    echo __('image approved by admin');
                } else
                {
                    echo __('image approved after report');
                }
                break;
            case 'waiting':
                if (get_option('admin_system') == 'admin')
                {
                    echo __('image waiting for approval');
                } else
                {
                    echo __('image not approved but showing');
                }
                break;
        }
        echo '</td></tr>';

    }

?>
<tr><td>
<select name="action">
<option value="approve"><?php

    echo __('approve image');

?></option>

<option value="block"><?php

    echo __('block image');

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
</div>
    <?php

}

function imagements_view_reported_images()
{
    echo '<div class="wrap">';
    echo '<h2>';
    echo __('Reported images');
    echo '</h2>';

    global $wpdb;
    $table_name = $wpdb->prefix . 'imagements_reports';
    $sql = "
        SELECT *
        FROM $table_name
        ";
    $results = $wpdb->get_results($sql);
    if ($results == null)
    {
        echo __('No reports to show!');
        echo '</div>';
    } else
    {

?>
<form method="post" action="#">
<input type="hidden" name="choice" value="<?php echo $_POST['choice']; ?>">
<table border="4">
<tr><td><b>checkbox</b></td><td><b><?php

        echo __('comment');

?></b></td><td><b><?php

        echo __('image');

?></b></td><td><b><?php

        echo __('author');

?></b></td></tr>
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
            $path = UPLOAD_PATH . 'images/' . $path;
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
<select name="action_reports">
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
<?php

    }
}

function imagements_view_waiting_images()
{
    echo '<h2>';
    echo __('images waiting for approval');
    echo '</h2>';

    global $wpdb;
    $table_name = $wpdb->prefix . 'imagements';
    $sql = "
        SELECT id, naam, path
        FROM $table_name
        WHERE status = 'waiting'
        ";
    $result = $wpdb->get_results($sql);
    if ($result == null)
    {
        echo __('No images waiting for approval!');
        echo '</div>';
    } else
    {

?>
<form method="post" action="#">
<input type="hidden" name="choice" value="<?php echo $_POST['choice']; ?>">
<table border="3">
<tr><td><b><?php

        echo __('checkbox');

?></b></td><td><b><?php

        echo __('name image');

?></b></td><td><b><?php

        echo __('image');

?></b></td></tr>
<?php

        foreach ($result as $data)
        {
            $path = UPLOAD_PATH . 'images/' . $data->path;
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
<select name="action">
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

?>" />
</td><td></td></tr>
</table>
</form>
</div>
<?php

    }
}

function imagements_view_blocked_images()
{
    echo '<h2>';
    echo __('blocked images');
    echo '</h2>';

    global $wpdb;
    $table_name = $wpdb->prefix . 'imagements';
    $sql = "
        SELECT id, naam, path
        FROM $table_name
        WHERE status = 'blocked'
        ";
    $result = $wpdb->get_results($sql);
    if ($result == null)
    {
        echo __('No blocked images to show!');
        echo '</div>';
    } else
    {

?>
<form method="post" action="#">
<input type="hidden" name="choice" value="<?php echo $_POST['choice']; ?>">
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
            $path = UPLOAD_PATH . 'images/' . $data->path;
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
<select name="action">
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
</div>
<?php

    }
}
function imagements_option_admin_system()
{
    $option = get_option('admin_system');
    switch ($option)
    {
        case 'free':
            echo "<select id='admin_system' name = 'admin_system'>
            <option value='free'>free</option>
            <option value='admin'>admin</option>
            </select>
            ";
            break;
        case 'admin':
            echo "<select id='admin_system' name = 'admin_system'>
            <option value='admin'>admin</option>
            <option value='free'>free</option>
            </select>
            ";
            break;
    }
}

function imagements_option_folder(){
    $option = get_option('upload_folder');
    echo '<input type="text" id="upload_folder" name="upload_folder" size="40" value="' . $option . '"/>';
}

function imagements_option_uploadlimit()
{
    $option = get_option('uploadlimit');
    echo "<input id='uploadlimit' name='uploadlimit' size='40' type='text' value='$option'/>";
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

function imagements_option_member_only(){
    $option = get_option('members_only');
    if($option == 'yes'){
        ?>
        <select name="members_only" id="members_only">
        <option value="yes"><?php echo __('yes'); ?></option>
        <option value="no"><?php echo __('no'); ?></option>
        </select>
        <?php
    }elseif($option == 'no'){
        ?>
        <select name="members_only" id="members_only">
        <option value="no"><?php echo __('no'); ?></option>
        <option value="yes"><?php echo __('yes'); ?></option>
        </select>
        <?php 
    }
}

function imagements_option_lightbox_on(){
    $option = get_option('lightbox_on');
    ?>
    <select name="lightbox_on" id="lightbox_on">
    <option value=1 <?php if($option){echo 'selected = true';} ?>><?php echo __('On'); ?></option>
    <option value=0 <?php if(!$option){echo 'selected = true';} ?>><?php echo __('Off'); ?></option>
    </select>
    <?php
}

?>