<?php

######################################################################
# remove backend options by removing them from the config array
######################################################################
add_filter('woocommerce_general_settings','avia_woocommerce_general_settings_filter');
add_filter('woocommerce_page_settings','avia_woocommerce_general_settings_filter');
add_filter('woocommerce_catalog_settings','avia_woocommerce_general_settings_filter');
add_filter('woocommerce_inventory_settings','avia_woocommerce_general_settings_filter');
add_filter('woocommerce_shipping_settings','avia_woocommerce_general_settings_filter');
add_filter('woocommerce_tax_settings','avia_woocommerce_general_settings_filter');


function avia_woocommerce_general_settings_filter($options)
{  
	$remove   = array('woocommerce_enable_lightbox', 'woocommerce_frontend_css');
	//$remove = array('image_options', 'woocommerce_enable_lightbox', 'woocommerce_catalog_image', 'woocommerce_single_image', 'woocommerce_thumbnail_image', 'woocommerce_frontend_css');

	foreach ($options as $key => $option)
	{
		if( isset($option['id']) && in_array($option['id'], $remove) ) 
		{  
			unset($options[$key]); 
		}
	}

	return $options;
}



//on theme activation set default image size, disable woo lightbox and woo stylesheet. options are already hidden by previous filter function
function avia_woocommerce_set_defaults()
{
	global $avia_config;

	update_option('shop_catalog_image_size', $avia_config['imgSize']['shop_catalog']);
	update_option('shop_single_image_size', $avia_config['imgSize']['shop_single']);
	update_option('shop_thumbnail_image_size', $avia_config['imgSize']['shop_thumbnail']);

	//set custom
	
	update_option('avia_woocommerce_column_count', 3);
	update_option('avia_woocommerce_product_count', 15);
	
	//set blank
	$set_false = array('woocommerce_enable_lightbox', 'woocommerce_frontend_css');
	foreach ($set_false as $option) { update_option($option, false); }
	
	//set blank
	$set_no = array('woocommerce_single_image_crop');
	foreach ($set_no as $option) { update_option($option, 'no'); }

}

add_action( 'avia_backend_theme_activation', 'avia_woocommerce_set_defaults', 10);


//activate the plugin options when this file is included for the first time
add_action('admin_init', 'avia_woocommerce_first_activation' , 45 );
function avia_woocommerce_first_activation()
{
	if(!is_admin()) return;
	
	$themeNice = avia_backend_safe_string(THEMENAME);

	if(get_option("{$themeNice}_woo_settings_enabled")) return;
	update_option("{$themeNice}_woo_settings_enabled", '1');
	
	avia_woocommerce_set_defaults();
}


function avia_please_install_woo()
{
	$url = network_site_url( 'wp-admin/plugin-install.php?tab=search&type=term&s=WooCommerce&plugin-search-input=Search+Plugins');
	$output = "<p><strong>You need to install and activate the <a href='$url'>WooCommerce Shop Plugin</a> to display Products</strong></p>";
	return $output;
}





//add new options to the catalog settings
add_filter('woocommerce_catalog_settings','avia_woocommerce_page_settings_filter');

function avia_woocommerce_page_settings_filter($options)
{  

	$options[] = array(
		'name' => 'Column and Product Count',
        'type' => 'title',
        'desc' => 'The following settings allow you to choose how many columns and items should appear on your default shop overview page and your product archive pages.<br/><small>Notice: These options are added by the <strong>'.THEMENAME.' Theme</strong> and wont appear on other themes</small>',
        'id'   => 'column_options'
	);
	
	$options[] = array(
			'name' => 'Column Count',
            'desc' => '',
            'id' => 'avia_woocommerce_column_count',
            'css' => 'min-width:175px;',
            'std' => '3',
            'desc_tip' => "This controls how many columns should appear on overview pages.",
            'type' => 'select',
            'options' => array
                (
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5'
                )
	);
	
	$itemcount = array('-1'=>'All');
	for($i = 3; $i<101; $i++) $itemcount[$i] = $i;	
	
		$options[] = array(
			'name' => 'Product Count',
            'desc' => "",
            'id' => 'avia_woocommerce_product_count',
            'css' => 'min-width:175px;',
            'desc_tip' => 'This controls how many products should appear on overview pages.',
            'std' => '24',
            'type' => 'select',
            'options' => $itemcount
	);
	
	$options[] = array(
        
            'type' => 'sectionend',
            'id' => 'column_options'
        );
	
	
	return $options;
}



#
# add custom product meta boxes
#

add_filter('avf_builder_boxes','avia_woocommerce_product_options');

function avia_woocommerce_product_options($boxes)
{
	$boxes[] = array( 'title' =>__('Product Hover','avia_framework' ), 'id'=>'avia_product_hover', 'page'=>array('product'), 'context'=>'side', 'priority'=>'low');

	$counter = 0;
	foreach($boxes as $box)
	{
		if($box['title'] == 'Layout') $boxes[$counter]['page'][] = 'product';
		$counter++;
	}
	return $boxes;
}

add_filter('avf_builder_elements','avia_woocommerce_product_elements');

function avia_woocommerce_product_elements($elements)
{
	global $post, $typenow, $current_screen;
	//we have a post so we can just get the post type from that
	if ($post && $post->post_type)
	{
		$posttype = $post->post_type;
	}//check the global $typenow - set in admin.php
	elseif($typenow)
	{
		$posttype = $typenow;
	}//check the global $current_screen object - set in sceen.php
	elseif($current_screen && $current_screen->post_type)
	{
		$posttype = $current_screen->post_type;
	}//lastly check the post_type querystring
	elseif(isset($_REQUEST['post_type']))
	{
		$posttype = sanitize_key($_REQUEST['post_type']);
	}

    if(!empty($posttype) && $posttype == 'product')
    {
    	
        $elements[] = array("slug"	=> "avia_product_hover",
            "name" 	=> "Hover effect on <strong>Overview Pages</strong>",
            "desc" 	=> "Do you want to display a hover effect on overview pages and replace the default thumbnail with the first image of the gallery?",
            "id" 	=> "_product_hover",
            "type" 	=> "select",
            "std" 	=> "",
            "class" => "avia-style",
            "subtype" => array("Yes - show first gallery image on hover" => 'hover_active', "No hover effect" => ''));
            
        $counter = 0;
        foreach($elements as $element)
        {
            if($element['id'] == 'sidebar') $elements[$counter]['required'] = '';
            if($element['id'] == 'layout') unset($elements[$counter]);
            $counter++;
        }
    }

	return $elements;
}
