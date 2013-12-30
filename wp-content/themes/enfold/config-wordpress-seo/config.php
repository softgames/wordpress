<?php
/*
 * Adjustments for the Yoast WordPress SEO Plugin
 */
if(!defined('WPSEO_VERSION') && !class_exists('wpSEO')) return;

/*
 * Make sure that the page analysis tool and Yoast Video SEO works with all pages & posts - even if the layout builder is used
 */
if(!function_exists('avia_wpseo_pre_analysis_post_content_fix'))
{
    function avia_wpseo_pre_analysis_post_content_fix($content)
    {
        global $post;

        if( is_admin() && !empty($post->ID) && ('active' == get_post_meta($post->ID, '_aviaLayoutBuilder_active', true)) )
        {
            $content = apply_filters('avia_builder_precompile', get_post_meta($post->ID, '_aviaLayoutBuilderCleanData', true));
            $content = apply_filters('the_content', do_shortcode($content));
        }

        return $content;
    }

    add_filter('wpseo_pre_analysis_post_content','avia_wpseo_pre_analysis_post_content_fix', 10, 1);
}

if(!function_exists('avia_wpseo_video_content_fix'))
{
    function avia_wpseo_video_content_fix($content, $vid)
    {
        if( is_admin() && !empty($post->ID) && ('active' == get_post_meta($post->ID, '_aviaLayoutBuilder_active', true)) )
        {
            $content = apply_filters('avia_builder_precompile', get_post_meta($vid['post_id'], '_aviaLayoutBuilderCleanData', true));
            $content = apply_filters('the_content', do_shortcode($content));
        }

        return $content;
    }

    add_filter('wpseo_video_index_content','avia_wpseo_video_content_fix', 10, 2);
}

/*
 * There's no need for the default set follow function. Yoast SEO takes care of it and user can set custom robot meta values for each post/page.
 */
if(!function_exists('avia_wpseo_deactivate_avia_set_follow'))
{
    function avia_wpseo_deactivate_avia_set_follow($meta)
    {
        return false;
    }

    add_filter('avf_set_follow','avia_wpseo_deactivate_avia_set_follow', 10, 1);
}

/*
 * Yoast SEO takes care of the title. It uses the wp_title() hook and the output data is stored in $wptitle. So just return $wptitle and leave everything else to Yoast.
 */
if(!function_exists('avia_wpseo_change_title_adjustment'))
{
    function avia_wpseo_change_title_adjustment($title, $wptitle)
    {
        return $wptitle;
    }

    add_filter('avf_title_tag', 'avia_wpseo_change_title_adjustment', 10, 2);
}
