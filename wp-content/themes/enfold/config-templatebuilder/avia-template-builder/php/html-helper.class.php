<?php
/**
* Central Template builder class
*/

// Don't load directly
if ( !defined('ABSPATH') ) { die('-1'); }

if ( !class_exists( 'AviaHtmlHelper' ) ) {

	class AviaHtmlHelper 
	{
		static $metaData 		= array(); //required for meta key storing when creating a metabox. necessary to set already stored std values
		static $elementValues 	= array(); //all element values in an id=>value array so we can check dependencies
		static $elementHidden 	= array(); //all elements that didnt pass the dependency test and are hidden
		static $imageCount      = 0; //count all image elements to assign the right attachment id
		
		static function render_metabox($element)
		{
			//query the metadata of the current post and check if a key is set, if not set the default value to the standard value, otherwise to the key value
			if(!isset(self::$metaData[$element['current_post']]))
			{
				self::$metaData[$element['current_post']] = get_post_custom($element['current_post']);
			}
			
			if(isset(self::$metaData[$element['current_post']][$element['id']]))
			{
				$element['std'] = self::$metaData[$element['current_post']][$element['id']][0];
			}
			
			return self::render_element($element);
		}
		
		
		
		static function render_multiple_elements($elements, $parent_class = false)
		{
			$output = "";
			
			foreach ($elements as $element)
			{
				$output .= self::render_element($element, $parent_class);
			}
			
			return $output;
		}
		
		static function ajax_modify_id($element)
		{
			// check if its an ajax request. if so prepend a string to ensure that the ids are unqiue. 
			// If there are multiple modal windows called prepend the string multiple times
			if(isset($_POST['ajax_fetch'])) 
			{ 
				$prepends = isset($_POST['instance']) ? $_POST['instance'] : 0;
				$element['ajax'] = true; 
				
				for($i = 0; $i < $prepends; $i++)
				{
					$element['id'] = "aviaTB" . $element['id'];
				}
			}
			return $element;
		}
		
	
		static function render_element($element, $parent_class = false)
		{
		
			$defaults		= array('id'=>'', 'name'=>'', 'label' => '', 'std' => '', 'class' =>'', 'container_class'=>'', 'desc' =>'', 'required'=>array(), 'target'=>array(), 'shortcode_data'=>array());
			$element		= array_merge($defaults, $element);
			$output			= "";
			self::$elementValues[$element['id']] = $element['std']; //save the values into a unique array in case we need it for dependencies
			
			//create default data und class string and checks the dependencies of an object
			extract(self::check_dependencies($element));
			
			
			// check if its an ajax request. if so prepend a string to ensure that the ids are unqiue. 
			// If there are multiple modal windows called prepend the string multiple times
			$element = self::ajax_modify_id($element);
			
			
			$id_string 		 = empty($element['id']) ? "" : "id='".$element['id']."-form-container'";
			$class_string 	.= empty($element['container_class']) ? "" : $element['container_class']; 
			
			$target_string = "";
			if(!empty($element['target']))
			{
				$data['target-element'] = $element['target'][0];
				$data['target-property'] = $element['target'][1];
				$target_string = AviaHelper::create_data_string($data);
				$class_string .= " avia-attach-targeting ";
			}

			if(!empty($element['fetchTMPL'])) $class_string .= " avia-attach-templating ";
			

			
			$output .= "<div class='avia_clearfix avia-form-element-container ".$class_string." avia-element-".$element['type']."' ".$id_string." ".$data_string." ".$target_string.">";
			
				if( !empty($element['name']) ||!empty($element['desc']))  
				{
					$output .= "<div class='avia-name-description'>";
					if( !empty($element['name'])) $output .= "<strong>".$element['name']."</strong>";
					if( !empty($element['desc'])) $output .= "<span>".$element['desc']."</span>";
					$output .= "</div>";
				}
				$output .= "<div class='avia-form-element ".$element['class']."'>";
				$output .= self::$element['type']($element, $parent_class);
				
				if(!empty($element['fetchTMPL']))
				{
					$output .= "<div class='template-container'></div>";
				}
				
				$output .= "</div>";
			
				
			
			$output .= "</div>";
			
			
			
			
			
			return $output;
		}
		
		
		/*
		* Helper function that checks dependencies between objects based on the $element['required'] array
		*
		* If the array is set it needs to have exactly 3 entries.
		* The first entry describes which element should be monitored by the current element. eg: "content"
		* The second entry describes the comparison parameter. eg: "equals, not, is_larger, is_smaller ,contains"
		* The third entry describes the value that we are comparing against.
		*
		* Example: if the required array is set to array('content','equals','Hello World'); then the current
		* element will only be displayed if the element with id "content" has exactly the value "Hello World"
		* 
		*/
		static function check_dependencies($element)
		{	
			$params = array('data_string' => "", 'class_string' => "");
		
			if(!empty($element['required']))
			{
				$data['check-element'] 		= $element['required'][0];
				$data['check-comparison'] 	= $element['required'][1];
				$data['check-value'] 		= $element['required'][2];
				$params['data_string'] 		= AviaHelper::create_data_string($data);
				$return = false;
				
				//required element must not be hidden. otherwise hide this one by default
				if(!isset(self::$elementHidden[$data['check-element']]))
				{
					if(isset(self::$elementValues[$data['check-element']]))
					{
						$value1		= self::$elementValues[$data['check-element']];
						$value2		= $data['check-value'];
						
						switch($data['check-comparison'])
						{
							case 'equals': 			if($value1 == $value2) $return = true; break;
							case 'not': 			if($value1 != $value2) $return = true; break;
							case 'is_larger': 		if($value1 >  $value2) $return = true; break;
							case 'is_smaller': 		if($value1 <  $value2) $return = true; break;
							case 'contains': 		if(strpos($value1,$value2) !== false) $return = true; break;
							case 'doesnt_contain': 	if(strpos($value1,$value2) === false) $return = true; break;
							case 'is_empty_or': 	if(empty($value1) || $value1 == $value2) $return = true; break;
							case 'not_empty_and': 	if(!empty($value1) && $value1 != $value2) $return = true; break;
							
							
							
						}
					}
				}
				
				if(!$return)
				{
					$params['class_string'] = ' avia-hidden ';
					self::$elementHidden[$element['id']] = true;
				}
			}
			
			return $params;
		}
		
		
		
		
		/**
         * Creates a wrapper around a set of elements. This set can be cloned with javascript
         * @param array $element the array holds data like id, class and some js settings
         * @return string $output the string returned contains the html code generated within the method
         */
         
		function modal_group($element, $parent_class)
		{
		
			$iterations = count($element['std']);
			
			$output = "";
			$output .= "<div class='avia-modal-group-wrapper' >";
			
			if(!empty($element['creator'])) $output .= self::render_element($element['creator']);
			
			$output .= "<div class='avia-modal-group' id='".$element['id']."' >";
			
			for ($i = 0; $i < $iterations; $i++)
			{
				if(isset($_POST['extracted_shortcode']))
				{
					$element['shortcode_data'] = $_POST['extracted_shortcode'][$i]['attr'];
				}
			
				$output .= self::modal_group_sub($element, $parent_class, $i);
			}
			
			
			$label = isset($element['add_label']) ? $element['add_label'] : __('Add','avia_framework' );
			$label_class = isset($element['add_label']) ? "avia-custom-label" : "";
			
			$output .= "</div>";
			$output .= "<a class='avia-attach-modal-element-add avia-add {$label_class}'>".$label."</a>";
			
			//go the new wordpress way and instead of ajax-loading new items, prepare an empty js template
			$output .= '	<script type="text/html" class="avia-tmpl-modal-element">';
			$output .= self::modal_group_sub($element, $parent_class);
			$output .= '	</script>';
			$output .= "</div>";

			return $output;
		}
		
		
		function modal_group_sub($element, $parent_class, $i = false)
		{
			$output = "";
			
			$args = array();
			$content = NULL;
			
			//iterate over the subelements and set the default values
			foreach($element['subelements'] as $key => $subelement)
			{
				if(isset($element['std']) && is_array($element['std']) && isset($element['std'][$i][$subelement['id']]))
				{
					$subelement['std'] = $element['std'][$i][$subelement['id']];
				}
				
				//if $i is not set, meaning we need a totaly empty template reset the std values
				if($i === false) $subelement['std'] = "";
				
				
				if($subelement['id'] == 'content')
				{
					$content = $subelement['std'];
				}
				else
				{
					$args[$subelement['id']] = $subelement['std'];
				}
			}
			
			if($i !== false && is_array($element['shortcode_data']))
			{
				$args = array_merge($element['shortcode_data'], $args);
			}
	
		
			$params['args'] = $args;
			$params['content'] = $content;

			
			$defaults = array('class'=>'', 'innerHtml'=>'');
			$params = array_merge($defaults, $params);
			$params = $parent_class->editor_sub_element($params);
			extract($params);
			
			$data['modal_title'] 		= $element['modal_title'];
			$data['shortcodehandler'] 	= $parent_class->config['shortcode_nested'][0];
			$data['modal_ajax_hook'] 	= $parent_class->config['shortcode_nested'][0];
			$data['modal_on_load'] 		= array();
			
			if(!empty($element['modal_on_load']))
			{
				$data['modal_on_load'] 	= array_merge($data['modal_on_load'], $element['modal_on_load']);
			}
			
			if(!empty($parent_class->config['modal_on_load']))
			{
				$data['modal_on_load'] 	= array_merge($data['modal_on_load'], $parent_class->config['modal_on_load']);
			}
			
			$dataString  	= AviaHelper::create_data_string($data);
				
			
			$output .= "<div class='avia-modal-group-element ' {$dataString}>";
			$output .= "<a class='avia-attach-modal-element-move avia-move-handle'>".__('Move','avia_framework' )."</a>";
			$output .= "<a class='avia-attach-modal-element-delete avia-delete'>".__('Delete','avia_framework' )."</a>";
			$output .= "<div class='avia-modal-group-element-inner'>";
			$output .= $params['innerHtml'];
			$output .= "</div>";
			
			$output .= "<textarea data-name='text-shortcode' cols='20' rows='4' name='{$element['id']}'>".ShortcodeHelper::create_shortcode_by_array($parent_class->config['shortcode_nested'][0], $content, $args)."</textarea>";
			$output .= "</div>";
			return $output;
		}
		
		
		
		
		
		/**
         * 
         * Empty Element - The heading method renders a text and description only that might allow to describe some following functionallity
         * @param array $element the array holds data like type, value, id, class, description which are necessary to render the whole option-section
         * @return string $output the string returned contains the html code generated within the method
         */
		function heading( $element )
		{	
			return;
		}
		
		/**
         * 
         * Empty Element - The heading method renders a text and description only that might allow to describe some following functionallity
         * @param array $element the array holds data like type, value, id, class, description which are necessary to render the whole option-section
         * @return string $output the string returned contains the html code generated within the method
         */
		function hr( $element )
		{	
		
			$output = "<div class='avia-builder-hr'></div>";
			return $output;
		}
		
		
		
		/**
         * 
         * The tiny_mce method renders a tiny mce text field, also known as the wordpress visual editor
         * @param array $element the array holds data like type, value, id, class, description which are necessary to render the whole option-section
         * @return string $output the string returned contains the html code generated within the method
         */
		static function tiny_mce($element)
		{
			//tinymce only allows ids in the range of [a-z] so we need to filter them. 
			$element['id']  = preg_replace('![^a-zA-Z_]!', "", $element['id']);
			
			
			/* monitor this: seems only ajax elements need the replacement */
			$user_ID = get_current_user_id();
			
			if(get_user_meta($user_ID, 'rich_editing', true) == "true" && isset($element['ajax']))
			{
				//replace new lines with brs, otherwise the editor will mess up
				$element['std'] = str_replace("\n",'<br>',$element['std']);
			}
			
			
			ob_start();
	        wp_editor( $element['std'], $element['id'] , array('editor_class' => 'avia_advanced_textarea avia_tinymce', 'media_buttons' => true ) );
	        $output = ob_get_clean();
	        
	        return $output;
		}
		
	
	
		/**
         * 
         * The input method renders one or more input type:text elements, based on the definition of the $elements array
         * @param array $element the array holds data like type, value, id, class, description which are necessary to render the whole option-section
         * @return string $output the string returned contains the html code generated within the method
         */
		static function input($element)
		{
			$output = '<input type="text" class="'.$element['class'].'" value="'.nl2br($element['std']).'" id="'.$element['id'].'" name="'.$element['id'].'"/>';
			return $output;
		}
		

		
		/**
         * 
         * The hidden method renders a single input type:hidden element
         * @param array $element the array holds data like type, value, id, class, description which are necessary to render the whole option-section
         * @return string $output the string returned contains the html code generated within the method
         */
		static function hidden( $element )
		{			
			$output  = '<input type="hidden" value="'.$element['std'].'" id="'.$element['id'].'" name="'.$element['id'].'"/>';
			return $output;
		}
		
		/**
         * 
         * The checkbox method renders a single input type:checkbox element
         * @param array $element the array holds data like type, value, id, class, description which are necessary to render the whole option-section
         * @return string $output the string returned contains the html code generated within the method
         * @todo: fix: checkboxes at metaboxes currently dont work
         */
  		static function checkbox( $element )
		{	
			$checked = "";
			if( $element['std'] != "" ) { $checked = 'checked = "checked"'; }
	
			$output   = '<input '.$checked.' type="checkbox" class="'.$element['class'].'" ';
			$output  .= 'value="'.$element['id'].'" id="'.$element['id'].'" name="'.$element['id'].'"/>';
			
			return $output;
		}
		
		/**
         * 
         * The textarea method renders a single textarea element
         * @param array $element the array holds data like type, value, id, class, description which are necessary to render the whole option-section
         * @return string $output the string returned contains the html code generated within the method
         */
		static function textarea( $element )
		{	
			$output  = '<textarea rows="5" cols="30" class="'.$element['class'].'" id="'.$element['id'].'" name="'.$element['id'].'">';
			$output .= $element['std'].'</textarea>';
			return $output;
		}
		
		
		/**
         * 
         * The iconfont method renders a single icon-select element based on a font
         * @param array $element the array holds data like type, value, id, class, description which are necessary to render the whole option-section
         * @return string $output the string returned contains the html code generated within the method
         */
		static function iconfont( $element )
		{	
			$output = "";
			
			if(!empty($element['chars']) && is_array($element['chars']))
			{
				$chars = $element['chars'];
			}
			else
			{
				$chars 	= avia_font_manager::load_charlist();
			}
			
			//get either the passed font or the default font
			$std_font = isset($element['shortcode_data']['font']) ? $element['shortcode_data']['font'] : key(AviaBuilder::$default_iconfont);
			
			$output .= "<div class='avia_icon_select_container avia-attach-element-container'>";
		
			$run = 0;
			$active_font = "";
			foreach($chars as $font => $charset)
			{
				$run ++;
				asort($charset);
				
				if($run === 1)
				{
					//if the el value is empty set it to the first char
					if(empty($element['std'])) $element['std'] = key($charset);
					$standard = avia_font_manager::get_display_char($element['std'], $std_font);
				}
				
				$output .= "<div class='av-iconselect-heading'>Font: {$font}</div>";
				foreach($charset as $key => $char)
				{	
					$char = avia_font_manager::try_decode_icon($char);
					$charcode_prefix 	= "Charcode: \\";
					$active_char 		= "";
					
					if($char == $standard)
					{
						$active_char = "avia-active-element";
						$active_font = $font;
					}
					
					$output .= "<span title='{$charcode_prefix}{$key}' data-element-nr='{$key}' data-element-font='{$font}' class='avia-attach-element-select avia_icon_preview avia-font-{$font} {$active_char}'>{$char}</span>";
				}
			}
			
			//default icon value
			$output .= self::hidden($element);
			
			//fake character value needed for backend editor
			$element['id'] = $element['id']."_fakeArg";
			$element['std'] = empty($standard) ? "" : $standard;
			$output .= self::hidden($element);
			
			//font value needed for backend and editor
			$element['id'] = "font";
			$element['std'] = $active_font;
			$element = self::ajax_modify_id($element);
			$output .= self::hidden($element);
			
			
			$output .= "</div>";

			return $output;
		}
		

		
		
		
		/**
         * 
         * The colorpicker method renders a colorpicker element that allows you to select a color of your choice
         * @param array $element the array holds data like type, value, id, class, description which are necessary to render the whole option-section
         * @return string $output the string returned contains the html code generated within the method
         */
		static function colorpicker($element)
		{
			$output = '<input type="text" class="colorpicker '.$element['class'].'" value="'.$element['std'].'" id="'.$element['id'].'" name="'.$element['id'].'"/>';
			return $output;
		}
		
		
		/**
         * 
         * The linkpicker method renders a linkpicker element that allows you to select a link to a post type or taxonomy type of your choice
         * @param array $element the array holds data like type, value, id, class, description which are necessary to render the whole option-section
         * @return string $output the string returned contains the html code generated within the method
         */
		static function linkpicker($element)
		{	
			$original = $element;
			$new_std = explode(',', $element['std'], 2);
	
			$pt = array_flip(AviaHelper::public_post_types());
			$ta = array_flip(AviaHelper::public_taxonomies(false, true));
			
			if(isset($new_std[1])) $original['std'] = $new_std[1];
			
			$allowed_pts = isset($original['posttype']) ? $original['posttype'] : $pt;
			$allowed_tas = isset($original['taxtype']) ? $original['taxtype'] : $ta;

			if(in_array('single', $element['subtype']))
			{
				foreach($pt as $key => $type)
				{
					if(in_array($type, $allowed_pts))
					{
						$original['subtype'] = $type;
						$html = self::select($original); 
						
						if( $html ) { AviaHelper::register_template($type, $html); } else { unset($pt[$key] ); }
					}
					else
					{
						unset($pt[$key]);
					}
				}
			}
			
			if(in_array('taxonomy', $element['subtype']))
			{
				foreach($ta as $key => $type)
				{
					if(in_array($type, $allowed_tas))
					{
						$original['subtype'] = 'cat';
						$original['taxonomy'] = $type;
					
						$html = self::select($original); 
						if( $html ) {AviaHelper::register_template($type, $html); } else { unset($ta[$key] ); }
					}
					else
					{
						unset($ta[$key]);
					}
				}
			}
			
			if(isset($new_std[1])) $element['std'] = $new_std[1];

			$original['subtype'] = ""; 
			foreach($element['subtype'] as $value => $key) //register templates
			{
				switch($key)
				{
					case "manually": 
					
					if($new_std[0] != $key) $element['std'] = "http://";
					
					$original['subtype'][$value] = $key;
					$html = self::input($element); 
					AviaHelper::register_template($key, $html);
					break;
					
					case "single": 
					$original['subtype'][$value] = $pt;
					break;
					
					case "taxonomy": 
					$original['subtype'][$value] = $ta;
					break;
					
					default: $original['subtype'][$value] = $key; break;
					
				}
			}
			
			if(!empty($element['ajax'])) // if we got an ajax request we also need to call the printing since the default wordpress hook is already executed
			{
				AviaHelper::print_templates();
			}
		
			$original['std'] = $new_std[0];
			unset($original['multiple']);
			$output = self::select($original);
			return $output;
		}
		
		
		/**
         * 
         * The image method renders an image upload button that allows the user to select an image from the media uploader and insert it
         * @param array $element the array holds data like type, value, id, class, description which are necessary to render the whole option-section
         * @return string $output the string returned contains the html code generated within the method
         */
		static function image($element)
		{
			if(empty($element['data']))
			{
				$fetch = isset($element['fetch']) ? $element['fetch'] : "url";
				$state = isset($element['state']) ? $element['state'] : "avia_insert_single";
				
				if(empty($element['show_options']))
				{
					$class = $fetch == "id" ? "avia-media-img-only-no-sidebars" : "avia-media-img-only";
				}
				else if($element['show_options'] == true)
				{
					$class = "avia-media-img-only";
				}
				
				
				$element['data'] =  array(	'target' => $element['id'], 
											'title'  => $element['title'], 
											'type'   => $element['type'], 
											'button' => $element['button'],
											'class'  => 'media-frame '.$class.' '.$element['container_class'] ,
											'frame'  => 'select',
											'state'  => $state,
											'fetch'  => $fetch,
											'save_to'=> 'hidden'
									);
			}
			
			if(isset($element['modal_class'])) $element['data']['class'] .= " ".$element['modal_class'];
			
			
			$data 	= AviaHelper::create_data_string($element['data']);
			$class 	= 'button aviabuilder-image-upload avia-builder-image-insert '.$element['class'];
			$output = '	<a href="#" class="'.$class.'" '.$data.' title="'.esc_attr($element['title']).'">'.$element['title'].'</a>';
			
			if(isset($element['delete'])) $output .= '<a href="#" class="button avia-delete-gallery-button" title="'.esc_attr($element['delete']).'">'.$element['delete'].'</a>';
					
			if($element['type'] != 'video')
			{
				$output .= self::display_image($element['std']);			
			}
			$output .= self::$element['data']['save_to']($element);
			
			//fake img for multi_image element
			if(isset($fetch))
			{
				$fake_img_id = str_replace ( str_replace('aviaTB','',$element['id']) ,'img_fakeArg', $element['id']);
				$img_id_field = str_replace ( str_replace('aviaTB','',$element['id']) ,'attachment', $element['id']);
				
				$fake_img = $fetch == "id" ? wp_get_attachment_image( $element['std'], 'thumbnail') : '<img src="'.$element['std'].'" />';

				$attachmentids = !empty($element['shortcode_data']['attachment']) ? explode(',', $element['shortcode_data']['attachment']) : array();
				$attachmentid = !empty($attachmentids[self::$imageCount]) ? $attachmentids[self::$imageCount] : '';
				
				$output .= '<input type="hidden" class="hidden-image-url '.$element['class'].'" value="'.htmlentities($fake_img, ENT_QUOTES, get_bloginfo( 'charset' )).'" id="'.$fake_img_id.'" name="'.$fake_img_id.'"/>';
				$output .= '<input type="hidden" class="hidden-attachment-id '.$element['class'].'" value="'.$attachmentid.'" id="'.$img_id_field.'" name="'.$img_id_field.'"/>';
			}

			self::$imageCount++;
			return $output;
		}
		
		/**
         * 
         * The gallery method renders an image upload button that allows the user to select an image from the media uploader and insert it
         * @param array $element the array holds data like type, value, id, class, description which are necessary to render the whole option-section
         * @return string $output the string returned contains the html code generated within the method
         */
		static function gallery($element)
		{
			if(empty($element['data']))
			{
				$element['data'] = array(	'target' => $element['id'], 
											'title'  => $element['title'], 
											'type'   => $element['type'], 
											'button' => $element['button'],
											'class'  => 'media-frame avia-media-gallery-insert '.$element['container_class'] ,
											'state'  => 'gallery-library',
											'frame'  => 'post',
											'fetch'  => 'id',
											'save_to'=> 'hidden'
										);
			}

			return AviaHtmlHelper::image($element);
		}
		
		/**
         * 
         * The video method renders a video upload button that allows the user to select an video from the media uploader and insert it
         * @param array $element the array holds data like type, value, id, class, description which are necessary to render the whole option-section
         * @return string $output the string returned contains the html code generated within the method
         */
		static function video($element)
		{
			if(empty($element['data']))
			{
				$element['data'] = array(	'target' => $element['id'], 
											'title'  => $element['title'], 
											'type'   => $element['type'], 
											'button' => $element['button'],
											'class'  => 'media-frame avia-blank-insert '.$element['container_class'] ,
											'state'  => 'avia_insert_video',
											'frame'  => 'select',
											'fetch'  => 'url',
											'save_to'=> 'input'
										);
			}

			return AviaHtmlHelper::image($element);
		}
		
		
		/**
         * 
         * The multi_image method allows us to insert many images into a modal template at once
         * @param array $element the array holds data like type, value, id, class, description which are necessary to render the whole option-section
         * @return string $output the string returned contains the html code generated within the method
         */
		static function multi_image($element)
		{
			if(empty($element['data']))
			{
				$fetch = isset($element['fetch']) ? $element['fetch'] : "template";
				$state = isset($element['state']) ? $element['state'] : "avia_insert_multi";
				
				$class = $fetch == "template" ? "avia-media-img-only-no-sidebars" : "avia-media-img-only";
				
				$element['data'] =  array(	'target' => $element['id'], 
											'title'  => $element['title'], 
											'type'   => $element['type'], 
											'button' => $element['button'],
											'class'  => 'media-frame '.$class.' '.$element['container_class'] ,
											'frame'  => 'select',
											'state'  => $state,
											'fetch'  => $fetch,
									);
			}
			
			$data 	= AviaHelper::create_data_string($element['data']);
			$class 	= 'button aviabuilder-image-upload avia-builder-image-insert '.$element['class'];
			$output = '	<a href="#" class="'.$class.'" '.$data.' title="'.esc_attr($element['title']).'">
						<span class="wp-media-buttons-icon"></span>'.$element['title'].'</a>';
						
			
			return $output;
		}
		
		
		
		
		
		
		
		/**
         * 
         * The select method renders a single select element: it either lists custom values, all wordpress pages or all wordpress categories
         * @param array $element the array holds data like type, value, id, class, description which are necessary to render the whole option-section
         * @return string $output the string returned contains the html code generated within the method
         */
		static function select( $element )
		{	
			$select = __('Select','avia_framework' );
			
			if($element['subtype'] == 'cat')
			{
				$add_taxonomy = "";
				
				if(!empty($element['taxonomy'])) $add_taxonomy = "&taxonomy=".$element['taxonomy'];
			
				$entries = get_categories('title_li=&orderby=name&hide_empty=0'.$add_taxonomy);
				
			}
			else if(!is_array($element['subtype']))
			{
				global $wpdb;
				$table_name = $wpdb->prefix . "posts";
	 			$limit 		= apply_filters( 'avf_dropdown_post_number', 4000 );
	    			
				$prepare_sql = "SELECT distinct ID, post_title FROM {$table_name} WHERE post_status = 'publish' AND post_type = '".$element['subtype']."' ORDER BY post_title ASC LIMIT {$limit}";
				$prepare_sql = apply_filters('avf_dropdown_post_query', $prepare_sql, $table_name, $limit, $element);
				$entries 	= $wpdb->get_results($prepare_sql);
	    			
	    			//$entries 	= $wpdb->get_results( "SELECT ID, post_title FROM {$table_name} WHERE post_status = 'publish' AND post_type = '".$element['subtype']."' ORDER BY post_title ASC LIMIT {$limit}" );
				//$entries = get_posts(array('numberposts' => apply_filters( 'avf_dropdown_post_number', 200 ), 'post_type' => $element['subtype'], 'post_status'=> 'publish', 'orderby'=> 'post_date', 'order'=> 'ASC'));
			}
			else
			{	
				$select = 'Select...';
				$entries = $element['subtype'];
				
				if(isset($element['folder']))
				{	
					$add_file_array = avia_backend_load_scripts_by_folder(AVIA_BASE.$element['folder']);
	
					if(is_array($add_file_array))
					{
						foreach($add_file_array as $file)
						{
							if(strpos($file, '.') !== 0)
							$entries[$element['folderlabel'].$file] = AVIA_BASE_URL.$element['folder'].$file; 
						}
					}
				}
			}
			
			$data_string = "";
			if(isset($element['data'])) 
			{
				foreach ($element['data'] as $key => $data)
				{
					$data_string .= "data-".$key."='".$data."'";
				}
			}
			
			if(empty($entries)) return;
			
			$multi = $multi_class = "";
			if(isset($element['multiple'])) 
			{
				$multi_class = " avia_multiple_select";
				$multi = 'multiple="multiple" size="'.$element['multiple'].'"';
				$element['std'] = explode(',', $element['std']);
			}
			
			$id_string = empty($element['id']) ? "" : "id='".$element['id']."'";
			$name_string = empty($element['id']) ? "" : "name='".$element['id']."'";
			
			$output = '<select '.$multi.' class="'.$element['class'].'" '. $id_string .' '. $name_string . ' '.$data_string.'> ';
			
			
			if(isset($element['with_first'])) { $output .= '<option value="">'.$select .'</option>  '; $fake_val = $select; }
			
			$real_entries = array();
			foreach ($entries as $key => $entry)
			{
				if(!is_array($entry))
				{
					$real_entries[$key] = $entry;
				}
				else
				{
					$real_entries['option_group_'.$key] = $key;
				
					foreach($entry as $subkey => $subentry)
					{
						$real_entries[$subkey] = $subentry;
					}
					
					$real_entries['close_option_group_'.$key] = "close";
				}
			}
			
			$entries = $real_entries;
			
			foreach ($entries as $key => $entry)
			{
				if($element['subtype'] == 'cat')
				{
					if(isset($entry->term_id))
					{
						$id = $entry->term_id;
						$title = $entry->name;
					}
				}
				else if(!is_array($element['subtype']))
				{
					$id = $entry->ID;
					$title = $entry->post_title;
				}
				else
				{
					$id = $entry;
					$title = $key;				
				}
			
				if(!empty($title) || (isset($title) && $title === 0))
				{
					if(!isset($fake_val)) $fake_val = $title;
					$selected = "";
					if ($element['std'] == $id || (is_array($element['std']) && in_array($id, $element['std']))) { $selected = "selected='selected'"; $fake_val = $title;}
					
					if(strpos ( $title , 'option_group_') === 0) 
					{
						$output .= "<optgroup label='". $id."'>";
					}
					else if(strpos ( $title , 'close_option_group_') === 0) 
					{
						$output .= "</optgroup>";
					}
					else
					{
						$output .= "<option $selected value='". $id."'>". $title."</option>";
					}
					
				}
			}
			$output .= '</select>';

			
			return $output;
		}

		
		/**
         * 
         * The radio method renders one or more input type:radio elements, based on the definition of the $elements array
         * @param array $element the array holds data like type, value, id, class, description which are necessary to render the whole option-section
         * @return string $output the string returned contains the html code generated within the method
         */
		static function radio( $element )
		{	
			$output = "";
			$counter = 1;
			foreach($element['options'] as $key => $radiobutton)
			{	
				$checked = "";
				if( $element['std'] == $key ) { $checked = 'checked = "checked"'; }
				
				$output  .= '<span class="avia_radio_wrap">';
				$output  .= '<input '.$checked.' type="radio" class="radio_'.$key.'" ';
				$output  .= 'value="'.$key.'" id="'.$element['id'].$counter.'" name="'.$element['id'].'"/>';
				
				$output  .= '<label for="'.$element['id'].$counter.'"><span class="labeltext">'.$radiobutton.'</span>';
				if(!empty($element['images'][$key])) $output  .= "<img class='radio_image' src='".$element['images'][$key]."' />";
				$output  .= '</label>';
				$output  .= '</span>';
				
				$counter++;
			}	
				
			return $output;
		}
		
		
		
		static function table ( $element , $parent)
		{
			$values = !empty($_POST['extracted_shortcode']) ? $_POST['extracted_shortcode'] : false;
			$prepared = array();
			$rows = $columns = 3;
			
			//prepare values based on the sc array
			if($values)
			{
    			foreach($values as $value)
    			{
    				switch($value['tag'])
    				{
    					case 'av_cell': $prepared['cells'][] = array('content' => stripslashes($value['content']), 'col_style' => $value['attr']['col_style']);  break;
    					case 'av_row': $prepared['rows'][] = array('row_style' => $value['attr']['row_style']);  break;
    				
    				}
    			}
			}
			
			if($prepared)
			{
				$rows = count($prepared['rows']);
				$columns = count($prepared['cells']) / $rows;
			}
			
			$params = array('class'=>'', 'parent_class' => $parent);
			
			$output  = "";
			$output .= "<div class='avia-table-builder-wrapper'>";
			
			$output .= "	<div class='avia-table-builder-add-buttons'>";
			$output .= "		<a class='avia-attach-table-row button button-primary button-large'>".__('Add Table Row','avia_framework' )."</a>";
			$output .= "		<a class='avia-attach-table-col button button-primary button-large'>".__('Add Table Column','avia_framework' )."</a>";
			$output .= "	</div>";
			
			$output .= "	<div class='avia-table'>";
			
			
			$output .= self::table_row(false, $columns,  array('class'=>'avia-table-col-style avia-attach-table-col-style avia-noselect', 'col_option' => true, 'no-edit' => true), $element, $prepared);
			
			for($i = 1; $i <= $rows; $i++)
			{
				if($prepared)
				{
					$params['row_style'] = $prepared['rows'][$i-1]['row_style'];
				}
				
				$output .= self::table_row($i, $columns, $params, $element, $prepared);
			}

			$output .= self::table_row(false, $columns,  array('class'=>'avia-template-row'), $element , $prepared);
			$output .= self::table_row(false, $columns,  array('class'=>'avia-delete-row avia-noselect', 'no-edit' => true), $element);
				
			$output .= "	</div>";
			$output .= "</div>";
			return $output;
		}
		
		static function table_row($row, $columns, $params, $element, $prepared = array())
		{
			$defaults = array('class'=>'', 'content'=>'', 'row_style'=>'');
			$params = array_merge($defaults, $params);
			$extraclass = "";
			$output  = "";
			$output .= "	<div class='avia-table-row  {$params['class']} {$params['row_style']}'>";
			
			$output .= "	<div class='avia-table-cell avia-table-cell-style avia-attach-table-row-style avia-noselect'>";
			$output .= self::select(array('std'=>$params['row_style'], 'subtype'=>$element['row_style'], 'id'=>'row_style', 'class'=>''));
			$output .= "	</div>";
			
			for($j = 1; $j <= $columns; $j++)
			{
				if($prepared)
				{
					if(!$row) $row = 1;
				
					$rows = count($prepared['rows']);
					$columns = count($prepared['cells']) / $rows;
					$key = (($row - 1) * $columns) + ($j -1);
					
					if($params['class'] == 'avia-template-row')
					{
						$params['content'] = "";
					}
					else
					{
						$params['content'] = $prepared['cells'][$key]['content'];
					}
					$extraclass = $prepared['cells'][$key]['col_style'];
				}
				
				if(isset($params['col_option']))
				{
					$params['content']  = self::select(array('std'=>$extraclass, 'subtype'=>$element['column_style'], 'id'=>'column_style', 'class'=>''));
				}
				
				if(isset($params['parent_class']) && $params['row_style'] == "avia-button-row" && strpos($params['content'], "[") !== false)
				{
				
					$params['parent_class']->builder->text_to_interface($params['content']);
					$values = end($_POST['extracted_shortcode']);
					$params['content'] = $params['parent_class']->builder->shortcode_class[$params['parent_class']->builder->shortcode[$values['tag']]]->prepare_editor_element( $values['content'], $values['attr'] );
					
				}
				
				$output .= "	<div class='avia-table-cell ".$extraclass."'>";
				$output .= "		<div class='avia-table-content'>";
				$output .= 	$params['content'] ? stripslashes($params['content']) : "";
				$output .= "		</div>";
				
				if(empty($params['no-edit']) && empty($values))
				{
					$output .= "		<textarea class='avia-table-data-container' name='content'>";
					$output .= 	$params['content'] ? stripslashes($params['content']) : "";
					$output .= 			"</textarea>";
				}
				$output .= "	</div>";
			}
			
			$output .= "	<div class='avia-table-cell avia-table-cell-delete avia-attach-delete-table-row avia-noselect'>";
			$output .= "	</div>";
			
			
			
			$output .= "	</div>";
			return $output;
		}
		

		
		
		static function display_image($img = "")
		{
			$final = array();
			
			if(preg_match('/^.*\.(jpg|jpeg|png|gif)$/i', $img))
			{
				$final[] = '<img src="'.$img.'" />';
			}
			else if(!empty($img))
			{
				$args = array('post_type' => 'attachment','numberposts' => -1, 'include'=> $img, 'orderby' => 'post__in');
				$attachments = get_posts( $args );
				
				foreach ( $attachments as $attachment ) 
				{
					$final[] = wp_get_attachment_link($attachment->ID, 'thumbnail', false, false);
				}				
			}
		
			$output = "";
			$hidden = "avia-hidden";
			
			$output .= "<div class='avia-builder-prev-img-container'>";
			if(!empty($final))
			{
				if(count($final) == 1) $hidden = "";
				
				foreach ( $final as $img ) 
				{
					$output .= "<span class='avia-builder-prev-img'>{$img}</span>";
				}
			}
			
			$output .= "</div>";
			$output .= "<a href='#delete' class='avia-delete-image {$hidden}'>".__('Remove Image', 'avia_framework' )."</a>";
			return $output;
		}
		
		
		
		
		static function number_array($from = 0, $to = 100, $steps = 1, $array = array())
		{
			for ($i = $from; $i <= $to; $i += $steps) {
			    $array[$i] = $i;
			}
		
			return $array;
		}




		static function linking_options()
		{
		    if(current_theme_supports('avia_rel_nofollow_for_links'))
			{
			    $linkoptions = array(
					__('Open in same window',  'avia_framework' ) =>'',
					__('Open in same window and use rel=nofollow',  'avia_framework' ) =>'nofollow',
					__('Open in new window',  'avia_framework' ) =>'_blank',
					__('Open in new window and use rel=nofollow',  'avia_framework' ) =>'_blank nofollow'
			    );
			}
			else
			{
			    $linkoptions = array(
					__('Open in same window',  'avia_framework' ) =>'',
					__('Open in new window',  'avia_framework' ) =>'_blank'
			    );
			}

		    return $linkoptions;
		}

	} // end class

} // end if !class_exists









