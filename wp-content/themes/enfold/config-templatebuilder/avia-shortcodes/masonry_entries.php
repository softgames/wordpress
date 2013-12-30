<?php
/**
 * Masonry
 * Shortcode that allows to display a fullwidth masonry of any post type
 */

if ( !class_exists( 'avia_sc_masonry_entries' ) ) 
{
	class avia_sc_masonry_entries extends aviaShortcodeTemplate
	{	
			/**
			 * Create the config array for the shortcode button
			 */
			function shortcode_insert_button()
			{
				$this->config['name']			= __('Fullwidth Masonry', 'avia_framework' );
				$this->config['tab']			= __('Content Elements', 'avia_framework' );
				$this->config['icon']			= AviaBuilder::$path['imagesURL']."sc-masonry.png";
				$this->config['order']			= 38;
				$this->config['target']			= 'avia-target-insert';
				$this->config['shortcode'] 		= 'av_masonry_entries';
				$this->config['tooltip'] 	    = __('Display a fullwidth masonry/grid with blog entries', 'avia_framework' );
				$this->config['tinyMCE'] 		= array('disable' => "true");
				$this->config['drag-level'] 	= 1;
			}
			
			
			function extra_assets()
			{
				add_action('wp_ajax_avia_ajax_masonry_more', array('avia_masonry','load_more'));
				add_action('wp_ajax_nopriv_avia_ajax_masonry_more', array('avia_masonry','load_more'));
				
				if(!is_admin() && !session_id()) session_start();
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
						"name" 	=> __("Which Entries?", 'avia_framework' ),
						"desc" 	=> __("Select which entries should be displayed by selecting a taxonomy", 'avia_framework' ),
						"id" 	=> "link",
						"fetchTMPL"	=> true,
						"type" 	=> "linkpicker",
						"subtype"  => array( __('Display Entries from:',  'avia_framework' )=>'taxonomy'),
						"multiple"	=> 6,
						"std" 	=> "category"
				),
				
				array(
						"name" 	=> __("Sortable?", 'avia_framework' ),
						"desc" 	=> __("Should sorting options based on the taxonomies above be displayed?", 'avia_framework' ),
						"id" 	=> "sort",
						"type" 	=> "select",
						"std" 	=> "yes",
						"subtype" => array(
							__('yes',  'avia_framework' ) => 'yes',
							__('no',  'avia_framework' )  => 'no')),
				
				array(
					"name" 	=> __("Post Number", 'avia_framework' ),
					"desc" 	=> __("How many items should be displayed per page?", 'avia_framework' ),
					"id" 	=> "items",
					"type" 	=> "select",
					"std" 	=> "12",
					"subtype" => AviaHtmlHelper::number_array(1,100,1, array('All'=>'-1'))),
				
				array(
					"name" 	=> __("Pagination", 'avia_framework' ),
					"desc" 	=> __("Should a pagination or load more option be displayed to view additional entries?", 'avia_framework' ),
					"id" 	=> "paginate",
					"type" 	=> "select",
					"std" 	=> "yes",
					"required" => array('items','not','-1'),
					"subtype" => array(
						__('Display Pagination',  'avia_framework' ) =>'pagination',
						__('Display "Load More" Button',  'avia_framework' ) =>'load_more',
						__('No option to view additional entries',  'avia_framework' ) =>'none')),
				
					
				array(
					"name" 	=> __("Size Settings", 'avia_framework' ),
					"desc" 	=> __("Here you can select how the masonry should behave and handle all entries and the feature images of those entries", 'avia_framework' ),
					"id" 	=> "size",
					"type" 	=> "radio",
					"std" 	=> "fixed masonry",
					"options" => array(
						'flex' => __('Flexible Masonry: All entries get the same width but Images of each entry are displayed with their original height and width ratio',  'avia_framework' ),
						'fixed' => __('Perfect Grid: Display a perfect grid where each element has exactly the same size. Images get cropped/stretched if they don\'t fit',  'avia_framework' ),
						'fixed masonry' => __('Perfect Automatic Masonry: Display a grid where most elements get the same size, only elements with very wide images get twice the width and elements with very high images get twice the height. To qualify for "very wide" or "very high" the image must have a aspect ratio of 16:9 or higher',  'avia_framework' ),
						'fixed manually' => __('Perfect Manual Masonry: Manually control the height and width of entries by adding either a "landscape" or "portrait" tag when creating the entry. Elements with no such tag use a fixed default size, elements with both tags will display extra large',  'avia_framework' ),
					)),
					
					
				array(
					"name" 	=> __("Gap between elements", 'avia_framework' ),
					"desc" 	=> __("Select the gap between the elements", 'avia_framework' ),
					"id" 	=> "gap",
					"type" 	=> "select",
					"std" 	=> "1px",
					"subtype" => array(
						__('No Gap',  'avia_framework' ) =>'no',
						__('1 Pixel Gap',  'avia_framework' ) =>'1px',
						__('Large Gap',  'avia_framework' ) =>'large',
					)),
				
				array(
					"name" 	=> __("Image overlay effect", 'avia_framework' ),
					"desc" 	=> __("Do you want to display the image overlay effect that gets removed on mouseover?", 'avia_framework' ),
					"id" 	=> "overlay_fx",
					"type" 	=> "select",
					"std" 	=> "active",
					"subtype" => array(
						__('Overlay activated',  'avia_framework' ) =>'active',
						__('Overlay deactivated',  'avia_framework' ) =>'',
					)),
					
				
				array(
					"name" 	=> __("Element Title and Excerpt", 'avia_framework' ),
					"desc" 	=> __("You can choose if you want to display title and/or excerpt", 'avia_framework' ),
					"id" 	=> "caption_elements",
					"type" 	=> "select",
					"std" 	=> "title excerpt",
					"subtype" => array(
						__('Display Title and Excerpt',  'avia_framework' ) =>'title excerpt',
						__('Display Title',  'avia_framework' ) =>'title',
						__('Display Excerpt',  'avia_framework' ) =>'excerpt',
						__('Display Neither',  'avia_framework' ) =>'none',
					)),	
				
					
				array(
					"name" 	=> __("Element Title and Excerpt", 'avia_framework' ),
					"desc" 	=> __("You can choose whether to always display Title and Excerpt or only on hover", 'avia_framework' ),
					"id" 	=> "caption_display",
					"type" 	=> "select",
					"std" 	=> "always",
					"required" => array('caption_elements','not','none'),
					"subtype" => array(
						__('Always Display',  'avia_framework' ) =>'always',
						__('Display on mouse hover',  'avia_framework' ) =>'on-hover',
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
				$masonry  = new avia_masonry($atts);
				$masonry->extract_terms();
				$masonry->query_entries();
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





if ( !class_exists( 'avia_masonry' ) )
{
	class avia_masonry
	{
		static  $element = 0;
		protected $atts;
		protected $entries;

		function __construct($atts = array())
		{
			self::$element += 1;
			$this->atts = shortcode_atts(array(	'ids'	=> false,
												'action'=> false,
												'link' 	=> 'category',
												'items' => 24,
												'size'	=> 'fixed',
												'gap'	=> '1px',
												'overlay_fx' 		=> 'active',
												'offset'			=> 0,
												'container_links'	=> true,
												'container_class'	=> "",
												'paginate'			=> 'paginate',
												'caption_elements' 	=> 'title excerpt',
												'caption_display' 	=> 'always',
												'sort'				=> 'no',
												'auto_ratio' 		=> 1.7, //equals a 16:9 ratio
												'set_breadcrumb' 	=> true //no shortcode option for this, modifies the breadcrumb nav, must be false on taxonomy overview
		                                 		), $atts);
		                                 		
		  	$this->atts = apply_filters('avf_masonry_settings', $this->atts, self::$element);
		}
		
		//ajax function to load additional items
		static function load_more()
		{
			//increase the post items by one to fetch an additional item. this item is later removed by the javascript but it tells the script if there are more items to load or not
			$_POST['items'] = $_POST['items'] + 1;
		
			$masonry  = new avia_masonry($_POST);
			
			if(!empty($_POST['ids']))
			{
				$masonry->query_entries_by_id();
			}
			else
			{
				$masonry->extract_terms();
				$masonry->query_entries();
			}
			
			$output = $masonry->html();
					
			echo '{av-masonry-loaded}'.$output;
			exit();
		}
		
		
		function extract_terms()
		{
			if(isset($this->atts['link']))
			{
				$this->atts['link'] = explode(',', $this->atts['link'], 2 );
				$this->atts['taxonomy'] = $this->atts['link'][0];

				if(isset($this->atts['link'][1]))
				{
					$this->atts['categories'] = $this->atts['link'][1];
				}
				else
				{
					$this->atts['categories'] = array();
				}
			}
		}
		
		function sort_buttons()
		{
			$sort_terms = get_terms( $this->atts['taxonomy'] , array('hide_empty'=>true) );
			
			$current_page_terms	= array();
			$term_count 		= array();
			$display_terms 		= is_array($this->atts['categories']) ? $this->atts['categories'] : array_filter(explode(',',$this->atts['categories']));

			foreach ($this->loop as $entry)
			{
				if($current_item_terms = get_the_terms( $entry['ID'], $this->atts['taxonomy'] ))
				{
					if(!empty($current_item_terms))
					{
						foreach($current_item_terms as $current_item_term)
						{
							if(empty($display_terms) || in_array($current_item_term->term_id, $display_terms))
							{
								$current_page_terms[$current_item_term->term_id] = $current_item_term->term_id;

								if(!isset($term_count[$current_item_term->term_id] ))
								{
									$term_count[$current_item_term->term_id] = 0;
								}

								$term_count[$current_item_term->term_id] ++;
							}
						}
					}
				}
			}
			
			$output = "<div class='av-masonry-sort main_color' data-masonry-id='".self::$element."' >";
			$hide 	= count($current_page_terms) <= 1 ? "hidden" : "";


			$first_item_name = apply_filters('avf_masonry_sort_first_label', __('All','avia_framework' ), $this->atts);
			$output .= apply_filters('avf_masonry_sort_heading', "", $this->atts);
			$output .= "<div class='av-sort-by-term {$hide} '>";
			$output .= '<a href="#" data-filter="all_sort" class="all_sort_button active_sort"><span class="inner_sort_button"><span>'.$first_item_name.'</span><small class="avia-term-count"> '.count($this->loop).' </small></span></a>';

			foreach($sort_terms as $term)
			{
				if(in_array($term->term_id, $current_page_terms))
				{
					$output .= 	"<span class='text-sep ".$term->slug."_sort_sep'>/</span>";
					$output .= 	'<a href="#" data-filter="'.$term->slug.'_sort" class="'.$term->slug.'_sort_button" ><span class="inner_sort_button">';
					$output .= 		"<span>".esc_html(trim($term->name))."</span>";
					$output .= 		"<small class='avia-term-count'> ".$term_count[$term->term_id]." </small></span>";
					$output .= 	"</a>";
				}
			}

			$output .= "</div></div>";
			
			return $output;

			
		}
		
		//get the categories for each post and create a string that serves as classes so the javascript can sort by those classes
		function sort_array($the_id)
		{
			$sort_classes 	= array("all_sort");
			$item_terms 	= get_the_terms( $the_id, $this->atts['taxonomy']);

			if(is_object($item_terms) || is_array($item_terms))
			{
				foreach ($item_terms as $term)
				{
					$sort_classes[] = $term->slug.'_sort ';
				}
			}

			return $sort_classes;
		}

		
		
		function html()
		{
			if(empty($this->loop)) return;
			
			$output 	= "";
			$items		= "";
			$size 		= strpos($this->atts['size'], 'fixed') !== false ? 'fixed' : "flex";
			$auto 		= strpos($this->atts['size'], 'masonry') !== false ? true : false;
			$manually	= strpos($this->atts['size'], 'manually') !== false ? true : false;
			$defaults 	= array('ID'=>'', 
								'thumb_ID'=>'', 
								'title' =>'', 
								'url' => '',  
								'class' => array(),  
								'date' => '', 
								'excerpt' => '', 
								'data' => '', 
								'attachment'=> array(), 
								'attachment_overlay' => array(),
								'bg' => "", 
								'before_content'=>'', // if set replaces the whole bg part 
								'text_before'=>'', 
								'text_after'=>'', 
								'img_before'=>'');
			
			
			$output .= "<div id='av-masonry-".self::$element."' class='av-masonry noHover av-{$size}-size av-{$this->atts['gap']}-gap av-hover-overlay-{$this->atts['overlay_fx']} av-caption-{$this->atts['caption_display']} {$this->atts['container_class']}' >";
			
			$output .= $this->atts['sort'] == "yes" ? $this->sort_buttons() : "";
			
			$output .= "<div class='av-masonry-container isotope av-js-disabled ' >";
			$all_sorts  = array();
			$sort_array = array();
			foreach($this->loop as $entry)
			{
				extract(array_merge($defaults, $entry));
				$img_html		= "";
				$img_style		= "";
				if($this->atts['sort'] == "yes")
				{
					$sort_array		= $this->sort_array($entry['ID']);
				}
				$class_string 	= implode(' ', $class).' '.implode(' ', $sort_array);
				$all_sorts 		= array_merge($all_sorts, $sort_array);
				
				if(!empty($attachment))
				{
                    $alt = get_post_meta($thumb_ID, '_wp_attachment_image_alt', true);
                    $alt = !empty($alt) ? esc_attr($alt) : '';
                    $title = esc_attr(get_the_title($thumb_ID));

					if(isset($attachment[0]))
					{
						$img_html  = '<img src="'.$attachment[0].'" title="'.$title.'" alt="'.$alt.'" />';
						$img_style = 'style="background-image: url('.$attachment[0].');"';
						$class_string .= " av-masonry-item-with-image";
					}
					
					if(isset($attachment_overlay[0]))
					{
						$over_html  = '<img src="'.$attachment_overlay[0].'" title="'.$title.'" alt="'.$alt.'" />';
						$over_style = 'style="background-image: url('.$attachment_overlay[0].');"';
						$img_before = '<div class="av-masonry-image-container av-masonry-overlay" '.$over_style.'>'.$over_html.'</div>';
					}
					
					$bg = '<div class="av-masonry-outerimage-container">'.$img_before.'<div class="av-masonry-image-container" '.$img_style.'>'.$img_html.'</div></div>';
					
				}
				else
				{
					$class_string .= " av-masonry-item-no-image";
				}
				
				
				if($size == 'fixed')
				{
					if(!empty($attachment) || !empty($before_content))
					{
						if($auto)
							$class_string .= $this->ratio_check_by_image_size($attachment);
							
						if($manually)
							$class_string .= $this->ratio_check_by_tag($entry['tags']);	
					}
				}



                if($post_type == 'attachment' && strpos($html_tags[0], 'a href=') !== false)
                {
                    $linktitle = 'title="'.esc_attr($description).'"';
                }
                else if(strpos($html_tags[0], 'a href=') !== false)
                {
                    $linktitle = 'title="'.esc_attr($the_title).'"';
                }
                $markup = ($post_type == 'attachment') ? avia_markup_helper(array('context' => 'image_url','echo'=>false)) : avia_markup_helper(array('context' => 'entry','echo'=>false));

				$items .= 	"<{$html_tags[0]} class='{$class_string}' $linktitle $markup>";
				$items .= 		"<div class='av-inner-masonry-sizer'></div>"; //responsible for the size
				$items .=		"<figure class='av-inner-masonry main_color'>";
				$items .= 			$bg;
				
				//title and excerpt
				if($this->atts['caption_elements'] != 'none' || !empty($text_add))
				{
					$items .=	"<figcaption class='av-inner-masonry-content site-background'><div class='av-inner-masonry-content-pos'><div class='avia-arrow'></div>".$text_before;
					
					if(strpos($this->atts['caption_elements'], 'title') !== false){
                        $markup = avia_markup_helper(array('context' => 'entry_title','echo'=>false));
						$items .=	"<h3 class='av-masonry-entry-title entry-title' {$markup}>{$the_title}</h3>";
					}

					if(strpos($this->atts['caption_elements'], 'excerpt') !== false && !empty($content)){
                        $markup = avia_markup_helper(array('context' => 'entry_content','echo'=>false));
						$items .=	"<div class='av-masonry-entry-content entry-content' {$markup}>{$content}</div>";
					}
					$items .=	$text_after."</div></figcaption>";
				}
				$items .= 		"</figure>";
				$items .= 	"</{$html_tags[1]}><!--end av-masonry entry-->";					
			}
			
			//if its an ajax call return the items only without container
			if(isset($this->atts['action']) && $this->atts['action'] == 'avia_ajax_masonry_more')
			{
				return $items;
			}
			
			// if its no ajax load prepend an empty invisible element as the first element. this is used for calculating the correct width of a default element.
			// in theory this is not necessary because the masonry can detect that with an extra js parameter but sorting becomes slugish if that param is set
			
			$all_sort_string = implode(' ', array_unique($all_sorts));
			$items = "<div class='av-masonry-entry isotope-item av-masonry-item-no-image {$all_sort_string}'></div>".$items;
			
			$output .= $items;
			$output .= 	"</div>";
			
			
			//append pagination
			if($this->atts['paginate'] == "pagination" && $avia_pagination = avia_pagination($this->entries->max_num_pages, 'nav')) 
			{
				$output .= "<div class='av-masonry-pagination av-masonry-pagination-{$this->atts['paginate']}'>{$avia_pagination}</div>";
			}
			else if($this->atts['paginate'] == "load_more" && $this->entries->max_num_pages > count($this->entries))
			{
				$output .= $this->load_more_button();
			}
			
			$output .= "</div>";
			
			return $output;
		}
		
				
		function load_more_button()
		{
			$data_string = AviaHelper::create_data_string($this->atts);
		
			$output  = "";
			$output .= 		"<a class='av-masonry-pagination av-masonry-load-more' href='#load-more' {$data_string}>".__('Load more','avia_framework')."</a>";
			
			return $output;
		}	
		
		function ratio_check_by_image_size($attachment)
		{
			$img_size = ' av-grid-img';
			
			if(!empty($attachment[1]) && !empty($attachment[2]))
			{
				if($attachment[1] > $attachment[2]) //landscape
				{
					//only consider it landscape if its 1.7 times wider than high
					if($attachment[1] / $attachment[2] > $this->atts['auto_ratio']) $img_size = ' av-landscape-img';
				}
				else //same check with portrait
				{
					if($attachment[2] / $attachment[1] > $this->atts['auto_ratio']) $img_size = ' av-portrait-img';
				}
			}
			
			return $img_size;
		}
		
		function ratio_check_by_tag($tags)
		{
			$img_size = '';
			
			if(is_array($tags))
			{	
				if(in_array('portrait', $tags)) { $img_size .= ' av-portrait-img'; }
				if(in_array('landscape', $tags)){ $img_size .= ' av-landscape-img'; }
			}
			
			if(empty($img_size))  $img_size = ' av-grid-img';
			
			return $img_size;
			
		}
		
		
		function prepare_loop_from_entries()
		{
			$this->loop = array();
			if(empty($this->entries) || empty($this->entries->posts)) return;
			$tagTax = "post_tag"; 
			$date_format = get_option('date_format');
			
			
			foreach($this->entries->posts as $key => $entry)
			{ 	
				$overlay_img = $custom_url			= false;
				$img_size	 						= 'masonry';
				$this->loop[$key]['text_before']	= "";
				$this->loop[$key]['text_after']		= "";
				$this->loop[$key]['ID'] = $id		= $entry->ID;
				$this->loop[$key]['post_type'] 		= $entry->post_type;
				$this->loop[$key]['thumb_ID'] 		= get_post_thumbnail_id($id);
				$this->loop[$key]['the_title'] 		= get_the_title($id);
				$this->loop[$key]['url']			= get_permalink($id);
				$this->loop[$key]['date'] 			= "<span class='av-masonry-date meta-color'>".get_the_time($date_format, $id)."</span>";
				$this->loop[$key]['class'] 			= get_post_class("av-masonry-entry isotope-item", $id); 
				$this->loop[$key]['content']		= $entry->post_excerpt;
                $this->loop[$key]['description']	= !empty($entry->post_content) ? $entry->post_content : $entry->post_excerpt;
				
				if(empty($this->loop[$key]['content']))
				{
					$this->loop[$key]['content'] 	= avia_backend_truncate($entry->post_content, apply_filters( 'avf_masonry_excerpt_length' , 60) , apply_filters( 'avf_masonry_excerpt_delimiter' , " "), "…", true, '');
				}
				
				//post type specific
				switch($entry->post_type)
				{
					case 'post': 
					
					$post_format 		= get_post_format($id) ? get_post_format($id) : 'standard';
					$this->loop[$key]	= apply_filters( 'post-format-'.$post_format, $this->loop[$key] );
					$this->loop[$key]['text_after'] .= $this->loop[$key]['date'];
					
						switch($post_format)
						{
							case 'quote' :
							case 'link' :
							case 'image' :
							case 'gallery' :
								if(!$this->loop[$key]['thumb_ID']) 
								{
									$this->loop[$key]['text_before'] = av_icon_display($post_format);
								}
							break;
							
							case 'audio' :
							case 'video' :
								if(!$this->loop[$key]['thumb_ID']) 
								{
									$this->loop[$key]['text_before'] = av_icon_display($post_format);
								}
								else
								{
									$this->loop[$key]['text_before'] = av_icon_display($post_format, 'av-masonry-media');
								}
							break;
						}
					
					
					
					break;
					
					case 'portfolio':
					
					//set portfolio breadcrumb navigation
					if($this->atts['set_breadcrumb'] && is_page()) $_SESSION["avia_{$entry->post_type}"] = get_the_ID();
					
					//check if the user has set up a custom link
					if(!post_password_required($id)){
						$custom_link = get_post_meta( $id ,'_portfolio_custom_link', true) != "" ? get_post_meta( $id ,'_portfolio_custom_link_url', true) : false;
						if($custom_link) $this->loop[$key]['url'] = $custom_link;
					}
					break;
					
					
					case 'attachment':
					
					$custom_url = get_post_meta( $id, 'av-custom-link', true );
					$this->loop[$key]['thumb_ID'] = $id;
					$this->loop[$key]['url'] =  $custom_url ? $custom_url : reset(wp_get_attachment_image_src($id, apply_filters('avf_avia_builder_masonry_lightbox_img_size','large')));
					$this->loop[$key]['content']		= $entry->post_excerpt;
					break; 
					
					case 'product':
					//check if woocommerce is enabled in the first place so we can use woocommerce functions
					if(function_exists('avia_woocommerce_enabled') && avia_woocommerce_enabled())
					{
						$tagTax 		= "product_tag"; 
						$product 		= get_product( $id );
						$overlay_img 	= avia_woocommerce_gallery_first_thumbnail($id, $img_size, true);

						$this->loop[$key]['text_after'] .= '<span class="av-masonry-price price">'.$product->get_price_html()."</span>";
						if($product->is_on_sale( )) $this->loop[$key]['text_after'] .= '<span class="onsale">'.__( 'Sale!', 'avia_framework' ).'</span>';
					}
					break; 
				}
				
				
				//check if post is password protected
				if(post_password_required($id))
				{
					$this->loop[$key]['content'] 		= "";
					$this->loop[$key]['class'][]		= "entry-protected";
					$this->loop[$key]['thumb_ID'] 		= "";
					$this->loop[$key]['text_before'] 	= av_icon_display('closed');
					$this->loop[$key]['text_after']		= $this->loop[$key]['date'];
				}
				
				
				
				//set the html tags. depending on the link settings use either an a tag or a div tag
				if(!empty($this->atts['container_links']) || !empty($custom_url))
				{
					$this->loop[$key]['html_tags'] = array('a href="'.$this->loop[$key]['url'].'"','a'); //opening and closing tag for the masonry container
				}
				else
				{
					$this->loop[$key]['html_tags'] = array('div','div');
				}
				
				
				//get post tags
				$this->loop[$key]['tags']		= wp_get_post_terms($id, $tagTax, array( 'fields' => 'slugs' ));
				
				//check if the image got landscape as well as portrait class applied. in that case use a bigger image size
				if(strlen($this->ratio_check_by_tag($this->loop[$key]['tags'])) > 20) $img_size = 'extra_large';
				
				//get attachment data
				$this->loop[$key]['attachment'] = !empty($this->loop[$key]['thumb_ID']) ? wp_get_attachment_image_src($this->loop[$key]['thumb_ID'], $img_size) : "";
				
				//get overlay attachment in case the overlay is set
				$this->loop[$key]['attachment_overlay'] = !empty($overlay_img) ? wp_get_attachment_image_src($overlay_img, $img_size) : "";
				
				//apply filter for other post types, in case we want to use them and display additional/different information
				$this->loop[$key] = apply_filters('avf_masonry_loop_prepare', $this->loop[$key], $this->entries);
			}
		}
		
		
		//fetch new entries
		public function query_entries($params = array())
		{
			global $avia_config;

			if(empty($params)) $params = $this->atts;

			if(empty($params['custom_query']))
            {
				$query = array();

				if(!empty($params['categories']))
				{
					//get the portfolio categories
					$terms 	= explode(',', $params['categories']);
				}

				$page = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : get_query_var( 'page' );
				if(!$page) $page = 1;

				//if we find no terms for the taxonomy fetch all taxonomy terms
				if(empty($terms[0]) || is_null($terms[0]) || $terms[0] === "null")
				{
					$terms = array();
					$allTax = get_terms( $params['taxonomy']);
					foreach($allTax as $tax)
					{
						$terms[] = $tax->term_id;
					}
				}
				
				
					$post_types = get_post_types();
									
					$query = array(	'orderby' 	=> 'date',
									'order' 	=> 'DESC',
									'paged' 	=> $page,
									'post_type' => $post_types,
									'offset'	=> $params['offset'],
									'posts_per_page' => $params['items'],
									'tax_query' => array( 	array( 	'taxonomy' 	=> $params['taxonomy'],
																	'field' 	=> 'id',
																	'terms' 	=> $terms,
																	'operator' 	=> 'IN')));
				
					
																
					
			}
			else
			{
				$query = $params['custom_query'];
			}


			$query = apply_filters('avia_masonry_entries_query', $query, $params);

			$this->entries = new WP_Query( $query );
			$this->prepare_loop_from_entries();
		}
		
		
		public function query_entries_by_id($params = array())
		{
			global $avia_config;

			if(empty($params)) $params = $this->atts;
			
			$ids = is_array($this->atts['ids']) ? $this->atts['ids'] : array_filter(explode(',',$this->atts['ids']));
			
			$page = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : get_query_var( 'page' );
			if(!$page) $page = 1;
			
			$query = array(
				'post__in' => $ids,
				'post_status' => 'inherit',
				'post_type' => 'attachment',
				'post_mime_type' => 'image',
				'paged' 	=> $page,
				'order' => 'ASC',
				'offset'	=> $params['offset'],
				'posts_per_page' => $params['items'],
				'orderby' => 'post__in'
			);
			
			
			$query = apply_filters('avia_masonry_entries_query', $query, $params);

			$this->entries = new WP_Query( $query );
			$this->prepare_loop_from_entries();
			
			
		}
	}
}
















