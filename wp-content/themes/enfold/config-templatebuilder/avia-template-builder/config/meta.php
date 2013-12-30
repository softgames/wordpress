<?php
global $builder;

$boxes = array(
	array( 'title' =>__('Avia Layout Builder','avia_framework' ), 'id'=>'avia_builder', 'page'=>array('portfolio','page'), 'context'=>'normal', 'priority'=>'high', 'expandable'=>true ),
	array( 'title' =>__('Layout','avia_framework' ), 'id'=>'layout', 'page'=>array('portfolio', 'page' , 'post'), 'context'=>'side', 'priority'=>'low'),
	array( 'title' =>__('Additional Portfolio Settings','avia_framework' ), 'id'=>'preview', 'page'=>array('portfolio'), 'context'=>'normal', 'priority'=>'high' ),
);

$boxes = apply_filters('avf_builder_boxes', $boxes);


$elements = array(
array(
		"slug"			=> "avia_builder",
		"name" 			=> __("Visual layout editor",'avia_framework'),
		"id" 			=> "layout_editor",
		"type" 			=> array($builder,'visual_editor'),
		"tab_order"		=> array(__('Layout Elements','avia_framework' ), __('Content Elements','avia_framework' ) , __('Media Elements','avia_framework' )),
		"desc"			=>  '<h4>'.__('Quick Info & Hotkeys', 'avia_framework' )."</h4>".
							'<strong>'.__('General Info', 'avia_framework' ).'</strong>'.
							"<ul>".
							'   <li>'.__('To insert an Element either click the insert button for that element or drag the button onto the canvas', 'avia_framework' ).'</li>'.
							'   <li>'.__('If you place your mouse above the insert button a short info tooltip will appear', 'avia_framework' ).'</li>'.
							'   <li>'.__('To sort and arrange your elements just drag them to a position of your choice and release them', 'avia_framework' ).'</li>'.
							'   <li>'.__('Valid drop targets will be highlighted. Some elements like fullwidth sliders and color section can not be dropped onto other elements', 'avia_framework' ).'</li>'.
							"</ul>".
							'<strong>'.__('Edit Elements in Popup Window:', 'avia_framework' ).'</strong>'.
							"<ul>".
							'   <li>'.__('Most elements open a popup window if you click them', 'avia_framework' ).'</li>'.
							'   <li>'.__('Press TAB to navigate trough the various form fields of a popup window.', 'avia_framework' ).'</li>'.
							'   <li>'.__('Press ESC on your keyboard or the Close Button to close popup window.', 'avia_framework' ).'</li>'.
							'   <li>'.__('Press ENTER on your keyboard or the Save Button to save current state of a popup window', 'avia_framework' ).'</li>'.
							"</ul>"
	),

	array(
        "container_class" => "av_2columns av_col_1 avia-style",
        "slug"	=> "preview",
        "name" 	=> __("Overwrite Portfolio Link setting",'avia_framework'),
        "desc" 	=> __("If this entry is displayed in a portfolio grid, it will use the grids link settings (open either in lightbox, or open link url). You may overwrite this setting here",'avia_framework'),
        "id" 	=> "_portfolio_custom_link",
        "type" 	=> "select",
        "std" 	=> "",
        "subtype" => array( "Use default setting"   => '',
        					"Define custom link" => 'custom',

        )),

	array(
        "slug"	=> "preview",
		"name" 	=> __("Link portfolio item to external URL",'avia_framework' ),
		"desc" 	=> __("You can add a link to any (external) page here. <br/> If you add a link to a video that video will open in a lightbox",'avia_framework' ),
		"id" 	=> "_portfolio_custom_link_url",
		"type" 	=> "input",
        "required" 	=> array('_portfolio_custom_link','equals','custom'),
        "container_class" => "avia-style av_2columns av_col_2",
		"std" 	=> "http://"),

	array(
        "slug"	=> "preview",
		"id" 	=> "_portfolio_hr",
		"type" 	=> "hr",
		"std" 	=> ""),


	array(
        "slug"	=> "preview",
		"name" 	=> __("Ajax Portfolio Preview Settings",'avia_framework' ),
		"desc" 	=> __("If you have selected to display your portfolio grid as an 'Ajax Portfolio' please choose preview images here and write some preview text. Once the user clicks on the portfolio item a preview element with those images and info will open.",'avia_framework' ),
		"id" 	=> "_preview_heading",
		"type" 	=> "heading",
		"std" 	=> ""),


	array(
        "slug"	=> "preview",
        "container_class" => "av_2columns av_col_1",
		"name" 	=> __("Add Preview Images",'avia_framework' ),
		"desc" 	=> __("Create a new Preview Gallery or Slideshow by selecting existing or uploading new images",'avia_framework' ),
		"id" 	=> "_preview_ids",
		"type" 	=> "gallery",
		"title" => __("Add Preview Images",'avia_framework' ),
		"delete" => __("Remove Images",'avia_framework' ),
		"button" => __("Insert Images",'avia_framework' ),
		"std" 	=> ""),

		array(
        "container_class" => "av_2columns av_col_2",
        "slug"	=> "preview",
        "name" 	=> __("Display Preview Images",'avia_framework'),
        "desc" 	=> __("Display Images as either gallery, slideshow or as a list below each other",'avia_framework'),
        "id" 	=> "_preview_display",
        "type" 	=> "select",
        "std" 	=> "gallery",
        "class" => "avia-style",
        "subtype" => array( __("Gallery",'avia_framework')      => 'gallery',
        					__("Slideshow",'avia_framework')    => 'slideshow',
        					__("Image List",'avia_framework')   => 'list',
                            __("Don't show the images at all and display the preview text only",'avia_framework')  => 'no'

        ),
		),

		array(
        "container_class" => "av_2columns av_col_2",
        "slug"	=> "preview",
        "name" 	=> __("Autorotation",'avia_framework'),
        "desc" 	=> __("Slideshow autorotation Settings in Seconds",'avia_framework'),
        "id" 	=> "_preview_autorotation",
        "type" 	=> "select",
        "std" 	=> "disabled",
        "class" => "avia-style",
        "required" 	=> array('_preview_display','equals','slideshow'),
        "subtype" => array(
        					__("Disabled",'avia_framework')  => 'disabled',
        					"3"   => '3',
        					"4"   => '4',
        					"5"   => '5',
        					"6"   => '6',
        					"7"   => '7',
        					"8"   => '8',
        					"9"   => '9',
        					"10"   => '10',
        					"15"   => '15',
        					"20"   => '20',

        ),
		),

				array(
        "container_class" => "av_2columns av_col_2",
        "slug"	=> "preview",
        "name" 	=> __("Gallery Thumbnail Columns",'avia_framework'),
        "desc" 	=> __("How many Thumbnails should be displayed beside each other",'avia_framework'),
        "id" 	=> "_preview_columns",
        "type" 	=> "select",
        "std" 	=> "6",
        "class" => "avia-style",
        "required" 	=> array('_preview_display','equals','gallery'),
        "subtype" => array(
        					"2"   => '2',
        					"3"   => '3',
        					"4"   => '4',
        					"5"   => '5',
        					"6"   => '6',
        					"7"   => '7',
        					"8"   => '8',
        					"9"   => '9',
        					"10"   => '10',
        					"11"   => '11',
        					"12"   => '12',

        ),
		),



	array(
        "slug"	=> "preview",
        "container_class" => "avia_clear",
		"name" 	=> __("Add Preview Text",'avia_framework' ),
		"desc" 	=> __("The text will appear beside your gallery/slideshow",'avia_framework' ),
		"id" 	=> "_preview_text",
		"type" 	=> "tiny_mce",
		"std" 	=> ""),


	array(

        "slug"	=> "layout",
        "name" 	=> __("Layout",'avia_framework'),
        "desc" 	=> __("Select the desired Page layout",'avia_framework'),
        "id" 	=> "layout",
        "type" 	=> "select",
        "std" 	=> "",
        "class" => "avia-style",
        "subtype" => array( __("Default Layout - set in",'avia_framework')." ".THEMENAME." > " . __('Layout & Options','avia_framework') => '',
        					__("No Sidebar",'avia_framework')       => 'fullsize',
        					__("Left Sidebar",'avia_framework')     => 'sidebar_left',
        					__("Right Sidebar",'avia_framework')    => 'sidebar_right',

        ),
		),

	array(

        "slug"	=> "layout",
        "name" 	=> __("Sidebar Setting",'avia_framework'),
        "desc" 	=> __("Choose a custom sidebar for this entry",'avia_framework'),
        "id" 	=> "sidebar",
        "type" 	=> "select",
        "std" 	=> "",
        "class" => "avia-style",
        "required" => array('layout','not','fullsize'),
        "subtype" => AviaHelper::get_registered_sidebars(array('Default Sidebars' => ""), array('Displayed Everywhere'))

		),
		array(

        "slug"	=> "layout",
        "name" 	=> __("Header Settings",'avia_framework'),
        "desc" 	=> __("Display the Header with Page Title and Breadcrumb Navigation?",'avia_framework'),
        "id" 	=> "header",
        "type" 	=> "select",
        "std" 	=> "yes",
        "class" => "avia-style",
        "subtype" => array( __("Display the Header",'avia_framework')  => 'yes',
        					__("Don't display the Header",'avia_framework')   => "no",

                    )
        ),


        array(

        "slug"  => "layout",
        "name"  => __("Footer Settings",'avia_framework'),
        "desc"  => __("Display the footer widgets?",'avia_framework'),
        "id"    => "footer",
        "type"  => "select",
        "std"   => "",
        "class" => "avia-style",
        "subtype" => array(
        				__("Default Layout - set in",'avia_framework')." ".THEMENAME." > ". __('Footer','avia_framework') => '',
                        __('Display the footer widgets & socket','avia_framework')=>'all',
                        __('Display only the footer widgets (no socket)','avia_framework')=>'nosocket',
                        __('Display only the socket (no footer widgets)','avia_framework')=>'nofooterwidgets',
                        __('Don\'t display the socket & footer widgets','avia_framework')=>'nofooterarea'
                    ),

    ),




);


$elements = apply_filters('avf_builder_elements', $elements);




/*
array(

        "slug"	=> "avia_builder",
        "name" 	=> "Layout",
        "desc" 	=> "Select the desired Page layout",
        "id" 	=> "layout",
        "type" 	=> "radio",
        "class" => "image_radio image_radio_layout",
        "std" 	=> "fullwidth",
        "options" => array( 'default' 		=> "Default layout",
        					'sidebar_left' 	=> "Left Sidebar",
        					'sidebar_right' => "Right Sidebar",
        					'fullwidth' 	=> "No Sidebar"
        ),

        "images" => array(  'default' 		=> AviaBuilder::$path['imagesURL']."layout-slideshow.png",
        					'sidebar_left' 	=> AviaBuilder::$path['imagesURL']."layout-left.png",
        					'sidebar_right' => AviaBuilder::$path['imagesURL']."layout-right.png",
        					'fullwidth' 	=> AviaBuilder::$path['imagesURL']."layout-fullwidth.png",
        ),
    ),
*/