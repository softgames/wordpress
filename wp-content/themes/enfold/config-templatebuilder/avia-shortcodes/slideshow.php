<?php
/**
 * Slider
 * Shortcode that allows to display a simple slideshow
 */

if ( !class_exists( 'avia_sc_slider' ) )
{
	class avia_sc_slider extends aviaShortcodeTemplate
	{
			/**
			 * Create the config array for the shortcode button
			 */
			function shortcode_insert_button()
			{
				$this->config['name']			= __('Easy Slider', 'avia_framework' );
				$this->config['tab']			= __('Media Elements', 'avia_framework' );
				$this->config['icon']			= AviaBuilder::$path['imagesURL']."sc-slideshow.png";
				$this->config['order']			= 85;
				$this->config['target']			= 'avia-target-insert';
				$this->config['shortcode'] 		= 'av_slideshow';
				$this->config['shortcode_nested'] = array('av_slide');
				$this->config['tooltip'] 	    = __('Display a simple slideshow element', 'avia_framework' );
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
								"std" 	=> ""),

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
							"subtype" =>  AviaHelper::get_registered_image_sizes(array('thumbnail','logo','widget','slider_thumb'))
							),

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
				'content'		=> ShortcodeHelper::shortcode2array($content),
				'class'			=> $meta['el_class']
				), $atts);

				$slider = new avia_slideshow($atts);
				return $slider->html();
			}

	}
}









if ( !class_exists( 'avia_slideshow' ) )
{
	class avia_slideshow
	{
		static  $slider = 0; 				//slider count for the current page
		protected $config;	 				//base config set on initialization
		protected $slides;	 				//attachment posts for the current slider
		protected $slide_count = 0;			//number of slides
		protected $id_array = array();
		function __construct($config)
		{

			$this->config = array_merge(array(
				'size'			=> 'featured',
				'animation'		=> 'slide',
				'ids'    	 	=> '',
				'autoplay'		=> 'false',
				'bg_slider'		=> 'false',
				'slide_height'	=> '',
				'handle'		=> '',
				'interval'		=> 5,
				'class'			=> "",
				'css_id'		=> "",
				'content'		=> array()
				), $config);

			$this->config['lightbox_size'] = 'large';
			$this->config = apply_filters('avf_slideshow_config', $this->config);

			//check how large the slider is and change the classname accordingly
			global $_wp_additional_image_sizes;
			$width = 1500;

			if(isset($_wp_additional_image_sizes[$this->config['size']]['width']))
			{
				$width = $_wp_additional_image_sizes[$this->config['size']]['width'];
			}
			else if($size = get_option( $this->config['size'].'_size_w' ))
			{
				$width = $size;
			}

			if($width < 600)
			{
				$this->config['class'] .= " avia-small-width-slider";
			}

			if($width < 305)
			{
				$this->config['class'] .= " avia-super-small-width-slider";
			}

			//if we got subslides overwrite the id array
			if(!empty($config['content']))
			{
				$this->extract_subslides($config['content']);
			}

			$this->set_slides($this->config['ids']);
		}

		public function set_slides($ids)
		{
			if(empty($ids)) return;

			$this->slides = get_posts(array(
				'include' => $ids,
				'post_status' => 'inherit',
				'post_type' => 'attachment',
				'post_mime_type' => 'image',
				'order' => 'ASC',
				'orderby' => 'post__in')
				);



			//resort slides so the id of each slide matches the post id
			$new_slides = array();
			foreach($this->slides as $slide)
			{
				$new_slides[$slide->ID] = $slide;
			}

			$slideshow_data = array();
			$slideshow_data['slides'] = $new_slides;
			$slideshow_data['id_array'] = explode(',',$this->config['ids']);
			$slideshow_data['slide_count'] = count($slideshow_data['id_array']);
			
			$slideshow_data = apply_filters('avf_avia_builder_slideshow_filter', $slideshow_data);
			
			$this->slides = $slideshow_data['slides'];
			$this->id_array = $slideshow_data['id_array'];
			$this->slide_count = $slideshow_data['slide_count'];
		}

		public function set_size($size)
		{
			$this->config['size'] = $size;
		}

		public function set_extra_class($class)
		{
			$this->config['class'] .= " ".$class;
		}



		public function html()
		{
			$html = "";
			$counter = 0;
			avia_slideshow::$slider++;
			if($this->slide_count == 0) return $html;

            $markup = avia_markup_helper(array('context' => 'image','echo'=>false));

			$data = AviaHelper::create_data_string($this->config);

			$html .= "<div {$data} class='avia-slideshow avia-slideshow-".avia_slideshow::$slider." avia-slideshow-".$this->config['size']." ".$this->config['handle']." ".$this->config['class']." avia-".$this->config['animation']."-slider ' $markup>";
			$html .= "<ul class='avia-slideshow-inner'>";

			$html .= empty($this->subslides) ? $this->default_slide() : $this->advanced_slide();

			$html .= "</ul>";

			if($this->slide_count > 1)
			{
				$html .= $this->slide_navigation_arrows();
				$html .= $this->slide_navigation_dots();
			}

			$html .= "</div>";

			return $html;
		}

		//function that renders the usual slides. use when we didnt use sub-shorcodes to define the images but ids
		protected function default_slide()
		{
			$html = "";
			$counter = 0;

            $markup_url = avia_markup_helper(array('context' => 'image_url','echo'=>false));

			foreach($this->id_array as $id)
			{
				if(isset($this->slides[$id]))
				{
					$slide = $this->slides[$id];

					$counter ++;
					$img 	 = wp_get_attachment_image_src($slide->ID, $this->config['size']);
					$link	 = wp_get_attachment_image_src($slide->ID, $this->config['lightbox_size']);
					$caption = trim($slide->post_excerpt) ? '<div class="avia-caption capt-bottom capt-left"><div class="avia-inner-caption">'.wptexturize($slide->post_excerpt)."</div></div>": "";

                    $imgalt = get_post_meta($slide->ID, '_wp_attachment_image_alt', true);
                    $imgalt = !empty($imgalt) ? esc_attr($imgalt) : '';
                    $imgtitle = trim($slide->post_title) ? esc_attr($slide->post_title) : "";
                    $imgdescription = trim($slide->post_content) ? esc_attr($slide->post_content) : "";

					$tags = apply_filters('avf_slideshow_link_tags', array("a href='".$link[0]."' title='".$imgdescription."'",'a')); // can be filtered and for example be replaced by array('div','div')

					$html .= "<li class='slide-{$counter} slide-id-".$slide->ID."'>";
					$html .= "<".$tags[0]." >{$caption}<img src='".$img[0]."' title='".$imgtitle."' alt='".$imgalt."' $markup_url /></ ".$tags[1]." >";
					$html .= "</li>";
				}
				else
				{
					$this->slide_count --;
				}
			}

			return $html;
		}

		//function that renders the slides. use when we did use sub-shorcodes to define the images
		protected function advanced_slide()
		{
			$html = "";
			$counter = 0;
			$this->ie8_fallback = "";

            $markup_url = avia_markup_helper(array('context' => 'image_url','echo'=>false));

			foreach($this->id_array as $key => $id)
			{
			
				$meta = array_merge( array( 'content'		=> $this->subslides[$key]['content'],
											'title'			=>'',
											'link'			=>'',
											'link_target'	=>'',
											'position'		=>'center center',
											'caption_pos'	=>'capt-bottom capt-left',
											'link_apply'	=>'',
											'button_label'	=>'',
											'video_cover'	=>'',
											'slide_type'	=>'',
											'button_color'	=>'light'


										), $this->subslides[$key]['attr']);
				
				extract($meta);
				
				
				if(isset($this->slides[$id]) || $slide_type == 'video')
				{
					$img			= array('');
					$slide			= "";
					$bg_slider_style= "";
					$attachment_id	= isset($slide->ID) ? $slide->ID : false;
					$link			= aviaHelper::get_url($link, $attachment_id);
					$extra_class 	= "";
					$linkdescription= "";
					$linkalt 		= "";
					$this->service  = false;
					
					if($slide_type == 'video')
					{
						$video 			 = $this->set_video_slide($video); // after this call this->service will be availabel
						$extra_class 	.= " av-video-slide ".$video_cover." av-video-service-".$this->service;
					}
					else //img slide
					{
						$slide 			 = $this->slides[$id];
						$linktitle 		 = trim($slide->post_title) ? esc_attr($slide->post_title) : "";
                    	$linkdescription = (trim($slide->post_content) && empty($link)) ? "title='".esc_attr($slide->post_content)."'" : "";
                    	$linkalt 		 = get_post_meta($slide->ID, '_wp_attachment_image_alt', true);
                    	$linkalt 		 = !empty($linkalt) ? esc_attr($linkalt) : '';
						$img   			 = wp_get_attachment_image_src($slide->ID, $this->config['size']);
						$video			 = "";
					}
					
					
					$blank = (strpos($link_target, '_blank') !== false || $link_target == 'yes') ? ' target="_blank" ' : "";
					$blank .= strpos($link_target, 'nofollow') !== false ? ' rel="nofollow" ' : "";
					$tags 			= !empty($link) ? array("a href='{$link}'{$blank}",'a') : array('div','div');
					$caption  		= "";
					$button_html 	= "";
					$counter ++;

					//if we got a CTA button apply the link to the button istead of the slide
					if($link_apply == 'button')
					{
						$button_html .= "<".$tags[0]." class='avia-slideshow-button avia-button-{$button_color}' data-duration='800' data-easing='easeInOutQuad'>";
						$button_html .= $button_label;
						$button_html .= "</".$tags[1].">";
						$tags = array('div','div');
					}

					//check if we got a caption
                    $markup_description = avia_markup_helper(array('context' => 'description','echo'=>false));
                    $markup_name = avia_markup_helper(array('context' => 'name','echo'=>false));
					if(trim($title) != "")   $title 	= "<h2 class='avia-caption-title' $markup_name>".trim(apply_filters('avf_slideshow_title', $title))."</h2>";
					
					if(is_array($content)) $content = implode(' ',$content); //temp fix for trim() expects string warning until I can actually reproduce the problem
					if(trim($content) != "") $content 	= "<div class='avia-caption-content' $markup_description>".ShortcodeHelper::avia_apply_autop(ShortcodeHelper::avia_remove_autop(trim($content)))."</div>";

					if(trim($title.$content.$button_html) != "")
					{
						if(trim($title) != "" && trim($button_html) != "" && trim($content) == "") $content = "<br/>";

						if($this->config['handle'] == 'av_slideshow_full' || $this->config['handle'] == 'av_fullscreen')
						{
							$caption .= '<div class = "caption_fullwidth '.$caption_pos.'">';
							$caption .= 	'<div class = "container caption_container">';
							$caption .= 			'<div class = "slideshow_caption">';
							$caption .= 				'<div class = "slideshow_inner_caption">';
							$caption .= 					'<div class = "slideshow_align_caption">';
							$caption .=						$title;
							$caption .=						$content;
							$caption .=						$button_html;
							$caption .= 					'</div>';
							$caption .= 				'</div>';
							$caption .= 			'</div>';
							$caption .= 	'</div>';
							$caption .= '</div>';
						}
						else
						{
							$caption = '<div class="avia-caption"><div class="avia-inner-caption">'.$title.$content."</div></div>";
						}
					}


                   
                    
					
					if(!empty($img[0]))
					{
						$bg_slider_style = $this->config['bg_slider'] == "true" ? "style='background-position:{$position};' data-img-url='".$img[0]."'" : "";
						
						if($bg_slider_style )
						{
							if(empty($this->ie8_fallback))
							{
						    	$this->ie8_fallback .= "<!--[if lte IE 8]>";
								$this->ie8_fallback .= "<style type='text/css'>";
							}
							$this->ie8_fallback .= "\n #{$this->config['css_id']} .slide-{$counter}{";
							$this->ie8_fallback .= "\n -ms-filter: \"progid:DXImageTransform.Microsoft.AlphaImageLoader(src='{$img[0]}', sizingMethod='scale')\"; ";
						    $this->ie8_fallback .= "\n filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='{$img[0]}', sizingMethod='scale'); ";
							$this->ie8_fallback .= "\n } \n";
						}
					}


					$html .= "<li {$bg_slider_style} class='{$extra_class} slide-{$counter} ' >";
					$html .= "<".$tags[0]." data-rel='slideshow-".avia_slideshow::$slider."' class='avia-slide-wrap' {$linkdescription} >{$caption}";
					if($this->config['bg_slider'] != "true" && empty($video))
					{
						$html .= "<img src='".$img[0]."' title='".$linktitle."' alt='".$linkalt."' $markup_url />";
					}
					$html .= $video;
					$html .= "</".$tags[1].">";
					$html .= "</li>";
			}
			else
			{
				$this->slide_count --;
			}
		}

			if(!empty($this->ie8_fallback))
			{
				$this->ie8_fallback .= "</style> <![endif]-->";
				add_action('wp_footer', array($this, 'add_ie8_fallback_to_footer'));
			}

			return $html;
		}

		public function add_ie8_fallback_to_footer()
		{
			// echo $this->ie8_fallback;
		}


		protected function slide_navigation_arrows()
		{
			global $avia_config;
		
			$html  = "";
			$html .= "<div class='avia-slideshow-arrows avia-slideshow-controls'>";
			$html .= 	"<a href='#prev' class='prev-slide' ".av_icon_string('prev_big').">".__('Previous','avia_framework' )."</a>";
			$html .= 	"<a href='#next' class='next-slide' ".av_icon_string('next_big').">".__('Next','avia_framework' )."</a>";
			$html .= "</div>";

			return $html;
		}

		protected function slide_navigation_dots()
		{
			$html   = "";
			$html  .= "<div class='avia-slideshow-dots avia-slideshow-controls'>";
			$active = "active";

			for($i = 1; $i <= $this->slide_count; $i++)
			{
				$html .= "<a href='#{$i}' class='goto-slide {$active}' >{$i}</a>";
				$active = "";
			}

			$html .= "</div>";

			return $html;
		}

		protected function extract_subslides($slide_array)
		{
			$this->config['ids']= array();
			$this->subslides 	= array();
		
			foreach($slide_array as $key => $slide)
			{
				$this->subslides[$key] = $slide;
				$this->config['ids'][] = $slide['attr']['id'];
			}

			$this->config['ids'] = implode(',',$this->config['ids'] );
			
			unset($this->config['content']);
		}
		
		protected function set_video_slide($video_url)
		{
			$video = "";
			$this->service = $this->which_video_service($video_url);
			
			switch($this->service )
			{
				case "html5": $video = avia_html5_video_embed($video_url); break;
				case "iframe":$video = $video_url; break;
				case "youtube":
				case "vimeo":
					
					$uid = 'player_'.get_the_ID().'_'.mt_rand().'_'.mt_rand();
					$video = "<video class='avia_video' id='{$uid}'><source src='{$video_url}' type='video/{$this->service}' ></video>";
					
				break;
			}
			
			return $video;
			
		}
		
		//get the video service based on the url string fo the video
		protected function which_video_service($video_url)
		{
			$service = "";
			
			if(avia_backend_is_file($video_url, 'html5video'))
			{
				$service = "html5";
			}
			else if(strpos($video_url,'<iframe') !== false)
			{
				$service = "iframe";
			}
			else
			{
				if(strpos($video_url, 'youtube.com/watch') !== false)
				{
					$service = "youtube";
				}
				else if(strpos($video_url, 'vimeo.com') !== false)
				{
					$service = "vimeo";
				}
			}
			
			return $service;
		}
		
		
		
		
		
		
		
		
		
		
		
		
		
	}
}





















