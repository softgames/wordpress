<?php

/*
 * The function within this file are theme specific:
 * they are used only by this theme and not by the Avia Framework in general
 */

/* wrap embeds into a proportion containing div */

if(!function_exists('avia_iframe_proportion_wrap'))
{
	add_filter( 'embed_oembed_html', 'avia_iframe_proportion_wrap', 10, 4 );

	function avia_iframe_proportion_wrap ( $html, $url, $attr, $post_ID  )
	{
		if(strpos($html, '<iframe') !== false)
		{
			$html = "<div class='avia-iframe-wrap'>{$html}</div>";
		}
	    return $html;
	}
}



/* GOOGLE AUTHOR */
if(!function_exists('avia_google_author_image'))
{
	function avia_google_author_image()
	{
		$icons = avia_get_option('social_icons');
		if(is_array($icons) && !empty($icons))
		{
			foreach($icons as $icon) if($icon['social_icon'] == 'gplus') $url = $icon['social_icon_link'];
		}

		if(!empty($url))
		{
			//add author url as described here: http://yoast.com/push-rel-author-head/
			echo '<link rel="author" href="'.$url.'"/>';
		}

	}
}

add_action( 'wp_head', 'avia_google_author_image' );




/* AJAX SEARCH */
if(!function_exists('avia_append_search_nav'))
{
	//first append search item to main menu
	add_filter( 'wp_nav_menu_items', 'avia_append_search_nav', 10, 2 );
	add_filter( 'avf_fallback_menu_items', 'avia_append_search_nav', 10, 2 );

	function avia_append_search_nav ( $items, $args )
	{	
		if(avia_get_option('header_searchicon','header_searchicon') != "header_searchicon") return $items;
	
	    if ((is_object($args) && $args->theme_location == 'avia') || (is_string($args) && $args = "fallback_menu"))
	    {
	        global $avia_config;
	        ob_start();
	        get_search_form();
	        $form =  htmlspecialchars(ob_get_clean()) ;

	        $items .= '<li id="menu-item-search" class="noMobile menu-item menu-item-search-dropdown"><a href="?s=" rel="nofollow" data-avia-search-tooltip="'.$form.'" '.av_icon_string('search').'></a></li>';
	    }
	    return $items;
	}
}




if(!function_exists('avia_ajax_search'))
{
	//now hook into wordpress ajax function to catch any ajax requests
	add_action( 'wp_ajax_avia_ajax_search', 'avia_ajax_search' );
	add_action( 'wp_ajax_nopriv_avia_ajax_search', 'avia_ajax_search' );

	function avia_ajax_search()
	{
	    global $avia_config;
		
	    unset($_REQUEST['action']);
	    if(empty($_REQUEST['s'])) $_REQUEST['s'] = array_shift(array_values($_REQUEST));
		if(empty($_REQUEST['s'])) die();
		

	    $defaults = array('numberposts' => 5, 'post_type' => 'any', 'post_status' => 'publish', 'post_password' => '', 'suppress_filters' => false);
	    $_REQUEST['s'] = apply_filters( 'get_search_query', $_REQUEST['s']);

	    $search_parameters 	= array_merge($defaults, $_REQUEST);
	    $search_query 		= apply_filters('avf_ajax_search_query', http_build_query($search_parameters));
	    $query_function     = apply_filters('avf_ajax_search_function', 'get_posts', $search_query, $search_parameters, $defaults);
	    $posts		= (($query_function == 'get_posts') || !function_exists($query_function))  ? get_posts($search_query) : $query_function($search_query, $search_parameters, $defaults);
	
	    $search_messages = array(
	            'no_criteria_matched' => __("Sorry, no posts matched your criteria", 'avia_framework'),
	            'another_search_term' => __("Please try another search term", 'avia_framework'),
	            'time_format'         => get_option('date_format'),
	            'all_results_link'    => http_build_query($_REQUEST),
	            'view_all_results'    => __('View all results','avia_framework')
            );
		
	    $search_messages = apply_filters('avf_ajax_search_messages', $search_messages, $search_query);
		
	    if(empty($posts))
	    {
	        $output  = "<span class='ajax_search_entry ajax_not_found'>";
	        $output .= "<span class='ajax_search_image ".av_icon_string('info')."'>";
	        $output .= "</span>";
	        $output .= "<span class='ajax_search_content'>";
	        $output .= "    <span class='ajax_search_title'>";
            	$output .= $search_messages['no_criteria_matched'];
	        $output .= "    </span>";
	        $output .= "    <span class='ajax_search_excerpt'>";
            	$output .= $search_messages['another_search_term'];
	        $output .= "    </span>";
	        $output .= "</span>";
	        $output .= "</span>";
	        echo $output;
	        die();
	    }

	    //if we got posts resort them by post type
	    $output = "";
	    $sorted = array();
	    $post_type_obj = array();
	    foreach($posts as $post)
	    {
	        $sorted[$post->post_type][] = $post;
	        if(empty($post_type_obj[$post->post_type]))
	        {
	            $post_type_obj[$post->post_type] = get_post_type_object($post->post_type);
	        }
	    }



	    //now we got everything we need to preapre the output
	    foreach($sorted as $key => $post_type)
	    {
	        if(isset($post_type_obj[$key]->labels->name))
	        {
                $label = apply_filters('avf_ajax_search_label_names', $post_type_obj[$key]->labels->name);
	            $output .= "<h4>".$label."</h4>";
	        }
	        else
	        {
	            $output .= "<hr />";
	        }

	        foreach($post_type as $post)
	        {
	            $image = get_the_post_thumbnail( $post->ID, 'thumbnail' );

	            $extra_class = $image ? "with_image" : "";
	            $post_type   = $image ? "" : get_post_format($post->ID) != "" ? get_post_format($post->ID) : "standard";
	            $iconfont    = $image ? "" : av_icon_string($post_type);
	            $excerpt     = "";

	            if(!empty($post->post_excerpt))
	            {
	                 $excerpt =  apply_filters( 'avf_ajax_search_excerpt', avia_backend_truncate($post->post_excerpt,70," ","...", true, '', true) );
	            }
	            else
	            {
	                 $excerpt = get_the_time($search_messages['time_format'], $post->ID);
	            }

	            $link = apply_filters('av_custom_url', get_permalink($post->ID), $post);

	            $output .= "<a class ='ajax_search_entry {$extra_class}' href='".$link."'>";
	            $output .= "<span class='ajax_search_image' {$iconfont}>";
	            $output .= $image;
	            $output .= "</span>";
	            $output .= "<span class='ajax_search_content'>";
	            $output .= "    <span class='ajax_search_title'>";
	            $output .=      get_the_title($post->ID);
	            $output .= "    </span>";
	            $output .= "    <span class='ajax_search_excerpt'>";
	            $output .=      $excerpt;
	            $output .= "    </span>";
	            $output .= "</span>";
	            $output .= "</a>";
	        }
	    }

	    $output .= "<a class='ajax_search_entry ajax_search_entry_view_all' href='".home_url('?' . $search_messages['all_results_link'] )."'>".$search_messages['view_all_results']."</a>";

	    echo $output;
	    die();
	}
}


if(!function_exists('avia_social_widget_icon'))
{
	/*modify twitter social count widget and add social icons as iconfont*/
	add_filter('avf_social_widget', 'avia_social_widget_icon',2,2);

	function avia_social_widget_icon($content, $icon)
	{
		global $avia_config;

		$content = "<span class='social_widget_icon' ".av_icon_string($icon)."></span>".$content;
		return $content;
	}
}





//call functions for the theme
add_filter('the_content_more_link', 'avia_remove_more_jump_link');
add_post_type_support('page', 'excerpt');




//allow mp4, webm and ogv file uploads
if(!function_exists('avia_upload_mimes'))
{
	add_filter('upload_mimes','avia_upload_mimes');
	function avia_upload_mimes($mimes){ return array_merge($mimes, array ('mp4' => 'video/mp4', 'ogv' => 'video/ogg', 'webm' => 'video/webm')); }
}




//change default thumbnail size and fullwidth size on theme activation
if(!function_exists('avia_set_thumb_size'))
{
	add_action('avia_backend_theme_activation', 'avia_set_thumb_size');
	function avia_set_thumb_size()
	{
		update_option( 'thumbnail_size_h', 80 ); update_option( 'thumbnail_size_w', 80 );
		update_option( 'large_size_w', 1030 ); 	 update_option( 'large_size_h', 1030 );
	}
}




//add support for post thumbnails
add_theme_support( 'post-thumbnails' );




//advanced title + breadcrumb function
if(!function_exists('avia_title'))
{
	function avia_title($args = false, $id = false)
	{
		global $avia_config;

		if(!$id) $id = avia_get_the_id();

		$defaults 	 = array(

			'title' 		=> get_the_title($id),
			'subtitle' 		=> "", //avia_post_meta($id, 'subtitle'),
			'link'			=> get_permalink($id),
			'html'			=> "<div class='{class} title_container'><div class='container'><{heading} class='main-title entry-title'>{title}</{heading}>{additions}</div></div>",
			'class'			=> 'stretch_full container_wrap alternate_color '.avia_is_dark_bg('alternate_color', true),
			'breadcrumb'	=> true,
			'additions'		=> "",
			'heading'		=> 'h1' //headings are set based on this article: http://yoast.com/blog-headings-structure/
		);

		if ( is_tax() || is_category() || is_tag() )
		{
			global $wp_query;

			$term = $wp_query->get_queried_object();
			$defaults['link'] = get_term_link( $term );
		}
		else if(is_archive())
		{
			$defaults['link'] = "";
		}



		// Parse incomming $args into an array and merge it with $defaults
		$args = wp_parse_args( $args, $defaults );
		$args = apply_filters('avf_title_args', $args, $id);

		// OPTIONAL: Declare each item in $args as its own variable i.e. $type, $before.
		extract( $args, EXTR_SKIP );

		if(empty($title)) $class .= " empty_title ";
        $markup = avia_markup_helper(array('context' => 'avia_title','echo'=>false));
		if(!empty($link)) $title = "<a href='".$link."' rel='bookmark' title='".__('Permanent Link:','avia_framework')." ".esc_attr( $title )."' $markup>".$title."</a>";
		if(!empty($subtitle)) $additions .= "<div class='title_meta meta-color'>".wpautop($subtitle)."</div>";
		if($breadcrumb) $additions .= avia_breadcrumbs(array('separator' => '/', 'richsnippet' => true));


		$html = str_replace('{class}', $class, $html);
		$html = str_replace('{title}', $title, $html);
		$html = str_replace('{additions}', $additions, $html);
		$html = str_replace('{heading}', $heading, $html);



		if(!empty($avia_config['slide_output']) && !avia_is_dynamic_template($id) && !avia_is_overview())
		{
			$avia_config['small_title'] = $title;
		}
		else
		{
			return $html;
		}
	}
}



if(!function_exists('avia_post_nav'))
{
	function avia_post_nav($same_category = false, $taxonomy = 'category')
	{
		global $wp_version;
	        $settings = array();
	        $settings['same_category'] = $same_category;
	        $settings['excluded_terms'] = '';
		$settings['wpversion'] = $wp_version;
        
		//dont display if a fullscreen slider is available since they overlap 
		if((class_exists('avia_sc_layerslider') && !empty(avia_sc_layerslider::$slide_count)) || 
			class_exists('avia_sc_slider_full') && !empty(avia_sc_slider_full::$slide_count) ) $settings['is_fullwidth'] = true;

		$settings['type'] = get_post_type();
		$settings['taxonomy'] = ($settings['type'] == 'portfolio') ? 'portfolio_entries' : $taxonomy;

		if(!is_singular() || is_post_type_hierarchical($settings['type'])) $settings['is_hierarchical'] = true;
		if($settings['type'] === 'topic' || $settings['type'] === 'reply') $settings['is_bbpress'] = true;

	        $settings = apply_filters('avia_post_nav_settings', $settings);
	        if(!empty($settings['is_bbpress']) || !empty($settings['is_hierarchical']) || !empty($settings['is_fullwidth'])) return;
	
	        if(version_compare($settings['wpversion'], '3.8', '>=' ))
	        {
	            $entries['prev'] = get_previous_post($settings['same_category'], $settings['excluded_terms'], $settings['taxonomy']);
	            $entries['next'] = get_next_post($settings['same_category'], $settings['excluded_terms'], $settings['taxonomy']);
	        }
	        else
	        {
	            $entries['prev'] = get_previous_post($settings['same_category']);
	            $entries['next'] = get_next_post($settings['same_category']);
	        }
	        
		$entries = apply_filters('avia_post_nav_entries', $entries, $settings);
        	$output = "";


		foreach ($entries as $key => $entry)
		{
            if(empty($entry)) continue;

            $tc1   = $tc2 = "";
            $link  = get_permalink($entry->ID);
            $image = get_the_post_thumbnail($entry->ID, 'thumbnail');
            $class = $image ? "with-image" : "without-image";

            $output .= "<a class='avia-post-nav avia-post-{$key} {$class}' href='{$link}' >";
		    $output .= "    <span class='label iconfont' ".av_icon_string($key)."></span>";
		    $output .= "    <span class='entry-info-wrap'>";
		    $output .= "        <span class='entry-info'>";
		    $tc1     = "            <span class='entry-title'>".avia_backend_truncate(get_the_title($entry->ID),75," ")."</span>";
if($image)  $tc2     = "            <span class='entry-image'>{$image}</span>";
            $output .= $key == 'prev' ?  $tc1.$tc2 : $tc2.$tc1;
            $output .= "        </span>";
            $output .= "    </span>";
		    $output .= "</a>";
		}
		return $output;
	}
}



if(!function_exists('avia_legacy_websave_fonts'))
{
	add_filter('avia_style_filter', 'avia_legacy_websave_fonts');

	function avia_legacy_websave_fonts($styles)
	{
		global $avia_config;

		$os_info 	= avia_get_browser(false);
		$activate	= false;

		if('windows' == $os_info['platform'] && avia_get_option('websave_windows') == 'active')
		{
			if($os_info['shortname'] == 'MSIE' && $os_info['mainversion'] < 9) $activate = true;
			if($os_info['shortname'] == 'Firefox' && $os_info['mainversion'] < 8) $activate = true;
			if($os_info['shortname'] == 'Opera' && $os_info['mainversion'] < 11) $activate = true;

			if($activate == true)
			{
				foreach ($styles as $key => $style)
				{
					if($style['key'] == 'google_webfont')
					{
						if (strpos($style['value'], '-websave') !== false)
						{
							$websave = explode(',',$style['value']);
							$websave = strtolower(" ".$websave[0]);
							$websave = str_replace('"','',$websave);
							$websave = str_replace("'",'',$websave);
							$websave = str_replace("-websave",'',$websave);

							$avia_config['font_stack'] .= $websave.'-websave';
						}

					unset($styles[$key]);
					}
				}

			if(empty($avia_config['font_stack'])) $avia_config['font_stack'] = 'arial-websave';
			}
		}

		return $styles;
	}
}






//wrap ampersands into special calss to apply special styling

if(!function_exists('avia_ampersand'))
{
	add_filter('avia_ampersand','avia_ampersand');

	function avia_ampersand($content)
	{
		//ampersands
		$content = str_replace(" &amp; "," <span class='special_amp'>&amp;</span> ",$content);
		$content = str_replace(" &#038; "," <span class='special_amp'>&amp;</span> ",$content);
	
		
		// quotes
		$content = str_replace("“","<span class='special_amp'>“</span>",$content); // left double quotation mark “
		$content = str_replace("”","<span class='special_amp'>”</span>",$content); // right double quotation mark ”
		$content = str_replace("„","<span class='special_amp'>„</span>",$content); // double low-9 quotation mark „
		
		
		$content = str_replace("&#8220;","<span class='special_amp'>&#8220;</span>",$content); // left double quotation mark “
		$content = str_replace("&#8221;","<span class='special_amp'>&#8221;</span>",$content); // right double quotation mark ”
		$content = str_replace("&#8222;","<span class='special_amp'>&#8222;</span>",$content); // double low-9 quotation mark „

		return $content;
	}
}





// checks if a background color of a specific region is dark  or light and returns a class name
if(!function_exists('avia_is_dark_bg'))
{
	function avia_is_dark_bg($region, $return_only = false)
	{
		global $avia_config;

		$return = "";
		$color = $avia_config['backend_colors']['color_set'][$region]['bg'];

		$is_dark = avia_backend_calc_preceived_brightness($color, 70);

		$return = $is_dark ? "dark_bg_color" : "light_bg_color";
		if($return_only)
		{
			return $return;
		}
		else
		{
			echo $return;
		}
	}
}






//set post excerpt to be visible on theme acivation in user backend
if(!function_exists('avia_show_menu_description'))
{

	//add_action('avia_backend_theme_activation', 'avia_show_menu_description');
	function avia_show_menu_description()
	{
		global $current_user;
	    get_currentuserinfo();
		$old_meta_data = $meta_data = get_user_meta($current_user->ID, 'metaboxhidden_page', true);

		if(is_array($meta_data) && isset($meta_data[0]))
		{
			$key = array_search('postexcerpt', $meta_data);

			if($key !== false)
			{
				unset($meta_data[$key]);
				update_user_meta( $current_user->ID, 'metaboxhidden_page', $meta_data, $old_meta_data );
			}
		}
		else
		{
				update_user_meta( $current_user->ID, 'metaboxhidden_page', array('postcustom', 'commentstatusdiv', 'commentsdiv', 'slugdiv', 'authordiv', 'revisionsdiv') );
		}
	}
}




/*
* make google analytics code work, even if the user only enters the UA id. if the new async tracking code is entered add it to the header, else to the footer
*/

if(!function_exists('avia_get_tracking_code'))
{
	add_action('init', 'avia_get_tracking_code');

	function avia_get_tracking_code()
	{
		global $avia_config;

		$avia_config['analytics_code'] = avia_option('analytics', false, false, true);
		if(empty($avia_config['analytics_code'])) return;

		if(strpos($avia_config['analytics_code'],'UA-') === 0) // if we only get passed the UA-id create the script for the user (async tracking code)
		{

			$avia_config['analytics_code'] = "

<script type='text/javascript'>

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '".$avia_config['analytics_code']."']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

";
		}

		if(strpos($avia_config['analytics_code'],'_gaq.push') !== false) // check for async code (put at top)
		{
			add_action('wp_head', 'avia_print_tracking_code');
		}
		else // else print code in footer
		{
			add_action('wp_footer', 'avia_print_tracking_code');
		}
	}

	function avia_print_tracking_code()
	{
		global $avia_config;

		if(!empty($avia_config['analytics_code']))
		{
			echo $avia_config['analytics_code'];
		}
	}

}


/*
function that checks which header style we are using. In general the whole site has the same header active, based on the option in theme options->header
however, for the theme demo we need to showcase all headers, thats why we can simply add a custom field key of header_setting to overwrite the default heading
*/

if(!function_exists('avia_header_setting'))
{
	function avia_header_setting()
	{
		$header = avia_get_option('header_setting','fixed_header');

		$post_id = @get_the_ID();
		if($post_id)
		{
			$overwrite = get_post_meta($post_id, 'header_setting', true);
			if(!empty($overwrite)) $header = $overwrite;
		}

		return $header;
	}
}


/*
* Display a subnavigation for pages that is automatically generated, so the users doesnt need to work with widgets
*/
if(!function_exists('avia_sidebar_menu'))
{
    function avia_sidebar_menu($echo = true)
    {
        $sidebar_menu = "";

        $subNav = avia_get_option('page_nesting_nav');
  
        
        $the_id = @get_the_ID();
        $args 	= array();
		global $post;

        if($subNav && $subNav != 'disabled' && !empty($the_id) && is_page())
        {
            $subNav = false;
            $parent = $post->ID;
            $sidebar_menu = "";

            if (!empty($post->post_parent))
            {
                if(isset($post->ancestors)) $ancestors  = $post->ancestors;
                if(!isset($ancestors)) $ancestors  = get_post_ancestors($post->ID);
                $root		= count($ancestors)-1;
                $parent 	= $ancestors[$root];
            }

            $args = array('title_li'=>'', 'child_of'=>$parent, 'echo'=>0, 'sort_column'=>'menu_order, post_title');

            //enables user to change query args
            $args = apply_filters('avia_sidebar_menu_args', $args, $post);

            //hide or show child pages in menu - if the class is set to 'widget_nav_hide_child' the child pages will be hidden
            $display_child_pages = apply_filters('avia_sidebar_menu_display_child', 'widget_nav_hide_child', $args, $post);

            $children = wp_list_pages($args);

            if ($children)
            {
                $default_sidebar = false;
                $sidebar_menu .= "<nav class='widget widget_nav_menu $display_child_pages'><ul class='nested_nav'>";
                $sidebar_menu .= $children;
                $sidebar_menu .= "</ul></nav>";
            }
        }

        $sidebar_menu = apply_filters('avf_sidebar_menu_filter', $sidebar_menu, $args, $post);

        if($echo == true) { echo $sidebar_menu; } else { return $sidebar_menu; }
    }
}


/*
function that checks if updates for the theme are available
*/
if(!function_exists('avia_check_updates') && class_exists('avia_update_notifier'))
{
	function avia_check_updates()
	{
		if(class_exists('avia_update_notifier'))
        {
            $avia_update_notifier = new avia_update_notifier('http://www.kriesi.at/themes/wp-content/uploads/avia_xml/'.THEMENAME.'-Updates.xml');
        }
	}

	add_action('admin_menu', 'avia_check_updates', 1, 1);
}


/*
show tag archive page for post type - without this code you'll get 404 errors: http://wordpress.org/support/topic/custom-post-type-tagscategories-archive-page
*/
if(!function_exists('avia_fix_tag_archive_page'))
{
	function avia_fix_tag_archive_page($query)
	{
	    $post_types = get_post_types();

	    if ( is_category() || is_tag())
	    {
			if(!is_admin() && $query->is_main_query() )
	        {
		        $post_type = get_query_var(get_post_type());

		        if ($post_type) {
		            $post_type = $post_type;
		        } else {
		            $post_type = $post_types;
		        }
		        $query->set('post_type', $post_type);
			}
	    }


	    return $query;
	}
	add_filter('pre_get_posts', 'avia_fix_tag_archive_page');
}



/*
 * wrap widgets into a html5 section
 */
if(!function_exists('avia_sidebars_widgets'))
{
    add_filter('dynamic_sidebar_params', 'avia_sidebars_widgets');

    function avia_sidebars_widgets($params)
    {
        if(!empty($params) && is_array($params))
        {
            foreach($params as $key => $widget)
            {
            	if(isset($params[$key]['before_widget']))
            	{
                	$params[$key]['before_widget'] = '<section class="avia_widget_section">'.$params[$key]['before_widget'];
                }
                
                if(isset($params[$key]['after_widget']))
            	{
               	 	$params[$key]['after_widget'] = $params[$key]['after_widget'].'</section>';
            	}
            }
        }
        return $params;
    }
}

/*
 * add html5.js script to head section - required for IE compatibility
 */
if(!function_exists('avia_print_html5_js_script'))
{
    add_action('wp_head', 'avia_print_html5_js_script');

    function avia_print_html5_js_script()
    {
        $template_url = get_template_directory_uri();
        $output = '';

        $output .= '<!--[if lt IE 9]>';
        $output .= '<script src="'.$template_url.'/js/html5shiv.js"></script>';
        $output .= '<![endif]-->';
        echo $output;
    }
}


/*
function that saves the style options array into an external css file rather than fetching the data from the database
*/

if(!function_exists('avia_generate_stylesheet'))
{
	add_action('avia_ajax_after_save_options_page', 'avia_generate_stylesheet', 30, 1);
	function avia_generate_stylesheet($options)
	{
		global $avia;
		$safe_name = avia_backend_safe_string($avia->base_data['prefix']);

	    if( defined('AVIA_CSSFILE') && AVIA_CSSFILE === FALSE )
	    {
	        $dir_flag           = update_option( 'avia_stylesheet_dir_writable'.$safe_name, 'false' );
	        $stylesheet_flag    = update_option( 'avia_stylesheet_exists'.$safe_name, 'false' );
	        return;
	    }

	    $wp_upload_dir  = wp_upload_dir();
	    $stylesheet_dir = $wp_upload_dir['basedir'].'/dynamic_avia';
	    $stylesheet_dir = str_replace('\\', '/', $stylesheet_dir);
	    $stylesheet_dir = apply_filters('avia_dyn_stylesheet_dir_path',  $stylesheet_dir);
	    $isdir = avia_backend_create_folder($stylesheet_dir);

	    /*
	    * directory could not be created (WP upload folder not write able)
	    * @todo save error in db and output error message for user.
	    * @todo maybe add mkdirfix: http://php.net/manual/de/function.mkdir.php
	    */

	    if($isdir === false)
	    {
	        $dir_flag = update_option( 'avia_stylesheet_dir_writable'.$safe_name, 'false' );
	        $stylesheet_flag = update_option( 'avia_stylesheet_exists'.$safe_name, 'false' );
	        return;
	    }

	    /*
	     *  Go ahead - WP managed to create the folder as expected
	     */
	    $stylesheet = trailingslashit( $stylesheet_dir ) . $safe_name.'.css';
	    $stylesheet = apply_filters('avia_dyn_stylesheet_file_path', $stylesheet);


	    //import avia_superobject and reset the options array
	    $avia_superobject = $GLOBALS['avia'];
		$avia_superobject->reset_options();

		//regenerate style array after saving options page so we can create a new css file that has the actual values and not the ones that were active when the script was called
		avia_prepare_dynamic_styles();

	    //generate stylesheet content
	    $generate_style = new avia_style_generator($avia_superobject,false,false,false);
	    $styles         = $generate_style->create_styles();

	    $created        = avia_backend_create_file($stylesheet, $styles, true);

	    if($created === true)
	    {
	        $dir_flag = update_option( 'avia_stylesheet_dir_writable'.$safe_name, 'true' );
	        $stylesheet_flag = update_option( 'avia_stylesheet_exists'.$safe_name, 'true' );
	    }
	}
}
