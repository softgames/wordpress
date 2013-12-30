<?php

global $avia_config;

/*
 * if you run a child theme and dont want to load the default functions.php file
 * set the global var bellow in you childthemes function.php to true:
 *
 * example: global $avia_config; $avia_config['use_child_theme_functions_only'] = true;
 * The default functions.php file will then no longer be loaded. You need to make sure then
 * to include framework and functions that you want to use by yourself. 
 *
 * This is only recommended for advanced users
 */

if(isset($avia_config['use_child_theme_functions_only'])) return;

/*
 * create a global var which stores the ids of all posts which are displayed on the current page. It will help us to filter duplicate posts
 */
$avia_config['posts_on_current_page'] = array();


/*
 * wpml multi site config file
 * needs to be loaded before the framework
 */

require_once( 'config-wpml/config.php' );


/*
 * These are the available color sets in your backend.
 * If more sets are added users will be able to create additional color schemes for certain areas
 *
 * The array key has to be the class name, the value is only used as tab heading on the styling page
 */

$avia_config['color_sets'] = array(
    'header_color'      => 'Header',
    'main_color'        => 'Main Content',
    'alternate_color'   => 'Alternate Content',
    'footer_color'      => 'Footer',
    'socket_color'      => 'Socket'
 );
 
 

/*
 * add support for responsive mega menus
 */
 
add_theme_support('avia_mega_menu');


/*
 * deactivates the default mega menu and allows us to pass individual menu walkers when calling a menu
 */
 
add_filter('avia_mega_menu_walker', '__return_false');


/*
 * adds support for the new avia sidebar manager
 */
 
add_theme_support('avia_sidebar_manager');


##################################################################
# AVIA FRAMEWORK by Kriesi

# this include calls a file that automatically includes all
# the files within the folder framework and therefore makes
# all functions and classes available for later use

require_once( 'framework/avia_framework.php' );

##################################################################


/*
 * Register additional image thumbnail sizes
 * Those thumbnails are generated on image upload!
 *
 * If the size of an array was changed after an image was uploaded you either need to re-upload the image
 * or use the thumbnail regeneration plugin: http://wordpress.org/extend/plugins/regenerate-thumbnails/
 */

$avia_config['imgSize']['widget'] 			 	= array('width'=>36,  'height'=>36);						// small preview pics eg sidebar news
$avia_config['imgSize']['square'] 		 	    = array('width'=>180, 'height'=>180);		                 // small image for blogs
$avia_config['imgSize']['featured'] 		 	= array('width'=>1500, 'height'=>430 );						// images for fullsize pages and fullsize slider
$avia_config['imgSize']['extra_large'] 		 	= array('width'=>1500, 'height'=>1500 , 'crop' => false);	// images for fullscrren slider
$avia_config['imgSize']['portfolio'] 		 	= array('width'=>495, 'height'=>400 );						// images for portfolio entries (2,3 column)
$avia_config['imgSize']['portfolio_small'] 		= array('width'=>260, 'height'=>185 );						// images for portfolio 4 columns
$avia_config['imgSize']['gallery'] 		 		= array('width'=>710, 'height'=>575 );						// images for portfolio entries (2,3 column)
$avia_config['imgSize']['masonry'] 		 		= array('width'=>705, 'height'=>705 , 'crop' => false);		// images for fullscreen masonry
$avia_config['imgSize']['entry_with_sidebar'] 	= array('width'=>710, 'height'=>270);		                 // big images for blog and page entries
$avia_config['imgSize']['entry_without_sidebar']= array('width'=>1030, 'height'=>360 );						// images for fullsize pages and fullsize slider

//overwrite blog and fullwidth image on extra large layouts
if(avia_get_option('responsive_layout') == "responsive responsive_large")
{
	$avia_config['imgSize']['gallery'] 		 		= array('width'=>845, 'height'=>684 );					// images for portfolio entries (2,3 column)
	$avia_config['imgSize']['entry_with_sidebar'] 	= array('width'=>845, 'height'=>321);		            // big images for blog and page entries
	$avia_config['imgSize']['entry_without_sidebar']= array('width'=>1210, 'height'=>423 );					// images for fullsize pages and fullsize slider
}



$avia_config['selectableImgSize'] = array(
	'square' 	=> __('Square','avia_framework'),
	'featured'  => __('Featured','avia_framework'),
	'portfolio' => __('Portfolio','avia_framework'),
	'gallery' 	=> __('Gallery','avia_framework'),
	'entry_with_sidebar' 	=> __('Entry with Sidebar','avia_framework'),
	'entry_without_sidebar' 	=> __('Entry without Sidebar','avia_framework'),
	'extra_large' 	=> __('Fullscreen Sections/Sliders','avia_framework'),
	
);

avia_backend_add_thumbnail_size($avia_config);

if ( ! isset( $content_width ) ) $content_width = $avia_config['imgSize']['featured']['width'];




/*
 * register the layout sizes: the written number represents the grid size, if the elemnt should not have a left margin add "alpha"
 *
 * Calculation of the with: the layout is based on a twelve column grid system, so content + sidebar must equal twelve.
 * example:  'content' => 'nine alpha',  'sidebar' => 'three'
 *
 * if the theme uses fancy blog layouts ( meta data beside the content for example) use the meta and entry values.
 * calculation of those: meta + entry = content
 *
 */

$avia_config['layout']['fullsize'] 		= array('content' => 'twelve alpha', 'sidebar' => 'hidden', 	 'meta' => 'two alpha', 'entry' => 'eleven');
$avia_config['layout']['sidebar_left'] 	= array('content' => 'nine', 		 'sidebar' => 'three alpha' ,'meta' => 'two alpha', 'entry' => 'nine');
$avia_config['layout']['sidebar_right'] = array('content' => 'nine alpha',   'sidebar' => 'three alpha', 'meta' => 'two alpha', 'entry' => 'nine alpha');



/*
 * These are some of the font icons used in the theme, defined by the entypo icon font. the font files are included by the new aviaBuilder
 * common icons are stored here for easy retrieval
 */
 
 $avia_config['font_icons'] = apply_filters('avf_default_icons', array(
 
    //post formats
    'standard' 		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue836'),
    'link'    		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue822'),
    'image'    		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue80f'),
    'audio'    		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue801'),
    'quote'   		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue833'),
    'gallery'   	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue80e'),
    'video'   		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue80d'),
    				
    //social		
    'behance' 		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue915'),
	'dribbble' 		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue8fe'),
	'facebook' 		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue8f3'),
	'flickr' 		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue8ed'),
	'gplus' 		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue8f6'),
	'linkedin' 		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue8fc'),
	'instagram' 	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue909'),
	'pinterest' 	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue8f8'),
	'skype' 		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue90d'),
	'tumblr' 		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue8fa'),
	'twitter' 		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue8f1'),
	'vimeo' 		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue8ef'),
	'rss' 			=> array( 'font' =>'entypo-fontello', 'icon' => 'ue853'),  
	'youtube'		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue921'),  
	'xing'			=> array( 'font' =>'entypo-fontello', 'icon' => 'ue923'),  
	'soundcloud'	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue913'),  
	'five_100_px'	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue91d'),  
	'mail' 			=> array( 'font' =>'entypo-fontello', 'icon' => 'ue805'),
					
	//woocomemrce    
	'cart' 			=> array( 'font' =>'entypo-fontello', 'icon' => 'ue859'),
	'details'		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue84b'),

	//bbpress    
	'supersticky'	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue808'),
	'sticky'		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue809'),
	'one_voice'		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue83b'),
	'multi_voice'	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue83c'),
	'closed'		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue824'),
	'sticky_closed' => array( 'font' =>'entypo-fontello', 'icon' => 'ue808\ue824'),
	'supersticky_closed' => array( 'font' =>'entypo-fontello', 'icon' => 'ue809\ue824'),
					
	//navigation, slider & controls
	'play' 			=> array( 'font' =>'entypo-fontello', 'icon' => 'ue897'),
	'pause'			=> array( 'font' =>'entypo-fontello', 'icon' => 'ue899'),
	'next'    		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue879'),
    'prev'    		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue878'),
    'next_big'  	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue87d'),
    'prev_big'  	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue87c'),
	'close'			=> array( 'font' =>'entypo-fontello', 'icon' => 'ue814'),
	'reload'		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue891'),
	'mobile_menu'	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue8a5'),
					
	//image hover overlays		
    'ov_external'	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue832'),
    'ov_image'		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue869'),
    'ov_video'		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue897'),
    
					
	//misc			
    'search'  		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue803'),
    'info'    		=> array( 'font' =>'entypo-fontello', 'icon' => 'ue81e'),
	'clipboard' 	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue8d1'),
	'scrolltop' 	=> array( 'font' =>'entypo-fontello', 'icon' => 'ue876'),

));






add_theme_support( 'automatic-feed-links' );

##################################################################
# Frontend Stuff necessary for the theme:
##################################################################
/*
 * Register theme text domain
 */
if(!function_exists('avia_lang_setup'))
{
	add_action('after_setup_theme', 'avia_lang_setup');
	function avia_lang_setup()
	{
		$lang = get_template_directory()  . '/lang';
		load_theme_textdomain('avia_framework', $lang);
	}
}


/*
 * Register frontend javascripts:
 */
if(!function_exists('avia_register_frontend_scripts'))
{
	if(!is_admin()){
		add_action('wp_enqueue_scripts', 'avia_register_frontend_scripts');
	}

	function avia_register_frontend_scripts()
	{
		$template_url = get_template_directory_uri();
		$child_theme_url = get_stylesheet_directory_uri();

		//register js
		wp_register_script( 'avia-compat', $template_url.'/js/avia-compat.js', array('jquery'), 1, false ); //needs to be loaded at the top to prevent bugs
		wp_register_script( 'avia-default', $template_url.'/js/avia.js', array('jquery'), 1, true );
		wp_register_script( 'avia-shortcodes', $template_url.'/js/shortcodes.js', array('jquery'), 1, true );
		wp_register_script( 'avia-prettyPhoto',  $template_url.'/js/prettyPhoto/js/jquery.prettyPhoto.js', 'jquery', "3.1.5", true);
		// wp_register_script( 'wp-mediaelement',  $template_url.'/js/mediaelement/mediaelement-and-player.min.js', 'jquery', "1", true);

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'avia-compat' );
		wp_enqueue_script( 'avia-default' );
		wp_enqueue_script( 'avia-shortcodes' );
		wp_enqueue_script( 'avia-prettyPhoto' );
		wp_enqueue_script( 'wp-mediaelement' );


		if ( is_singular() && get_option( 'thread_comments' ) ) { wp_enqueue_script( 'comment-reply' ); }


		//register styles
		wp_register_style( 'avia-style' ,  $child_theme_url."/style.css", array(), '1', 'screen' ); //register default style.css file. only include in childthemes. has no purpose in main theme
		wp_register_style( 'avia-grid' ,   $template_url."/css/grid.css", array(), '1', 'screen' );
		wp_register_style( 'avia-base' ,   $template_url."/css/base.css", array(), '1', 'screen' );
		wp_register_style( 'avia-layout',  $template_url."/css/layout.css", array(), '1', 'screen' );
		wp_register_style( 'avia-scs',     $template_url."/css/shortcodes.css", array(), '1', 'screen' );
		wp_register_style( 'avia-custom',  $template_url."/css/custom.css", array(), '1', 'screen' );
		wp_register_style( 'avia-prettyP', $template_url."/js/prettyPhoto/css/prettyPhoto.css", array(), '1', 'screen' );
		wp_register_style( 'avia-media'  , $template_url."/js/mediaelement/skin-1/mediaelementplayer.css", array(), '1', 'screen' );


		//register styles

		wp_enqueue_style( 'avia-grid');
		wp_enqueue_style( 'avia-base');
		wp_enqueue_style( 'avia-layout');
		wp_enqueue_style( 'avia-scs');
		wp_enqueue_style( 'avia-prettyP');
		wp_enqueue_style( 'avia-media');



        global $avia;
		$safe_name = avia_backend_safe_string($avia->base_data['prefix']);

        if( get_option('avia_stylesheet_exists'.$safe_name) == 'true' )
        {
            $avia_upload_dir = wp_upload_dir();

            $avia_dyn_stylesheet_url = $avia_upload_dir['baseurl'] . '/dynamic_avia/'.$safe_name.'.css';
            wp_register_style( 'avia-dynamic', $avia_dyn_stylesheet_url, array(), '1', 'screen' );
            wp_enqueue_style( 'avia-dynamic');
        }

		wp_enqueue_style( 'avia-custom');


		if($child_theme_url !=  $template_url)
		{
			wp_enqueue_style( 'avia-style');
		}

	}
}


if(!function_exists('avia_remove_default_video_styling'))
{
	if(!is_admin()){
		add_action('wp_footer', 'avia_remove_default_video_styling', 1);
	}

	function avia_remove_default_video_styling()
	{
		//remove default style for videos
		wp_dequeue_style( 'mediaelement' );
		//wp_dequeue_style( 'wp-mediaelement' );
	}
}




/*
 * Activate native wordpress navigation menu and register a menu location
 */
if(!function_exists('avia_nav_menus'))
{
	function avia_nav_menus()
	{
		global $avia_config;

		add_theme_support('nav_menus');
		foreach($avia_config['nav_menus'] as $key => $value){ register_nav_menu($key, THEMENAME.' '.$value); }
	}

	$avia_config['nav_menus'] = array(	'avia' => 'Main Menu' ,
										'avia2' => 'Secondary Menu <br/><small>(Will be displayed if you selected a header layout that supports a submenu <a target="_blank" href="'.admin_url('?page=avia#goto_header').'">here</a>)</small>',
										'avia3' => 'Footer Menu <br/><small>(no dropdowns)</small>'
									);
	avia_nav_menus(); //call the function immediatly to activate
}









/*
 *  load some frontend functions in folder include:
 */

require_once( 'includes/admin/register-portfolio.php' );		// register custom post types for portfolio entries
require_once( 'includes/admin/register-widget-area.php' );		// register sidebar widgets for the sidebar and footer
require_once( 'includes/loop-comments.php' );					// necessary to display the comments properly
require_once( 'includes/helper-template-logic.php' ); 			// holds the template logic so the theme knows which tempaltes to use
require_once( 'includes/helper-social-media.php' ); 			// holds some helper functions necessary for twitter and facebook buttons
require_once( 'includes/helper-post-format.php' ); 				// holds actions and filter necessary for post formats
require_once( 'includes/helper-markup.php' ); 					// holds the markup logic (schema.org and html5)
require_once( 'includes/admin/register-plugins.php');			// register the plugins we need

if(current_theme_supports('avia_conditionals_for_mega_menu'))
{
	require_once( 'includes/helper-conditional-megamenu.php' );  // holds the walker for the responsive mega menu
}

require_once( 'includes/helper-responsive-megamenu.php' ); 		// holds the walker for the responsive mega menu




//adds the plugin initalization scripts that add styles and functions

if(!current_theme_supports('deactivate_layerslider')) require_once( 'config-layerslider/config.php' );//layerslider plugin

require_once( 'config-bbpress/config.php' );					//compatibility with  bbpress forum plugin
require_once( 'config-templatebuilder/config.php' );			//templatebuilder plugin
require_once( 'config-gravityforms/config.php' );				//compatibility with gravityforms plugin
require_once( 'config-woocommerce/config.php' );				//compatibility with woocommerce plugin
require_once( 'config-wordpress-seo/config.php' );				//compatibility with Yoast WordPress SEO plugin



/*
 *  dynamic styles for front and backend
 */
if(!function_exists('avia_custom_styles'))
{
	function avia_custom_styles()
	{
		require_once( 'includes/admin/register-dynamic-styles.php' );	// register the styles for dynamic frontend styling
		avia_prepare_dynamic_styles();
	}

	add_action('init', 'avia_custom_styles', 20);
	add_action('admin_init', 'avia_custom_styles', 20);
}




/*
 *  activate framework widgets
 */
if(!function_exists('avia_register_avia_widgets'))
{
	function avia_register_avia_widgets()
	{
		register_widget( 'avia_newsbox' );
		register_widget( 'avia_portfoliobox' );
		register_widget( 'avia_socialcount' );
		register_widget( 'avia_combo_widget' );
		register_widget( 'avia_partner_widget' );
		register_widget( 'avia_google_maps' );
	}

	avia_register_avia_widgets(); //call the function immediatly to activate
}



/*
 *  add post format options
 */
add_theme_support( 'post-formats', array('link', 'quote', 'gallery','video','image','audio' ) );



/*
 *  Remove the default shortcode function, we got new ones that are better ;)
 */
add_theme_support( 'avia-disable-default-shortcodes', true);


/*
 * compat mode for easier theme switching from one avia framework theme to another
 */
add_theme_support( 'avia_post_meta_compat');


/*
 * make sure that enfold widgets dont use the old slideshow parameter in widgets, but default post thumbnails
 */
add_theme_support('force-post-thumbnails-in-widget');


/*
 *  register custom functions that are not related to the framework but necessary for the theme to run
 */

require_once( 'functions-enfold.php');
