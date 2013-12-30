<?php
/**
 * HORIZONTAL RULERS
 * Creates a horizontal ruler that provides whitespace for the layout and helps with content separation
 */
 
// Don't load directly
if ( !defined('ABSPATH') ) { die('-1'); }



if ( !class_exists( 'avia_sc_hr' ) ) 
{
	class avia_sc_hr extends aviaShortcodeTemplate{
			
			/**
			 * Create the config array for the shortcode button
			 */
			function shortcode_insert_button()
			{
				$this->config['name']		= __('Horizontal Ruler', 'avia_framework' );
				$this->config['tab']		= __('Content Elements', 'avia_framework' );
				$this->config['icon']		= AviaBuilder::$path['imagesURL']."sc-hr.png";
				$this->config['order']		= 94;
				$this->config['target']		= 'avia-target-insert';
				$this->config['shortcode'] 	= 'av_hr';
				$this->config['modal_data'] = array('modal_class' => 'flexscreen');
				$this->config['tooltip'] 	= __('Creates a delimiter/whitespace to separate elements', 'avia_framework' );
				$this->config['tinyMCE']    = array('tiny_always'=>true);
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
							"name" 	=> __("Horizontal Ruler Styling", 'avia_framework' ),
							"desc" 	=> __("Here you can set the styling and size of the HR element", 'avia_framework' ),
							"id" 	=> "class",
							"type" 	=> "select",
							"std" 	=> "default",
							"subtype" => array(	__('Default', 'avia_framework' ) 							=>'default',
												__('Big Top and Bottom Margins', 'avia_framework' )		=>'big',
												__('Fullwidth Separator', 'avia_framework' )		        =>'full',
												__('Whitespace', 'avia_framework' ) 						=>'invisible',
												__('Short Separator', 'avia_framework' ) 					=>'short',
												)),
												
                    array(	"name" 	=> __("Height", 'avia_framework' ),
							"desc" 	=> __("How much whitespace do you need? Enter a pixel value", 'avia_framework' ),
				            "id" 	=> "height",
				            "type" 	=> "input",
				            "required" => array('class','equals','invisible'),
				            "std" => "50"),
				   
				     array(	
						"name" 	=> __("Section Top Shadow",'avia_framework' ),
						"id" 	=> "shadow",
						"desc"  => __("Display a small styling shadow at the top of the section",'avia_framework' ),
						"type" 	=> "select",
						"std" 	=> "no-shadow",
				        "required" => array('class','equals','full'),
						"subtype" => array(   __('Display shadow','avia_framework' )	=>'shadow',
						                      __('Do not display shadow','avia_framework' )	=>'no-shadow',
						                  )),
				            
				   array(	
							"name" 	=> __("Position", 'avia_framework' ),
							"desc" 	=> __("Set the position of the short ruler", 'avia_framework' ),
							"id" 	=> "position",
							"type" 	=> "select",
							"std" 	=> "center",
				            "required" => array('class','equals','short'),
							"subtype" => array(	__('Center', 'avia_framework' ) =>'center',
												__('Left', 'avia_framework' )  =>'left',
												__('Right', 'avia_framework' ) =>'right',
												)),      
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
				$params['content'] 		= NULL;
				$params['innerHtml']  	= "<span class='avia-divider'></span>";
				$params['class'] = "";

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
			    extract(shortcode_atts(array('class' => 'default', 'height' => '50', 'position'=>'center', 'shadow'=>'no-shadow'), $atts));
			
        		$output  = "";
        		$height  = trim($height, 'px% ');
        		$style   = $class == 'invisible' ? "style='height:{$height}px'" : "";
        		$class  .= $class == 'short' ? " hr-{$position}" : "";
        		$class  .= $class == 'full'  ? " hr-{$shadow}" : "";


        		$output .= "<div {$style} class='hr hr-{$class} ".$meta['el_class']."'><span class='hr-inner'><span class='hr-inner-style'></span></span></div>";
        		
        		
        		return $output;
        	}
			
			
	}
}
