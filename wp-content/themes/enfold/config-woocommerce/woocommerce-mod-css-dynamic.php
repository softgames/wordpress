<?php
/*add color styles

Example of available values
$bg 				=> #222222
$bg2 				=> #f8f8f8
$primary 			=> #c8ccc2
$secondary			=> #182402
$color	 			=> #ffffff
$border 			=> #e1e1e1
$img 				=> /wp-content/themes/skylink/images/background-images/dashed-cross-dark.png
$pos 				=> top left
$repeat 			=> no-repeat
$attach 			=> scroll
$heading 			=> #eeeeee
$meta 				=> #888888
$background_image	=> #222222 url(/wp-content/themes/skylink/images/background-images/dashed-cross-dark.png) top left no-repeat scroll
*/

if(!function_exists('avia_woo_dynamic_css'))
{
	add_filter('avia_dynamic_css_output', 'avia_woo_dynamic_css', 10, 2); 

	function avia_woo_dynamic_css($output, $color_set)
	{
		/*color sets*/
		foreach ($color_set as $key => $colors) // iterates over the color sets: usually $key is either: header_color, main_color, footer_color, socket_color
		{
			$key = ".".$key;
			extract($colors);
			
			$dark_bg2 = avia_backend_calculate_similar_color($bg2, 'darker', 1);
			
			$output .= "
			
			$key .cart_dropdown .dropdown_widget li a, #top $key  .avia_cart_buttons .button,  #top $key .dropdown_widget .buttons .button, $key .cart_dropdown_first .cart_dropdown_link {
			color: $color;
			}
			
			$key .woocommerce-tabs .tabs a, $key .product_meta, $key .quantity input.qty, $key .cart_dropdown .dropdown_widget, $key .avia_select_fake_val, $key address, $key .product>a $key .product_excerpt, $key .term_description, #top $key .price .from, #top #wrap_all $key del, $key .dynamic-title .dynamic-heading, $key .dynamic-title a{
			color: $meta;
			}
			
			$key div.product .woocommerce-tabs ul.tabs li.active a, $key .cart_dropdown .dropdown_widget .widget_shopping_cart_content,  $key .cart_dropdown_link, $key .inner_product_header, $key .avia-arrow, #top $key .variations select, #top $key .variations input, #top $key #reviews input[type='text'], $key #reviews .comment-text, $key #reviews #comment, $key .single-product-main-image .images a, #top $key .shop_table.cart .input-text, #top $key form.login .input-text, #top $key form.register .input-text{
			background-color: $bg;
			}
			
			$key .woocommerce-tabs .tabs .active, $key div.product .woocommerce-tabs .panel, $key .activeslideThumb, $key #payment li, $key .widget_price_filter .ui-slider-horizontal .ui-slider-range,  $key .avia_cart, $key form.login, $key form.register, $key .col-1, $key .col-2, $key .variations_form,  $key .dynamic-title, $key .single-product-main-image .thumbnails a , $key .quantity input.qty, $key .avia_cart_buttons,  #top  $key .dropdown_widget .buttons, div .dropdown_widget .cart_list li:hover, $key .woocommerce-info{
			background-color: $bg2;
			}
			
			$key .thumbnail_container img, #top $key #main .order_details {
			border-color: $border;
			}
			
			$key .summary div{
			border-color: $bg2;
			}
			
			$key .widget_layered_nav ul li.chosen, $key .widget_price_filter .price_slider_wrapper .price_slider .ui-slider-handle, #top $key a.remove, #top $key .onsale{
			background-color: $primary;
			}
			
			$key #shop_header a:hover, #top  $key .widget_layered_nav ul li.chosen a, #top $key .widget_layered_nav ul li.chosen small{
			color: #fff;
			}
			
			#top $key .price, $key .stock, #top #wrap_all $key ins{
			color:$primary;
			}
			
			$key .dynamic-title a:hover{
			color:$secondary;
			}
			
			$key .widget_price_filter .price_slider_wrapper .ui-widget-content{
			background: $border;
			}
			
			#top $key .chzn-container-single .chzn-single{
			border-color: $border;
			background-color: $bg2;
			color:$meta;
			}
			
			$key #payment {
			background-color: $bg2;
			}
			
			
			#top $key .quantity input.plus, #top $key .quantity input.minus {
			border-color: $border;
			background-color: $dark_bg2;
			color:$meta;
			}
			
	
			";
			
			
			//sort menu
			$output .= "
			
			$key .sort-param > li > span, $key .sort-param > li > a, $key .sort-param ul{
			background-color: $bg2;
			}
			
			$key .sort-param > li:hover > span, $key .sort-param > li:hover > a, $key .sort-param > li:hover ul, $key .product-sorting strong{
			color:$heading;
			}
			
			$key .sort-param  a{
			color:$meta;
			}
			
			#top $key .sort-param  a:hover{
			color:$secondary;
			}
			
			$key .avia-bullet{
			border-color: $meta;
			}
			
			#top $key a:hover .avia-bullet{
			border-color: $secondary;
			}
			
			$key .sort-param  .current-param a{
			color:$primary;
			}
			
			$key .sort-param .current-param .avia-bullet{
			border-color:$primary;
			}
			
			";
			
			if($key == '.main_color')
			{
				$output .= "
			
				.added_to_cart_notification, .added_to_cart_notification .avia-arrow{
				background-color: $bg;
				color: $meta;
				border-color: $border;
				}
				
				.added_to_cart_notification strong{
				color:$heading;
				}
				
				";
				
			}
			
			
			//unset all vars with the help of variable vars :)
			foreach($colors as $key=>$val){ unset($$key); }
		}
		
		return $output;
	}
}
		
		
		
		
	