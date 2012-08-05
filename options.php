<?php

/**
 * @author Frank
 * @copyright 2012
 */
 function imagements_admin_add_page(){
    add_options_page('imagements options menu', 'imagements', 'manage_options', 'imagements', 'imagements_menu_options');
 }
 
function imagements_admin_init(){
add_settings_section('imagements_main', 'imagements ', 'imagements_section_text', 'imagements');
register_setting('imagements_options', 'max_width', 'imagements_options_validate' );
register_setting('imagements_options', 'max_height', 'imagements_options_validate' );
add_settings_field('max_width', __('maximum width in pixels: '), 'imagements_option_width', 'imagements', 'imagements_main');
add_settings_field('max_height', __('maximum height in pixels: '), 'imagements_option_height', 'imagements', 'imagements_main');
}

function imagements_options_validate($input){
    if(! is_numeric($input['max_width'])){
        $input['max width'] = 300;
    }
    if(! is_numeric($input['max_height'])){
        $input['max_height'] = 300;
    }
return $input;
}

function imagements_option_width(){
    $option = get_option('max_width');
    echo "<input id='max_width' name='max_width' size='40' type='text' value='$option' />";
}

function imagements_option_height(){
    $option = get_option('max_height');
    echo "<input id='max_height' name='max_height' size='40' type='text' value='$option' />";
    
}

function imagements_section_text(){
    echo __('<p>here you can set the settings of the imagements plugin</p>');    
}

function imagements_menu_options()
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