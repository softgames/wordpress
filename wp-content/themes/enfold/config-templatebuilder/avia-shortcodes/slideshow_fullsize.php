<?php
/**
 * Slider
 * Shortcode that allows to display a simple slideshow
 */

if ( !class_exists( 'avia_sc_slider_full' ) ) 
{
	class avia_sc_slider_full extends aviaShortcodeTemplate
	{
			static $slide_count = 0;
	
			/**
			 * Create the config array for the shortcode button
			 */
			function shortcode_insert_button()
			{
				$this->config['name']			= __('Fullwidth Easy Slider', 'avia_framework' );
				$this->config['tab']			= __('Media Elements', 'avia_framework' );
				$this->config['icon']			= AviaBuilder::$path['imagesURL']."sc-slideshow-full.png";
				$this->config['order']			= 80;
				$this->config['target']			= 'avia-target-insert';
				$this->config['shortcode'] 		= 'av_slideshow_full';
				$this->config['shortcode_nested'] = array('av_slide_full');
				$this->config['tooltip'] 	    = __('Display a simple fullwidth slideshow element', 'avia_framework' );
				$this->config['tinyMCE'] 		= array('disable' => "true");
				$this->config['drag-level'] 	= 1;
			}

			/**
			 * Popup Elements
			 *
			 * If this function is defined in a child class the element automatically gets an edit button, that, when pressed
			 * opens a modal window that allows to edit the element properties
			 *
			 * @return void
			 */
			function popup_elements()
			{
				$this->elements = array(
			
					array(	
							"type" 			=> "modal_group", 
							"id" 			=> "content",
							'container_class' =>"avia-element-fullwidth avia-multi-img",
							"modal_title" 	=> __("Edit Form Element", 'avia_framework' ),
							"std"			=> array(),
							
							'creator'		=>array(
								
										"name" => __("Add Images", 'avia_framework' ),
										"desc" => __("Here you can add new Images to the slideshow.", 'avia_framework' ),
										"id" 	=> "id",
										"type" 	=> "multi_image",
										"title" => __("Add multiple Images",'avia_framework' ),
										"button" => __("Insert Images",'avia_framework' ),
										"std" 	=> ""
										),
															
							'subelements' 	=> array(
								
									array(	
									"name" 	=> __("Choose another Image",'avia_framework' ),
									"desc" 	=> __("Either upload a new, or choose an existing image from your media library",'avia_framework' ),
									"id" 	=> "id",
									"fetch" => "id",
									"type" 	=> "image",
									"title" => __("Change Image",'avia_framework' ),
									"button" => __("Change Image",'avia_framework' ),
									"std" 	=> ""),
									
									array(	
									"name" 	=> __("Caption Title", 'avia_framework' ),
									"desc" 	=> __("Enter a caption title for the slide here", 'avia_framework' ) ,
									"id" 	=> "title",
									"std" 	=> "",
									"type" 	=> "input"),
									
									 array(	
									"name" 	=> __("Caption Text", 'avia_framework' ),
									"desc" 	=> __("Enter some additional caption text", 'avia_framework' ) ,
									"id" 	=> "content",
									"type" 	=> "textarea",
									"std" 	=> "",
									),
									
									array(	
									"name" 	=> __("Caption Positioning",'avia_framework' ),
									"id" 	=> "caption_pos",
									"type" 	=> "select",
									"std" 	=> "caption_bottom",
									"subtype" => array(
												'Right Framed'=>'caption_right caption_right_framed caption_framed',
												'Left Framed'=>'caption_left caption_left_framed caption_framed', 
												'Bottom Framed'=>'caption_bottom caption_bottom_framed caption_framed',
												'Center Framed'=>'caption_center caption_center_framed caption_framed',
												'Right without Frame'=>'caption_right',
												'Left without Frame'=>'caption_left',
												'Bottom without Frame'=>'caption_bottom',
												'Center without Frame'=>'caption_center'
											),
									),
									
									array(	
									"name" 	=> __("Slide Link?", 'avia_framework' ),
									"desc" 	=> __("Where should the Slide link to?", 'avia_framework' ),
									"id" 	=> "link",
									"type" 	=> "linkpicker",
									"fetchTMPL"	=> true,
									"subtype" => array(	
														__('No Link', 'avia_framework' ) =>'',
														__('Lightbox', 'avia_framework' ) =>'lightbox',
														__('Set Manually', 'avia_framework' ) =>'manually',
														__('Single Entry', 'avia_framework' ) => 'single',
														__('Taxonomy Overview Page',  'avia_framework' ) => 'taxonomy',
														),
									"std" 	=> ""),
							
									array(	
									"name" 	=> __("Open Link in new Window?", 'avia_framework' ),
									"desc" 	=> __("Select here if you want to open the linked page in a new window", 'avia_framework' ),
									"id" 	=> "link_target",
									"type" 	=> "select",
									"std" 	=> "",
									"required"=> array('link','not_empty_and','lightbox'),
									"subtype" => AviaHtmlHelper::linking_options()),   
										
						)	           
					),
							
					array(	
							"name" 	=> __("Slideshow Image Size", 'avia_framework' ),
							"desc" 	=> __("Choose image size for your slideshow.", 'avia_framework' ),
							"id" 	=> "size",
							"type" 	=> "select",
							"std" 	=> "featured",
							"subtype" =>  AviaHelper::get_registered_image_sizes(1000)		
							),
					
					array(	
						"name" 	=> __("Stretch image to fit the slideshow size?",'avia_framework' ),
						"desc" 	=> __("By default the image stretches across the full width of the screen. You can deactivate this behavior and simply align it in the center of the slider",'avia_framework' ),
						"id" 	=> "stretch",
						"type" 	=> "select",
						"std" 	=> "",
						"subtype" => array(__('Yes, stretch the image','avia_framework' ) =>'',__('No, dont stretch the image. If the browser window is bigger than the image simply align it centered','avia_framework' ) =>'image_no_stretch')),	
								
					array(	
							"name" 	=> __("Slideshow Transition", 'avia_framework' ),
							"desc" 	=> __("Choose the transition for your Slideshow.", 'avia_framework' ),
							"id" 	=> "animation",
							"type" 	=> "select",
							"std" 	=> "slide",
							"subtype" => array(__('Slide','avia_framework' ) =>'slide',__('Fade','avia_framework' ) =>'fade'),	
							),
							
					array(	
						"name" 	=> __("Autorotation active?",'avia_framework' ),
						"desc" 	=> __("Check if the slideshow should rotate by default",'avia_framework' ),
						"id" 	=> "autoplay",
						"type" 	=> "select",
						"std" 	=> "false",
						"subtype" => array(__('Yes','avia_framework' ) =>'true',__('No','avia_framework' ) =>'false')),	
			
					array(	
						"name" 	=> __("Slideshow autorotation duration",'avia_framework' ),
						"desc" 	=> __("Images will be shown the selected amount of seconds.",'avia_framework' ),
						"id" 	=> "interval",
						"type" 	=> "select",
						"std" 	=> "5",
						"subtype" => 
						array('3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10','15'=>'15','20'=>'20','30'=>'30','40'=>'40','60'=>'60','100'=>'100')),

				     
					array(	
							"name" 	=> __("Slideshow Background Image",'avia_framework' ),
							"desc" 	=> __("If you are displaying transparent images like pngs you can set a static background image or pattern that will appear behind those pngs.",'avia_framework' ),
							"id" 	=> "src",
							"type" 	=> "image",
							"title" => __("Insert Image",'avia_framework' ),
							"button" => __("Insert",'avia_framework' ),
							"std" 	=> ""),
							
                    array(	
						"name" 	=> __("Background Image Position",'avia_framework' ),
						"id" 	=> "position",
						"type" 	=> "select",
						"std" 	=> "top left",
                        "required" => array('src','not',''),
						"subtype" => array(   __('Top Left','avia_framework' )       =>'top left',
						                      __('Top Center','avia_framework' )     =>'top center',
						                      __('Top Right','avia_framework' )      =>'top right', 
						                      __('Bottom Left','avia_framework' )    =>'bottom left',
						                      __('Bottom Center','avia_framework' )  =>'bottom center',
						                      __('Bottom Right','avia_framework' )   =>'bottom right', 
						                      __('Center Left','avia_framework' )    =>'center left',
						                      __('Center Center','avia_framework' )  =>'center center',
						                      __('Center Right','avia_framework' )   =>'center right'
						                      )
				    ),
						
	               array(	
						"name" 	=> __("Background Repeat",'avia_framework' ),
						"id" 	=> "repeat",
						"type" 	=> "select",
						"std" 	=> "no-repeat",
                        "required" => array('src','not',''),
						"subtype" => array(   __('No Repeat','avia_framework' )          =>'no-repeat',
						                      __('Repeat','avia_framework' )             =>'repeat',
						                      __('Tile Horizontally','avia_framework' )  =>'repeat-x',
						                      __('Tile Vertically','avia_framework' )    =>'repeat-y',
						                      __('Stretch to fit','avia_framework' )     =>'stretch'
						                      )
				  ),
						
	               array(	
						"name" 	=> __("Background Attachment",'avia_framework' ),
						"id" 	=> "attach",
						"type" 	=> "select",
						"std" 	=> "scroll",
                        "required" => array('src','not',''),
						"subtype" => array(__('Scroll','avia_framework' )=>'scroll',__('Fixed','avia_framework' ) =>'fixed')
						),
				);

			}
			
			/**
			 * Editor Element - this function defines the visual appearance of an element on the AviaBuilder Canvas
			 * Most common usage is to define some markup in the $params['innerHtml'] which is then inserted into the drag and drop container
			 * Less often used: $params['data'] to add data attributes, $params['class'] to modify the className
			 *
			 *
			 * @param array $params this array holds the default values for $content and $args. 
			 * @return $params the return array usually holds an innerHtml key that holds item specific markup.
			 */
			function editor_element($params)
			{	
				$params['innerHtml'] = "<img src='".$this->config['icon']."' title='".$this->config['name']."' />";
				$params['innerHtml'].= "<div class='avia-element-label'>".$this->config['name']."</div>";
				return $params;
			}
			
			/**
			 * Editor Sub Element - this function defines the visual appearance of an element that is displayed within a modal window and on click opens its own modal window
			 * Works in the same way as Editor Element
			 * @param array $params this array holds the default values for $content and $args. 
			 * @return $params the return array usually holds an innerHtml key that holds item specific markup.
			 */
			function editor_sub_element($params)
			{	
				$img_template 		= $this->update_template("img_fakeArg", "{{img_fakeArg}}");
				$template 			= $this->update_template("title", "{{title}}");
				$content 			= $this->update_template("content", "{{content}}");
				
				$thumbnail = isset($params['args']['id']) ? wp_get_attachment_image($params['args']['id']) : "";
				
		
				$params['innerHtml']  = "";
				$params['innerHtml'] .= "<div class='avia_title_container'>";
				$params['innerHtml'] .= "	<span class='avia_slideshow_image' {$img_template} >{$thumbnail}</span>";
				$params['innerHtml'] .= "	<div class='avia_slideshow_content'>";
				$params['innerHtml'] .= "		<h4 class='avia_title_container_inner' {$template} >".$params['args']['title']."</h4>";
				$params['innerHtml'] .= "		<p class='avia_content_container' {$content}>".stripslashes($params['content'])."</p>";
				$params['innerHtml'] .= "	</div>";
				$params['innerHtml'] .= "</div>";
				
				
				
				return $params;
			}
			
			
			
			/**
			 * Frontend Shortcode Handler
			 *
			 * @param array $atts array of attributes
			 * @param string $content text within enclosing form of shortcode element 
			 * @param string $shortcodename the shortcode found, when == callback name
			 * @return string $output returns the modified html string 
			 */
			function shortcode_handler($atts, $content = "", $shortcodename = "", $meta = "")
			{
				$atts = shortcode_atts(array(
				'size'			=> 'featured',
				'animation'		=> 'slide',
				'ids'    	 	=> '',
				'autoplay'		=> 'false',
				'interval'		=> 5,
				'handle'		=> $shortcodename,
				'src' 			=> '', 
				'position'	 	=> 'top left', 
				'repeat' 		=> 'no-repeat', 
				'attach' 		=> 'scroll',
				//'easing'		=> 'easeInOutQuint',
				'stretch'		=> '',
				'content'		=> ShortcodeHelper::shortcode2array($content)
				
				), $atts);
				
				extract($atts);
				$output  	= "";
				$background  = "";
			    $class = "";
			    
			    if($src != "")
			    {
			         if($repeat == 'stretch')
			         {
			             $background .= "background-repeat: no-repeat; ";
			             $class .= " avia-full-stretch";
			         }
			         else
			         {
			             $background .= "background-repeat: {$repeat}; ";
			         }
			    
			         $background .= "background-image: url({$src}); ";
			         $background .= "background-attachment: {$attach}; ";
			         $background .= "background-position: {$position}; ";
			    }
			    
			    if($background) $params['bg'] = "style = '{$background}'";
			    
				$skipSecond = false;
				avia_sc_slider_full::$slide_count++;
				
				$params['class'] = "avia-fullwidth-slider main_color avia-shadow ".$meta['el_class'].$class;
				$params['open_structure'] = false;
				if($meta['index'] == 0) $params['close'] = false;
				if($meta['index'] != 0) $params['class'] .= " slider-not-first";
				if($meta['index'] == 0 && get_post_meta(get_the_ID(), 'header', true) != "no") $params['class'] .= " slider-not-first";
				
				$params['id'] = "full_slider_".avia_sc_slider_full::$slide_count;
				
				$output .=  avia_new_section($params);
				
				$slider  = new avia_slideshow($atts);
				$slider->set_extra_class($stretch);
				$output .= $slider->html();
				
				$output .= "</div>"; //close section
				
				
				//if the next tag is a section dont create a new section from this shortcode
				if(!empty($meta['siblings']['next']['tag']) && in_array($meta['siblings']['next']['tag'],  AviaBuilder::$full_el ))
				{
				    $skipSecond = true;
				}

				//if there is no next element dont create a new section.
				if(empty($meta['siblings']['next']['tag']))
				{
				    $skipSecond = true;
				}
				
				if(empty($skipSecond)) {
				
				$output .= avia_new_section(array('close'=>false, 'id' => "after_full_slider_".avia_sc_slider_full::$slide_count));
				
				}
				
				return $output;

			}
			
	}
}



