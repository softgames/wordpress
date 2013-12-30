<?php
global $avia_config, $post_loop_count;


if(empty($post_loop_count)) $post_loop_count = 1;
$blog_style = avia_get_option('blog_style','multi-big');

// check if we got posts to display:
if (have_posts()) :

	while (have_posts()) : the_post();

	/*
     * get the current post id, the current post class and current post format
 	 */

	$the_id 		= get_the_ID();
	$parity			= $post_loop_count % 2 ? 'odd' : 'even';
	$last           = count($wp_query->posts) == $post_loop_count ? " post-entry-last " : "";
	$post_class 	= "post-entry-".$the_id." post-loop-".$post_loop_count." post-parity-".$parity.$last." ".$blog_style;
	$post_format 	= get_post_format() ? get_post_format() : 'standard';

	/*
     * retrieve slider, title and content for this post,...
     */
    $size = strpos($blog_style, 'big') ? strpos(avia_layout_class( 'main' , false), 'sidebar') ? 'entry_with_sidebar' : 'entry_without_sidebar' : 'square';
	$current_post['slider']  	= get_the_post_thumbnail($the_id, $size);
	$current_post['title']   	= get_the_title();
	$current_post['content'] 	= get_the_excerpt();
	$with_slider    = empty($current_post['slider']) ? "" : "with-slider";


	/*
     * ...now apply a filter, based on the post type... (filter function is located in includes/helper-post-format.php)
     */
	$current_post	= apply_filters( 'post-format-'.$post_format, $current_post );

	/*
     * ... last apply the default wordpress filters to the content
     */
	$current_post['content'] = str_replace(']]>', ']]&gt;', apply_filters('the_content', $current_post['content'] ));

	/*
	 * Now extract the variables so that $current_post['slider'] becomes $slider, $current_post['title'] becomes $title, etc
	 */
	extract($current_post);








	/*
	 * render the html:
	 */
	?>

		<article <?php post_class('post-entry post-entry-type-'.$post_format . " " . $post_class . " ".$with_slider); ?>' <?php avia_markup_helper(array('context' => 'entry')); ?>>


			<div class="entry-content-wrapper clearfix <?php echo $post_format; ?>-content">
                <header class="entry-content-header">
                    <?php

                    //echo the post title
                    echo $title;

                    ?>
                    <span class='post-meta-infos'>
                        <span class='date-container minor-meta updated'><?php the_time(get_option('date_format')); ?></span>

                        <?php if ( get_comments_number() != "0" || comments_open() ){
                        echo "<span class='text-sep'>/</span>";
                        echo "<span class='comment-container minor-meta'>";
                        comments_popup_link(  "0 ".__('Comments','avia_framework'),
                                              "1 ".__('Comment' ,'avia_framework'),
                                              "% ".__('Comments','avia_framework'),'comments-link',
                                              "".__('Comments Disabled','avia_framework'));
                        echo "</span>";
                        }


                        $taxonomies  = get_object_taxonomies(get_post_type($the_id));
                        $cats = '';
                        $excluded_taxonomies =  apply_filters('avf_exclude_taxonomies', array('post_tag','post_format'), get_post_type($the_id), $the_id);

                        if(!empty($taxonomies))
                        {
                            foreach($taxonomies as $taxonomy)
                            {
                                if(!in_array($taxonomy, $excluded_taxonomies))
                                {
                                    $cats .= get_the_term_list($the_id, $taxonomy, '', ', ','').' ';
                                }
                            }
                        }

                        if(!empty($cats))
                        {
                            echo "<span class='text-sep'>/</span>";
                            echo '<span class="blog-categories minor-meta">'.__('in','avia_framework')." ";
                            echo $cats;
                            echo '</span>';
                        }

                        ?>

                    </span>
                </header>

				<?php
				// echo the post content
				echo '<div class="entry-content"'.avia_markup_helper(array('context' => 'entry_content','echo'=>false)).'>';
				echo wpautop($content);
				echo '</div>';

				?>
			</div>

            <footer class="entry-footer"></footer>
		</article><!--end post-entry-->
	<?php

	$post_loop_count++;
	endwhile;
	else:

?>

        <article class="entry">
            <header class="entry-content-header">
                <h1 class='post-title entry-title'><?php _e('Nothing Found', 'avia_framework'); ?></h1>
            </header>

            <p class="entry-content" <?php avia_markup_helper(array('context' => 'entry_content')); ?>><?php _e('Sorry, no posts matched your criteria', 'avia_framework'); ?></p>

            <footer class="entry-footer"></footer>
        </article>

<?php

	endif;

	if(!isset($avia_config['remove_pagination'] ))
	{
		echo avia_pagination('', 'nav');
		// paginate_links(); posts_nav_link(); next_posts_link(); previous_posts_link();
	}
?>