<?php
/**
 * Sidebar
 * Displays one of the registered Widget Areas of the theme
 */

if ( !class_exists( 'avia_sc_testimonial' ) )
{
	class avia_sc_testimonial extends aviaShortcodeTemplate
	{
			static $columnClass;
			static $rows;
			static $counter;
			static $columns;
			static $style;

			/**
			 * Create the config array for the shortcode button
			 */
			function shortcode_insert_button()
			{
				$this->config['name']		= __('Testimonials', 'avia_framework' );
				$this->config['tab']		= __('Content Elements', 'avia_framework' );
				$this->config['icon']		= AviaBuilder::$path['imagesURL']."sc-testimonials.png";
				$this->config['order']		= 20;
				$this->config['target']		= 'avia-target-insert';
				$this->config['shortcode'] 	= 'av_testimonials';
				$this->config['shortcode_nested'] = array('av_testimonial_single');
				$this->config['tooltip'] 	= __('Creates a Testimonial Grid', 'avia_framework' );
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
							"name" => __("Add/Edit Testimonial", 'avia_framework' ),
							"desc" => __("Here you can add, remove and edit your Testimonials.", 'avia_framework' ),
							"type" 			=> "modal_group",
							"id" 			=> "content",
							"modal_title" 	=> __("Edit Testimonial", 'avia_framework' ),
							"std"			=> array(

													array('name'=>__('Name', 'avia_framework' ), 'Subtitle'=>'', 'check'=>'is_empty'),

													),


							'subelements' 	=> array(

									array(
									"name" 	=> __("Image",'avia_framework' ),
									"desc" 	=> __("Either upload a new, or choose an existing image from your media library",'avia_framework' ),
									"id" 	=> "src",
									"type" 	=> "image",
									"fetch" => "id",
									"title" =>  __("Insert Image",'avia_framework' ),
									"button" => __("Insert",'avia_framework' ),
									"std" 	=> ""),

									array(
									"name" 	=> __("Name", 'avia_framework' ),
									"desc" 	=> "Enter the Name of the Person to quote",
									"id" 	=> "name",
									"std" 	=> "",
									"type" 	=> "input"),

									array(
									"name" 	=> __("Subtitle below name", 'avia_framework' ),
									"desc" 	=> "Can be used for a job description",
									"id" 	=> "subtitle",
									"std" 	=> "",
									"type" 	=> "input"),

							        array(
									"name" 	=> __("Quote", 'avia_framework' ),
									"desc" 	=> __("Enter the testimonial here", 'avia_framework' ),
									"id" 	=> "content",
									"std" 	=> "",
									"type" 	=> "tiny_mce"
									),

									array(
									"name" 	=> __("Website Link", 'avia_framework' ),
									"desc" 	=> "Link to the Persons website",
									"id" 	=> "link",
									"std" 	=> "http://",
									"type" 	=> "input"),

									array(
									"name" 	=> __("Website Name", 'avia_framework' ),
									"desc" 	=> "Linktext for the above Link",
									"id" 	=> "linktext",
									"std" 	=> "",
									"type" 	=> "input"),

						)
					),


array(
							"name" 	=> __("Testimonial Style", 'avia_framework' ),
							"desc" 	=> __("Here you can select how to display the testimonials. You can either create a testimonial slider or a testimonial grid with multiple columns", 'avia_framework' ) ,
							"id" 	=> "style",
							"type" 	=> "select",
							"std" 	=> "grid",
							"subtype" => array(	__('Testimonial Grid', 'avia_framework' ) =>'grid',
												__('Testimonial Slider', 'avia_framework' ) =>'slider',
							)
						),


						array(
							"name" 	=> __("Testimonial Grid Columns", 'avia_framework' ),
							"desc" 	=> __("How many columns do you want to display", 'avia_framework' ) ,
							"id" 	=> "columns",
							"required" 	=> array('style', 'equals', 'grid'),
							"type" 	=> "select",
							"std" 	=> "2",
							"subtype" => AviaHtmlHelper::number_array(1,4,1)
							),

						array(
						"name" 	=> __("Slideshow autorotation duration",'avia_framework' ),
						"desc" 	=> __("Slideshow will rotate every X seconds",'avia_framework' ),
						"id" 	=> "interval",
						"type" 	=> "select",
						"std" 	=> "5",
						"required" 	=> array('style','equals','slider'),
						"subtype" => array('3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10','15'=>'15','20'=>'20','30'=>'30','40'=>'40','60'=>'60','100'=>'100')),


				);


			}

			/**
			 * Editor Sub Element - this function defines the visual appearance of an element that is displayed within a modal window and on click opens its own modal window
			 * Works in the same way as Editor Element
			 * @param array $params this array holds the default values for $content and $args.
			 * @return $params the return array usually holds an innerHtml key that holds item specific markup.
			 */
			function editor_sub_element($params)
			{
				$template = $this->update_template("name", __("Testimonial by", 'avia_framework' ). ": {{name}}");

				$params['innerHtml']  = "";
				$params['innerHtml'] .= "<div class='avia_title_container' {$template}>".__("Testimonial by", 'avia_framework' ).": ".$params['args']['name']."</div>";

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
				$atts =  shortcode_atts(array('style'=> "grid",  'columns'=> "2", "autoplay"=>true, "interval"=>5), $atts);
				$custom_class = !empty($meta['custom_class']) ? $meta['custom_class'] : "";
				extract($atts);

				$output = "";

				switch($columns)
				{
					case 1: $columnClass = "av_one_full flex_column no_margin"; break;
					case 2: $columnClass = "av_one_half flex_column no_margin"; break;
					case 3: $columnClass = "av_one_third flex_column no_margin"; break;
					case 4: $columnClass = "av_one_fourth flex_column no_margin"; break;
				}

				$data = AviaHelper::create_data_string(array('autoplay'=>$autoplay, 'interval'=>$interval, 'animation' => 'fade', 'hoverpause' => true));

				$output .= "<div {$data} class='avia-testimonial-wrapper avia-{$style}-testimonials avia-{$style}-{$columns}-testimonials avia_animate_when_almost_visible {$custom_class}'>";

				avia_sc_testimonial::$counter = 1;
				avia_sc_testimonial::$rows = 1;
				avia_sc_testimonial::$columnClass = $columnClass;
				avia_sc_testimonial::$columns = $columns;
				avia_sc_testimonial::$style = $style;

				//if we got a slider we only need a single row wrpper
				if($style == "slider") avia_sc_testimonial::$columns = 100000;

				$output .= ShortcodeHelper::avia_remove_autop($content, true);

				//close unclosed wrapper containers
				if(avia_sc_testimonial::$counter != 1){
				$output .= "</section>";
				}

				$output .= "</div>";

				return $output;
			}


			function av_testimonial_single($atts, $content = "", $shortcodename = "")
			{
				extract(shortcode_atts(array('src'=> "",  'name'=> "",  'subtitle'=> "",  'link'=> "", 'linktext'=>"" ), $atts));

				$output = "";
				$avatar = "";
				$grid = avia_sc_testimonial::$style == 'grid' ? true :false;
				$class = avia_sc_testimonial::$columnClass." avia-testimonial-row-".avia_sc_testimonial::$rows." ";
				//if(count($testimonials) <= $rows * $columns) $class.= " avia-testimonial-row-last ";
				if(avia_sc_testimonial::$counter == 1) $class .= "avia-first-testimonial";
				if(avia_sc_testimonial::$counter == avia_sc_testimonial::$columns) $class .= "avia-last-testimonial";
				if($link && !$linktext) $linktext = $link;

                

				if(avia_sc_testimonial::$counter == 1)
                {
				    $output .= "<section class ='avia-testimonial-row'>";
				}


	//avatar size filter
	$avatar_size = apply_filters('avf_testimonials_avatar_size', 'thumbnail', $src, $class);

	//avatar
                $markup = avia_markup_helper(array('context' => 'single_image','echo'=>false));
	if($src)	$avatar  = "<div class='avia-testimonial-image' $markup>".wp_get_attachment_image( $src , $avatar_size , false, array('alt'=>esc_attr(strip_tags($name))))."</div>";

	//meta
                $markup_text = avia_markup_helper(array('context' => 'entry','echo'=>false));
                $markup_name = avia_markup_helper(array('context' => 'name','echo'=>false));
                $markup_job = avia_markup_helper(array('context' => 'job','echo'=>false));
                if(strstr($link, '@'))
                {
                    $markup_url = avia_markup_helper(array('context' => 'email','echo'=>false));
                }
                else
                {
                    $markup_url = avia_markup_helper(array('context' => 'url','echo'=>false));
                }

	//final output
				
				$markup = avia_markup_helper(array('context' => 'person','echo'=>false));
				
				$output .= "<div class='avia-testimonial {$class}' $markup>";
				$output .= "<div class='avia-testimonial_inner'>";
	if($grid)   $output .= $avatar;
				$output .= 		"<div class='avia-testimonial-content' $markup_text>";
				$output .= 		ShortcodeHelper::avia_apply_autop(ShortcodeHelper::avia_remove_autop($content));
				$output .= 		"</div>";
				$output .= 			"<div class='avia-testimonial-meta'><div class='avia-testimonial-arrow-wrap'><div class='avia-arrow'></div></div>";
	if(!$grid)  $output .=  $avatar;
				$output .= 				"<div class='avia-testimonial-meta-mini'>";
	if($name)	$output .= 					"<strong  class='avia-testimonial-name'  {$markup_name}>{$name}</strong>";
if($subtitle)	$output .= 					"<span  class='avia-testimonial-subtitle'  {$markup_job}>{$subtitle}</span>";
    if($link)	$output .= 					"<span class='hidden avia-testimonial-markup-link'  {$markup_url}>{$link}</span>";
	if($link)	$output .= 					" &ndash; <a class='aviablank avia-testimonial-link' href='{$link}' >{$linktext}</a>";
				$output .= 				"</div>";
				$output .= 			"</div>";
				$output .= "</div>";
				$output .= "</div>";



				if(avia_sc_testimonial::$counter == avia_sc_testimonial::$columns)
                {
				    $output .= "</section>";
				}

				avia_sc_testimonial::$counter++;
				if(avia_sc_testimonial::$counter > avia_sc_testimonial::$columns) { avia_sc_testimonial::$counter = 1; avia_sc_testimonial::$rows++; }


				return $output;
			}

	}
}






