<?php

if(class_exists('GFForms') )
{
	add_action('wp_enqueue_scripts', 'avia_add_gravity_scripts');
}


function avia_add_gravity_scripts()
{
	wp_register_style( 'avia-gravity' ,   get_template_directory_uri()."/config-gravityforms/gravity-mod.css", array(), '1', 'screen' );
	wp_enqueue_style( 'avia-gravity');
}

add_filter('avf_ajax_form_class', 'avia_change_ajax_form_class', 10, 3);
function avia_change_ajax_form_class($class, $formID, $form_params)
{
	return 'avia_ajax_form';
}