<?php
global $avia_config;

//avia pages holds the data necessary for backend page creation
$avia_pages = array(

	array( 'slug' => 'avia', 		'parent'=>'avia', 'icon'=>"hammer_screwdriver.png" , 	'title' =>  'Theme Options' ),
	array( 'slug' => 'styling', 	'parent'=>'avia', 'icon'=>"palette.png", 				'title' =>  'Styling'  ),
	array( 'slug' => 'layout', 		'parent'=>'avia', 'icon'=>"blueprint_horizontal.png", 	'title' =>  'General Settings'  ),
	array( 'slug' => 'header', 		'parent'=>'avia', 'icon'=>"layout_select.png", 			'title' =>  'Header'  ),
	array( 'slug' => 'sidebars', 	'parent'=>'avia', 'icon'=>"layout_select_sidebar.png", 	'title' =>  'Sidebar '  ),
	array( 'slug' => 'footer', 		'parent'=>'avia', 'icon'=>"layout_select_footer.png", 	'title' =>  'Footer'  ),
	//array( 'slug' => 'portfolio', 'parent'=>'avia', 'icon'=>"photo_album.png" , 			'title' =>  'Portfolio' ),

);





/*Frontpage Settings*/


if(!current_theme_supports('avia_disable_dummy_import')){
$avia_elements[] =	array(
					"slug"	=> "avia",
					"name" 	=> "Import Dummy Content: Posts, Pages, Categories",
					"desc" 	=> "If you are new to wordpress or have problems creating posts or pages that look like the <a href='http://www.kriesi.at/themes/enfold/'>Theme Demo</a> you can import dummy posts and pages here that will definitely help to understand how those tasks are done.",
					"id" 	=> "import",
					"type" 	=> "import");
}


if(is_child_theme()){
$avia_elements[] =	array(
					"slug"	=> "avia",
					"name" 	=> "Import Settings from your Parent Theme",
					"desc" 	=> "We have detected that you are using a Child Theme. That's Great!. If you want to we can import the settings of your Parent theme to your Child theme. <br/>Please be aware that this will overwrite your current child theme settings.",
					"id" 	=> "parent_setting_import",
					"type" 	=> "parent_setting_import");
}

if(current_theme_supports('avia_enable_export')){ // hidden until heavily tested. display it by default in enfold 2.5 or 2.6
$avia_elements[] =	array(
    "slug"	=> "avia",
    "name" 	=> "Export Theme Settings",
    "desc" 	=> "Click the button to generate and download a config file which contains the theme settings. You can use the config file to import the theme settings on another sever.",
    "id" 	=> "theme_settings_export",
    "type" 	=> "theme_settings_export");

$avia_elements[] =	array(
    "slug"		=> "avia",
    "name" 		=> "Import Theme Settings",
    "desc" 		=> "Upload an Enfold theme configuration file here. Note that the configuration file settings will overwrite your current configuration and you can't restore the current configuration afterwards.",
    "id" 		=> "config_file_upload",
    "title" 	=> "Upload Settings File",
    "button" 	=> "Insert Settings File",
    "trigger" 	=> "av_config_file_insert",
    // "fopen_check" 	=> "true",
    "std"	  	=> "",
    "type" 		=> "file_upload");
}

$avia_elements[] =	array(
					"slug"	=> "avia",
					"name" 	=> "Frontpage Settings",
					"desc" 	=> "Select which page to display on your Frontpage. If left blank the Blog will be displayed",
					"id" 	=> "frontpage",
					"type" 	=> "select",
					"subtype" => 'page');

$avia_elements[] =	array(
					"slug"	=> "avia",
					"name" 	=> "And where do you want to display the Blog?",
					"desc" 	=> "Select which page to display as your Blog Page. If left blank no blog will be displayed",
					"id" 	=> "blogpage",
					"type" 	=> "select",
					"subtype" => 'page',
					"required" => array('frontpage','{true}')
					);

$avia_elements[] =	array(
					"slug"	=> "avia",
					"name" 	=> "Logo",
					"desc" 	=> "Upload a logo image, or enter the URL to an image if its already uploaded. The themes default logo gets applied if the input field is left blank<br/>Logo Dimension: 340px * 156px (if your logo is larger you might need to modify style.css to align it perfectly)",
					"id" 	=> "logo",
					"type" 	=> "upload",
					"label"	=> "Use Image as logo");

$avia_elements[] =	array(
					"slug"	=> "avia",
					"name" 	=> "Favicon",
					"desc" 	=> "Specify a <a href='http://en.wikipedia.org/wiki/Favicon'>favicon</a> for your site. <br/>Accepted formats: .ico, .png, .gif",
					"id" 	=> "favicon",
					"type" 	=> "upload",
					"label"	=> "Use Image as Favicon");





$avia_elements[] =	array(
					"slug"	=> "avia",
					"name" 	=> "Google Analytics Tracking Code",
					"desc" 	=> "Enter your Google analytics tracking Code here. It will automatically be added so google can track your visitors behavior.",
					"id" 	=> "analytics",
					"type" 	=> "textarea"
					);

include_once('register-backend-styles.php');
$avia_elements[] =	array(
					"slug"	=> "styling",
					"name" 	=> "Select a predefined color scheme",
					"desc" 	=> "Choose a predefined color scheme here. You can edit the settings of the scheme bellow then.",
					"id" 	=> "color_scheme",
					"type" 	=> "link_controller",
					"std" 	=> "Blue",
					"class"	=> "link_controller_list",
					"subtype" => $styles);

/*Styling Settings*/
$avia_elements[] =	array(
					"slug"	=> "styling",
					"id" 	=> "default_slideshow_target",
					"type" 	=> "target",
					"std" 	=> "
					<style type='text/css'>

						#boxed .live_bg_wrap{ padding:23px;   border:1px solid #e1e1e1; background-position: top center;}
						.live_bg_small{font-size:10px; color:#999;}
						.live_bg_wrap{ padding: 0; background:#f8f8f8; overflow:hidden; background-position: top center;}
						.live_bg_wrap div{overflow:hidden; position:relative;}
						.live_bg_wrap h3{margin: 0 0 5px 0;}
						.live_bg_wrap .main_h3{font-weight:bold; font-size:17px;}
						.border{border:1px solid; border-bottom:none; padding:13px; width:562px;}
						#boxed .border{  width:514px;}

						.live_header_color {position: relative;width: 100%;left: }
						.bg2{border:1px solid; margin:4px; display:block; float:right; width:220px; padding:5px;}
						.content_p{display:block; float:left; width:250px;}
						.live-socket_color{font-size:11px;}
						.live-footer_color a{text-decoration:none;}
						.live-socket_color a{text-decoration:none;  position:absolute; top:28%; right:13px;}

						#avia_preview .webfont_google_webfont{  font-weight:normal; }
						.webfont_default_font{  font-weight:normal; font-size:13px; line-height:1.7em;}

						div .link_controller_list a{ width:95px; font-size:11px;}
						.avia_half{width: 267px; float:left; height:183px;}
						.avia_half .bg2{float:none; margin-left:0;}
						.avia_half_2{border-left:none; padding-left:14px;}
						#boxed  .avia_half { width: 243px; }
						.live-slideshow_color{text-align:center;}
						.text_small_outside{position:relative; top:-15px; display:block; left: 10px;}
					</style>





					<small class='live_bg_small'>A rough preview of the frontend.</small>

					<div id='avia_preview' class='live_bg_wrap webfont_default_font'>
					<!--<small class='text_small_outside'>Next Event: in 10 hours 5 minutes.</small>-->


						<div class='live-header_color border'>
							<span class='text'>Header Text</span>
							<a class='a_link' href='#'>A link</a>
							<a class='an_activelink' href='#'>A hovered link</a>
							<div class='bg2'>Highlight Background + Border Color</div>
						</div>

						<!--<div class='live-slideshow_color border'>
							<h3 class='webfont_google_webfont main_h3'>Slideshow Area/Page Title Area</h3>
								<p class='slide_p'>Slideshow caption<br/>
									<a class='a_link' href='#'>A link</a>
									<a class='an_activelink' href='#'>A hovered link</a>
								</p>
						</div>-->

						<div class='live-main_color border avia_half'>
							<h3 class='webfont_google_webfont main_h3'>Main Content heading</h3>
								<p class='content_p'>This is default content with a default heading. Font color, headings and link colors can be choosen below. <br/>
									<a class='a_link' href='#'>A link</a>
									<a class='an_activelink' href='#'>A hovered link</a>
								</p>

								<div class='bg2'>Highlight Background + Border Color</div>
						</div>



						<div class='live-alternate_color border avia_half avia_half_2'>
								<h3 class='webfont_google_webfont main_h3'>Alternate Content Area</h3>
								<p>This is content of an alternate content area. Choose font color, headings and link colors below. <br/>
									<a class='a_link' href='#'>A link</a>
									<a class='an_activelink' href='#'>A hovered link</a>
								</p>

								<div class='bg2'>Highlight Background + Border Color</div>
						</div>

						<div class='live-footer_color border'>
							<h3 class='webfont_google_webfont'>Demo heading (Footer)</h3>
							<p>This is text on the footer background</p>
							<a class='a_link' href='#'>Link | Link 2</a>
						</div>

						<div class='live-socket_color border'>Socket Text <a class='a_link' href='#'>Link | Link 2</a></div>
					</div>

					",
					"nodescription" => true
					);





$avia_elements[] = array(	"slug"	=> "styling", "type" => "visual_group_start", "id" => "avia_tab1", "nodescription" => true, 'class'=>'avia_tab_container avia_set');


$avia_elements[] = array(	"slug"	=> "styling", "type" => "visual_group_start", "id" => "avia_tab5", "nodescription" => true, 'class'=>'avia_tab avia_tab2','name'=>'General');


$avia_elements[] =		array(	"name" 	=> "Heading Font",
								"slug"	=> "styling",
								"desc" 	=> "The Font heading utilizes google fonts and allows you to use a wide range of custom fonts for your headings",
					            "id" 	=> "google_webfont",
					            "type" 	=> "select",
					            "no_first" => true,
					            "class" => "av_2columns av_col_1",
					            "onchange" => "avia_add_google_font",
					            "std" 	=> "Open Sans",
					            "subtype" => apply_filters('avf_google_heading_font', array('no custom font'=>'',

					            					'Alice'=>'Alice',
					            					'Allerta'=>'Allerta',
					            					'Arvo'=>'Arvo',
					            					'Antic'=>'Antic',

					            					'Bangers'=>'Bangers',
					            					'Bitter'=>'Bitter',

					            					'Cabin'=>'Cabin',
					            					'Cardo'=>'Cardo',
					            					'Carme'=>'Carme',
					            					'Coda'=>'Coda',
					            					'Coustard'=>'Coustard',
					            					'Gruppo'=>'Gruppo',

					            					'Damion'=>'Damion',
					            					'Dancing Script'=>'Dancing Script',
					            					'Droid Sans'=>'Droid Sans',
					            					'Droid Serif'=>'Droid Serif',

					            					'EB Garamond'=>'EB Garamond',

					            					'Fjord One'=>'Fjord One',

					            					'Inconsolata'=>'Inconsolata',

					            					'Josefin Sans' => 'Josefin Sans',
					            					'Josefin Slab'=>'Josefin Slab',

					            					'Kameron'=>'Kameron',
					            					'Kreon'=>'Kreon',

					            					'Lato'=>'Lato',
					            					'Lobster'=>'Lobster',
					            					'League Script'=>'League Script',

					            					'Mate SC'=>'Mate SC',
					            					'Mako'=>'Mako',
					            					'Merriweather'=>'Merriweather',
					            					'Metrophobic'=>'Metrophobic',
					            					'Molengo'=>'Molengo',
					            					'Muli'=>'Muli',

					            					'Nobile'=>'Nobile',
					            					'News Cycle'=>'News Cycle',

					            					'Open Sans'=>'Open Sans:400,600',
					            					'Orbitron'=>'Orbitron',
					            					'Oswald'=>'Oswald',

					            					'Pacifico'=>'Pacifico',
					            					'Poly'=>'Poly',
					            					'Podkova'=>'Podkova',
					            					'PT Sans'=>'PT Sans',

					            					'Quattrocento'=>'Quattrocento',
					            					'Questrial'=>'Questrial',
					            					'Quicksand'=>'Quicksand',

					            					'Raleway'=>'Raleway',

					            					'Salsa'=>'Salsa',
					            					'Sunshiney'=>'Sunshiney',
					            					'Signika Negative'=>'Signika Negative',


					            					'Tangerine'=>'Tangerine',
					            					'Terminal Dosis'=>'Terminal Dosis',
					            					'Tenor Sans'=>'Tenor Sans',

					            					'Varela Round'=>'Varela Round',

					            					'Yellowtail'=>'Yellowtail',


					            					)));

$avia_elements[] =	array(	"name" 	=> "Defines the Font for your body text",
							"slug"	=> "styling",
							"desc" 	=> "Choose between web safe fonts (faster rendering) and google webkit fonts (more unqiue)",
				            "id" 	=> "default_font",
				            "type" 	=> "select",
				            "no_first" => true,
				            "class" => "av_2columns av_col_2",
				            "onchange" => "avia_add_google_font",
				            "std" 	=> "Helvetica-Neue,Helvetica-websave",
				            "subtype" => apply_filters('avf_google_content_font', array(	':: :: Web save fonts :: ::'=>'',
				            					'Arial'=>'Arial-websave',
				            					'Georgia'=>'Georgia-websave',
				            					'Verdana'=>'Verdana-websave',
				            					'Helvetica'=>'Helvetica-websave',
				            					'Helvetica Neue'=>'Helvetica-Neue,Helvetica-websave',
				            					'Lucida'=>'"Lucida-Sans",-"Lucida-Grande",-"Lucida-Sans-Unicode-websave"',
				            					':: :: Google fonts :: ::'=>'',
				            					'Arimo'=>'Arimo',
				            					'Cardo'=>'Cardo',
				            					'Droid Sans'=>'Droid Sans',
				            					'Droid Serif'=>'Droid Serif',
				            					'Kameron'=>'Kameron',
				            					'Maven Pro'=>'Maven Pro',
				            					'Open Sans'=>'Open Sans:400,600',
					            				'Lato'=>'Lato',
				            					'Lora'=>'Lora',

				            					)));

$avia_elements[] = array(	"slug"	=> "styling", "type" => "hr", "id" => "hrxy", "nodescription" => true);

$avia_elements[] =	array(
					"slug"	=> "styling",
					"name" 	=> "Use stretched or boxed layout?",
					"desc" 	=> "The stretched layout expands from the left side of the viewport to the right.",
					"id" 	=> "color-body_style",
					"type" 	=> "select",
					"std" 	=> "stretched",
					"no_first"=>true,
					"class" => "av_2columns av_col_1",
					"target" => array("default_slideshow_target::.avia_control_container::set_id"),
					"subtype" => array('Stretched layout'=>'stretched','Boxed Layout'=>'boxed'));


$avia_elements[] =	array(
					"slug"	=> "styling",
					"name" 	=> "Body Background color",
					"desc" 	=> "Background color for your site<br/><br/>",
					"id" 	=> "color-body_color",
					"type" 	=> "colorpicker",
					"std" 	=> "#eeeeee",
					"class" => "av_2columns av_col_2",
					"required" => array("color-body_style",'boxed'),
					"target" => array("default_slideshow_target::.live_bg_wrap::background-color"),
					);

/*
$avia_elements[] =	array(
					"slug"	=> "styling",
					"name" 	=> "Body Background color",
					"desc" 	=> "Background color for your site<br/><br/>",
					"id" 	=> "color-body_fontcolor",
					"type" 	=> "colorpicker",
					"std" 	=> "#ffffff",
					"class" => "av_2columns av_col_1",
					//"required" => array("color-body_style",'boxed'),
					"target" => array("default_slideshow_target::.text_small_outside::color"),
					);
*/


	$avia_elements[] = array(	"slug"	=> "styling", "type" => "hr", "id" => "hrx", "nodescription" => true);

	$avia_elements[] = array(
						"slug"	=> "styling",
						"id" 	=> "color-body_img",
						"name" 	=> "Background Image",
						"desc" 	=> "The background image of your Body<br/><br/>",
						"type" 	=> "select",
						"subtype" => array('No Background Image'=>'','Upload custom image'=>'custom','----------------------'=>''),
						"std" 	=> "",
						"no_first"=>true,
						"class" => "av_2columns av_col_1 set_blank_on_hide",
						"target" => array("default_slideshow_target::.live_bg_wrap::background-image"),
						"folder" => "images/background-images/",
						"required" => array("color-body_style",'boxed'),
						"folderlabel" => "");


	$avia_elements[] =	array(
						"slug"	=> "styling",
						"name" 	=> "Custom Background Image",
						"desc" 	=> "Upload a BG image for your Body<br/><br/>",
						"id" 	=> "color-body_customimage",
						"type" 	=> "upload",
						"std" 	=> "",
						"class" => "set_blank_on_hide av_2columns av_col_2",
						"label"	=> "Use Image",
						"required" => array("color-body_img",'custom'),
						"target" => array("default_slideshow_target::.live_bg_wrap::background-image"),
						);


	$avia_elements[] =	array(
						"slug"	=> "styling",
						"name" 	=> "Position of the image",
						"desc" 	=> "",
						"id" 	=> "color-body_pos",
						"type" 	=> "select",
						"std" 	=> "top left",
						"no_first"=>true,
						"class" => "av_2columns av_col_1",
						"required" => array("color-body_img",'{true}'),
						"target" => array("default_slideshow_target::.live_bg_wrap::background-position"),
						"subtype" => array('Top Left'=>'top left','Top Center'=>'top center','Top Right'=>'top right', 'Bottom Left'=>'bottom left','Bottom Center'=>'bottom center','Bottom Right'=>'bottom right', 'Center Left '=>'center left','Center Center'=>'center center','Center Right'=>'center right'));

	$avia_elements[] =	array(
						"slug"	=> "styling",
						"name" 	=> "Repeat",
						"desc" 	=> "",
						"id" 	=> "color-body_repeat",
						"type" 	=> "select",
						"std" 	=> "no-repeat",
						"class" => "av_2columns av_col_2",
						"no_first"=>true,
						"required" => array("color-body_img",'{true}'),
						"target" => array("default_slideshow_target::.live_bg_wrap::background-repeat"),
						"subtype" => array('no repeat'=>'no-repeat','Repeat'=>'repeat','Tile Horizontally'=>'repeat-x','Tile Vertically'=>'repeat-y', 'Stretch Fullscreen'=>'fullscreen'));

	$avia_elements[] =	array(
						"slug"	=> "styling",
						"name" 	=> "Attachment",
						"desc" 	=> "",
						"id" 	=> "color-body_attach",
						"type" 	=> "select",
						"std" 	=> "scroll",
						"class" => "av_2columns av_col_1",
						"no_first"=>true,
						"required" => array("color-body_img",'{true}'),
						"target" => array("default_slideshow_target::.live_bg_wrap::background-attachment"),
						"subtype" => array('Scroll'=>'scroll','Fixed'=>'fixed'));


$avia_elements[] = array(	"slug"	=> "styling", "type" => "visual_group_end", "id" => "avia_tab5_end", "nodescription" => true);


//create color sets for #header, Main Content, Secondary Content, Footer, Socket, Slideshow

$colorsets = $avia_config['color_sets'];
$iterator = 1;

foreach($colorsets as $set_key => $set_value)
{
	$iterator ++;

	$avia_elements[] = array(	"slug"	=> "styling", "type" => "visual_group_start", "id" => "avia_tab".$iterator, "nodescription" => true, 'class'=>'avia_tab avia_tab'.$iterator,'name'=>$set_value);

	$avia_elements[] =	array(
					"slug"	=> "styling",
					"name" 	=> "$set_value background color",
					"id" 	=> "colorset-$set_key-bg",
					"type" 	=> "colorpicker",
					"class" => "av_2columns av_col_1",
					"std" 	=> "#ffffff",
					"desc" 	=> "Default Background color",
					"target" => array("default_slideshow_target::.live-$set_key::background-color"),
					);

	$avia_elements[] =	array(
					"slug"	=> "styling",
					"name" 	=> "Alternate Background color",
					"desc" 	=> "Alternate Background for menu hover, tables etc",
					"id" 	=> "colorset-$set_key-bg2",
					"type" 	=> "colorpicker",
					"class" => "av_2columns av_col_2",
					"std" 	=> "#f8f8f8",
					"target" => array("default_slideshow_target::.live-$set_key .bg2::background-color"),
					);

	$avia_elements[] =	array(
					"slug"	=> "styling",
					"name" 	=> "Primary color",
					"desc" 	=> "Font color for links, dropcaps and other elements",
					"id" 	=> "colorset-$set_key-primary",
					"type" 	=> "colorpicker",
					"class" => "av_2columns av_col_1",
					"std" 	=> "#719430",
					"target" => array("default_slideshow_target::.live-$set_key .a_link, .live-$set_key-wrap-top::color,border-color"),
					);


	$avia_elements[] =	array(
					"slug"	=> "styling",
					"name" 	=> "Highlight color",
					"desc" 	=> "Secondary color for link and button hover, etc<br/>",
					"id" 	=> "colorset-$set_key-secondary",
					"type" 	=> "colorpicker",
					"class" => "av_2columns av_col_2",
					"std" 	=> "#8bba34",
					"target" => "default_slideshow_target::.live-$set_key .an_activelink::color",
					);


	$avia_elements[] =	array(
						"slug"	=> "styling",
						"name" 	=> "$set_value font color",
						"id" 	=> "colorset-$set_key-color",
						"type" 	=> "colorpicker",
						"class" => "av_2columns av_col_1",
						"std" 	=> "#666666",
						"target" => array("default_slideshow_target::.live-$set_key::color"),
						);

	$avia_elements[] =	array(
					"slug"	=> "styling",
					"name" 	=> "Border colors ",
					"id" 	=> "colorset-$set_key-border",
					"type" 	=> "colorpicker",
					"class" => "av_2columns av_col_2",
					"std" 	=> "#e1e1e1",
					"target" => array("default_slideshow_target::.live-$set_key.border, .live-$set_key .bg2::border-color"),
					);






	$avia_elements[] = array(	"slug"	=> "styling", "type" => "hr", "id" => "hr".$set_key, "nodescription" => true);

	$avia_elements[] = array(
						"slug"	=> "styling",
						"id" 	=> "colorset-$set_key-img",
						"name" 	=> "Background Image",
						"desc" 	=> "The background image of your $set_value<br/><br/>",
						"type" 	=> "select",
						"subtype" => array('No Background Image'=>'','Upload custom image'=>'custom','----------------------'=>''),
						"std" 	=> "",
						"no_first"=>true,
						"class" => "av_2columns av_col_1",
						"target" => array("default_slideshow_target::.live-$set_key::background-image"),
						"folder" => "images/background-images/",
						"folderlabel" => "");


	$avia_elements[] =	array(
						"slug"	=> "styling",
						"name" 	=> "Custom Background Image",
						"desc" 	=> "Upload a BG image for your $set_value<br/><br/>",
						"id" 	=> "colorset-$set_key-customimage",
						"type" 	=> "upload",
						"std" 	=> "",
						"class" => "set_blank_on_hide av_2columns av_col_2",
						"label"	=> "Use Image",
						"required" => array("colorset-$set_key-img",'custom'),
						"target" => array("default_slideshow_target::.live-$set_key::background-image"),
						);


	$avia_elements[] =	array(
						"slug"	=> "styling",
						"name" 	=> "Position of the image",
						"desc" 	=> "",
						"id" 	=> "colorset-$set_key-pos",
						"type" 	=> "select",
						"std" 	=> "top left",
						"no_first"=>true,
						"class" => "av_2columns av_col_1",
						"required" => array("colorset-$set_key-img",'{true}'),
						"target" => array("default_slideshow_target::.live-$set_key::background-position"),
						"subtype" => array('Top Left'=>'top left','Top Center'=>'top center','Top Right'=>'top right', 'Bottom Left'=>'bottom left','Bottom Center'=>'bottom center','Bottom Right'=>'bottom right', 'Center Left '=>'center left','Center Center'=>'center center','Center Right'=>'center right'));

	$avia_elements[] =	array(
						"slug"	=> "styling",
						"name" 	=> "Repeat",
						"desc" 	=> "",
						"id" 	=> "colorset-$set_key-repeat",
						"type" 	=> "select",
						"std" 	=> "no-repeat",
						"class" => "av_2columns av_col_2",
						"no_first"=>true,
						"required" => array("colorset-$set_key-img",'{true}'),
						"target" => array("default_slideshow_target::.live-$set_key::background-repeat"),
						"subtype" => array('no repeat'=>'no-repeat','Repeat'=>'repeat','Tile Horizontally'=>'repeat-x','Tile Vertically'=>'repeat-y'));

	$avia_elements[] =	array(
						"slug"	=> "styling",
						"name" 	=> "Attachment",
						"desc" 	=> "",
						"id" 	=> "colorset-$set_key-attach",
						"type" 	=> "select",
						"std" 	=> "scroll",
						"class" => "av_2columns av_col_1",
						"no_first"=>true,
						"required" => array("colorset-$set_key-img",'{true}'),
						"target" => array("default_slideshow_target::.live-$set_key::background-attachment"),
						"subtype" => array('Scroll'=>'scroll','Fixed'=>'fixed'));







	$avia_elements[] = array(	"slug"	=> "styling", "type" => "visual_group_end", "id" => "avia_tab_end".$iterator, "nodescription" => true);
}








$avia_elements[] = array(	"slug"	=> "styling", "type" => "visual_group_end", "id" => "avia_tab_container_end", "nodescription" => true);


$avia_elements[] =	array(
					"slug"	=> "styling",
					"name" 	=> "Quick CSS",
					"desc" 	=> "Just want to do some quick CSS changes? Enter them here, they will be applied to the theme. If you need to change major portions of the theme please use the custom.css file.",
					"id" 	=> "quick_css",
					"type" 	=> "textarea"
					);


/*General Settings*/


$avia_elements[] =	array(
					"slug"	=> "layout",
					"name" 	=> "Responsive Layout",
					"desc" 	=> "By default the theme adapts to the screen size of the visitor and uses a layout best suited. You can disable this behavior so the theme will only show the default layout without adaptation. <br/><br/>If you choose to display responsive layout that adapts to the screen size, you can choose between 2 maximum width settings",
					"id" 	=> "responsive_layout",
					"type" 	=> "select",
					"std" 	=> "responsive",
					"no_first"=>true,
					"subtype" => array( 'Responsive Layout Default (Max width: 1030px)' =>'responsive',
										'Responsive Layout Large (Max width: 1210px)' =>'responsive responsive_large',
										'Fixed layout'=>'static_layout',
										));


$avia_elements[] =	array(
					"slug"	=> "layout",
					"name" 	=> "Websafe fonts fallback for Windows",
					"desc" 	=> "Older Browsers on Windows dont render custom fonts as smooth as modern ones. If you want to force websafe fonts instead of custom fonts for those browsers activate the setting here (affects older versions of IE, Firefox and Opera)",
					"id" 	=> "websave_windows",
					"type" 	=> "select",
					"std" 	=> "",
					"no_first"=>true,
					"subtype" => array( 'Not activated' =>'',
										'Activated'=>'active',
										));


$avia_elements[] =	array(
					"slug"	=> "sidebars",
					"name" 	=> "Sidebar on Archive Pages",
					"desc" 	=> "Choose the archive sidebar position here. This setting will be applied to all archive pages",
					"id" 	=> "archive_layout",
					"type" 	=> "select",
					"std" 	=> "sidebar_right",
					"no_first"=>true,
					"subtype" => array( 'left sidebar' =>'sidebar_left',
										'right sidebar'=>'sidebar_right',
										'no sidebar'=>'fullsize'
										));




$avia_elements[] =	array(
					"slug"	=> "sidebars",
					"name" 	=> "Sidebar on Blog Page",
					"desc" 	=> "Choose the blog sidebar position here. This setting will be applied to the blog page",
					"id" 	=> "blog_layout",
					"type" 	=> "select",
					"std" 	=> "sidebar_right",
					"no_first"=>true,
					"subtype" => array( 'left sidebar' =>'sidebar_left',
										'right sidebar'=>'sidebar_right',
										'no sidebar'=>'fullsize'
										));




$avia_elements[] =	array(
					"slug"	=> "sidebars",
					"name" 	=> "Sidebar on Single Post Pages",
					"desc" 	=> "Choose the blog post sidebar position here. This setting will be applied to single blog posts",
					"id" 	=> "single_layout",
					"type" 	=> "select",
					"std" 	=> "sidebar_right",
					"no_first"=>true,
					"subtype" => array( 'left sidebar' =>'sidebar_left',
										'right sidebar'=>'sidebar_right',
										'no sidebar'=>'fullsize'
										));




$avia_elements[] =	array(
					"slug"	=> "layout",
					"name" 	=> "Blog Style",
					"desc" 	=> "Choose the default blog layout here.",
					"id" 	=> "blog_style",
					"type" 	=> "select",
					"std" 	=> "single-small",
					"no_first"=>true,
					"subtype" => array( 'Multi Author Blog (displays Gravatar of the article author beside the entry and feature images above)' =>'multi-big',
										'Single Author, small preview Pic (no author picture is displayed, feature image is small)' =>'single-small',
										'Single Author, big preview Pic (no author picture is displayed, feature image is big)' =>'single-big',
										'Grid Layout' =>'blog-grid',
										));



$avia_elements[] =	array(
    "slug"	=> "layout",
    "name" 	=> "Single Post Style",
    "desc" 	=> "Choose the single post style here.",
    "id" 	=> "single_post_style",
    "type" 	=> "select",
    "std" 	=> "single-big",
    "no_first"=>true,
    "subtype" => array( 'Single post with small preview image (featured image)' =>'single-small',
        'Single post with big preview image (featured image)' =>'single-big',
        'Multi Author Blog (displays Gravatar of the article author beside the entry and feature images above)' =>'multi-big'
    ));



$avia_elements[] =	array(
					"slug"	=> "sidebars",
					"name" 	=> "Sidebar on Pages",
					"desc" 	=> "Choose the default page layout here. You can change the setting of each individual page when editing that page",
					"id" 	=> "page_layout",
					"type" 	=> "select",
					"std" 	=> "sidebar_right",
					"no_first"=>true,
					"subtype" => array( 'left sidebar' =>'sidebar_left',
										'right sidebar'=>'sidebar_right',
										'no sidebar'=>'fullsize'
										));


$avia_elements[] =	array(
					"slug"	=> "sidebars",
					"name" 	=> "Sidebar on Smartphones",
					"desc" 	=> "Show sidebar on smartphones (Sidebar is displayed then bellow the actual content)",
					"id" 	=> "smartphones_sidebar",
					"type" 	=> "checkbox",
					"std" 	=> "",
					"no_first"=>true,
					"subtype" => array( 'Hide sidebar on smartphones' 	=>'',
										'Show sidebar on smartphones'	=>'smartphones_sidebar_visible'
										));


$avia_elements[] =	array(
					"slug"	=> "sidebars",
					"name" 	=> "Page Sidebar navigation",
					"desc" 	=> "Display a sidebar navigation for all nested subpages of a page automatically? ",
					"id" 	=> "page_nesting_nav",
					"type" 	=> "checkbox",
					"std" 	=> "true",
					"no_first"=>true,
					"subtype" => array( 'Display sidebar navigation'=>'true',
										'Don\'t display Sidebar navigation' => ""
										));


$avia_elements[] =	array(	"name" => "Create new Sidebar Widget Areas",
							"desc" => "The theme supports the creation of custom widget areas. Simply open your <a target='_blank' href='".admin_url('widgets.php')."'>Widgets Page</a> and add a new Sidebar Area. Afterwards you can choose to display this Widget Area in the Edit Page Screen.",
							"id" => "widgetdescription",
							"std" => "",
							"slug"	=> "sidebars",
							"type" => "heading",
							"nodescription"=>true);

/*Header*/

/* PREPARING BETTER HEADER OPTIONS 


$avia_elements[] = array(
					"slug"	=> "header",
					"name" 	=> "Header Search Form",
					"desc" 	=> "Should the form let users search the entire site (default WordPress Search) or just for Products (Product Search)",
					"id" 	=> "header_search",
					"type" 	=> "select",
					"std" 	=> "default",
					"no_first"=>true,
					"subtype" => array('Default WordPress Search'=>'default','Product Search'=>'product'));
*/


/*

$avia_elements[] = array(	"name" => "Header Options",
								"desc" => "Set the layout options for your header bellow:",
								"std" => "",
								"slug"	=> "header",
								"type" => "heading",
								"nodescription"=>true);
								
$avia_elements[] = array(	"slug"	=> "header", "type" => "visual_group_start", "id" => "avia_socialheader-options", "nodescription" => true);
$avia_elements[] =	array(
					"slug"	=> "header",
					"name" 	=> "Menu and Logo Position",
					"desc" 	=> "You can choose various different logo and main menu positions here",
					"id" 	=> "header_layout",
					"type" 	=> "select",
					"std" 	=> "",
					"no_first"=>true,
					"subtype" => array( 'Logo left, Menu right'  	=>'logo_left menu_right',
										'Logo right, Menu Left'	 	=>'logo_right menu_left',
										'Logo left, Menu bellow' 	=>'logo_left bottom_nav_header',
										'Logo right, Menu bellow' 	=>'logo_right bottom_nav_header',
										'Logo center, Menu bellow' 	=>'logo_center bottom_nav_header',
										));
$avia_elements[] = array(
							"name" 	=> "Sticky Header",
							"desc" 	=> "If checked the header will stick to the top of your site if user scrolls down (ignored on mobile devices)",
							"id" 	=> "header_sticky",
							"type" 	=> "checkbox",
							"std"	=> "true",
							"slug"	=> "header");								

$avia_elements[] = array(
							"name" 	=> "Shrinking Header",
							"desc" 	=> "If checked the sticky header will shrink once the user scrolls down",
							"id" 	=> "header_sticky",
							"type" 	=> "checkbox",
							"std"	=> "true",
							"required" => array('header_sticky','{true}'),
							"slug"	=> "header");

$avia_elements[] = array(
							"name" 	=> "Search Icon",
							"desc" 	=> "If checked the header main menu will get a search Icon appended, which will open a search window when clicked",
							"id" 	=> "header_search",
							"type" 	=> "checkbox",
							"std"	=> "true",
							"slug"	=> "header");	

$avia_elements[] = array(
							"name" 	=> "Header Secondary Area",
							"desc" 	=> "A secondary area at the top of the header that can hold a small sub menu",
							"id" 	=> "header_social", //social_header
							"type" 	=> "checkbox",
							"std"	=> "",
							"slug"	=> "header");	
								
								
$avia_elements[] = array(
							"name" 	=> "Social media Icons in header",
							"desc" 	=> "If checked the header will show social media icons",
							"id" 	=> "header_social",
							"type" 	=> "checkbox",
							"std"	=> "",
							"slug"	=> "header");


$avia_elements[] =	array(
					"slug"	=> "header",
					"name" 	=> "Header Size",
					"desc" 	=> "You can choose various different header sizes here",
					"id" 	=> "header_size",
					"type" 	=> "select",
					"std" 	=> "",
					"no_first"=>true,
					"subtype" => array( 'slim'  	=>'slim',
										'large'	 	=>'large',
										'extra large' 	=>'extra_large',
										));

																
$avia_elements[] = array(	"slug"	=> "header", "type" => "visual_group_end", "id" => "avia_headeroptions_end", "nodescription" => true);

*/





$avia_elements[] =	array(
					"slug"	=> "header",
					"name" 	=> "Header Type",
					"desc" 	=> "You can choose various different headers stylings here",
					"id" 	=> "header_setting",
					"type" 	=> "select",
					"std" 	=> "fixed_header",
					"no_first"=>true,
					"subtype" => array( 'Small fixed Header' =>'fixed_header',
										'Small non-fixed Header'=>'nonfixed_header',
										'Fixed Header with Social Icons and additional Navigation'=>'fixed_header social_header',
										'Non-fixed Header with Social Icons and additional Navigation'=>'nonfixed_header social_header',
										'Header with Social Icons and bottom Navigation'=>'nonfixed_header social_header bottom_nav_header',
										));

$avia_elements[] = array(	"slug"	=> "header", "type" => "visual_group_start", "id" => "avia_socialheader-start", "nodescription" => true, "required" => array('header_setting','{contains}social'), "class"=>'group_inside' );



$avia_elements[] = array(
						"name" 	=> "Phone Number or small info text",
						"desc" 	=> "Here you can place your phone number or a small info text. It will be displayed at the top right of your page.",
						"id" 	=> "phone",
						"type" 	=> "text",
						"std"	=> "",
						"slug"	=> "header");


$avia_elements[] =	array(
					"type" 			=> "group",
					"id" 			=> "social_icons",
					"slug"			=> "header",
					"linktext" 		=> "Add another social icon",
					"deletetext" 	=> "Remove icon",
					"blank" 		=> true,
					"nodescription" => true,
					"std"			=> array(
										array('social_icon'=>'twitter', 'social_icon_link'=>'http://twitter.com/kriesi'),
										array('social_icon'=>'dribbble', 'social_icon_link'=>'http://dribbble.com/kriesi'),
										array('social_icon'=>'rss', 'social_icon_link'=>''),
										),
					'subelements' 	=> array(

							array(
								"name" 	=> "Social Icon",
								"desc" 	=> "",
								"id" 	=> "social_icon",
								"type" 	=> "select",
								"slug"	=> "sidebar",
								"class" => "av_2columns av_col_1",
								"subtype" => apply_filters('avf_social_icons_options', array(

									'500px' 	=> 'five_100_px',
									'Behance' 	=> 'behance',
									'Dribbble' 	=> 'dribbble',
									'Facebook' 	=> 'facebook',
									'Flickr' 	=> 'flickr',
									'Google Plus' => 'gplus',
									'Instagram'  => 'instagram',
									'LinkedIn' 	=> 'linkedin',
									'Pinterest' 	=> 'pinterest',
									'Skype' 	=> 'skype',
									'Soundcloud'=> 'soundcloud',
									'Tumblr' 	=> 'tumblr',
									'Twitter' 	=> 'twitter',
									'Vimeo' 	=> 'vimeo',
									'Xing' 		=> 'xing',
									'Youtube'   => 'youtube',
									'Special: RSS (add RSS URL, leave blank if you want to use default WordPress RSS feed)' => 'rss',
									'Special: Email Icon (add URL to a contact form)' => 'mail',

								))),

							array(
								"name" 	=> "Social Icon URL:",
								"desc" 	=> "",
								"id" 	=> "social_icon_link",
								"type" 	=> "text",
								"slug"	=> "sidebar",
								"class" => "av_2columns av_col_2"),
						        )
						);





$avia_elements[] = array(	"slug"	=> "header", "type" => "visual_group_end", "id" => "avia_socialheader_end", "nodescription" => true);


$avia_elements[] = array(
							"name" 	=> "Append search icon to main menu",
							"desc" 	=> "If enabled a search Icon will be appended to the main menu that allows the users to perform an 'AJAX' Search ",
							"id" 	=> "header_searchicon",
							"type" 	=> "checkbox",
							"std"	=> "true",
							"slug"	=> "header");	


$avia_elements[] =	array(
					"slug"	=> "header",
					"name" 	=> "Responsive Header Main Menu",
					"desc" 	=> "You can choose which kind of main menu you would like to display on small devices like tablets or smartphones",
					"id" 	=> "header_menu",
					"type" 	=> "select",
					"std" 	=> "drop_down",
					"no_first"=>true,
					"subtype" => array( 'Display as Dropdown Menu' =>'mobile_drop_down',
										'Display as Slide Out Menu'=>'mobile_slide_out',
										));
										
										


/*
$avia_elements[] =	array(
					"slug"	=> "layout",
					"name" 	=> "Portfolio: Enter a page slug that should be used for your portfolio single items",
					"desc" 	=> "For example if you enter 'portfolio-item' the link to the item will be <strong>".home_url().'/portfolio-item/post-name</strong><br/><br/>Dont use characters that are not allowed in urls and make sure that this slug is not used anywere else on your site (for example as a category or a page)',
					"id" 	=> "portfolio-slug",
					"std" 	=> "portfolio-item",
					"type" 	=> "text");
*/					
					
$avia_elements[] =	array(
					"slug"		=> "layout",
					"name" 		=> "Iconfont Manager",
					"desc" 		=> "You can upload additional Iconfont Packages generated with <a href='http://fontello.com/' target='_blank'>Fontello</a> to use them in your Layout Builder.<br/><br/>The 'Default Font' can't be deleted. <br/><br/>Make sure to delete any fonts that you are not using, to keep the loading time for your visitors low",
					"id" 		=> "iconfont_upload",
					"title" 	=> "Upload/Select Fontello Font Zip",
					"button" 	=> "Insert Zip File",
					"trigger" 	=> "av_fontello_zip_insert",
					// "fopen_check" 	=> "true",
					"std"	  	=> "",
					"type" 		=> "file_upload");					
		
					
					


/*footer settings*/


$avia_elements[] =	array(
					"slug"	=> "footer",
					"name" 	=> "Default Footer Widgets & Socket Settings",
					"desc" 	=> "Do you want to display the footer widgets & footer socket?",
					"id" 	=> "display_widgets_socket",
					"type" 	=> "select",
					"std" 	=> "all",
					"no_first" => true,
					"subtype" => array(
				                    'Display the footer widgets & socket'=>'all',
				                    'Display only the footer widgets (no socket)'=>'nosocket',
				                    'Display only the socket (no footer widgets)'=>'nofooterwidgets',
				                    'Don\'t display the socket & footer widgets'=>'nofooterarea'
									)
					);




$avia_elements[] =	array(
					"slug"	=> "footer",
					"name" 	=> "Footer Columns",
					"desc" 	=> "How many colmns should be diplayed in your footer",
					"id" 	=> "footer_columns",
					"type" 	=> "select",
					"std" 	=> "4",
					"subtype" => array('1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5'));

$avia_elements[] =	array(
					"slug"	=> "footer",
					"name" 	=> "Copyright",
					"desc" 	=> "Add a custom copyright text at the bottom of your site. eg: <br/><strong>&copy; ".__('Copyright','avia_framework')."  - ".get_bloginfo('name')."</strong>",
					"id" 	=> "copyright",
					"type" 	=> "text",
					"std" 	=> ""

					);
