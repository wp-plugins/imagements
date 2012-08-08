<?php

/**
 * @author Frank
 * @copyright 2012
 */


function imagements_admin_add_page()
{
    add_options_page(__('imagements general options menu'), __('imagements general'), 'manage_options', 'imagements', 'imagements_general_menu_options');
    add_comments_page(__('imagements reports'), __('imagements reports'), 'manage_options', 'imagements_reports', 'imagements_reports_form');
}

function imagements_admin_init()
{
    add_settings_section('imagements_main', 'imagements', 'imagements_general_section_text', 'imagements');
    register_setting('imagements_options', 'tag');
    register_setting('imagements_options', 'max_width', 'imagements_general_options_validate');
    register_setting('imagements_options', 'max_height', 'imagements_general_options_validate');
    add_settings_field('tag', __('the tag used to insert an image: '), 'imagements_option_tag', 'imagements', 'imagements_main');
    add_settings_field('max_width', __('maximum width in pixels: '), 'imagements_option_width', 'imagements', 'imagements_main');
    add_settings_field('max_height', __('maximum height in pixels: '), 'imagements_option_height', 'imagements', 'imagements_main');
}

function imagements_reports_form()
{

?>
<div class="wrap">
<h2>imagements reports</h2>
<form method="post" action="">
<table border="4">
<tr><td><b>checkbox</b></td><td><b>comment</b></td><td><b>image</b></td><td><b>author</b></td></tr>
<?php
global $wpdb;
$table_name = $wpdb->prefix . 'imagements_reports';
$sql = "
SELECT *
FROM $table_name
";
$results = $wpdb->get_results($sql);
foreach($results as $data){
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
    echo '<tr><td>';
    echo '<input type="checkbox" name="' . $comment_ID . '" value="selected">';
    echo '</td><td>';
    echo $content;
    echo '</td><td>';
    echo '<img src="' . $path . '">';
    echo '</td><td>';
    echo $author;
    echo '</td></tr>';
}


?> 
<tr><td></td><td><input name="Submit" type="submit" value="<?php

    $buttontext = __('Apply');
    esc_attr_e($buttontext);

?>" /></td></tr>	
</table>
</form>
</div>
<?php

}

function imagements_general_options_validate($input)
{
    if (!is_numeric($input['max_width']))
    {
        $input['max width'] = 300;
    }
    if (!is_numeric($input['max_height']))
    {
        $input['max_height'] = 300;
    }
    return $input;
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