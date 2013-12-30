<?php
/**
 * Textblock
 * Shortcode which creates a text element wrapped in a div
 */

if ( !class_exists( 'avia_sc_icon_box' ) )
{
	class avia_sc_icon_box extends aviaShortcodeTemplate
	{
			/**
			 * Create the config array for the shortcode button
			 */
			function shortcode_insert_button()
			{
				$this->config['name']			= __('Icon Box', 'avia_framework' );
				$this->config['tab']			= __('Content Elements', 'avia_framework' );
				$this->config['icon']			= AviaBuilder::$path['imagesURL']."sc-icon_box.png";
				$this->config['order']			= 90;
				$this->config['target']			= 'avia-target-insert';
				$this->config['shortcode'] 		= 'av_icon_box';
				$this->config['tooltip'] 	    = __('Creates a content block with icon to the left or above', 'avia_framework' );
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
							"name" 	=> __("IconBox Icon",'avia_framework' ),
							"desc" 	=> __("Select an IconBox Icon below",'avia_framework' ),
							"id" 	=> "icon",
							"type" 	=> "iconfont",
							"std" 	=> ""),

					 array(
							"name" 	=> __("Icon Position", 'avia_framework' ),
							"desc" 	=> __("Should the icon be positioned at the left or at the top?", 'avia_framework' ),
							"id" 	=> "position",
							"type" 	=> "select",
							"std" 	=> "left",
							"subtype" => array( __('Left', 'avia_framework' )=>'left',
												__('Top',  'avia_framework' )=>'top')),

					array(
							"name" 	=> __("Title",'avia_framework' ),
							"desc" 	=> __("Add an IconBox title here",'avia_framework' ),
							"id" 	=> "title",
							"type" 	=> "input",
							"std" 	=> __("IconBox Title",'avia_framework' )),

                    array(
                        "name" 	=> __("Title Link?", 'avia_framework' ),
                        "desc" 	=> __("Do you want to apply  a link to the title?", 'avia_framework' ),
                        "id" 	=> "link",
                        "type" 	=> "linkpicker",
                        "fetchTMPL"	=> true,
                        "std"	=> "",
                        "subtype" => array(
                            __('No Link', 'avia_framework' ) =>'',
                            __('Set Manually', 'avia_framework' ) =>'manually',
                            __('Single Entry', 'avia_framework' ) =>'single',
                            __('Taxonomy Overview Page',  'avia_framework' )=>'taxonomy',
                        ),
                        "std" 	=> ""),

                    array(
                        "name" 	=> __("Open in new window", 'avia_framework' ),
                        "desc" 	=> __("Do you want to open the link in a new window", 'avia_framework' ),
                        "id" 	=> "linktarget",
                        "required" 	=> array('link', 'not', ''),
                        "type" 	=> "select",
                        "std" 	=> "",
						"subtype" => AviaHtmlHelper::linking_options()),   

					array(
							"name" 	=> __("Content",'avia_framework' ),
							"desc" 	=> __("Add some content for this IconBox",'avia_framework' ),
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
				extract(av_backend_icon($params)); // creates $font and $display_char if the icon was passed as param "icon" and the font as "font" 
				
				$inner  = "<div class='avia_iconbox avia_textblock avia_textblock_style'>";
				$inner .= "		<div ".$this->class_by_arguments('position' ,$params['args']).">";
				$inner .= "			<span ".$this->class_by_arguments('font' ,$font).">";
				$inner .= "				<span data-update_with='icon_fakeArg' class='avia_iconbox_icon'>".$display_char."</span>";
				$inner .= "			</span>";
				$inner .= "			<div class='avia_iconbox_content_wrap'>";
				$inner .= "				<h4  class='avia_iconbox_title' data-update_with='title'>".html_entity_decode($params['args']['title'])."</h4>";
				$inner .= "				<div class='avia_iconbox_content' data-update_with='content'>".stripslashes(wpautop(trim(html_entity_decode($params['content']))))."</div>";
				$inner .= "			</div>";
				$inner .= "		</div>";
				$inner .= "</div>";

				$params['innerHtml'] = $inner;
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
				extract(shortcode_atts(array('title' => 'Title', 'icon' => '1', 'position' => 'left', 'link' =>'', 'linktarget' => 'no', 'font' =>''), $atts));

				
				$display_char = av_icon($icon, $font);
				
				if($position == 'top') $position .= " main_color";

				$blank = (strpos($linktarget, '_blank') !== false || $linktarget == 'yes') ? ' target="_blank" ' : "";
				$blank .= strpos($linktarget, 'nofollow') !== false ? ' rel="nofollow" ' : "";
                $link = aviaHelper::get_url($link);
                if(!empty($link))
                {
                    $title = "<a href='$link' title='".esc_attr($title)."' $blank>$title</a>";
                }

        		// add blockquotes to the content
                $markup = avia_markup_helper(array('context' => 'entry','echo'=>false));
        		$output  = '<article class="iconbox iconbox_'.$position.' '.$meta['el_class'].'" '.$markup.'>';
        		$output .= '        <div class="iconbox_content">';
                $output .= '        <header class="entry-content-header">';
        		$output .= '            <div class="iconbox_icon heading-color" '.$display_char.'></div>';
                $markup = avia_markup_helper(array('context' => 'entry_title','echo'=>false));
        		$output .= "            <h3 class='iconbox_content_title' $markup>".$title."</h3>";
                $output .= '        </header>';

                $markup = avia_markup_helper(array('context' => 'entry_content','echo'=>false));
                $output .= "        <div class='iconbox_content_container' $markup>";
                $output .=              ShortcodeHelper::avia_apply_autop(ShortcodeHelper::avia_remove_autop($content));
                $output .= '        </div>';

                $output .= '    </div>';
                $output .= '    <footer class="entry-footer"></footer>';
                $output .= '</article>';

        		return $output;
			}

	}
}