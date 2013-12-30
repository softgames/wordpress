<?php
/**
 * Textblock
 * Shortcode which creates a text element wrapped in a div
 */

if ( !class_exists( 'avia_sc_text' ) )
{
	class avia_sc_text extends aviaShortcodeTemplate
	{
			/**
			 * Create the config array for the shortcode button
			 */
			function shortcode_insert_button()
			{
				$this->config['name']			= __('Text Block', 'avia_framework' );
				$this->config['tab']			= __('Content Elements', 'avia_framework' );
				$this->config['icon']			= AviaBuilder::$path['imagesURL']."sc-text_block.png";
				$this->config['order']			= 100;
				$this->config['target']			= 'avia-target-insert';
				$this->config['shortcode'] 		= 'av_textblock';
				$this->config['tinyMCE'] 	    = array('disable' => true);
				$this->config['tooltip'] 	    = __('Creates a simple text block', 'avia_framework' );

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
							"name" 	=> __("Content",'avia_framework' ),
							"desc" 	=> __("Enter some content for this textblock",'avia_framework' ),
							"id" 	=> "content",
							"type" 	=> "tiny_mce",
							"std" 	=> __("Click here to add your own text", "avia_framework" )),
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
				$params['class'] = "";
				$params['innerHtml'] = "<div class='avia_textblock avia_textblock_style' data-update_with='content'>".stripslashes(wpautop(trim(html_entity_decode( $params['content']) )))."</div>";
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
				$custom_class = !empty($meta['custom_class']) ? $meta['custom_class'] : "";
                $output = '';
                $markup = avia_markup_helper(array('context' => 'entry','echo'=>false));
                $markup_text = avia_markup_helper(array('context' => 'entry_content','echo'=>false));

                $output .= '<section class="av_textblock_section" '.$markup.'>';
                $output .= "<div class='avia_textblock {$custom_class}' $markup_text>".ShortcodeHelper::avia_apply_autop(ShortcodeHelper::avia_remove_autop($content) )."</div>";
                $output .= '</section>';

                return $output;
			}

	}
}
