<?php


function avia_woocommerce_enabled()
{
	if ( class_exists( 'woocommerce' ) ){ return true; }
	return false;
}


global $avia_config;

//product thumbnails
$avia_config['imgSize']['shop_thumbnail'] 	= array('width'=>120, 'height'=>120);
$avia_config['imgSize']['shop_catalog'] 	= array('width'=>450, 'height'=>450);
$avia_config['imgSize']['shop_single'] 		= array('width'=>450, 'height'=>999, 'crop' => false);

avia_backend_add_thumbnail_size($avia_config);

include('admin-options.php');
include('admin-import.php');
include( 'woocommerce-mod-css-dynamic.php');

add_theme_support( 'woocommerce' );

//check if the plugin is enabled, otherwise stop the script
if(!avia_woocommerce_enabled()) { return false; }


//register my own styles, remove wootheme stylesheet
if(!is_admin()){
	add_action('init', 'avia_woocommerce_register_assets');
}



function avia_woocommerce_register_assets()
{
	wp_enqueue_style( 'avia-woocommerce-css', AVIA_BASE_URL.'config-woocommerce/woocommerce-mod.css');
	wp_enqueue_script( 'avia-woocommerce-js', AVIA_BASE_URL.'config-woocommerce/woocommerce-mod.js', array('jquery'), 1, true);
}

define('WOOCOMMERCE_USE_CSS', false);






######################################################################
# config
######################################################################

//add avia_framework config defaults

$avia_config['shop_overview_column']  = get_option('avia_woocommerce_column_count');  // columns for the overview page
$avia_config['shop_overview_products']= get_option('avia_woocommerce_product_count'); // products for the overview page

$avia_config['shop_single_column'] 	 	 = 4;			// columns for related products and upsells
$avia_config['shop_single_column_items'] = 4;	// number of items for related products and upsells
$avia_config['shop_overview_excerpt'] = false;		// display excerpt

if(!$avia_config['shop_overview_column']) $avia_config['shop_overview_column'] = 3;


######################################################################
# Create the correct template html structure
######################################################################

//remove woo defaults
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
remove_action( 'woocommerce_pagination', 'woocommerce_catalog_ordering', 20 );
remove_action( 'woocommerce_pagination', 'woocommerce_pagination', 10 );
remove_action( 'woocommerce_before_single_product', array($woocommerce, 'show_messages'), 10);



//add theme actions && filter
add_action( 'woocommerce_after_shop_loop_item_title', 'avia_woocommerce_overview_excerpt', 10);
add_filter( 'loop_shop_columns', 'avia_woocommerce_loop_columns');
add_filter( 'loop_shop_per_page', 'avia_woocommerce_product_count' );

//single page adds
add_action( 'avia_add_to_cart', 'woocommerce_template_single_add_to_cart', 30, 2 );



/*update woocommerce v2*/

remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 ); /*remove result count above products*/
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 ); /*remove woocommerce ordering dropdown*/
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 ); //remove rating
remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 ); //remove woo pagination


######################################################################
# FUNCTIONS
######################################################################


#
# removes the default post image from shop overview pages and replaces it with this image
#
add_action( 'woocommerce_before_shop_loop_item_title', 'avia_woocommerce_thumbnail', 10);
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);

function avia_woocommerce_thumbnail($asdf)
{
	global $product, $avia_config;
	$rating = $product->get_rating_html(); //get rating

	$id = get_the_ID();
	$size = 'shop_catalog';

	echo "<div class='thumbnail_container'>";
		echo avia_woocommerce_gallery_first_thumbnail( $id , $size);
		echo get_the_post_thumbnail( $id , $size );
		if(!empty($rating)) echo "<span class='rating_container'>".$rating."</span>";
		if($product->product_type == 'simple') echo "<span class='cart-loading'></span>";
	echo "</div>";
}


function avia_woocommerce_gallery_first_thumbnail($id, $size, $id_only = false)
{
	$active_hover = get_post_meta( $id, '_product_hover', true );

	if(!empty($active_hover))
	{
		$product_gallery = get_post_meta( $id, '_product_image_gallery', true );

		if(!empty($product_gallery))
		{
			$gallery	= explode(',',$product_gallery);
			$image_id 	= $gallery[0];
			
			//return id only
			if(!empty($id_only)) return $image_id;
			
			$image 		= wp_get_attachment_image( $image_id, $size, false, array( 'class' => "attachment-$size avia-product-hover" ));
			
			//return image
			if(!empty($image)) return $image;
		}
	}
}




#
# add ajax cart / options buttons to the product
#

add_action( 'woocommerce_after_shop_loop_item', 'avia_add_cart_button', 16);
function avia_add_cart_button()
{
	global $product, $avia_config;

	if ($product->product_type == 'bundle' ){
		$product = new WC_Product_Bundle($product->id);
	}

	$extraClass  = "";

	ob_start();
	woocommerce_template_loop_add_to_cart();
	$output = ob_get_clean();

	if(!empty($output))
	{
		$pos = strpos($output, ">");

		if ($pos !== false) {
		    $output = substr_replace($output,"><span ".av_icon_string('cart')."></span> ", $pos , strlen(1));
		}
	}


	if($product->product_type == 'variable' && empty($output))
	{
		$output = "<a class='add_to_cart_button button product_type_variable' href='".get_permalink($product->id)."'><span ".av_icon_string('details')."></span> ".__('Select options','avia_framework')."</a>";
	}

	if($product->product_type == 'simple')
	{
		$output .= "<a class='button show_details_button' href='".get_permalink($product->id)."'><span ".av_icon_string('details')."></span>  ".__('Show Details','avia_framework')."</a>";
	}
	else
	{
		$extraClass  = "single_button";
	}

	if(empty($extraClass)) $output .= " <span class='button-mini-delimiter'></span>";


	if($output && !post_password_required()) echo "<div class='avia_cart_buttons $extraClass'>$output</div>";
}





#
# wrap products on overview pages into an extra div for improved styling options. adds "product_on_sale" class if prodct is on sale
#

add_action( 'woocommerce_before_shop_loop_item', 'avia_shop_overview_extra_div', 5);
function avia_shop_overview_extra_div()
{
	global $product;
	$product_class = $product->is_on_sale() ? "product_on_sale" : "";

	echo "<div class='inner_product main_color wrapped_style noLightbox $product_class'>";
}

add_action( 'woocommerce_after_shop_loop_item',  'avia_close_div', 1000);
function avia_close_div()
{
	echo "</div>";
}


#
# wrap product titles and sale number on overview pages into an extra div for improved styling options
#

add_action( 'woocommerce_before_shop_loop_item_title', 'avia_shop_overview_extra_header_div', 20);
function avia_shop_overview_extra_header_div()
{
	echo "<div class='inner_product_header'><div class='avia-arrow'></div>";
}

add_action( 'woocommerce_after_shop_loop_item_title',  'avia_close_div', 1000);


#
# remove on sale badge from usual location and add it to the bottom of the product
#
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);


#
# create the shop navigation with account links, as well as cart and checkout, called as fallback function by the wp_nav_menu function in header.php
#
function avia_shop_nav($args)
{
	$output = "";
	$url = avia_collect_shop_urls();

	$output .= "<ul>";

	if( is_user_logged_in() )
	{
		$current = $sub1 = $sub2 = $sub3 = "";
		if(is_account_page()) $current = "current-menu-item";
		if(is_page(get_option('woocommerce_change_password_page_id'))) $sub1 = "current-menu-item";
		if(is_page(get_option('woocommerce_edit_address_page_id'))) $sub2 = "current-menu-item";
		if(is_page(get_option('woocommerce_view_order_page_id'))) $sub3 = "current-menu-item";


		$output .= "<li class='$current account_overview_link'><a href='".$url['account_overview']."'>".__('My Account', 'avia_framework')."</a>";
			$output .= "<ul>";
			$output .= "<li class='$sub1 account_change_pw_link'><a href='".$url['account_change_pw']."'>".__('Change Password', 'avia_framework')."</a></li>";
			$output .= "<li class='$sub2 account_edit_adress_link'><a href='".$url['account_edit_adress']."'>".__('Edit Address', 'avia_framework')."</a></li>";
			$output .= "<li class='$sub3 account_view_order_link'><a href='".$url['account_view_order']."'>".__('View Order', 'avia_framework')."</a></li>";
			$output .= "</ul>";
		$output .= "</li>";
		$output .= "<li class='account_logout_link'><a href='".$url['logout']."'>".__('Log Out', 'avia_framework')."</a></li>";
	}
	else
	{
		$sub1 = $sub2 = "";
		if(is_page(get_option('woocommerce_myaccount_page_id')))
		{
			if(isset($_GET['account_visible']) && $_GET['account_visible'] == 'register') $sub1 = "current-menu-item";
			if(isset($_GET['account_visible']) && $_GET['account_visible'] == 'login') $sub2 = "current-menu-item";
		}

		$url_param = strpos($url['account_overview'], '?') === false ? "?" : "&";

		if (get_option('woocommerce_enable_myaccount_registration') =='yes')
		{
			$output .= "<li class='register_link $sub1'><a href='".$url['account_overview'].$url_param."account_visible=register'>".__('Register', 'avia_framework')."</a></li>";
		}

		$output .= "<li class='login_link $sub2'><a href='".$url['account_overview'].$url_param."account_visible=login'>".__('Log In', 'avia_framework')."</a></li>";
	}

	$output .= "</ul>";

	if($args['echo'] == true)
	{
		echo $output;
	}
	else
	{
		return $output;
	}
}


#
# helper function that collects all the necessary urls for the shop navigation
#

function avia_collect_shop_urls()
{
	global $woocommerce;

	$url['cart']				= $woocommerce->cart->get_cart_url();
	$url['checkout']			= $woocommerce->cart->get_checkout_url();
	$url['account_overview'] 	= get_permalink(get_option('woocommerce_myaccount_page_id'));
	$url['account_edit_adress']	= get_permalink(get_option('woocommerce_edit_address_page_id'));
	$url['account_view_order']	= get_permalink(get_option('woocommerce_view_order_page_id'));
	$url['account_change_pw'] 	= get_permalink(get_option('woocommerce_change_password_page_id'));
	$url['logout'] 				= wp_logout_url(home_url('/'));

	return $url;
}




#
# check which page is displayed and if the automatic sidebar menu for subpages should be prevented
#
add_filter( 'avf_sidebar_menu_filter', 'avia_woocommerce_sidebar_filter');

function avia_woocommerce_sidebar_filter($menu)
{
	$id = avia_get_the_ID();
	if(is_cart() || is_checkout() || get_option('woocommerce_thanks_page_id') == $id){$menu = "";}
	return $menu;
}


#
# check if a single product is displayed and always set the sidebar styling to that of a right sidebar
#
add_filter( 'avf_sidebar_position', 'avia_woocommerce_sidebar_pos');

function avia_woocommerce_sidebar_pos($sidebar)
{
	if(is_product())
	{
		$sidebar = "sidebar_right";
	}

	return $sidebar;
}



function avia_add_to_cart($post, $product )
{
	echo "<div class='avia_cart avia_cart_".$product->product_type."'>";
	do_action( 'avia_add_to_cart', $post, $product );
	echo "</div>";
}



#
# replace thumbnail image size with full size image on single pages
#
/*

add_filter( 'single_product_small_thumbnail_size', 'avia_woocommerce_thumb_size');

function avia_woocommerce_thumb_size()
{
	return 'shop_single';
}
*/




#
# if we are viewing a woocommerce page modify the breadcrumb nav
#

if(!function_exists('avia_woocommerce_breadcrumb'))
{
	add_filter('avia_breadcrumbs_trail','avia_woocommerce_breadcrumb');

	function avia_woocommerce_breadcrumb($trail)
	{
		global $avia_config;

		if(is_woocommerce())
		{

			$home 		= $trail[0];
			$last 		= array_pop($trail);
			$shop_id 	= woocommerce_get_page_id('shop');
			$taxonomy 	= "product_cat";

			// on the shop frontpage simply display the shop name, rather than shop name + "All Products"
			if(is_shop())
			{
				if(!empty($shop_id) && $shop_id  != -1)  $trail = array_merge( $trail, avia_breadcrumbs_get_parents( $shop_id ) );
				$last = "";

				if(is_search())
				{
					$last = __('Search results for:','avia_framework').' '.esc_attr($_GET['s']);
				}
			}

			// on the product page single page modify the breadcrumb to read [home] [if available:parent shop pages] [shop] [if available:parent categories] [category] [title]
			if(is_product())
			{
				//fetch all product categories and search for the ones with parents. if none are avalaible use the first category found
				$product_category = $parent_cat = array();
				$temp_cats = get_the_terms(get_the_ID(), $taxonomy);

				if(!empty($temp_cats))
				{
					foreach($temp_cats as $key => $cat)
					{
						if($cat->parent != 0 && !in_array($cat->term_taxonomy_id, $parent_cat))
						{
							$product_category[] = $cat;
							$parent_cat[] = $cat->parent;
						}
					}

					//if no categories with parents use the first one
					if(empty($product_category)) $product_category[] = reset($temp_cats);

				}
				//unset the trail and build our own
				unset($trail);

				$trail[0] = $home;
				if(!empty($shop_id) && $shop_id  != -1)    $trail = array_merge( $trail, avia_breadcrumbs_get_parents( $shop_id ) );
				if(!empty($parent_cat)) $trail = array_merge( $trail, avia_breadcrumbs_get_term_parents( $parent_cat[0] , $taxonomy ) );
				if(!empty($product_category)) $trail[] = '<a href="' . get_term_link( $product_category[0]->slug, $taxonomy ) . '" title="' . esc_attr( $product_category[0]->name ) . '">' . $product_category[0]->name . '</a>';

			}


			// add the [shop] trail to category/tag pages: [home] [if available:parent shop pages] [shop] [if available:parent categories] [category/tag]
			if(is_product_category() || is_product_tag())
			{
				if(!empty($shop_id) && $shop_id  != -1)
				{
					$shop_trail = avia_breadcrumbs_get_parents( $shop_id ) ;
					array_splice($trail, 1, 0, $shop_trail);
				}
			}

			if(is_product_tag())
			{
				$last = __("Tag",'avia_framework').": ".$last;
			}


			if(!empty($last)) $trail[] = $last;
		}

		return $trail;
	}

}



#
# creates the avia framework container arround the shop pages
#
add_action( 'woocommerce_before_main_content', 'avia_woocommerce_before_main_content', 10);


function avia_woocommerce_before_main_content()
{
	global $avia_config;

	if(!isset($avia_config['shop_overview_column'])) $avia_config['shop_overview_column'] = "auto";
	if($new = avia_post_meta( get_option('woocommerce_shop_page_id'), 'layout'))
	{
		 //split up result
	 	 $results = explode(' : ', $new);
	 	 foreach($results as $result)
	 	 {
	 	 	$result = explode('|', $result);
	 	 	$avia_config[$result[0]] = $result[1];
	 	 }

	 	 $avia_config['layout'] = apply_filters('avia_layout_filter',  $avia_config['layout']);
	}

	$title_args = array();

	if(is_woocommerce())
	{
		$t_link = "";

		if(is_shop()) $title  = get_option('woocommerce_shop_page_title');

		$shop_id = woocommerce_get_page_id('shop');
		if($shop_id && $shop_id != -1)
		{
			if(empty($title)) $title = get_the_title($shop_id);
			$t_link = get_permalink($shop_id);
		}

		if(!$title) $title  = __("Shop",'avia_framework');

		if(is_product_category() || is_product_tag())
        {
            global $wp_query;
            $tax = $wp_query->get_queried_object();
            $title = $tax->name;
            $t_link = '';
        }

		$title_args = array('title' => $title, 'link' => $t_link);
	}

	if( get_post_meta(get_the_ID(), 'header', true) != 'no') echo avia_title($title_args);

	echo "<div class='container_wrap container_wrap_first main_color ".avia_layout_class( 'main' , false )." template-shop shop_columns_".$avia_config['shop_overview_column']."'>";

		echo "<div class='container'>";

		if(!is_singular()) { $avia_config['overview'] = true; }
}

#
# closes the avia framework container arround the shop pages
#

add_action( 'woocommerce_after_main_content', 'avia_woocommerce_after_main_content', 10);
function avia_woocommerce_after_main_content()
{

	global $avia_config;
	$avia_config['currently_viewing'] = "shop";

			//reset all previous queries
			wp_reset_query();

			//get the sidebar
			if(!is_singular())
			get_sidebar();

	//	echo "</div>"; // end container - gets already closed at the top of footer.php

		echo "</div>"; // end tempate-shop content
		echo "</div>"; // close default .container_wrap element
}




#
# wrap an empty product search into extra div
#
add_action( 'woocommerce_before_main_content', 'avia_woocommerce_404_search', 9111);
function avia_woocommerce_404_search()
{
	global $wp_query;

	if( (is_search() || is_archive()) && empty($wp_query->found_posts) )
	{
		echo "<div class='template-page template-search template-search-none content ".avia_layout_class( 'content', false )." units'>";
		echo "<div class='entry entry-content-wrapper' id='search-fail'>";
	}
}

add_action( 'woocommerce_after_main_content', 'avia_woocommerce_404_search_close', 1);
function avia_woocommerce_404_search_close()
{
	global $wp_query;

	if( (is_search() || is_shop() || is_archive()) && empty($wp_query->found_posts) )
	{
		get_template_part('includes/error404');
		echo "</div>";
		echo "</div>"; // close default .container_wrap element
	}
}




#
# modifies the class of a page so we can display single login and single register
#
add_filter( 'avia_layout_class_filter_main', 'avia_register_login_class');

function avia_register_login_class($layout)
{
	if(isset($_GET['account_visible']))
	{
		if($_GET['account_visible'] == 'register') $layout .= " template-register";
		if($_GET['account_visible'] == 'login') $layout .= " template-login";
	}

	return $layout;
}







#
# creates the avia framework content container arround the shop loop
#
add_action( 'woocommerce_before_shop_loop', 'avia_woocommerce_before_shop_loop', 1);

function avia_woocommerce_before_shop_loop()
{

	global $avia_config;
	if(isset($avia_config['dynamic_template'])) return;
    $markup = avia_markup_helper(array('context' => 'content','echo'=>false,'post_type'=>'products'));
	echo "<main class='template-shop content ".avia_layout_class( 'content' , false)." units' $markup><div class='entry-content-wrapper'>";
}

#
# closes the avia framework content container arround the shop loop
#
add_action( 'woocommerce_after_shop_loop', 'avia_woocommerce_after_shop_loop', 10);

function avia_woocommerce_after_shop_loop()
{
			global $avia_config;
			if(isset($avia_config['dynamic_template'])) return;
			if(isset($avia_config['overview'] )) echo avia_pagination('', 'nav');
			echo "</div></main>"; //end content
}



#
# echo the excerpt
#
function avia_woocommerce_overview_excerpt()
{
	global $avia_config;

	if(!empty($avia_config['shop_overview_excerpt']))
	{
		echo "<div class='product_excerpt'>";
		the_excerpt();
		echo "</div>";
	}
}



#
# creates the preview images based on page/category image
#
remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
remove_action( 'woocommerce_product_archive_description', 'woocommerce_product_archive_description', 10 );

add_action( 'woocommerce_before_shop_loop', 'avia_woocommerce_overview_banner_image', 10);
add_action( 'woocommerce_before_shop_loop', 'woocommerce_taxonomy_archive_description', 11 );
//add_action( 'woocommerce_before_shop_loop', 'woocommerce_product_archive_description', 12 ); //causes warning


function avia_woocommerce_overview_banner_image()
{
	global $avia_config;
	if(avia_is_dynamic_template() || is_paged() || is_search() ) return false;

	$image_size = "entry_with_sidebar";
	$layout = avia_layout_class( 'main' , false );
	if($layout == 'fullsize') $image_size = 'entry_without_sidebar';

	if(is_shop())
	{
		$shop_id = woocommerce_get_page_id('shop');
		if($shop_id != -1)
		{
			$image = get_the_post_thumbnail($shop_id, $image_size);
			if($image) echo "<div class='page-thumb'>{$image}</div>";
		}
	}

	if(is_product_category())
	{
		global $wp_query;
		$image	= "";
		if(isset($wp_query->query_vars['taxonomy']))
		{
			$term 			= get_term_by( 'slug', get_query_var($wp_query->query_vars['taxonomy']), $wp_query->query_vars['taxonomy']);

			if(!empty($term->term_id))
			{
				$attachment_id 	= get_woocommerce_term_meta($term->term_id, 'thumbnail_id');

				if(!empty($attachment_id))
				{
					$image = wp_get_attachment_image( $attachment_id, $image_size, false, array('class'=>'category_thumb'));
					if($image) echo "<div class='page-thumb'>{$image}</div>";
				}
			}
		}
	}

}







#
# creates the title + description for overview pages
#
function avia_woocommerce_advanced_title()
{

	global $wp_query;
	$titleClass 	= "";
	$image		 	= "";


	if(!empty($attachment_id))
	{
		$titleClass .= "title_container_image ";
		$image		= wp_get_attachment_image( $attachment_id, 'thumbnail', false, array('class'=>'category_thumb'));
	}

	echo "<div class='extralight-border title_container shop_title_container $titleClass'>";
	//echo avia_breadcrumbs();
	woocommerce_catalog_ordering();
	echo $image;
}





#
# removes the default post image from shop overview pages and replaces it with this image
#
add_action( 'ava_main_header', 'avia_woocommerce_cart_dropdown', 10);

function avia_woocommerce_cart_dropdown()
{
	global $woocommerce, $avia_config;
	$cart_subtotal = $woocommerce->cart->get_cart_subtotal();
	$link = $woocommerce->cart->get_cart_url();


	$output = "";
	$output .= "<ul class = 'cart_dropdown' data-success='".__('was added to the cart', 'avia_framework')."'><li class='cart_dropdown_first'>";
	$output .= "<a class='cart_dropdown_link' href='".$link."'><span ".av_icon_string('cart')."></span></a><!--<span class='cart_subtotal'>".$cart_subtotal."</span>-->";
	$output .= "<div class='dropdown_widget dropdown_widget_cart'><div class='avia-arrow'></div>";
	$output .= '<div class="widget_shopping_cart_content"></div>';
	$output .= "</div>";
	$output .= "</li></ul>";

	echo $output;
}



#
# modify shop overview column count
#
function avia_woocommerce_loop_columns()
{
	global $avia_config;
	return $avia_config['shop_overview_column'];
}


#
# modify shop overview product count
#

function avia_woocommerce_product_count()
{
	global $avia_config;
	return $avia_config['shop_overview_products'];
}


#
# filter cross sells on the cart page. display 4 on fullwidth pages and 3 on carts with sidebar
#

add_filter('woocommerce_cross_sells_total', 'avia_woocommerce_cross_sale_count');
add_filter('woocommerce_cross_sells_columns', 'avia_woocommerce_cross_sale_count');

function avia_woocommerce_cross_sale_count($count)
{
	return 4;
}

#
# move cross sells bellow the shipping
#

remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
add_action( 'woocommerce_after_cart', 'woocommerce_cross_sell_display' , 10);




#
# display tabs and related items within the summary wrapper
#
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
add_action(    'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 1 );



#
# display upsells and related products within dedicated div with different column and number of products
#
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products',20);
remove_action( 'woocommerce_after_single_product', 'woocommerce_output_related_products',10);
add_action( 'woocommerce_after_single_product_summary', 'avia_woocommerce_output_related_products', 20);

function avia_woocommerce_output_related_products()
{
	global $avia_config;
	$output = "";

	ob_start();
	woocommerce_related_products($avia_config['shop_single_column_items'],$avia_config['shop_single_column']); // X products, X columns
	$content = ob_get_clean();
	if($content)
	{
		$output .= "<div class='product_column product_column_".$avia_config['shop_single_column']."'>";
		//$output .= "<h3>".(__('Related Products', 'avia_framework'))."</h3>";
		$output .= $content;
		$output .= "</div>";
	}

	$avia_config['woo_related'] = $output;

}

remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
remove_action( 'woocommerce_after_single_product', 'woocommerce_upsell_display',10);
add_action( 'woocommerce_after_single_product_summary', 'avia_woocommerce_output_upsells', 21); // needs to be called after the "related product" function to inherit columns and product count

function avia_woocommerce_output_upsells()
{
	global $avia_config;

	$output = "";

	ob_start();
	woocommerce_upsell_display($avia_config['shop_single_column_items'],$avia_config['shop_single_column']); // 4 products, 4 columns
	$content = ob_get_clean();
	if($content)
	{
		$output .= "<div class='product_column product_column_".$avia_config['shop_single_column']."'>";
		//$output .= "<h3>".(__('You may also like', 'avia_framework'))."</h3>";
		$output .= $content;
		$output .= "</div>";
	}

	$avia_config['woo_upsells'] = $output;

}

add_action( 'woocommerce_after_single_product_summary', 'avia_woocommerce_display_output_upsells', 30); //display the related products and upsells

function avia_woocommerce_display_output_upsells()
{
	global $avia_config;

	$products = $avia_config['woo_upsells'].$avia_config['woo_related'];

	if(!empty($products))
	{

		$output  = "</div></div></div>";
		$output .= '<div id="av_section_1" class="avia-section alternate_color avia-section-small  container_wrap fullsize"><div class="container"><div class="template-page content  twelve alpha units">';
		$output .= $products;

		echo $output;
	}
}



#
# wrap single product image in an extra div
#
add_action( 'woocommerce_before_single_product_summary', 'avia_add_image_div', 2);
add_action( 'woocommerce_before_single_product_summary',  'avia_close_image_div', 20);
function avia_add_image_div()
{
	echo "<div class='four units single-product-main-image alpha'>";
}

function avia_close_image_div()
{
	global $avia_config;
	$avia_config['currently_viewing'] = "shop_single";
	get_sidebar();
	echo "</div>";
}


#
# wrap single product summary in an extra div
#
add_action( 'woocommerce_before_single_product_summary', 'avia_add_summary_div', 25);
add_action( 'woocommerce_after_single_product_summary',  'avia_close_div', 3);
function avia_add_summary_div()
{
	echo "<div class='eight units single-product-summary'>";
}


//remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );







#
# displays a front end interface for modifying the shoplist query parameters like sorting order, product count etc
#
if(!function_exists('avia_woocommerce_frontend_search_params'))
{
	add_action( 'woocommerce_before_shop_loop', 'avia_woocommerce_frontend_search_params', 20);

	function avia_woocommerce_frontend_search_params()
	{
		global $avia_config;

		if(!empty($avia_config['woocommerce']['disable_sorting_options'])) return false;

		$product_order['default'] 	= __("Default Order",'avia_framework');
		$product_order['title'] 	= __("Name",'avia_framework');
		$product_order['price'] 	= __("Price",'avia_framework');
		$product_order['date'] 		= __("Date",'avia_framework');
		$product_order['popularity'] = __("Popularity",'avia_framework');

		$product_sort['asc'] 		= __("Click to order products ascending",  'avia_framework');
		$product_sort['desc'] 		= __("Click to order products descending",  'avia_framework');

		$per_page_string 		 	= __("Products per page",'avia_framework');


		$per_page 		 		 	= get_option('avia_woocommerce_product_count');
		if(!$per_page) $per_page 	= get_option('posts_per_page');
		if(!empty($avia_config['woocommerce']['default_posts_per_page'])) $per_page = $avia_config['woocommerce']['default_posts_per_page'];


		parse_str($_SERVER['QUERY_STRING'], $params);

		$po_key = !empty($avia_config['woocommerce']['product_order']) ? $avia_config['woocommerce']['product_order'] : 'default';
		$ps_key = !empty($avia_config['woocommerce']['product_sort'])  ? $avia_config['woocommerce']['product_sort'] : 'asc';
		$pc_key = !empty($avia_config['woocommerce']['product_count']) ? $avia_config['woocommerce']['product_count'] : $per_page;

		$ps_key = strtolower($ps_key);

		//generate markup
		$output  = "";
		$output .= "<div class='product-sorting'>";
		$output .= "    <ul class='sort-param sort-param-order'>";
		$output .= "    	<li><span class='currently-selected'>".__("Sort by",'avia_framework')." <strong>".$product_order[$po_key]."</strong></span>";
		$output .= "    	<ul>";
		$output .= "    	<li".avia_woo_active_class($po_key, 'default')."><a href='".avia_woo_build_query_string($params, 'product_order', 'default')."'>	<span class='avia-bullet'></span>".$product_order['default']."</a></li>";
		$output .= "    	<li".avia_woo_active_class($po_key, 'title')."><a href='".avia_woo_build_query_string($params, 'product_order', 'title')."'>	<span class='avia-bullet'></span>".$product_order['title']."</a></li>";
		$output .= "    	<li".avia_woo_active_class($po_key, 'price')."><a href='".avia_woo_build_query_string($params, 'product_order', 'price')."'>	<span class='avia-bullet'></span>".$product_order['price']."</a></li>";
		$output .= "    	<li".avia_woo_active_class($po_key, 'date')."><a href='".avia_woo_build_query_string($params, 'product_order', 'date')."'>	<span class='avia-bullet'></span>".$product_order['date']."</a></li>";
		$output .= "    	<li".avia_woo_active_class($po_key, 'popularity')."><a href='".avia_woo_build_query_string($params, 'product_order', 'popularity')."'>	<span class='avia-bullet'></span>".$product_order['popularity']."</a></li>";
		$output .= "    	</ul>";
		$output .= "    	</li>";
		$output .= "    </ul>";

		$output .= "    <ul class='sort-param sort-param-sort'>";
		$output .= "    	<li>";
		if($ps_key == 'desc') 	$output .= "    		<a title='".$product_sort['asc']."' class='sort-param-asc'  href='".avia_woo_build_query_string($params, 'product_sort', 'asc')."'>".$product_sort['desc']."</a>";
		if($ps_key == 'asc') 	$output .= "    		<a title='".$product_sort['desc']."' class='sort-param-desc' href='".avia_woo_build_query_string($params, 'product_sort', 'desc')."'>".$product_sort['asc']."</a>";
		$output .= "    	</li>";
		$output .= "    </ul>";

		$output .= "    <ul class='sort-param sort-param-count'>";
		$output .= "    	<li><span class='currently-selected'>".__("Display",'avia_framework')." <strong>".$pc_key." ".$per_page_string."</strong></span>";
		$output .= "    	<ul>";
		$output .= "    	<li".avia_woo_active_class($pc_key, $per_page).">  <a href='".avia_woo_build_query_string($params, 'product_count', $per_page)."'>		<span class='avia-bullet'></span>".$per_page." ".$per_page_string."</a></li>";
		$output .= "    	<li".avia_woo_active_class($pc_key, $per_page*2)."><a href='".avia_woo_build_query_string($params, 'product_count', $per_page * 2)."'>	<span class='avia-bullet'></span>".($per_page * 2)." ".$per_page_string."</a></li>";
		$output .= "    	<li".avia_woo_active_class($pc_key, $per_page*3)."><a href='".avia_woo_build_query_string($params, 'product_count', $per_page * 3)."'>	<span class='avia-bullet'></span>".($per_page * 3)." ".$per_page_string."</a></li>";
		$output .= "    	</ul>";
		$output .= "    	</li>";
		$output .= "	</ul>";



		$output .= "</div>";
		echo $output;
	}
}

//helper function to create the active list class
if(!function_exists('avia_woo_active_class'))
{
	function avia_woo_active_class($key1, $key2)
	{
		if($key1 == $key2) return " class='current-param'";
	}
}


//helper function to build the query strings for the catalog ordering menu
if(!function_exists('avia_woo_build_query_string'))
{
	function avia_woo_build_query_string($params = array(), $overwrite_key, $overwrite_value)
	{
		$params[$overwrite_key] = $overwrite_value;
		$paged = (array_key_exists('product_count', $params)) ? 'paged=1&' : '';
		return "?" . $paged . http_build_query($params);
	}
}

//function that actually overwrites the query parameters
if(!function_exists('avia_woocommerce_overwrite_catalog_ordering'))
{
	add_action( 'woocommerce_get_catalog_ordering_args', 'avia_woocommerce_overwrite_catalog_ordering', 20);

	function avia_woocommerce_overwrite_catalog_ordering($args)
	{
		global $avia_config;

		if(!empty($avia_config['woocommerce']['disable_sorting_options'])) return $args;

		//check the folllowing get parameters and session vars. if they are set overwrite the defaults
		$check = array('product_order', 'product_count', 'product_sort');
		if(empty($avia_config['woocommerce'])) $avia_config['woocommerce'] = array();

		foreach($check as $key)
		{
			if(isset($_GET[$key]) ) $_SESSION['avia_woocommerce'][$key] = esc_attr($_GET[$key]);
			if(isset($_SESSION['avia_woocommerce'][$key]) ) $avia_config['woocommerce'][$key] = $_SESSION['avia_woocommerce'][$key];
		}


		// is user wants to use new product order remove the old sorting parameter
		if(isset($_GET['product_order']) && !isset($_GET['product_sort']) && isset($_SESSION['avia_woocommerce']['product_sort']))
		{
			unset($_SESSION['avia_woocommerce']['product_sort'], $avia_config['woocommerce']['product_sort']);
		}

		extract($avia_config['woocommerce']);

		// set the product order
		if(!empty($product_order))
		{
			switch ( $product_order ) {
				case 'date'  : $orderby = 'date'; $order = 'desc'; $meta_key = '';  break;
				case 'price' : $orderby = 'meta_value_num'; $order = 'asc'; $meta_key = '_price'; break;
				case 'popularity' : $orderby = 'meta_value_num'; $order = 'desc'; $meta_key = 'total_sales'; break;
				case 'title' : $orderby = 'title'; $order = 'asc'; $meta_key = ''; break;
				case 'default':
				default : $orderby = 'menu_order title'; $order = 'asc'; $meta_key = ''; break;
			}
		}



		// set the product count
		if(!empty($product_count) && is_numeric($product_count))
		{
			$avia_config['shop_overview_products_overwritten'] = true;
			$avia_config['shop_overview_products'] = (int) $product_count;
		}

		//set the product sorting
		if(!empty($product_sort))
		{
			switch ( $product_sort ) {
				case 'desc' : $order = 'desc'; break;
				case 'asc' : $order = 'asc'; break;
				default : $order = 'asc'; break;
			}
		}


		if(isset($orderby)) $args['orderby'] = $orderby;
		if(isset($order)) 	$args['order'] = $order;
		if (!empty($meta_key))
		{
			$args['meta_key'] = $meta_key;
		}


		$avia_config['woocommerce']['product_sort'] = $args['order'];

		return $args;
	}


}

//remove produt information on password protected products
if(!function_exists('avia_woocommerce_remove_hooks'))
{
	add_action('woocommerce_before_single_product', 'avia_woocommerce_remove_hooks');

	function avia_woocommerce_remove_hooks()
	{
		/*remove content from password protected products*/
		if(post_password_required())
		{
			add_action( 'woocommerce_after_single_product_summary', 'avia_woocommerce_echo_password', 1 );
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 1 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
			remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
			remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
			remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
			remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
			remove_action( 'woocommerce_simple_add_to_cart', 'woocommerce_simple_add_to_cart', 30 );
			remove_action( 'woocommerce_grouped_add_to_cart', 'woocommerce_grouped_add_to_cart', 30 );
			remove_action( 'woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30 );
			remove_action( 'woocommerce_external_add_to_cart', 'woocommerce_external_add_to_cart', 30 );

			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
			remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
		}
	}
}

if(!function_exists('avia_woocommerce_echo_password'))
{
	add_action('ava_woocomemrce_password_protection_remove_hooks', 'avia_woocommerce_remove_hooks');

	function avia_woocommerce_echo_password()
	{
		/*remove content from password protected products*/
		if(post_password_required())
		{
			echo get_the_password_form();
		}
	}
}



if(!function_exists('avia_woocommerce_image_description'))
{
	add_filter('woocommerce_single_product_image_html','avia_woocommerce_image_description', 10, 2);
	function avia_woocommerce_image_description($img, $post_id){
		global $post, $woocommerce, $product;

		if(has_post_thumbnail())
		{
			$image_title = esc_attr(get_post_field('post_content', get_post_thumbnail_id()));
			$image_link  = wp_get_attachment_url( get_post_thumbnail_id() );
			$image  = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array(
				'title' => $image_title
				) );
			$attachment_count = count( $product->get_gallery_attachment_ids() );

			if ( $attachment_count > 0 ) {
				$gallery = '[product-gallery]';
			} else {
				$gallery = '';
			}

			return sprintf( '<a href="%s" itemprop="image" class="woocommerce-main-image zoom" title="%s"  rel="prettyPhoto' . $gallery . '">%s</a>', $image_link, $image_title, $image);
		}

		return $img;
	}
}