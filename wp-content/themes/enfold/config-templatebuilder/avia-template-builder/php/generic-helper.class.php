<?php
/**
* Central AviaHelper class which holds quite a few unrelated functions
*/

// Don't load directly
if ( !defined('ABSPATH') ) { die('-1'); }

if ( !class_exists( 'AviaHelper' ) ) {
	
	class AviaHelper
	{
		static $cache = array(); 		//holds database requests or results of complex functions
		static $templates = array(); 	//an array that holds all the templates that should be created when the print_media_templates hook is called
		
		/**
    	 * get_url - Returns a url based on a string that holds either post type and id or taxonomy and id
    	 */
    	static function get_url($link, $post_id = false) 
    	{
    		$link = explode(',', $link);
    		
    		if($link[0] == 'lightbox')        
    		{
    			$link = wp_get_attachment_image_src($post_id, 'large');
    			return $link[0];
    		}
    		
    		if(empty($link[1]))               return $link[0];
    		if($link[0] == 'manually')        return $link[1];
            if(post_type_exists( $link[0] ))  return get_permalink($link[1]);
            if(taxonomy_exists( $link[0]  ))  return get_term_link(get_term($link[1], $link[0]));
    	}
    	
    	/**
    	 * get_entry - fetches an entry based on a post type and id
    	 */
    	static function get_entry($entry) 
    	{
    		$entry = explode(',', $entry);
    		
    		if(empty($entry[1]))              return false;
    		if($entry[0] == 'manually')        return false;
            if(post_type_exists( $entry[0] ))  return get_post($entry[1]);
    	}
    	
    	/**
    	 * fetch all available sidebars
    	 */
    	static function get_registered_sidebars($sidebars = array(), $exclude = array())
    	{
    		//fetch all registered sidebars and save them to the sidebars array
			global $wp_registered_sidebars;
			
			foreach($wp_registered_sidebars as $sidebar)
			{
				if( !in_array($sidebar['name'], $exclude))
				{
					$sidebars[$sidebar['name']] = $sidebar['name']; 
				}
			}
			
			return $sidebars;
    	}
    	
    	static function get_registered_image_sizes($exclude = array(), $enforce_both = false)
    	{
    		global $_wp_additional_image_sizes;
    		
    		 // Standard sizes
	        $image_sizes = array(   'no scaling'=> array("width"=>"Original Width ", "height"=>" Original Height"),
	        						'thumbnail' => array("width"=>get_option('thumbnail_size_w'), "height"=>get_option('thumbnail_size_h')),
	        						'medium' 	=> array("width"=>get_option('medium_size_w'), "height"=>get_option('medium_size_h')), 
	        						'large' 	=> array("width"=>get_option('large_size_w'), "height"=>get_option('large_size_h')));
	        
	        if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) )
	                $image_sizes = array_merge( $image_sizes, $_wp_additional_image_sizes  );
	                
    		$result = array();
    		foreach($image_sizes as $key => $image)
			{
				if( (is_array($exclude) && !in_array($key, $exclude)) || (is_numeric($exclude) && ($image['width'] > $exclude || $image['height'] > $exclude)) || !is_numeric($image['height']))
				{
					if($enforce_both == true && is_numeric($image['height']))
					{
						if($image['width'] < $exclude || $image['height'] < $exclude) continue;
					}
					
					
					$title = str_replace("_",' ', $key) ." (".$image['width']."x".$image['height'].")";
					
					$result[ucwords( $title )] =  $key; 
				}
			}
    		
    		return $result;
    	}


    	/**
    	 * is_ajax - Returns true when the page is loaded via ajax.
    	 */
    	static function is_ajax() 
    	{
    		if ( defined('DOING_AJAX') )
    			return true;
    
    		return ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) ? true : false;
    	}
		
		
		/**
		 * function that gets called on backend pages and hooks other functions into wordpress
		 *
		 * @return void
		 */
		static function backend()
		{
			add_action( 'print_media_templates', array('AviaHelper', 'print_templates' )); 		//create js templates for AviaBuilder Canvas Elements
		}
		
		
		/**
		 * Helper function that prints all the javascript templates
		 *
		 * @return void
		 */
		
		static function print_templates()
		{
			foreach (self::$templates as $key => $template)
			{
				echo "\n<script type='text/html' id='avia-tmpl-{$key}'>\n";
				echo $template;
				echo "\n</script>\n\n";
			}
			
			//reset the array
			self::$templates = array();
		}
		
		/**
		 * Helper function that creates a new javascript template to be called
		 *
		 * @return void
		 */
		
		static function register_template($key, $html)
		{
			self::$templates[$key] = $html;
		}
		
		
		
		/**
		 * Helper function that fetches all "public" post types.
		 *
		 * @return array $post_types example output: data-modal='true'
		 */
		
		static function public_post_types()
		{
			$post_types 		= get_post_types(array('public' => false, 'name' => 'attachment', 'show_ui'=>false, 'publicly_queryable'=>false), 'names', 'NOT');
			$post_types['page'] = 'page';
			$post_types 		= array_map("ucfirst", $post_types);
			$post_types			= apply_filters('avia_public_post_types', $post_types);
			self::$cache['post_types'] = $post_types;
			
			return $post_types;
		}
		
				
		
		/**
		 * Helper function that fetches all taxonomies attached to public post types.
		 *
		 * @return array $taxonomies
		 */
		
		static function public_taxonomies($post_types = false, $merged = false)
		{	
			$taxonomies = array();
			
			if(!$post_types)
				$post_types = empty(self::$cache['post_types']) ? self::public_post_types() : self::$cache['post_types'];
				
			if(!is_array($post_types))
				$post_types = array($post_types => ucfirst($post_types));
				
			foreach($post_types as $type => $post)
			{
				$taxonomies[$type] = get_object_taxonomies($type);
			}	
			
			$taxonomies = apply_filters('avia_public_taxonomies', $taxonomies);
			self::$cache['taxonomies'] = $taxonomies;
			
			if($merged)
			{
				$new = array();
				foreach($taxonomies as $taxonomy)
				{
					foreach($taxonomy as $tax)
					{
						$new[$tax] = ucwords(str_replace("_", " ",$tax));
					}
				}
				
				$taxonomies = $new;
			}
			
			return $taxonomies;
		}
		
		
	
		/**
		 * Helper function that converts an array into a html data string
		 *
		 * @param array $data example input: array('modal'=>'true')
		 * @return string $data_string example output: data-modal='true'
		 */
		 
		static function create_data_string($data = array())
		{
			$data_string = "";
			
			foreach($data as $key=>$value)
			{
				if(is_array($value)) $value = implode(", ",$value);
				$data_string .= " data-$key='$value' ";
			}
		
			return $data_string;
		}

    	/**
    	* Create a lower case version of a string without spaces so we can use that string for database settings
    	* 
    	* @param string $string to convert
    	* @return string the converted string
    	*/
    	function save_string( $string , $replace = "_")
    	{
    		$string = strtolower($string);
    	
    		$trans = array(
    					'&\#\d+?;'				=> '',
    					'&\S+?;'				=> '',
    					'\s+'					=> $replace,
    					'ä'						=> 'ae',
    					'ö'						=> 'oe',
    					'ü'						=> 'ue',
    					'Ä'						=> 'Ae',
    					'Ö'						=> 'Oe',
    					'Ü'						=> 'Ue',
    					'ß'						=> 'ss',
    					'[^a-z0-9\-\._]'		=> '',
    					$replace.'+'			=> $replace,
    					$replace.'$'			=> $replace,
    					'^'.$replace			=> $replace,
    					'\.+$'					=> ''
    				  );
    				  
    		$trans = apply_filters('avf_save_string_translations', $trans, $string, $replace);
    
    		$string = strip_tags($string);
    
    		foreach ($trans as $key => $val)
    		{
    			$string = preg_replace("#".$key."#i", $val, $string);
    		}
    		
    		return stripslashes($string);
    	}
		

		
		
		
		
		
		
		
		
	}
	
}
