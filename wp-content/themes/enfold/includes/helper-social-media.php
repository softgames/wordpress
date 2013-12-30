<?php

######################################################################
# social icon builder
######################################################################

if(!function_exists('avia_social_media_icons'))
{
	function avia_social_media_icons($args = array())
	{
		$icons = new avia_social_media_icons($args);
		echo $icons->html();
	}
}



if(!class_exists('avia_social_media_icons'))
{
	class avia_social_media_icons
	{
		var $args;
		var $icons = array();
		var $html  = "";
		var $counter = 1;

		/*
		 * constructor
		 * initialize the variables necessary for all social media links
		 */

		function __construct($args = array())
		{
			$default_arguments = array('outside'=>'ul', 'inside'=>'li', 'class' => 'social_bookmarks', 'append' => '');
			$this->args = array_merge($default_arguments, $args);

			$this->icons = apply_filters( 'avia_filter_social_icons', avia_get_option('social_icons') );
		}

		/*
		 * function build_icon
		 * builds the html string for a single item, with a few options for special items like rss feeds
		 */

		function build_icon($icon)
		{
			global $avia_config;

			//special cases
			switch($icon['social_icon'])
			{
				case 'rss':  if(empty($icon['social_icon_link'])) $icon['social_icon_link'] = get_bloginfo('rss2_url'); break;
				case 'twitter':
				case 'dribbble':
				case 'vimeo':
				case 'behance':

				if(strpos($icon['social_icon_link'], 'http') === false && !empty($icon['social_icon_link']))
				{
					$icon['social_icon_link'] = "http://".$icon['social_icon'].".com/".$icon['social_icon_link']."/";
				}
				break;
			}

			if(empty($icon['social_icon_link'])) $icon['social_icon_link'] = "#";
			$blank = "target='_blank'";
			
			//dont add target blank to relative urls or urls to the same dmoain
			if(strpos($icon['social_icon_link'], 'http') === false || strpos($icon['social_icon_link'], home_url()) !== false) $blank = "";
			
			$html  = "";
			$html .= "<".$this->args['inside']." class='".$this->args['class']."_".$icon['social_icon']." social_icon_".$this->counter."'>";
			$html .= "<a {$blank} href='".$icon['social_icon_link']."' ".av_icon_string($icon['social_icon'])." title='".ucfirst($icon['social_icon'])."'></a>";
			$html .= "</".$this->args['inside'].">";

			return $html;
		}

		/*
		 * function html
		 * builds the html, based on the available icons
		 */

		function html()
		{
			if(!empty($this->icons))
			{
				$this->html = "<".$this->args['outside']." class='noLightbox ".$this->args['class']." icon_count_".count($this->icons)."'>";

				foreach ($this->icons as $icon)
				{
					if(!empty($icon['social_icon']))
					{
						$this->html .= $this->build_icon($icon);
						$this->counter ++;
					}
				}

				$this->html .= $this->args['append'];
				$this->html .= "</".$this->args['outside'].">";
			}


			return $this->html;
		}
	}
}






