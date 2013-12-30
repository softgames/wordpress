<?php
/**
 * Masonry
 * Shortcode that allows to display a fullwidth masonry of any post type
 */

if ( !class_exists( 'avia_sc_masonry_gallery' ) ) 
{
	class avia_sc_masonry_gallery extends aviaShortcodeTemplate
	{	
			/**
			 * Create the config array for the shortcode button
			 */
			function shortcode_insert_button()
			{
				$this->config['name']			= __('Fullwidth Masonry Gallery', 'avia_framework' );
				$this->config['tab']			= __('Media Elements', 'avia_framework' );
				$this->config['icon']			= AviaBuilder::$path['imagesURL']."sc-masonry-gallery.png";
				$this->config['order']			= 5;
				$this->config['target']			= 'avia-target-insert';
				$this->config['shortcode'] 		= 'av_masonry_gallery';
				$this->config['tooltip'] 	    = __('Display a fullwidth masonry/grid gallery', 'avia_framework' );
				$this->config['tinyMCE'] 		= array('disable' => "true");
				$this->config['drag-level'] 	= 1;
			}
			
			
			function extra_assets()
			{
				add_action('wp_ajax_avia_ajax_masonry_more', array('avia_masonry','load_more'));
				add_action('wp_ajax_nopriv_avia_ajax_masonry_more', array('avia_masonry','load_more'));
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
							"name" 	=> __("Edit Gallery",'avia_framework' ),
							"desc" 	=> __("Create a new Gallery by selecting existing or uploading new images",'avia_framework' ),
							"id" 	=> "ids",
							"type" 	=> "gallery",
							"modal_class" => 'av-show-image-custom-link',
							"title" => __("Add/Edit Gallery",'avia_framework' ),
							"button" => __("Insert Images",'avia_framework' ),
							"std" 	=> ""),
					
				array(
					"name" 	=> __("Image Number", 'avia_framework' ),
					"desc" 	=> __("How many images should be displayed per page?", 'avia_framework' ),
					"id" 	=> "items",
					"type" 	=> "select",
					"std" 	=> "24",
					"subtype" => AviaHtmlHelper::number_array(1,100,1, array('All'=>'-1'))),
				
				array(
					"name" 	=> __("Pagination", 'avia_framework' ),
					"desc" 	=> __("Should a pagination or load more option be displayed to view additional images?", 'avia_framework' ),
					"id" 	=> "paginate",
					"type" 	=> "select",
					"std" 	=> "yes",
					"required" => array('items','not','-1'),
					"subtype" => array(
						__('Display Pagination',  'avia_framework' ) =>'pagination',
						__('Display "Load More" Button',  'avia_framework' ) =>'load_more',
						__('No option to view additional images',  'avia_framework' ) =>'none')),
				
					
				array(
					"name" 	=> __("Size Settings", 'avia_framework' ),
					"desc" 	=> __("Here you can select how the masonry should behave and handle the images", 'avia_framework' ),
					"id" 	=> "size",
					"type" 	=> "radio",
					"std" 	=> "flex",
					"options" => array(
						'flex' => __('Flexible Masonry: All images get the same width but are displayed with their original height and width ratio',  'avia_framework' ),
						'fixed' => __('Perfect Grid: Display a perfect grid where each image has exactly the same size. Images get cropped/stretched if they don\'t fit',  'avia_framework' ),
						'fixed masonry' => __('Perfect Automatic Masonry: Display a grid where most images get the same size, only very wide images get twice the width and very high images get twice the height. To qualify for "very wide" or "very high" the image must have a aspect ratio of 16:9 or higher',  'avia_framework' ),
					)),
					
					
				array(
					"name" 	=> __("Gap between elements", 'avia_framework' ),
					"desc" 	=> __("Select the gap between the elements", 'avia_framework' ),
					"id" 	=> "gap",
					"type" 	=> "select",
					"std" 	=> "large",
					"subtype" => array(
						__('No Gap',  'avia_framework' ) =>'no',
						__('1 Pixel Gap',  'avia_framework' ) =>'1px',
						__('Large Gap',  'avia_framework' ) =>'large',
					)),
					
				array(
					"name" 	=> __("Image overlay", 'avia_framework' ),
					"desc" 	=> __("Do you want to display the image overlay effect that gets removed on mouseover?", 'avia_framework' ),
					"id" 	=> "overlay_fx",
					"type" 	=> "select",
					"std" 	=> "1px",
					"subtype" => array(
						__('Overlay activated',  'avia_framework' ) =>'active',
						__('Overlay deactivated',  'avia_framework' ) =>'',
					)),
					
				
				array(
					"name" 	=> __("Image Title and Caption", 'avia_framework' ),
					"desc" 	=> __("You can choose if you want to display title and/or caption", 'avia_framework' ),
					"id" 	=> "caption_elements",
					"type" 	=> "select",
					"std" 	=> "title excerpt",
					"subtype" => array(
						__('Display Title and Caption',  'avia_framework' ) =>'title excerpt',
						__('Display Title',  'avia_framework' ) =>'title',
						__('Display Caption',  'avia_framework' ) =>'excerpt',
						__('Display Neither',  'avia_framework' ) =>'none',
					)),	
				
					
				array(
					"name" 	=> __("Element Title and Caption", 'avia_framework' ),
					"desc" 	=> __("You can choose whether to always display Title and Caption or only on hover", 'avia_framework' ),
					"id" 	=> "caption_display",
					"type" 	=> "select",
					"std" 	=> "always",
					"required" => array('caption_elements','not','none'),
					"subtype" => array(
						__('Always Display',  'avia_framework' ) =>'always',
						__('Display on mouse hover',  'avia_framework' ) =>'on-hover',
					)),	
				
				array(
					"name" 	=> __("Image Link", 'avia_framework' ),
					"desc" 	=> __("By default images link to a larger image version in a lightbox. You can deactivate that link. You can also set custom links when editing the images in the gallery", 'avia_framework' ),
					"id" 	=> "container_links",
					"type" 	=> "select",
					"std" 	=> "active",
					"subtype" => array(
						__('Lightbox linking active',  'avia_framework' ) =>'active',
						__('Lightbox linking deactivated. (Custom links will still be used)',  'avia_framework' ) =>'',
					)),	
				
					
					
				 array(	"name" 	=> __("For Developers: Section ID", 'avia_framework' ),
						"desc" 	=> __("Apply a custom ID Attribute to the section, so you can apply a unique style via CSS. This option is also helpful if you want to use anchor links to scroll to a sections when a link is clicked", 'avia_framework' )."<br/><br/>".
								   __("Use with caution and make sure to only use allowed characters. No special characters can be used.", 'avia_framework' ),
			            "id" 	=> "id",
			            "type" 	=> "input",
			            "std" => ""),
					
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
				$output  = "";
				
				$skipSecond = false;
				
				//check if we got a layerslider
				global $wpdb;
				
				$params['class'] = "main_color ".$meta['el_class'];
				$params['open_structure'] = false;
				$params['id'] = !empty($atts['id']) ? AviaHelper::save_string($atts['id'],'-') : "";
				
				if($meta['index'] == 0) $params['close'] = false;
				if($meta['index'] != 0) $params['class'] .= " masonry-not-first";
				if($meta['index'] == 0 && get_post_meta(get_the_ID(), 'header', true) != "no") $params['class'] .= " masonry-not-first";
				
				
				$output .=  avia_new_section($params);
				$atts['container_class'] = "av-masonry-gallery";
				$masonry  = new avia_masonry($atts);
				$masonry->query_entries_by_id();
				$output .= $masonry->html();
				$output .= "</div>"; //close section
				
				
				//if the next tag is a section dont create a new section from this shortcode
				if(!empty($meta['siblings']['next']['tag']) && in_array($meta['siblings']['next']['tag'], AviaBuilder::$full_el ))
				{
				    $skipSecond = true;
				}

				//if there is no next element dont create a new section.
				if(empty($meta['siblings']['next']['tag']))
				{
				    $skipSecond = true;
				}
				
				if(empty($skipSecond)) {
				
				$output .= avia_new_section(array('close'=>false, 'id' => "after_masonry"));
				
				}
				
				return $output;
			}
			
	}
}







