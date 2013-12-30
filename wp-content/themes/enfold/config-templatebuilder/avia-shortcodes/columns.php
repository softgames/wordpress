<?php
/**
 * COLUMNS
 * Shortcode which creates columns for better content separation
 */

 // Don't load directly
if ( !defined('ABSPATH') ) { die('-1'); }



if ( !class_exists( 'avia_sc_columns' ) )
{
	class avia_sc_columns extends aviaShortcodeTemplate{

			static $extraClass = "";

			/**
			 * Create the config array for the shortcode button
			 */
			function shortcode_insert_button()
			{
				$this->config['name']		= '1/1';
				$this->config['icon']		= AviaBuilder::$path['imagesURL']."sc-full.png";
				$this->config['tab']		= __('Layout Elements', 'avia_framework' );
				$this->config['order']		= 100;
				$this->config['target']		= "avia-section-drop";
				$this->config['shortcode'] 	= 'av_one_full';
				$this->config['html_renderer'] 	= false;
				$this->config['tinyMCE'] 	= array('instantInsert' => "[av_one_full first]Add Content here[/av_one_full]");
				$this->config['tooltip'] 	= __('Creates a single full width column', 'avia_framework' );
				$this->config['drag-level'] = 2;
				$this->config['drop-level'] = 2;
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
				extract($params);
				$name 		= $this->config['shortcode'];
				$drag 		= $this->config['drag-level'];
				$drop 		= $this->config['drop-level'];

				$size = array('av_one_full' => '1/1', 'av_one_half' => '1/2', 'av_one_third' => '1/3', 'av_one_fourth' => '1/4', 'av_one_fifth' => '1/5', 'av_two_third' => '2/3', 'av_three_fourth' => '3/4');
				$extraClass = isset($args[0]) ? $args[0] == 'first' ? ' avia-first-col' : "" : "";

				$output  = "<div class='avia_layout_column avia_pop_class ".$name.$extraClass." av_drag' data-dragdrop-level='{$drag}' data-width='{$name}'>";
				$output .= "<div class='avia_sorthandle menu-item-handle'>";

				$output .= "<a class='avia-smaller avia-change-col-size' href='#smaller' title='".__('Decrease Column Size','avia_framework' )."'>-</a>";
				$output .= "<span class='avia-col-size'>".$size[$name]."</span>";
				$output .= "<a class='avia-bigger avia-change-col-size'  href='#bigger' title='".__('Increase Column Size','avia_framework' )."'>+</a>";
				$output .= "<a class='avia-delete'  href='#delete' title='".__('Delete Column','avia_framework' )."'>x</a>";
			    //$output .= "<a class='avia-new-target'  href='#new-target' title='".__('Move Element','avia_framework' )."'>+</a>";
				$output .= "<a class='avia-clone'  href='#clone' title='".__('Clone Column','avia_framework' )."' >".__('Clone Column','avia_framework' )."</a></div>";

				$output .= "<div class='avia_inner_shortcode avia_connect_sort av_drop ' data-dragdrop-level='{$drop}'>";
				$output .= "<textarea data-name='text-shortcode' cols='20' rows='4'>".ShortcodeHelper::create_shortcode_by_array($name, $content, $args)."</textarea>";
				if($content)
				{
					$content = $this->builder->do_shortcode_backend($content);
				}
				$output .= $content;
				$output .= "</div></div>";

				return $output;
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
				global $avia_config;

				$avia_config['current_column'] = $shortcodename;

				$first = '';
				if (isset($atts[0]) && trim($atts[0]) == 'first')  $first = 'first';


				if($first)
				{
					if(!empty($meta['siblings']['prev']['tag']) &&
					in_array($meta['siblings']['prev']['tag'], array('av_one_full','av_one_half', 'av_one_third', 'av_two_third', 'av_three_fourth' , 'av_one_fourth' , 'av_one_fifth' ,'av_textblock')))
					{
						avia_sc_columns::$extraClass = "column-top-margin";
					}
					else
					{
						avia_sc_columns::$extraClass = "";
					}
				}


				$output  = '<div class="flex_column '.$shortcodename.' '.$first.' '.$meta['el_class'].' '.avia_sc_columns::$extraClass.'">';

				//if the user uses the column shortcode without the layout builder make sure that paragraphs are applied to the text
				$content =  (empty($avia_config['conditionals']['is_builder_template'])) ? ShortcodeHelper::avia_apply_autop(ShortcodeHelper::avia_remove_autop($content)) : ShortcodeHelper::avia_remove_autop($content, true);

				$output .= $content.'</div>';

				unset($avia_config['current_column']);

				return $output;
			}
	}
}









if ( !class_exists( 'avia_sc_columns_one_half' ) )
{
	class avia_sc_columns_one_half extends avia_sc_columns{

			function shortcode_insert_button()
			{
				$this->config['name']		= '1/2';
				$this->config['icon']		= AviaBuilder::$path['imagesURL']."sc-half.png";
				$this->config['tab']		= __('Layout Elements', 'avia_framework' );
				$this->config['order']		= 90;
				$this->config['target']		= "avia-section-drop";
				$this->config['shortcode'] 	= 'av_one_half';
				$this->config['html_renderer'] 	= false;
				$this->config['tinyMCE'] 	= array('name' => '1/2 + 1/2', 'instantInsert' => "[av_one_half first]Add Content here[/av_one_half]\n\n\n[av_one_half]Add Content here[/av_one_half]");
				$this->config['tooltip'] 	= __('Creates a single column with 50% width', 'avia_framework' );
				$this->config['drag-level'] = 2;
				$this->config['drop-level'] = 2;
		}
	}
}


if ( !class_exists( 'avia_sc_columns_one_third' ) )
{
	class avia_sc_columns_one_third extends avia_sc_columns{

			function shortcode_insert_button()
			{
				$this->config['name']		= '1/3';
				$this->config['icon']		= AviaBuilder::$path['imagesURL']."sc-third.png";
				$this->config['tab']		= __('Layout Elements', 'avia_framework' );
				$this->config['order']		= 80;
				$this->config['target']		= "avia-section-drop";
				$this->config['shortcode'] 	= 'av_one_third';
				$this->config['html_renderer'] 	= false;
				$this->config['tooltip'] 	= __('Creates a single column with 33% width', 'avia_framework' );
				$this->config['drag-level'] = 2;
				$this->config['drop-level'] = 2;
				$this->config['tinyMCE'] 	= array(
		      		'name' => '1/3 + 1/3 + 1/3',
				    'instantInsert' => "[av_one_third first]Add Content here[/av_one_third]\n\n\n[av_one_third]Add Content here[/av_one_third]\n\n\n[av_one_third]Add Content here[/av_one_third]"
				                                    );
			}
	}
}

if ( !class_exists( 'avia_sc_columns_two_third' ) )
{
	class avia_sc_columns_two_third extends avia_sc_columns{

			function shortcode_insert_button()
			{
				$this->config['name']		= '2/3';
				$this->config['icon']		= AviaBuilder::$path['imagesURL']."sc-two_third.png";
				$this->config['tab']		= __('Layout Elements', 'avia_framework' );
				$this->config['order']		= 70;
				$this->config['target']		= "avia-section-drop";
				$this->config['shortcode'] 	= 'av_two_third';
				$this->config['html_renderer'] 	= false;
				$this->config['tooltip'] 	= __('Creates a single column with 67% width', 'avia_framework' );
				$this->config['drag-level'] = 2;
				$this->config['drop-level'] = 2;
				$this->config['tinyMCE'] 	= array(
				    'name' => '2/3 + 1/3',
                    'instantInsert' => "[av_two_third first]Add 2/3 Content here[/av_two_third]\n\n\n[av_one_third]Add 1/3 Content here[/av_one_third]"
				                                    );
			}
	}
}

if ( !class_exists( 'avia_sc_columns_one_fourth' ) )
{
	class avia_sc_columns_one_fourth extends avia_sc_columns{

			function shortcode_insert_button()
			{
				$this->config['name']		= '1/4';
				$this->config['icon']		= AviaBuilder::$path['imagesURL']."sc-fourth.png";
				$this->config['tab']		= __('Layout Elements', 'avia_framework' );
				$this->config['order']		= 60;
				$this->config['target']		= "avia-section-drop";
				$this->config['shortcode'] 	= 'av_one_fourth';
				$this->config['tooltip'] 	= __('Creates a single column with 25% width', 'avia_framework' );
				$this->config['html_renderer'] 	= false;
				$this->config['drag-level'] = 2;
				$this->config['drop-level'] = 2;
				$this->config['tinyMCE'] 	= array(
                    'name' => '1/4 + 1/4 + 1/4 + 1/4',
                    'instantInsert' => "[av_one_fourth first]Add Content here[/av_one_fourth]\n\n\n[av_one_fourth]Add Content here[/av_one_fourth]\n\n\n[av_one_fourth]Add Content here[/av_one_fourth]\n\n\n[av_one_fourth]Add Content here[/av_one_fourth]"
				                                    );
			}
	}
}

if ( !class_exists( 'avia_sc_columns_three_fourth' ) )
{
	class avia_sc_columns_three_fourth extends avia_sc_columns{

			function shortcode_insert_button()
			{
				$this->config['name']		= '3/4';
				$this->config['icon']		= AviaBuilder::$path['imagesURL']."sc-three_fourth.png";
				$this->config['tab']		= __('Layout Elements', 'avia_framework' );
				$this->config['order']		= 50;
				$this->config['target']		= "avia-section-drop";
				$this->config['shortcode'] 	= 'av_three_fourth';
				$this->config['tooltip'] 	= __('Creates a single column with 75% width', 'avia_framework' );
				$this->config['html_renderer'] 	= false;
				$this->config['drag-level'] = 2;
				$this->config['drop-level'] = 2;
				$this->config['tinyMCE'] 	= array(
                    'name' => '3/4 + 1/4',
                    'instantInsert' => "[av_three_fourth first]Add 3/4 Content here[/av_three_fourth]\n\n\n[av_one_fourth]Add 1/4 Content here[/av_one_fourth]"
				                                    );
			}
	}
}

if ( !class_exists( 'avia_sc_columns_one_fifth' ) )
{
	class avia_sc_columns_one_fifth extends avia_sc_columns{

			function shortcode_insert_button()
			{
				$this->config['name']		= '1/5';
				$this->config['icon']		= AviaBuilder::$path['imagesURL']."sc-fifth.png";
				$this->config['tab']		= __('Layout Elements', 'avia_framework' );
				$this->config['order']		= 40;
				$this->config['target']		= "avia-section-drop";
				$this->config['shortcode'] 	= 'av_one_fifth';
				$this->config['html_renderer'] 	= false;
				$this->config['tooltip'] 	= __('Creates a single column with 20% width', 'avia_framework' );
				$this->config['drag-level'] = 2;
				$this->config['drop-level'] = 2;
				$this->config['tinyMCE'] 	= array(
                    'name' => '1/5 + 1/5 + 1/5 + 1/5 + 1/5',
                    'instantInsert' => "[av_one_fifth first]1/5[/av_one_fifth]\n\n\n[av_one_fifth]2/5[/av_one_fifth]\n\n\n[av_one_fifth]3/5[/av_one_fifth]\n\n\n[av_one_fifth]4/5[/av_one_fifth]\n\n\n[av_one_fifth]5/5[/av_one_fifth]"
				                                    );
			}
	}
}


