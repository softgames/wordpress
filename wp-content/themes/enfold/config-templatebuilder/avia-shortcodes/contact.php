<?php
/**
 * Sidebar
 * Displays one of the registered Widget Areas of the theme
 */

if ( !class_exists( 'avia_sc_contact' ) )
{
	class avia_sc_contact extends aviaShortcodeTemplate
	{
			/**
			 * Create the config array for the shortcode button
			 */
			function shortcode_insert_button()
			{
				$this->config['name']		= __('Contact Form', 'avia_framework' );
				$this->config['tab']		= __('Content Elements', 'avia_framework' );
				$this->config['icon']		= AviaBuilder::$path['imagesURL']."sc-contact.png";
				$this->config['order']		= 40;
				$this->config['target']		= 'avia-target-insert';
				$this->config['shortcode'] 	= 'av_contact';
				$this->config['shortcode_nested'] = array('av_contact_field');
				$this->config['tooltip'] 	= __('Creates a customizable contact form', 'avia_framework' );
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
						"name" 	=> __("Your email address", 'avia_framework' ),
						"desc" 	=> __("Enter the Email address where mails should be delivered to.", 'avia_framework' ) ." (".__("Default:", 'avia_framework' ) ." ". get_option('admin_email').")",
						"id" 	=> "email",
						'container_class' =>"avia-element-fullwidth",
						"std" 	=> get_option('admin_email'),
						"type" 	=> "input"),


						array(
							"name" => __("Add/Edit Contact Form Elements", 'avia_framework' ),
							"desc" => __("Here you can add, remove and edit the form Elements of your contact form.", 'avia_framework' )."<br/>".
									  __("Available form elements are: single line Input elements, Textareas, Checkboxes and Select-Dropdown menus.", 'avia_framework' )."<br/><br/>".
									  __("It is recommended to not delete the 'E-Mail' field if you want to use an auto responder.", 'avia_framework' ),

							"type" 			=> "modal_group",
							"id" 			=> "content",
							"modal_title" 	=> __("Edit Form Element", 'avia_framework' ),
							"std"			=> array(

													array('label'=>__('Name', 'avia_framework' ), 'type'=>'text', 'check'=>'is_empty'),
													array('label'=>__('E-Mail', 'avia_framework' ), 'type'=>'text', 'check'=>'is_email'),
													array('label'=>__('Subject', 'avia_framework' ), 'type'=>'text', 'check'=>'is_empty'),
													array('label'=>__('Message', 'avia_framework' ), 'type'=>'textarea', 'check'=>'is_empty'),

													),


							'subelements' 	=> array(

									array(
									"name" 	=> __("Form Element Label", 'avia_framework' ),
									"desc" 	=> "",
									"id" 	=> "label",
									"std" 	=> "",
									"type" 	=> "input"),


							        array(
									"name" 	=> __("Form Element Type", 'avia_framework' ),
									"desc" 	=> "",
									"id" 	=> "type",
									"type" 	=> "select",
									"std" 	=> "text",
									"no_first"=>true,
									"subtype" => array(	__('Text input', 'avia_framework' ) =>'text',
														__('Text Area', 'avia_framework' ) =>'textarea',
														__('Select Element', 'avia_framework' ) =>'select',
														__('Checkbox', 'avia_framework' ) =>'checkbox',
														__('Datepicker', 'avia_framework' ) =>'datepicker'
														)),

									array(
										"name" 	=> __("Form Element Options", 'avia_framework' ) ,
										"desc" 	=> __("Enter any number of options that the visitor can choose from. Separate these Options with a comma.", 'avia_framework' ) ."<br/><small>".
												   __("Example: Option 1, Option 2, Option 3", 'avia_framework' )."</small>" ,

										"id" 	=> "options",
										"required" => array('type','equals','select'),
										"std" 	=> "",
										"type" 	=> "input"),

								    array(
									"name" 	=> __("Form Element Validation", 'avia_framework' ),
									"desc" 	=> "",
									"id" 	=> "check",
									"type" 	=> "select",
									"std" 	=> "",
									"no_first"=>true,
									"subtype" => array(	__('No Validation', 'avia_framework' ) =>'',
														__('Is not empty', 'avia_framework' ) =>'is_empty',
														__('Valid E-Mail address', 'avia_framework' ) =>'is_email',
														__('Valid Phone Number', 'avia_framework' ) =>'is_phone',
														__('Valid Number', 'avia_framework' ) =>'is_number')),

									 array(
									"name" 	=> __("Form Element Width", 'avia_framework' ),
									"desc" 	=> __("If the width is set to 50% for 2 consecutive form items they will appear beside each other instead of underneath", 'avia_framework' ) ,
									"id" 	=> "width",
									"type" 	=> "select",
									"std" 	=> "",
									"no_first"=>true,
									"subtype" => array(	"100%" =>'', "50%" =>'element_half')),

						)
					),

						array(
						"name" 	=> __("Submit Button Label", 'avia_framework' ),
						"desc" 	=> __("Enter the submit buttons label text here", 'avia_framework' ),
						"id" 	=> "button",
						"std" 	=> __("Submit", 'avia_framework' ),
						"type" 	=> "input"),

						array(
						"name" 	=> __("Form Title", 'avia_framework' ),
						"desc" 	=> __("Enter a form title that is displayed above the form", 'avia_framework' ),
						"id" 	=> "title",
						"std" 	=> __("Send us mail", 'avia_framework' ),
						"type" 	=> "input"),

						array(
						"name" 	=> __("Message Sent label", 'avia_framework' ),
						"desc" 	=> __("What should be displayed once the message is sent?", 'avia_framework' ),
						"id" 	=> "sent",
						"std" 	=> __("Your message has been sent!", 'avia_framework' ),
						"type" 	=> "input"),
						
						array(
						"name" 	=> __("E-Mail Subject", 'avia_framework' ),
						"desc" 	=> __("You can define a custom Email Subject for your form here. If left empty the subject will be", 'avia_framework' )."<br/><small>".__("New Message", 'avia_framework') . " (".__('sent by contact form at','avia_framework')." ".get_option('blogname').")</small>" ,
						"id" 	=> "subject",
						"std" 	=> __("", 'avia_framework' ),
						"type" 	=> "input"),
						
		 				array(
							"name" 	=> __("Autorespond Text", 'avia_framework' ),
							"desc" 	=> __("Enter a message that will be sent to the users email address once he has submitted the form.", 'avia_framework' )."<br/><br/>".
									   __("If left empty no auto-response will be sent.", 'avia_framework' ),
							"id" 	=> "autorespond",
							"std" 	=> "",
							"type" 	=> "textarea"
							),

						array(
							"name" 	=> __("Contact Form Captcha", 'avia_framework' ),
							"desc" 	=> __("Do you want to display a Captcha field at the end of the form so users must prove they are human by solving a simply mathematical question?", 'avia_framework' )."</br></br>". 										   __("(It is recommended to only activate this if you receive spam from your contact form, since an invisible spam protection is also implemented that should filter most spam messages by robots anyways)", 'avia_framework' ),
							"id" 	=> "captcha",
							"type" 	=> "select",
							"std" 	=> "",
							"subtype" => array(__("Don't display Captcha", 'avia_framework' ) => '', __('Display Captcha', 'avia_framework' ) =>'active')
						),


				);


			}

			/**
			 * Editor Sub Element - this function defines the visual appearance of an element that is displayed within a modal window and on click opens its own modal window
			 * Works in the same way as Editor Element
			 * @param array $params this array holds the default values for $content and $args.
			 * @return $params the return array usually holds an innerHtml key that holds item specific markup.
			 */
			function editor_sub_element($params)
			{
				$template = $this->update_template("label", __("Element", 'avia_framework' ). ": {{label}}");

				$params['content'] = NULL;
				$params['innerHtml']  = "";
				$params['innerHtml'] .= "<div class='avia_title_container' {$template}>".__("Element", 'avia_framework' ).": ".$params['args']['label']."</div>";

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
				$atts =  shortcode_atts(array('email' 		=> get_option('admin_email'),
			                                 'button' 		=> __("Submit", 'avia_framework' ),
			                                 'autorespond' 	=> '',
			                                 'captcha' 		=> '',
			                                 'subject'		=> '',
			                                 'sent'			=> __("Your message has been sent!", 'avia_framework' ),
			                                 'title'		=> __("Send us mail", 'avia_framework' ),
			                                 ), $atts);
				extract($atts);

				$post_id = function_exists('avia_get_the_id') ? avia_get_the_id() : get_the_ID();

				$form_args = array(
					"heading" 				=> $title ? "<h3>".$title."</h3>" : "",
					"success" 				=> "<h3 class='avia-form-success'>".$sent."</h3>",
					"submit"  				=> $button,
					"myemail" 				=> $email,
					"action"  				=> get_permalink($post_id),
					"myblogname" 			=> get_option('blogname'),
					"autoresponder" 		=> $autorespond,
					"autoresponder_subject" => __('Thank you for your Message!','avia_framework' ),
					"autoresponder_email" 	=> $email,
					"subject"				=> $subject,
					"form_class" 			=> $meta['el_class'],
					"multiform"  			=> true, //allows creation of multiple forms without id collision
					"label_first"  			=> true
				);
				
				if(trim($form_args['myemail']) == '') $form_args['myemail'] = get_option('admin_email');

				//form fields passed by the user
				$form_fields = $this->helper_array2form_fields(ShortcodeHelper::shortcode2array($content));

				//fake username field that is not visible. if the field has a value a spam bot tried to send the form
				$elements['avia_username']  = array('type'=>'decoy', 'label'=>'', 'check'=>'must_empty');

				//captcha field for the user to verify that he is real
				if($captcha)
				$elements['avia_age'] =	array('type'=>'captcha', 'check'=>'captcha', 'label'=> __('Please prove that you are human by solving the equation','avia_framework' ));

				//merge all fields
				$form_fields = apply_filters('avia_contact_form_elements', array_merge($form_fields, $elements));
				$form_args   = apply_filters('avia_contact_form_args', $form_args);

				$contact_form = new avia_form($form_args);
				$contact_form->create_elements($form_fields);
				$output = $contact_form->display_form(true);


				return $output;
			}



			/*helper function that converts the shortcode sub array into the format necessary for the contact form*/
			function helper_array2form_fields($base)
			{
				$form_fields = array();

				if(is_array($base))
				{
					foreach($base as $field)
					{
						$form_fields[strtolower($field['attr']['label'])] = $field['attr'];
					}
				}

				return $form_fields;
			}




	}
}
