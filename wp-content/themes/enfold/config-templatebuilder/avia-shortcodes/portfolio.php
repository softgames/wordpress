<?php
/**
 * Sidebar
 * Displays one of the registered Widget Areas of the theme
 */

if ( !class_exists( 'avia_sc_portfolio' ) )
{
	class avia_sc_portfolio extends aviaShortcodeTemplate
	{
			/**
			 * Create the config array for the shortcode button
			 */
			function shortcode_insert_button()
			{
				$this->config['name']		= __('Portfolio Grid', 'avia_framework' );
				$this->config['tab']		= __('Content Elements', 'avia_framework' );
				$this->config['icon']		= AviaBuilder::$path['imagesURL']."sc-portfolio.png";
				$this->config['order']		= 39;
				$this->config['target']		= 'avia-target-insert';
				$this->config['shortcode'] 	= 'av_portfolio';
				$this->config['tooltip'] 	= __('Creates a grid of portfolio excerpts', 'avia_framework' );
			}

			function extra_assets()
			{
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

					array(	"name" 		=> __("Which categories should be used for the portfolio?", 'avia_framework' ),
							"desc" 		=> __("You can select multiple categories here. The Page will then show posts from only those categories.", 'avia_framework' ),
				            "id" 		=> "categories",
				            "type" 		=> "select",
	        				"multiple"	=> 6,
	        				"taxonomy" 	=> "portfolio_entries",
				            "subtype" 	=> "cat"),
				    /*
						array(
							"name" 	=> __("Style?", 'avia_framework' ),
							"desc" 	=> __("Choose the style of the entries here", 'avia_framework' ),
							"id" 	=> "style",
							"type" 	=> "select",
							"std" 	=> "",
							"subtype" => array( __('Default Style', 'avia_framework' ) => '',
												__('Circle Image Stlye',  'avia_framework' )=>'grid-circle')),
					*/

					array(
							"name" 	=> __("Columns", 'avia_framework' ),
							"desc" 	=> __("How many columns should be displayed?", 'avia_framework' ),
							"id" 	=> "columns",
							"type" 	=> "select",
							"std" 	=> "4",
							"subtype" => array(	__('1 Column',  'avia_framework' )=>'1',
												__('2 Columns', 'avia_framework' )=>'2',
												__('3 Columns', 'avia_framework' )=>'3',
												__('4 Columns', 'avia_framework' )=>'4',
												/* __('5 Columns', 'avia_framework' )=>'5', */
												)),

                    array(
                        "name" 	=> __("1 Column layout", 'avia_framework' ),
                        "desc" 	=> __("Choose the 1 column layout", 'avia_framework' ),
                        "id" 	=> "one_column_template",
                        "type" 	=> "select",
                        "required" 	=> array('columns','equals','1'),
                        "std" 	=> "special",
                        "subtype" => array(
                            __('Use special 1 column layout (side by side)',  'avia_framework' ) =>'special',
                            __('Use default portfolio layout',  'avia_framework' ) =>'default')),

					array(
							"name" 	=> __("Post Number", 'avia_framework' ),
							"desc" 	=> __("How many items should be displayed per page?", 'avia_framework' ),
							"id" 	=> "items",
							"type" 	=> "select",
							"std" 	=> "16",
							"subtype" => AviaHtmlHelper::number_array(1,100,1, array('All'=>'-1'))),

					array(
							"name" 	=> __("Excerpt", 'avia_framework' ),
							"desc" 	=> __("Display Excerpt and Title below the preview image?", 'avia_framework' ),
							"id" 	=> "contents",
							"type" 	=> "select",
							"std" 	=> "yes",
							"subtype" => array(
								__('Title and Excerpt',  'avia_framework' ) =>'excerpt',
								__('Only Title',  'avia_framework' ) =>'title',
								__('Only excerpt',  'avia_framework' ) =>'only_excerpt',
								__('No Title and no excerpt',  'avia_framework' ) =>'no')),

					array(
                        "name" 	=> __("Portfolio Grid Image Size", 'avia_framework' ),
                        "desc" 	=> __("Set the image size of the Portfolio Grid images", 'avia_framework' ),
                        "id" 	=> "preview_mode",
                        "type" 	=> "select",
                        "std" 	=> "auto",
                        "subtype" => array(__('Set the Portfolio Grid image size automatically based on column or layout width','avia_framework' ) =>'auto',__('Choose the Portfolio Grid image size manually (select thumbnail size)','avia_framework' ) =>'custom')),

                    array(
                        "name" 	=> __("Select custom image size", 'avia_framework' ),
                        "desc" 	=> __("Choose image size for Portfolio Grid Images", 'avia_framework' ) . "<br/><small>" . __("(Note: Images will be scaled to fit for the amount of columns chosen above)", 'avia_framework' )."</small>",
                        "id" 	=> "image_size",
                        "type" 	=> "select",
                        "required" 	=> array('preview_mode','equals','custom'),
                        "std" 	=> "portfolio",
                        "subtype" =>  AviaHelper::get_registered_image_sizes(array('logo','thumbnail','widget'))
                    ),

					array(
							"name" 	=> __("Link Handling", 'avia_framework' ),
							"desc" 	=> __("When clicking on a portfolio item you can choose to open the link to the single entry, open a preview (aka AJAX Portfolio) or show a bigger version of the image in a lightbox overlay", 'avia_framework' ),
							"id" 	=> "linking",
							"type" 	=> "select",
							"std" 	=> "",
							"subtype" => array(
								__('Open the entry on a new page',  'avia_framework' ) =>'',
								__('Open a preview of the entry (known as AJAX Portfolio)',  'avia_framework' ) =>'ajax',
								__('Display the big image in a lightbox',  'avia_framework' ) =>'lightbox')),

					array(
							"name" 	=> __("Sortable?", 'avia_framework' ),
							"desc" 	=> __("Should the sorting options based on categories be displayed?", 'avia_framework' ),
							"id" 	=> "sort",
							"type" 	=> "select",
							"std" 	=> "yes",
							"subtype" => array(
								__('yes',  'avia_framework' ) => 'yes',
								__('no',  'avia_framework' )  => 'no')),

					array(
							"name" 	=> __("Pagination", 'avia_framework' ),
							"desc" 	=> __("Should a pagination be displayed?", 'avia_framework' ),
							"id" 	=> "paginate",
							"type" 	=> "select",
							"std" 	=> "yes",
							"subtype" => array(
								__('yes',  'avia_framework' ) =>'yes',
								__('no',  'avia_framework' ) =>'no')),

				);
				
				
			        if(current_theme_supports('avia_template_builder_custom_post_type_grid'))
			        {
			            $this->elements[0] = array(
			                    "name" 	=> __("Which Entries?", 'avia_framework' ),
			                    "desc" 	=> __("Select which entries should be displayed by selecting a taxonomy", 'avia_framework' ),
			                    "id" 	=> "link",
			                    "fetchTMPL"	=> true,
			                    "type" 	=> "linkpicker",
			                    "subtype"  => array( __('Display Entries from:',  'avia_framework' )=>'taxonomy'),
			                    "multiple"	=> 6,
			                    "std" 	=> "category");
			        }

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
				$params['content'] 	 = NULL; //remove to allow content elements

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
				$atts['class'] = !empty($meta['custom_class']) ? $meta['custom_class'] : "";
				
				if(current_theme_supports('avia_template_builder_custom_post_type_grid'))
				{
				    if(isset($atts['link']))
				    {
				        $atts['link'] = explode(',', $atts['link'], 2 );
				        $atts['taxonomy'] = $atts['link'][0];
				
				        if(isset($atts['link'][1]))
				        {
				            $atts['categories'] = $atts['link'][1];
				        }
				    }
				
				    $post_types = get_post_types();
				    $atts['post_type'] = $post_types;
				}


				$grid = new avia_post_grid($atts);
				$grid->query_entries();
				return $grid->html();
			}
		}
}




if ( !class_exists( 'avia_post_grid' ) )
{
	class avia_post_grid
	{
		static  $grid = 0;
		static  $preview_template = array();
		protected $atts;
		protected $entries;

		function __construct($atts = array())
		{
			$this->atts = shortcode_atts(array(	'style'		=> '',
										 		'linking' 	=> '',
										 		'columns' 	=> '4',
		                                 		'items' 	=> '16',
		                                 		'contents' 	=> 'title',
		                                 		'sort' 		=> 'yes',
		                                 		'paginate' 	=> 'yes',
		                                 		'categories'=> '',
		                                 		'preview_mode' => 'auto',
                                                'image_size' => 'portfolio',
		                                 		'post_type'	=> 'portfolio',
		                                 		'taxonomy'  => 'portfolio_entries',
		                                 		'one_column_template' => 'special',
		                                 		'set_breadcrumb' => true, //no shortcode option for this, modifies the breadcrumb nav, must be false on taxonomy overview
		                                 		'class'		=> ""
		                                 		), $atts);



		    if($this->atts['linking'] == 'ajax')
				add_action('wp_footer' , array($this, 'print_preview_templates'));
		}

		//generates the html of the post grid
		public function html()
		{
			if(empty($this->entries) || empty($this->entries->posts)) return;

			avia_post_grid::$grid ++;
			extract($this->atts);

			$container_id 		= avia_post_grid::$grid;
			$extraClass 		= 'first';
			$grid 				= 'one_fourth';
			if($preview_mode == 'auto') $image_size = 'portfolio';
			$post_loop_count 	= 1;
			$loop_counter		= 1;
			$output				= "";
			$style_class		= empty($style) ? 'no_margin' : $style;
			$total				= $this->entries->post_count % 2 ? "odd" : "even";

			if($set_breadcrumb && is_page())
			{
				$_SESSION["avia_{$post_type}"] = get_the_ID();
			}

			switch($columns)
			{
				case "1": $grid = 'av_fullwidth';  if($preview_mode == 'auto') $image_size = 'featured'; break;
				case "2": $grid = 'av_one_half';   break;
				case "3": $grid = 'av_one_third';  break;
				case "4": $grid = 'av_one_fourth'; if($preview_mode == 'auto') $image_size = 'portfolio_small'; break;
				case "5": $grid = 'av_one_fifth';  if($preview_mode == 'auto') $image_size = 'portfolio_small'; break;
			}

			$output .= $sort == "yes" ? $this->sort_buttons($this->entries->posts, $this->atts) : "";

			if($linking == "ajax")
			{
			global $avia_config;

			$output .= "<div class='portfolio_preview_container' data-portfolio-id='{$container_id}'>
							<div class='ajax_controlls iconfont'>
								<a href='#prev' class='ajax_previous' 	".av_icon_string('prev')."></a>
								<a href='#next' class='ajax_next'		".av_icon_string('next')."></a>
								<a class='avia_close' href='#close'		".av_icon_string('close')."></a>
							</div>
							<div class='portfolio-details-inner'></div>
						</div>";
			}
			$output .= "<div class='{$class} grid-sort-container isotope {$style_class}-container with-{$contents}-container grid-total-{$total} grid-col-{$columns} grid-links-{$linking}' data-portfolio-id='{$container_id}'>";

			foreach ($this->entries->posts as $entry)
			{
				$the_id 	= $entry->ID;
				$parity		= $post_loop_count % 2 ? 'odd' : 'even';
				$last       = $this->entries->post_count == $post_loop_count ? " post-entry-last " : "";
				$post_class = "post-entry post-entry-{$the_id} grid-entry-overview grid-loop-{$post_loop_count} grid-parity-{$parity} {$last}";
				$sort_class = $this->sort_cat_string($the_id, $this->atts);

				switch($linking)
				{
					case "lightbox":  $link = wp_get_attachment_image_src(get_post_thumbnail_id($the_id), 'large'); $link = $link[0];	break;
					default: 		  $link = get_permalink($the_id); break;
				}

				$title_link  = get_permalink($the_id);
				$custom_link = get_post_meta( $the_id ,'_portfolio_custom_link', true) != "" ? get_post_meta( $the_id ,'_portfolio_custom_link_url', true) : false;

				if($custom_link)
				{
					$title_link = $link = $custom_link;
				}

				$excerpt 	= '';
				$title 		= '';

				switch($contents)
				{
					case "excerpt": $excerpt = $entry->post_excerpt; $title = $entry->post_title; break;
					case "title": $excerpt = ''; $title = $entry->post_title;  break;
					case "only_excerpt": $excerpt = $entry->post_excerpt; $title = ''; break;
					case "no": $excerpt = ''; $title = ''; break;
				}

				$custom_overlay = apply_filters('avf_portfolio_custom_overlay', "", $entry);
				$link_markup 	= apply_filters('avf_portfolio_custom_image_container', array("a href='{$link}' title='".esc_attr(strip_tags($title))."' ",'a'), $entry);

				$title 			= apply_filters('avf_portfolio_title', $title, $entry);
				$title_link     = apply_filters('avf_portfolio_title_link', $title_link, $entry);

                if($columns == "1" && $one_column_template == 'special')
                {
                    $extraClass .= ' special_av_fullwidth ';

                    $output .= "<div data-ajax-id='{$the_id}' class=' grid-entry flex_column isotope-item all_sort {$style_class} {$post_class} {$sort_class} {$grid} {$extraClass}'>";
                    $output .= "<article class='main_color inner-entry' ".avia_markup_helper(array('context' => 'entry','echo'=>false)).">";
                    $output .= apply_filters('avf_portfolio_extra', "", $entry);

                    $output .= "<div class='av_table_col first portfolio-entry grid-content'>";

                    if(!empty($title))
                    {
                        $markup = avia_markup_helper(array('context' => 'entry_title','echo'=>false));
                        $output .= '<header class="entry-content-header">';
                        $output .= "<h2 class='portfolio-grid-title entry-title' $markup><a href='{$title_link}'>".$title."</a></h2>";
                        $output .= '</header>';
                    }

                    if(!empty($excerpt))
                    {
                        $markup = avia_markup_helper(array('context' => 'entry_content','echo'=>false));

                        $output .= "<div class='entry-content-wrapper'>";
                        $output .= "<div class='grid-entry-excerpt entry-content' $markup>".$excerpt."</div>";
                        $output .= "</div>";
                    }
                    $output .= '<div class="avia-arrow"></div>';
                    $output .= "</div>";

                    $image = get_the_post_thumbnail( $the_id, $image_size );
                    if(!empty($image))
                    {
                        $output .= "<div class='av_table_col portfolio-grid-image'>";
                        $output .= "<".$link_markup[0]." data-rel='grid-".avia_post_grid::$grid."' class='grid-image avia-hover-fx'>".$custom_overlay.$image."</".$link_markup[1].">";
                        $output .= "</div>";
                    }
                    $output .= '<footer class="entry-footer"></footer>';
                    $output .= "</article>";
                    $output .= "</div>";
                }
                else
                {
                    $extraClass .= ' default_av_fullwidth ';

                    $output .= "<div data-ajax-id='{$the_id}' class=' grid-entry flex_column isotope-item all_sort {$style_class} {$post_class} {$sort_class} {$grid} {$extraClass}'>";
                    $output .= "<article class='main_color inner-entry' ".avia_markup_helper(array('context' => 'entry','echo'=>false)).">";
                    $output .= apply_filters('avf_portfolio_extra', "", $entry);
                    $output .= "<".$link_markup[0]." data-rel='grid-".avia_post_grid::$grid."' class='grid-image avia-hover-fx'>".$custom_overlay.get_the_post_thumbnail( $the_id, $image_size )."</".$link_markup[1].">";
                    $output .= !empty($title) || !empty($excerpt) ? "<div class='grid-content'><div class='avia-arrow'></div>" : '';

                    if(!empty($title))
                    {
                        $markup = avia_markup_helper(array('context' => 'entry_title','echo'=>false));
                        $output .= '<header class="entry-content-header">';
                        $output .= "<h3 class='grid-entry-title entry-title' $markup><a href='{$title_link}' title='".esc_attr(strip_tags($title))."'>".$title."</a></h3>";
                        $output .= '</header>';
                    }
                    $output .= !empty($excerpt) ? "<div class='grid-entry-excerpt entry-content' ".avia_markup_helper(array('context'=>'entry_content','echo'=>false)).">".$excerpt."</div>" : '';
                    $output .= !empty($title) || !empty($excerpt) ? "</div>" : '';
                    $output .= '<footer class="entry-footer"></footer>';
                    $output .= "</article>";
                    $output .= "</div>";
                }


				$loop_counter ++;
				$post_loop_count ++;
				$extraClass = "";

				if($loop_counter > $columns)
				{
					$loop_counter = 1;
					$extraClass = 'first';
				}
			}

			$output .= "</div>";

			//append pagination
			if($paginate == "yes" && $avia_pagination = avia_pagination($this->entries->max_num_pages, 'nav')) $output .= "<div class='pagination-wrap pagination-{$post_type}'>{$avia_pagination}</div>";

			return $output;
		}

		//generates the html for the sort buttons
		protected function sort_buttons($entries, $params)
		{
			//get all categories that are actually listed on the page
			$categories = get_categories(array(
				'taxonomy'	=> $params['taxonomy'],
				'hide_empty'=> 0
			));

			$current_page_cats 	= array();
			$cat_count 			= array();
			$display_cats 		= is_array($params['categories']) ? $params['categories'] : array_filter(explode(',',$params['categories']));

			foreach ($entries as $entry)
			{
				if($current_item_cats = get_the_terms( $entry->ID, $params['taxonomy'] ))
				{
					if(!empty($current_item_cats))
					{
						foreach($current_item_cats as $current_item_cat)
						{
							if(empty($display_cats) || in_array($current_item_cat->term_id, $display_cats))
							{
								$current_page_cats[$current_item_cat->term_id] = $current_item_cat->term_id;

								if(!isset($cat_count[$current_item_cat->term_id] ))
								{
									$cat_count[$current_item_cat->term_id] = 0;
								}

								$cat_count[$current_item_cat->term_id] ++;
							}
						}
					}
				}
			}

			$output = "<div class='sort_width_container' data-portfolio-id='".avia_post_grid::$grid."' ><div id='js_sort_items' >";
			$hide 	= count($current_page_cats) <= 1 ? "hidden" : "";


			$first_item_name = apply_filters('avf_portfolio_sort_first_label', __('All','avia_framework' ), $params);
			$output .= apply_filters('avf_portfolio_sort_heading', "", $params);
			$output .= "<div class='sort_by_cat {$hide} '>";
			$output .= '<a href="#" data-filter="all_sort" class="all_sort_button active_sort"><span class="inner_sort_button"><span>'.$first_item_name.'</span><small class="av-cat-count"> '.count($entries).' </small></span></a>';


			foreach($categories as $category)
			{
				if(in_array($category->term_id, $current_page_cats))
				{
					//fix for cyrillic, etc. characters - isotope does not support the % char
					$category->category_nicename = str_replace('%', '', $category->category_nicename);

					$output .= 	"<span class='text-sep ".$category->category_nicename."_sort_sep'>/</span>";
					$output .= 		'<a href="#" data-filter="'.$category->category_nicename.'_sort" class="'.$category->category_nicename.'_sort_button" ><span class="inner_sort_button">';
					$output .= 			"<span>".esc_html(trim($category->cat_name))."</span>";
					$output .= 			"<small class='av-cat-count'> ".$cat_count[$category->term_id]." </small></span>";
					$output .= 		"</a>";
				}
			}

			$output .= "</div></div></div>";

			return $output;
		}


		//get the categories for each post and create a string that serves as classes so the javascript can sort by those classes
		protected function sort_cat_string($the_id, $params)
		{
			$sort_classes = "";
			$item_categories = get_the_terms( $the_id, $params['taxonomy']);

			if(is_object($item_categories) || is_array($item_categories))
			{
				foreach ($item_categories as $cat)
				{
					//fix for cyrillic, etc. characters - isotope does not support the % char
					$cat->slug = str_replace('%', '', $cat->slug);
					
					$sort_classes .= $cat->slug.'_sort ';
				}
			}

			return $sort_classes;
		}

		protected function build_preview_template( $entry )
		{
			if(isset(avia_post_grid::$preview_template[$entry->ID])) return;
			avia_post_grid::$preview_template[$entry->ID] = true;

			$id 					= $entry->ID;
			$output 				= "";
			$defaults 				= array( 'ids' => get_post_thumbnail_id( $id ), 'text' => apply_filters( 'get_the_excerpt', $entry->post_excerpt) , "method" => 'gallery' , "auto" => "", "columns" => 5);
			$params['ids'] 			= get_post_meta( $id ,'_preview_ids', true);
			$params['text']		  	= get_post_meta( $id ,'_preview_text', true);
			$params['method']	  	= get_post_meta( $id ,'_preview_display', true);
			$params['interval']		= get_post_meta( $id ,'_preview_autorotation', true);
			$params['columns']      = get_post_meta( $id ,'_preview_columns', true);
			$params['preview_size'] = apply_filters('avf_ajax_preview_image_size',"gallery");
			$params['autoplay']		= is_numeric($params['interval']) ? "true" : "false";

			$link = get_post_meta( $id ,'_portfolio_custom_link', true) != "" ? get_post_meta( $id ,'_portfolio_custom_link_url', true) : get_permalink($id);


			//merge default and params array. remove empty params with array_filter
			$params = array_merge($defaults, array_filter($params));

			//set the content
			$content = str_replace(']]>', ']]&gt;', apply_filters('the_content', $params['text'] )); unset($params['text']);

			//set images
			$string = "";

			//set first class if preview images are deactivated
			$nogalleryclass = '';
			$params['ajax_request'] = true;
			switch($params['method'])
			{
				case 'gallery':

					$params['style'] =  "big_thumb";
					foreach($params as $key => $param) $string .= $key."='".$param."' ";
					$images = do_shortcode("[av_gallery {$string}]");
				break;

				case 'slideshow':
					$params['size'] = $params['preview_size'];
					foreach($params as $key => $param) $string .= $key."='".$param."' ";
					$images = do_shortcode("[av_slideshow {$string}]");
				break;

				case 'list':
					$images = $this->post_images($params['ids']);
				break;

				case 'no':
					$images = false;
					$nogalleryclass = ' no_portfolio_preview_gallery ';
				break;
			}

			$output .= "<div class='ajax_slide ajax_slide_{$id}' data-slide-id='{$id}' >";

				$output .= "<article class='inner_slide $nogalleryclass' ".avia_markup_helper(array('context' => 'entry','echo'=>false)).">";

				if(!empty($images))
				{
					$output .= "<div class='av_table_col first portfolio-preview-image'>";
					$output .= $images;
					$output .= "</div>";
				}

				if(!empty($nogalleryclass)) $nogalleryclass .= ' first ';

					$output .= "<div class='av_table_col $nogalleryclass portfolio-entry portfolio-preview-content'>";

                        $markup = avia_markup_helper(array('context' => 'entry_title','echo'=>false));
                        $output .= '<header class="entry-content-header">';
						$output .= "<h2 class='portfolio-preview-title entry-title' $markup><a href='{$link}'>".$entry->post_title."</a></h2>";
                        $output .= '</header>';

						$output .= "<div class='entry-content-wrapper entry-content' ".avia_markup_helper(array('context' => 'entry_content','echo'=>false)).">";
						$output .= $content;
						$output .= "</div>";
						$output .= "<span class='avia-arrow'></span>";
					$output .= "</div>";

                $output .= '<footer class="entry-footer"></footer>';
				$output .= "</article>";

			$output .= "</div>";

		return "<script type='text/html' id='avia-tmpl-portfolio-preview-{$id}'>\n{$output}\n</script>\n\n";

		}

		protected function post_images($ids)
		{
			if(empty($ids)) return;

			$attachments = get_posts(array(
				'include' => $ids,
				'post_status' => 'inherit',
				'post_type' => 'attachment',
				'post_mime_type' => 'image',
				'order' => 'ASC',
				'orderby' => 'post__in')
				);

			$output = "";

			foreach($attachments as $attachment)
			{
				$img	 = wp_get_attachment_image_src($attachment->ID, 'large');

                $alt = get_post_meta($attachment->ID, '_wp_attachment_image_alt', true);
                $alt = !empty($alt) ? esc_attr($alt) : '';
                $title = trim($attachment->post_title) ? esc_attr($attachment->post_title) : "";
                $description = trim($attachment->post_content) ? esc_attr($attachment->post_content) : "";

				$output .= " <a href='".$img[0]."' class='portolio-preview-list-image' title='".$description."' ><img src='".$img[0]."' title='".$title."' alt='".$alt."' /></a>";
			}

			return $output;
		}




		public function print_preview_templates()
		{
			foreach ($this->entries->posts as $entry)
			{
				echo $this->build_preview_template( $entry );
			}
		}



		//fetch new entries
		public function query_entries($params = array())
		{
			$query = array();
			if(empty($params)) $params = $this->atts;

			if(!empty($params['categories']))
			{
				//get the portfolio categories
				$terms 	= explode(',', $params['categories']);
			}

			$page = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : get_query_var( 'page' );
			if(!$page) $page = 1;

			//if we find categories perform complex query, otherwise simple one
			if(isset($terms[0]) && !empty($terms[0]) && !is_null($terms[0]) && $terms[0] != "null")
			{
				$query = array(	'orderby' 	=> 'post_date',
								'order' 	=> 'DESC',
								'paged' 	=> $page,
								'posts_per_page' => $params['items'],
								'post_type' => $params['post_type'],
								'tax_query' => array( 	array( 	'taxonomy' 	=> $params['taxonomy'],
																'field' 	=> 'id',
																'terms' 	=> $terms,
																'operator' 	=> 'IN')));
			}
			else
			{
				$query = array(	'paged'=> $page, 'posts_per_page' => $params['items'], 'post_type' => $params['post_type']);
			}

			$query = apply_filters('avia_post_grid_query', $query, $params);

			$this->entries = new WP_Query( $query );

		}


		//function that allows to set the query to an existing post query. usually only needed on pages that already did a query for the entries, like taxonomy archive pages.
		//Shortcode uses the query_entries function above
		public function use_global_query()
		{
			global $wp_query;
			$this->entries = $wp_query;
		}



	}
}


/*
Example: how to order posts randomly on page load. put this into functions.php

add_filter('avia_post_grid_query','avia_order_by_random');
function avia_order_by_random($query)
{
	$query['orderby'] = 'rand';
	return $query;
}
*/
