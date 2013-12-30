<?php
global $avia_config;


// check if we got posts to display:
if (have_posts()) :

    while (have_posts()) : the_post();

        ?>

        <article class='post-entry' <?php avia_markup_helper(array('context' => 'entry')); ?>>

            <!--meta info-->
            <div class="blog-meta grid3">

                <span class='post-date-comment-container'>
                    <time class='date-container updated' <?php avia_markup_helper(array('context' => 'entry_time')); ?>><strong><?php the_time('d') ?> <?php the_time('M') ?></strong><span><?php the_time('Y') ?></span></time>
                    <span class='comment-container'><?php comments_popup_link("<strong>0</strong> ".__('Comments','avia_framework'), "<strong>1</strong> ".__('Comment' ,'avia_framework'),
                            "<strong>%</strong> ".__('Comments','avia_framework'),'comments-link',
                            "<strong></strong> ".__('Comments<br/>Off','avia_framework')
                        ); ?>
                    </span>


                </span>

            </div><!--end meta info-->

            <div class="entry-content-wrapper clearfix">

                <header class="entry-content-header">
                    <h2 class='post-title entry-title' <?php avia_markup_helper(array('context' => 'entry_title')); ?>>
                        <a href="<?php echo get_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link:','avia_framework')?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a>
                    </h2>
                    <?php
                    $cats = get_the_category();

                    echo '<span class="minor-meta-wrap">';
                    if(!empty($cats))
                    {
                        echo '<span class="blog-categories minor-meta">';
                        echo '<strong>' . __('Categories:','avia_framework') . ' </strong><span>';
                        the_category(', ');
                        echo '</span></span>';
                    }

                    if(has_tag())
                    {
                        echo '<span class="blog-tags minor-meta">';
                        echo the_tags('<strong>'.__('Tags:','avia_framework').' </strong><span>');
                        echo '</span></span>';
                    }
                    echo '<span class="blog-author minor-meta">';
                    echo '<strong>Author: </strong><span>';
                    echo '<span class="entry-author-link" '.avia_markup_helper(array('context' => 'author_name','echo'=>false)).'>';
                    echo '<span class="vcard author"><span class="fn">';
                    the_author_posts_link();
                    echo '</span></span>';
                    echo '</span>';
                    echo '</span></span>';

                    echo '</span>';
                echo '</header>';

                echo '<div class="entry-content" '.avia_markup_helper(array('context' => 'entry_content','echo'=>false)).'>';
                the_content(__('Read more','avia_framework').'<span class="more-link-arrow">  &rarr;</span>');
                echo '</div>';
                ?>

            </div>

            <footer class="entry-footer"></footer>
        </article><!--end post-entry-->


    <?php
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
    echo avia_pagination('', 'nav');
?>