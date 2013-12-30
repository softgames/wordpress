<?php
global $avia_config, $post;

	if ( post_password_required() )
    {
		get_template_part( 'page' ); exit();
    }

	/*
	 * get_header is a basic wordpress function, used to retrieve the header.php file in your theme directory.
	 */
	 get_header();


 	 // set up post data
	 setup_postdata( $post );

	 //check if we want to display breadcumb and title
	 if( get_post_meta(get_the_ID(), 'header', true) != 'no') echo avia_title();

	 //filter the content for content builder elements
	 $content = apply_filters('avia_builder_precompile', get_post_meta(get_the_ID(), '_aviaLayoutBuilderCleanData', true));

	 //check first builder element. if its a section or a fullwidth slider we dont need to create the default openeing divs here

	 $first_el = isset(ShortcodeHelper::$tree[0]) ? ShortcodeHelper::$tree[0] : false;
	 $last_el  = !empty(ShortcodeHelper::$tree)   ? end(ShortcodeHelper::$tree) : false;
	 if(!$first_el || !in_array($first_el['tag'], AviaBuilder::$full_el ) )
	 {
        echo avia_new_section(array('close'=>false,'main_container'=>true));
	 }
	
	$content = apply_filters('the_content', $content);
	$content = apply_filters('avf_template_builder_content', $content);
	echo $content;


	$avia_wp_link_pages_args = apply_filters('avf_wp_link_pages_args', array(
																			'before' =>'<nav class="pagination_split_post">'.__('Pages:','avia_framework'),
														                    'after'  =>'</nav>',
														                    'pagelink' => '<span>%</span>',
														                    'separator'        => ' ',
														                    ));

	wp_link_pages($avia_wp_link_pages_args);

	
	
	//only close divs if the user didnt add fullwidth slider elements at the end. also skip sidebar if the last element is a slider
	if(!$last_el || !in_array($last_el['tag'], AviaBuilder::$full_el_no_section ) )
	{
		$cm = avia_section_close_markup();

		echo "</div>";
		echo "</div>$cm";

		//get the sidebar
		$avia_config['currently_viewing'] = 'page';
		get_sidebar();

	}
	else
	{
		echo "<div><div>";
	}



echo '		</div><!--end builder template-->';
echo '</div><!-- close default .container_wrap element -->';


get_footer();