(function($)
{
    "use strict";

    $(document).ready(function()
    {
    	//calculate the browser height and append a css rule to the head
		if($.fn.avia_browser_height)
		$('.av-minimum-height, .avia-fullscreen-slider').avia_browser_height();
    
		//creates team social icon tooltip
        new $.AviaTooltip({'class': "avia-tooltip", data: "avia-tooltip", delay:0, scope: "body"});
		
		//creates icon element tooltip
		new $.AviaTooltip({'class': "avia-tooltip avia-icon-tooltip", data: "avia-icon-tooltip", delay:0, scope: "body"});

		
        activate_shortcode_scripts();
        
        //layer slider height helper
        $('.avia-layerslider').layer_slider_height_helper();
        
        //"ajax" portfolio
        $('.grid-links-ajax').avia_portfolio_preview();
        
        // actiavte the masonry script: sorting/loading/etc
		if($.fn.avia_masonry)
		$('.av-masonry').avia_masonry();
		
    });


	$(window).load(function(){
	//initialize after images are loaded
	});


// -------------------------------------------------------------------------------------------
// ACTIVATE ALL SHORTCODES
// -------------------------------------------------------------------------------------------

	function activate_waypoints(container)
	{
		//activates simple css animations of the content once the user scrolls to an elements
		if($.fn.avia_waypoints)
		{
			if(typeof container == 'undefined'){ container = 'body';};

			$('.avia_animate_when_visible', container).avia_waypoints();
			$('.avia_animate_when_almost_visible', container).avia_waypoints({ offset: '80%'});
		}
	}


    function activate_shortcode_scripts(container)
	{
		if(typeof container == 'undefined'){ container = 'body';};
		
		$('.avia_ajax_form', container).avia_ajax_form();
		
		activate_waypoints(container);
		
	    //activates the toggle shortcode
		if($.fn.avia_sc_toggle)
		$('.togglecontainer', container).avia_sc_toggle();

		//activates the tabs shortcode
		if($.fn.avia_sc_tabs)
		{
			$('.top_tab', container).avia_sc_tabs();
			$('.sidebar_tab', container).avia_sc_tabs({sidebar:true});
		}

		//activates behavior and animation for gallery
		if($.fn.avia_sc_gallery)
		{
			$('.avia-gallery', container).avia_sc_gallery();
		}
		
		
		//animation for elements that are not connected like icon shortcode
		if($.fn.avia_sc_animation_delayed)
		{
			$('.av_font_icon', container).avia_sc_animation_delayed({delay:100});
		}
		

		//activates animation for iconlist
		if($.fn.avia_sc_iconlist)
		{
			$('.avia-icon-list', container).avia_sc_iconlist();
		}


		//activates animation for progress bar
		if($.fn.avia_sc_progressbar)
		{
			$('.avia-progress-bar-container', container).avia_sc_progressbar();
		}

		//activates animation for testimonial
		if($.fn.avia_sc_testimonial)
		{
			$('.avia-testimonial-wrapper', container).avia_sc_testimonial();
		}
		
		//activate the fullscreen slider
		$('.avia-slideshow.av_fullscreen', container).aviaFullscreenSlider();
		
		//activate the aviaslider
		$('.avia-slideshow:not(.av_fullscreen)', container).aviaSlider();
		
        //content slider
        $('.avia-content-slider-active', container).aviaSlider({wrapElement: '.avia-content-slider-inner', slideElement:'.slide-entry-wrap'});

        //testimonial slider
        $('.avia-slider-testimonials', container).aviaSlider({wrapElement: '.avia-testimonial-row', slideElement:'.avia-testimonial'});
		
		
		

    }

// -------------------------------------------------------------------------------------------
// Masonry
// -------------------------------------------------------------------------------------------

$.fn.avia_masonry = function(options)
{
	//return if we didnt find anything
	if(!this.length) return this;
	
	var the_body = $('body'),
		isMobile = document.documentElement.ontouchstart !== undefined ? true : false,
		loading = false,
		methods = {
	
		masonry_filter: function()
		{
			var current		= $(this),
		  		selector	= current.data('filter'),
		  		masonry 	= current.parents('.av-masonry:eq(0)'),
		  		container 	= masonry.find('.av-masonry-container:eq(0)'),
		  		links		= masonry.find('.av-masonry-sort a');
				
				links.removeClass('active_sort');
				current.addClass('active_sort');
				container.attr('id', 'masonry_id_'+selector);
				
				methods.applyMasonry(container, selector, function()
				{
					container.css({overflow:'visible'});
				});
				
				return false;
		},
		
		applyMasonry: function(container, selector, callback)
		{
			var filters = selector ? {filter: '.'+selector} : {};
			container.isotope(filters);
			
			if(typeof callback == 'function')
			{
				setTimeout(callback, 0);
			}
		},
		
		show_bricks: function(bricks, callback)
		{
			bricks.each(function(i)
			{
				var currentLink 	= $(this),
					browserPrefix 	= $.avia_utilities.supports('transition'),
					multiplier		= isMobile ? 0 : 100;
				
				setTimeout(function()
				{
					if(browserPrefix === false)
					{
						currentLink.css({visibility:"visible", opacity:0}).animate({opacity:1},1500);
					}
					else
					{
						currentLink.addClass('av-masonry-item-loaded');
					}
					
					if(i == bricks.length - 1 && typeof callback == 'function')
					{
						callback.call();
					}
					
				}, (multiplier * i));
			});
		},
		
		loadMore: function(e)
		{
			e.preventDefault();
			
			if(loading) return false;
			
			loading = true;
		
			var current		= $(this),
		  		data		= current.data(),
		  		masonry 	= current.parents('.av-masonry:eq(0)'),
		  		container	= masonry.find('.av-masonry-container'),
		  		loader		= $.avia_utilities.loading(),
		  		finished	= function(){ loading = false; loader.hide(); the_body.trigger('av_resize_finished'); };
		  	
		  	//calculate a new offset	
		  	if(!data.offset){ data.offset = 0; }	
		  	data.offset += data.items;
		  	data.action = 'avia_ajax_masonry_more';
		  	
		  	 $.ajax({
				url: avia_framework_globals.ajaxurl,
				type: "POST",
				data:data,
				beforeSend: function()
				{
					loader.show();
				},
				success: function(response)
				{
					if(response.indexOf("{av-masonry-loaded}") !== -1)
					{
						//fetch the response. if any php warnings were displayed before rendering of the items the are removed by the string split
						var response  = response.split('{av-masonry-loaded}'),
							new_items = $(response.pop()).filter('.isotope-item');
							
							//check if we got more items than we need. if not we have reached the end of items
							if(new_items.length > data.items)
							{
								new_items = new_items.not(':last');
							}
							else
							{
								current.addClass('av-masonry-no-more-items');
							}
							
							var load_container = $('<div class="loadcontainer"></div>').append(new_items);
							
							
							
							$.avia_utilities.preload({container: load_container, single_callback:  function()
							{
								loader.hide();
								container.isotope( 'insert', new_items); 
								$.avia_utilities.avia_ajax_call(container);
								setTimeout( function(){ methods.show_bricks( new_items , finished); },150);
							}
						});
					}
					else
					{
						finished();
					}
				},
				error: finished,
				complete: function()
				{
				    
				}
			});
		}

	};

	return this.each(function()
	{	
		var masonry			= $(this),
			container 		= masonry.find('.av-masonry-container'),
			bricks			= masonry.find('.isotope-item'), 
			filter			= masonry.find('.av-masonry-sort').css({visibility:"visible", opacity:0}).on('click', 'a',  methods.masonry_filter),
			load_more		= masonry.find('.av-masonry-load-more').css({visibility:"visible", opacity:0});
			
		$.avia_utilities.preload({container: container, single_callback:  function()
		{
			var start_animation = function()
			{ 
				filter.animate({opacity:1}, 400);
				
				//fix for non aligned elements because of scrollbar
				if(container.outerHeight() + container.offset().top + $('#footer').outerHeight() > $(window).height())
				{
					$('html').css({'overflow-y':'scroll'});
				}
				
				methods.applyMasonry(container, false, function()
				{
					masonry.addClass('avia_sortable_active');
					container.removeClass('av-js-disabled '); 
				});
				
				methods.show_bricks(bricks, function()
				{
					load_more.css({opacity:1}).on('click',  methods.loadMore);
				});
				
				//container.isotope( 'reLayout' );

			};
			
			if(isMobile)
			{
				start_animation();
			}
			else
			{
				masonry.waypoint(start_animation , { offset: '80%'} );
			}
					
			// update columnWidth on window resize
			$(window).smartresize(function()
			{
			  	methods.applyMasonry(container);
			});
		}
	});
		
		
	});
};




	
// -------------------------------------------------------------------------------------------
// Avia AJAX Portfolio
// -------------------------------------------------------------------------------------------

(function($)
{ 
	"use strict";
	$.avia_utilities = $.avia_utilities || {};
	
	$.fn.avia_portfolio_preview = function(passed_options) 
	{	
		var win  = $(window),
		the_body = $('body'),
		isMobile = document.documentElement.ontouchstart !== undefined ? true : false,
		defaults = 
		{
			open_in:	'.portfolio-details-inner',
			easing:		'easeOutQuint',
			timing:		800,
			transition:	'slide' // 'fade' or 'slide'
		},
		
		options = $.extend({}, defaults, passed_options);
	
		return this.each(function()
		{	
			var container			= $(this),
				portfolio_id		= container.data('portfolio-id'),
				target_wrap			= $('.portfolio_preview_container[data-portfolio-id="' + portfolio_id + '"]'),
				target_container	= target_wrap.find(options.open_in),
				items				= container.find('.grid-entry'),
				content_retrieved	= {},
				is_open				= false,
				animating			= false,
				index_open			= false,
				ajax_call			= false,
				methods,
				controls,
				loader				= $.avia_utilities.loading();
				
			methods = 
			{
				load_item: function(e)
				{
					e.preventDefault();

					var link			= $(this),
						post_container	= link.parents('.post-entry:eq(0)'),
						post_id			= "ID_" + post_container.data('ajax-id'),
						clickedIndex	= items.index(post_container);
					
					//check if current item is the clicked item or if we are currently animating
					if(post_id === is_open || animating == true) 
					{
						return false;
					}
					
					animating = true;
					
					container.find('.active_portfolio_item').removeClass('active_portfolio_item');
					post_container.addClass('active_portfolio_item');
					loader.show();
					
					methods.ajax_get_contents(post_id, clickedIndex);
				},
				
				scroll_top: function()
				{
					setTimeout(function()
					{
						var target_offset = target_wrap.offset().top - 175,
							window_offset = win.scrollTop();
											
						if(window_offset > target_offset || target_offset - window_offset > 100  )
						{
							$('html:not(:animated),body:not(:animated)').animate({ scrollTop: target_offset }, options.timing, options.easing);
						}
					},10);
				},
				
				attach_item: function(post_id)
				{
					content_retrieved[post_id] = $(content_retrieved[post_id]).appendTo(target_container);
					ajax_call = true;
				},
				
				remove_video: function()
				{
					var del = target_wrap.find('iframe, .avia-video').parents('.ajax_slide:not(.open_slide)');	
					
						if(del.length > 0)
						{
							del.remove();
							content_retrieved["ID_" + del.data('slideId')] = undefined;
						}
				},
				
				show_item: function(post_id, clickedIndex)
				{
				
					//check if current item is the clicked item or if we are currently animating
					if(post_id === is_open) 
					{
						return false;
					}
					animating = true;
					
					
					loader.hide();
					
					if(false === is_open)
					{
						target_wrap.addClass('open_container');
						content_retrieved[post_id].addClass('open_slide');
						
						methods.scroll_top();
						
						target_wrap.css({display:'none'}).slideDown(options.timing, options.easing, function()
						{
							if(ajax_call)
							{ 
								activate_shortcode_scripts(content_retrieved[post_id]); 
								$.avia_utilities.avia_ajax_call(content_retrieved[post_id]);
								the_body.trigger('av_resize_finished');
								ajax_call = false; 
							}
							
							methods.remove_video();
						});
						
							index_open	= clickedIndex;
							is_open		= post_id;
							animating	= false;
						
					}
					else
					{
						methods.scroll_top();
					
						var initCSS = { zIndex:3 },
							easing	= options.easing;
							
						if(index_open > clickedIndex) { initCSS.left = '-110%'; }
						if(options.transition === 'fade'){ initCSS.left = '0%'; initCSS.opacity = 0; easing = 'easeOutQuad'; }
						
						//fixate height for container during animation
						target_container.height(target_container.height()); //outerHeight = border problems?
						
						content_retrieved[post_id].css(initCSS).avia_animate({'left':"0%", opacity:1}, options.timing, easing);
						content_retrieved[is_open].avia_animate({opacity:0}, options.timing, easing, function()
						{
							content_retrieved[is_open].attr({'style':""}).removeClass('open_slide');
							content_retrieved[post_id].addClass('open_slide');
																										  //+ 2 fixes border problem (slides move up and down 2 px on transition)
							target_container.avia_animate({height: content_retrieved[post_id].outerHeight() + 2}, options.timing/2, options.easing, function()
							{
								target_container.attr({'style':""});
								is_open		= post_id;
								index_open	= clickedIndex;
								animating	= false;
								
								methods.remove_video();
								if(ajax_call)
								{ 
									the_body.trigger('av_resize_finished');
									activate_shortcode_scripts(content_retrieved[post_id]); 
									$.avia_utilities.avia_ajax_call(content_retrieved[post_id]);
									ajax_call = false; 
								}
	
							});
							
						});		
					}
				},
				
				ajax_get_contents: function(post_id, clickedIndex)
				{
					if(content_retrieved[post_id] !== undefined)
					{
						methods.show_item(post_id, clickedIndex);
						return;
					}
					
					content_retrieved[post_id] = $('#avia-tmpl-portfolio-preview-' + post_id.replace(/ID_/,"")).html();
					
					//this line is necessary to prevent w3 total cache from messing up the portfolio if inline js is compressed
					content_retrieved[post_id] = content_retrieved[post_id].replace('/*<![CDATA[*/','').replace('*]]>','');
					
					methods.attach_item(post_id);
					
					$.avia_utilities.preload({container: content_retrieved[post_id] , single_callback:  function(){ methods.show_item(post_id, clickedIndex); }});
				},
				
				add_controls: function()
				{
					controls = target_wrap.find('.ajax_controlls');

					target_wrap.avia_keyboard_controls({27:'.avia_close', 37:'.ajax_previous', 39:'.ajax_next'});
					//target_wrap.avia_swipe_trigger({prev:'.ajax_previous', next:'.ajax_next'});
					
					items.each(function(){
					
						var current = $(this), overlay;
						
						current.addClass('no_combo').bind('click', function(event)
						{
							overlay = current.find('.slideshow_overlay');
							
							if(overlay.length)
							{
								event.stopPropagation();
								methods.load_item.apply(current.find('a:eq(0)'));
								return false;
							}
						});
						
						
					});
				},
				
				control_click: function()
				{
					var showItem,
						activeID = container.find('.active_portfolio_item').data('ajax-id'),
						active   = container.find('.post-entry-'+activeID);
				
					switch(this.hash)
					{
						case '#next': 
						
							showItem = active.nextAll('.post-entry:not(.isotope-hidden):eq(0)').find('a:eq(0)');
							if(!showItem.length) { showItem = $('.post-entry:not(.isotope-hidden):eq(0)', container).find('a:eq(0)'); }
							showItem.trigger('click');
					
						break;
						case '#prev': 
							
							showItem = active.prevAll('.post-entry:not(.isotope-hidden):eq(0)').find('a:eq(0)');
							if(!showItem.length) { showItem = $('.post-entry:not(.isotope-hidden):last', container).find('a:eq(0)'); }
							showItem.trigger('click');
						
						break;
						case '#close':
						
							animating = true;
							
							target_wrap.slideUp( options.timing, options.easing, function()
							{ 
								container.find('.active_portfolio_item').removeClass('active_portfolio_item');
								content_retrieved[is_open].attr({'style':""}).removeClass('open_slide');
								target_wrap.removeClass('open_container');
								animating = is_open = index_open = false;
								methods.remove_video();
								the_body.trigger('av_resize_finished');
							});
							
						break;
					}
					return false;
				},
				
				
				resize_reset: function()
				{
					if(is_open === false)
					{
						target_container.html('');
						content_retrieved	= [];
					}
				}
			};
			
			methods.add_controls();
			
			container.on("click", "a", methods.load_item);
			controls.on("click", "a", methods.control_click);
			if(jQuery.support.leadingWhitespace) { win.bind('smartresize', methods.resize_reset); }
			
		});
	};
}(jQuery));	



// -------------------------------------------------------------------------------------------
// Fullscreen Slideshow 
// 
// extends avia slideshow script with a more sophisticated preloader and fixed size for slider
// -------------------------------------------------------------------------------------------


	$.AviaFullscreenSlider  =  function(options, slider)
	{
	    this.$slider  = $( slider );
	    this.$inner	  = this.$slider.find('.avia-slideshow-inner');
	    this.$caption = this.$inner.find('.avia-slide-wrap');
	    this.$win	  = $( window );
	    this.isMobile = 'ontouchstart' in document.documentElement;
	    
	    if($.avia_utilities.supported.transition === undefined)
		{
			$.avia_utilities.supported.transition = $.avia_utilities.supports('transition');
		}
		
	    this._init( options );
	}

	$.AviaFullscreenSlider.defaults  = {

		//height of the slider in percent
		height: 100,
		
		//subtract elements from the height
		subtract: '#wpadminbar, #header, #main>.title_container'
		
		
	};

  	$.AviaFullscreenSlider.prototype =
    {
    	_init: function( options )
    	{
    		var _self = this;
    		//set the default options
    		this.options = $.extend( true, {}, $.AviaFullscreenSlider.defaults, options );
    		
    		if(this.$slider.data('slide_height')) this.options.height = this.$slider.data('slide_height');
    		
    		//elements that get subtracted from the image height
    		this.$subtract = $(this.options.subtract);
    		
    		//set the slideshow size
    		this._setSize();
    		
    		//set resizing script on window resize
    		this.$win.on( 'debouncedresize',  $.proxy( this._setSize, this) );
    		
    		//parallax scroll if element if leaving viewport
			setTimeout(function()
			{
				if(!_self.isMobile) //disable parallax scrolling on mobile
    			_self.$win.on( 'scroll',  $.proxy( _self._parallax_scroll, _self) );
    			
    		},100);
			/**/
    		
			//activate the defaule slider
			this.$slider.aviaSlider({bg_slider:true});
			
			
    	},
    	
    	_setSize: function( )
    	{
    		var viewport		= this.$win.height(),
    			slide_height	= Math.ceil( (viewport / 100) * this.options.height );
			
			if(this.$subtract.length && this.options.height == 100)
			{
	    		this.$subtract.each(function()
	    		{
	    			slide_height -= this.offsetHeight - 0.5;
	    		});
    		}
    		else
    		{
    			slide_height -= 1;
    		}
    		this.$slider.height(slide_height);
    	},
    	
    	_parallax_scroll: function(e)
    	{
    		var winTop = this.$win.scrollTop(),
    			offset = this.$slider.offset().top,
    			scrollPos = 0, 
    			prop = {}, prop2 = {};
    		
    		if(offset < winTop)
    		{	
    			scrollPos = Math.round( (winTop - offset) * 0.9 );
    		}
    		else
    		{
    			scrollPos = "0";
    		}
    		
    		//slide background parallax
    		prop[$.avia_utilities.supported.transition+"transform"] = "translate(0px,"+ scrollPos +"px)";
    		this.$inner.css(prop);
    		
    		
    		//slider caption parallax
    		/*
			prop2[$.avia_utilities.supported.transition+"transform"] = "translate(0px,-"+ ( scrollPos * 1) +"px)";
    		prop2['opacity'] = Math.ceil((this.$slider.height() - (scrollPos * 2)) / 100)/ 10;
    		prop2['opacity'] = prop2['opacity'] < 0 ? 0 : prop2['opacity'];
    		this.$caption.css(prop2);
			*/
    	}
    };



$.fn.aviaFullscreenSlider = function( options )
{
	return this.each(function()
	{
		var active = $.data( this, 'aviaFullscreenSlider' );

		if(!active)
		{
			//make sure that the function doesnt get aplied a second time
			$.data( this, 'aviaFullscreenSlider', 1 );
			
			//create the preparations for fullscreen slider
			new $.AviaFullscreenSlider( options, this );
		}
	});
}
	
	
	
// -------------------------------------------------------------------------------------------
// makes sure that the fixed container height is removed once the layerslider is loaded, so it adapts to the screen resolution
// -------------------------------------------------------------------------------------------

$.fn.layer_slider_height_helper = function(options)
{
	return this.each(function()
	{
		var container 	= $(this),
			first_div 	= container.find('>div:first'),
			timeout 	= false,
			counter 	= 0,
			reset_size 	= function()
			{
				if(first_div.height() > 0 || counter > 5)
				{
					container.height('auto');
				}
				else
				{
					timeout = setTimeout(reset_size, 500);
					counter++;
				}
			};

		if(!first_div.length) return;

		timeout = setTimeout(reset_size, 0);
	});
}

// -------------------------------------------------------------------------------------------
// testimonial shortcode javascript
// -------------------------------------------------------------------------------------------

$.fn.avia_sc_testimonial = function(options)
{
	return this.each(function()
	{
		var container = $(this), elements = container.find('.avia-testimonial');


		//trigger displaying of thumbnails
		container.on('avia_start_animation', function()
		{
			elements.each(function(i)
			{
				var element = $(this);
				setTimeout(function(){ element.addClass('avia_start_animation') }, (i * 150));
			});
		});
	});
}


// -------------------------------------------------------------------------------------------
// Progress bar shortcode javascript
// -------------------------------------------------------------------------------------------

$.fn.avia_sc_progressbar = function(options)
{
	return this.each(function()
	{
		var container = $(this), elements = container.find('.progress');


		//trigger displaying of thumbnails
		container.on('avia_start_animation', function()
		{
			elements.each(function(i)
			{
				var element = $(this);
				setTimeout(function(){ element.addClass('avia_start_animation') }, (i * 250));
			});
		});
	});
}

// -------------------------------------------------------------------------------------------
// Iconlist shortcode javascript
// -------------------------------------------------------------------------------------------

$.fn.avia_sc_iconlist = function(options)
{
	return this.each(function()
	{
		var iconlist = $(this), elements = iconlist.find('>li');


		//trigger displaying of thumbnails
		iconlist.on('avia_start_animation', function()
		{
			elements.each(function(i)
			{
				var element = $(this);
				setTimeout(function(){ element.addClass('avia_start_animation') }, (i * 350));
			});
		});
	});
}


// -------------------------------------------------------------------------------------------
//  shortcode javascript for delayed animation even when non connected elements are used
// -------------------------------------------------------------------------------------------

$.fn.avia_sc_animation_delayed = function(options)
{
	var global_timer = 0,
		delay = options.delay || 50;

	return this.each(function()
	{
		var icon = $(this);

		//trigger displaying of thumbnails
		icon.on('avia_start_animation', function()
		{
			var element = $(this);
			global_timer ++;
			setTimeout(function(){ element.addClass('avia_start_delayed_animation'); global_timer --; }, (global_timer * delay));
		});
	});
}


// -------------------------------------------------------------------------------------------
// Section Height Helper
// -------------------------------------------------------------------------------------------

$.fn.avia_browser_height = function()
{
	if(!this.length) return;
	
	var win			= $(window),
		subtract	= $('#wpadminbar, #header, #main>.title_container'),
		css_block	= $("<style type='text/css' id='av-broser-height'></style>").appendTo('head:first'), 
		calc_height = function()
		{
			var css			= "",
				wh100 		= win.height(),
				ww100 		= win.width(),
				wh100_mod 	= wh100,
				whCover		= (wh100 / 9) * 16,
				wwCover		= (ww100 / 16) * 9,
				wh75		= Math.round( wh100 * 0.75 ),
				wh50		= Math.round( wh100 * 0.5  ),
				wh25		= Math.round( wh100 * 0.25 );
			
			subtract.each(function(){ wh100_mod -= this.offsetHeight - 1; });	
			
			var whCoverMod	= (wh100_mod / 9) * 16;
			
			//fade in of section content with minimum height once the height has been calculated
			css += ".avia-section.av-minimum-height .container{opacity: 1; }\n";
			
			//various section heights (100-25% as well as 100% - header/adminbar in case its the first builder element)
			css += ".av-minimum-height-100 .container	{height:"+wh100+"px;}\n";
			css += ".av-minimum-height-75 .container	{height:"+wh75+"px;}\n";
			css += ".av-minimum-height-50 .container {height:"+wh50+"px;}\n";
			css += ".av-minimum-height-25 .container {height:"+wh25+"px;}\n";
			css += ".avia-builder-el-0.av-minimum-height-100 .container{height:"+wh100_mod+"px;}\n";
			
			//fullscreen video calculations
			if(ww100/wh100 < 16/9)
			{
				css += "#top .av-element-cover iframe, #top .av-element-cover embed, #top .av-element-cover video{width:"+whCover+"px; left: -"+(whCover - ww100)/2+"px;}\n";
			}
			else
			{
				css += "#top .av-element-cover iframe, #top .av-element-cover embed, #top .av-element-cover video{height:"+wwCover+"px; top: -"+(wwCover - wh100)/2+"px;}\n";
			}
			
			if(ww100/wh100_mod < 16/9)
			{
				css += "#top .avia-builder-el-0 .av-element-cover iframe, #top .avia-builder-el-0 .av-element-cover embed, #top .avia-builder-el-0 .av-element-cover video{width:"+whCoverMod+"px; left: -"+(whCoverMod - ww100)/2+"px;}\n";
			}
			else
			{
				css += "#top .avia-builder-el-0 .av-element-cover iframe, #top .avia-builder-el-0 .av-element-cover embed, #top .avia-builder-el-0 .av-element-cover video{height:"+wwCover+"px; top: -"+(wwCover - wh100_mod)/2+"px;}\n";
			}
			
			css_block.text(css);
			
		};
	
	win.smartresize(calc_height);
	calc_height();
}




// -------------------------------------------------------------------------------------------
// Gallery shortcode javascript
// -------------------------------------------------------------------------------------------

$.fn.avia_sc_gallery = function(options)
{
	return this.each(function()
	{
		var gallery = $(this), images = gallery.find('img'), big_prev = gallery.find('.avia-gallery-big');


		//trigger displaying of thumbnails
		gallery.on('avia_start_animation', function()
		{
			images.each(function(i)
			{
				var image = $(this);
				setTimeout(function(){ image.addClass('avia_start_animation') }, (i * 110));
			});
		});
		
		if(gallery.hasClass('deactivate_avia_lazyload')) gallery.trigger('avia_start_animation');

		//trigger thumbnail hover and big prev image change
		if(big_prev.length)
		{
			gallery.on('mouseenter','.avia-gallery-thumb a', function()
			{
				var _self = this;

				big_prev.attr('data-onclick', _self.getAttribute("data-onclick"));
				big_prev.height(big_prev.height());
				big_prev.attr('href', _self.href)

				var newImg = _self.getAttribute("data-prev-img"),
					oldImg = big_prev.find('img').attr('src');

				if(newImg != oldImg)
				{
					var next_img = new Image();
					next_img.src = newImg;
					
					if(big_prev.hasClass('avia-gallery-big-no-crop-thumb'))
					{
						$(next_img).css({'height':big_prev.height(),'width':'auto'});
					}
					
					big_prev.stop().animate({opacity:0}, function()
					{
						big_prev.html(next_img);
						big_prev.animate({opacity:1});
					});
				}
			});

			big_prev.on('click', function()
			{
				if( !($(this).is('.aviaopeninbrowser')) )
				{
					gallery.find('.avia-gallery-thumb a').eq( this.getAttribute("data-onclick") - 1).trigger('click');
				}
				else
				{
					var imgurl = gallery.find('.avia-gallery-thumb a').eq( this.getAttribute("data-onclick") - 1).attr("href");

					if( $(this).is('.aviablank') && imgurl != '' )
					{
						window.open(imgurl, '_blank');
					}
					else if( imgurl != '' )
					{
						window.open(imgurl, '_self');
					}

				}
				return false;
			});


			$(window).on("debouncedresize", function()
			{
			  	big_prev.height('auto');
			});

		}
	});
}

// -------------------------------------------------------------------------------------------
// Toggle shortcode javascript
// -------------------------------------------------------------------------------------------

$.fn.avia_sc_toggle = function(options)
{
	var defaults =
	{
		single: '.single_toggle',
		heading: '.toggler',
		content: '.toggle_wrap',
		sortContainer:'.taglist'
	};

	var win = $(window),
		options = $.extend(defaults, options);

	return this.each(function()
	{
		var container 	= $(this).addClass('enable_toggles'),
			toggles		= $(options.single, container),
			heading 	= $(options.heading, container),
			allContent 	= $(options.content, container),
			sortLinks	= $(options.sortContainer + " a", container);

		heading.each(function(i)
		{
			var thisheading =  $(this), content = thisheading.next(options.content, container);

			function scroll_to_viewport()
			{
			    //check if toggle title is in viewport. if not scroll up
			    var el_offset = content.offset().top,
			        scoll_target = el_offset - 50 - parseInt($('html').css('margin-top'),10);

			    if(win.scrollTop() > el_offset)
			    {
			        $('html:not(:animated),body:not(:animated)').animate({scrollTop: scoll_target},200);
			    }
			}

			if(content.css('visibility') != "hidden")
			{
				thisheading.addClass('activeTitle');
			}

			thisheading.on('click', function()
			{
				if(content.css('visibility') != "hidden")
				{
					content.slideUp(200, function()
					{
						content.removeClass('active_tc').attr({style:''});
					});
					thisheading.removeClass('activeTitle');

				}
				else
				{
					if(container.is('.toggle_close_all'))
					{
						allContent.not(content).slideUp(200, function()
						{
							$(this).removeClass('active_tc').attr({style:''});
							scroll_to_viewport();
						});
						heading.removeClass('activeTitle');
					}
					content.addClass('active_tc').slideDown(200,
					
					function()
					{
                        if(!container.is('.toggle_close_all'))
                        {
                            scroll_to_viewport();
                        }
					}
					
					);
					thisheading.addClass('activeTitle');
					location.replace(thisheading.data('fake-id'));
				}
			});
		});


		sortLinks.click(function(e){

			e.preventDefault();
			var show = toggles.filter('[data-tags~="'+$(this).data('tag')+'"]'),
				hide = toggles.not('[data-tags~="'+$(this).data('tag')+'"]');

				sortLinks.removeClass('activeFilter');
				$(this).addClass('activeFilter');
				heading.filter('.activeTitle').trigger('click');
				show.slideDown();
				hide.slideUp();
		});


		function trigger_default_open(hash)
		{
			if(!hash && window.location.hash) hash = window.location.hash;
			if(!hash) return;
			
			var open = heading.filter('[data-fake-id="'+hash+'"]');

			if(open.length)
			{
				if(!open.is('.activeTitle')) open.trigger('click');
				window.scrollTo(0, container.offset().top - 70);
			}
		}
		trigger_default_open(false);
		
		$('a').on('click',function(){
            		var hash = $(this).attr('href').replace(/^.*?#/,'');
            		if(hash) trigger_default_open('#'+hash);
        	});

	});
};




// -------------------------------------------------------------------------------------------
// Tab Shortcode
// -------------------------------------------------------------------------------------------

$.fn.avia_sc_tabs= function(options)
{
	var defaults =
	{
		heading: '.tab',
		content:'.tab_content',
		active:'active_tab',
		sidebar: false
	};

	var win = $(window)
		options = $.extend(defaults, options);

	return this.each(function()
	{
		var container 	= $(this),
			tab_titles 	= $('<div class="tab_titles"></div>').prependTo(container),
			tabs 		= $(options.heading, container),
			content 	= $(options.content, container),
			newtabs 	= false,
			oldtabs 	= false;

		newtabs = tabs.clone();
		oldtabs = tabs.addClass('fullsize-tab');
		tabs = newtabs;

		tabs.prependTo(tab_titles).each(function(i)
		{
			var tab = $(this), the_oldtab = false;

			if(newtabs) the_oldtab = oldtabs.filter(':eq('+i+')');

			tab.addClass('tab_counter_'+i).bind('click', function()
			{
				open_content(tab, i, the_oldtab);
				return false;
			});

			if(newtabs)
			{
				the_oldtab.bind('click', function()
				{
					open_content(the_oldtab, i, tab);
					return false;
				});
			}
		});

		set_size();
		trigger_default_open(false);
		win.on("debouncedresize", set_size);
		
		$('a').on('click',function(){
            		var hash = $(this).attr('href').replace(/^.*?#/,'');
            		if(hash) trigger_default_open('#'+hash);
        	});

		function set_size()
		{
			if(!options.sidebar) return;
			content.css({'min-height': tab_titles.outerHeight() + 1});
		}

		function open_content(tab, i, alternate_tab)
		{
			if(!tab.is('.'+options.active))
			{
				$('.'+options.active, container).removeClass(options.active);
				$('.'+options.active+'_content', container).removeClass(options.active+'_content');

				tab.addClass(options.active);

				var new_loc = tab.data('fake-id');
				if(typeof new_loc == 'string') location.replace(new_loc);

				if(alternate_tab) alternate_tab.addClass(options.active);
				var active_c = content.filter(':eq('+i+')').addClass(options.active+'_content');

				if(typeof click_container != 'undefined' && click_container.length)
				{
					sidebar_shadow.height(active_c.outerHeight());
				}
				
				//check if tab title is in viewport. if not scroll up
				var el_offset = active_c.offset().top,
					scoll_target = el_offset - 50 - parseInt($('html').css('margin-top'),10);
				
				if(win.scrollTop() > el_offset)
				{
					$('html:not(:animated),body:not(:animated)').scrollTop(scoll_target);
				}
			}
		}

		function trigger_default_open(hash)
		{
			if(!hash && window.location.hash) hash = window.location.hash;
            		if(!hash) return;
            		
			var open = tabs.filter('[data-fake-id="'+hash+'"]');

			if(open.length)
			{
				if(!open.is('.active_tab')) open.trigger('click');
				window.scrollTo(0, container.offset().top - 70);
			}
		}

	});
};




// -------------------------------------------------------------------------------------------
// contact form ajax
// -------------------------------------------------------------------------------------------

(function($)
{
	$.fn.avia_ajax_form = function(variables)
	{
		var defaults =
		{
			sendPath: 'send.php',
			responseContainer: '.ajaxresponse'
		};

		var options = $.extend(defaults, variables);

		return this.each(function()
		{
			var form = $(this),
				form_sent = false,
				send =
				{
					formElements: form.find('textarea, select, input[type=text], input[type=checkbox], input[type=hidden]'),
					validationError:false,
					button : form.find('input:submit'),
					dataObj : {}
				},

				responseContainer = form.next(options.responseContainer+":eq(0)");

			send.button.bind('click', checkElements);

			function send_ajax_form()
			{
				if(form_sent){ return false; }

				form_sent = true;
				send.button.fadeOut(300);

				responseContainer.load(form.attr('action')+' '+options.responseContainer, send.dataObj, function()
				{
					responseContainer.removeClass('hidden').css({display:"block"});
					form.slideUp(400, function(){responseContainer.slideDown(400, function(){ $('body').trigger('av_resize_finished'); }); send.formElements.val('');});
				});


			}

			function checkElements()
			{
				// reset validation var and send data
				send.validationError = false;
				send.datastring = 'ajax=true';

				send.formElements.each(function(i)
				{
					var currentElement = $(this),
						surroundingElement = currentElement.parent(),
						value = currentElement.val(),
						name = currentElement.attr('name'),
					 	classes = currentElement.attr('class'),
					 	nomatch = true;

					 	if(currentElement.is(':checkbox'))
					 	{
					 		if(currentElement.is(':checked')) { value = true } else {value = ''}
					 	}

					 	send.dataObj[name] = encodeURIComponent(value);

					 	if(classes && classes.match(/is_empty/))
						{
							if(value == '')
							{
								surroundingElement.removeClass("valid error ajax_alert").addClass("error");
								send.validationError = true;
							}
							else
							{
								surroundingElement.removeClass("valid error ajax_alert").addClass("valid");
							}
							nomatch = false;
						}

						if(classes && classes.match(/is_email/))
						{
							if(!value.match(/^\w[\w|\.|\-]+@\w[\w|\.|\-]+\.[a-zA-Z]{2,4}$/))
							{
								surroundingElement.removeClass("valid error ajax_alert").addClass("error");
								send.validationError = true;
							}
							else
							{
								surroundingElement.removeClass("valid error ajax_alert").addClass("valid");
							}
							nomatch = false;
						}

						if(classes && classes.match(/is_phone/))
						{
							if(!value.match(/^(\d|\s|\-|\/|\(|\)|\[|\]|e|x|t|ension|\.|\+|\_|\,|\:|\;)*$/))
							{
								surroundingElement.removeClass("valid error ajax_alert").addClass("error");
								send.validationError = true;
							}
							else
							{
								surroundingElement.removeClass("valid error ajax_alert").addClass("valid");
							}
							nomatch = false;
						}

						if(classes && classes.match(/is_number/))
						{
							if(!value.match(/^(\d)*$/))
							{
								surroundingElement.removeClass("valid error ajax_alert").addClass("error");
								send.validationError = true;
							}
							else
							{
								surroundingElement.removeClass("valid error ajax_alert").addClass("valid");
							}
							nomatch = false;
						}

						if(classes && classes.match(/captcha/))
						{
							var verifier 	= form.find("#" + name + "_verifier").val(),
								lastVer		= verifier.charAt(verifier.length-1),
								finalVer	= verifier.charAt(lastVer);

							if(value != finalVer)
							{
								surroundingElement.removeClass("valid error ajax_alert").addClass("error");
								send.validationError = true;
							}
							else
							{
								surroundingElement.removeClass("valid error ajax_alert").addClass("valid");
							}
							nomatch = false;
						}

						if(nomatch && value != '')
						{
							surroundingElement.removeClass("valid error ajax_alert").addClass("valid");
						}
				});

				if(send.validationError == false)
				{
					send_ajax_form();
				}
				return false;
			}
		});
	};
})(jQuery);



// -------------------------------------------------------------------------------------------
// HELPER FUNCTIONS
// -------------------------------------------------------------------------------------------


//waipoint script when something comes into viewport
 $.fn.avia_waypoints = function(options_passed)
	{
		if(! $('html').is('.avia_transform')) return;

		var defaults = { offset: 'bottom-in-view' , triggerOnce: true},
			options  = $.extend({}, defaults, options_passed),
			isMobile = document.documentElement.ontouchstart !== undefined ? true : false;

		return this.each(function()
		{
			var element = $(this);
			
			setTimeout(function()
			{
				if(isMobile)
				{
					element.addClass('avia_start_animation').trigger('avia_start_animation');
				}
				else
				{
					element.waypoint(function(direction)
					{
					 	$(this).addClass('avia_start_animation').trigger('avia_start_animation');
	
					}, options );
				}
			},100)
			
		});
	};







// window resize script
var $event = $.event, $special, resizeTimeout;

$special = $event.special.debouncedresize = {
	setup: function() {
		$( this ).on( "resize", $special.handler );
	},
	teardown: function() {
		$( this ).off( "resize", $special.handler );
	},
	handler: function( event, execAsap ) {
		// Save the context
		var context = this,
			args = arguments,
			dispatch = function() {
				// set correct event type
				event.type = "debouncedresize";
				$event.dispatch.apply( context, args );
			};

		if ( resizeTimeout ) {
			clearTimeout( resizeTimeout );
		}

		execAsap ?
			dispatch() :
			resizeTimeout = setTimeout( dispatch, $special.threshold );
	},
	threshold: 150
};





$.easing['jswing'] = $.easing['swing'];

$.extend( $.easing,
{
	def: 'easeOutQuad',
	swing: function (x, t, b, c, d) { return $.easing[$.easing.def](x, t, b, c, d); },
	easeInQuad: function (x, t, b, c, d) { return c*(t/=d)*t + b; },
	easeOutQuad: function (x, t, b, c, d) { return -c *(t/=d)*(t-2) + b; },
	easeInOutQuad: function (x, t, b, c, d) { if ((t/=d/2) < 1) return c/2*t*t + b; return -c/2 * ((--t)*(t-2) - 1) + b; },
	easeInCubic: function (x, t, b, c, d) { return c*(t/=d)*t*t + b; },
	easeOutCubic: function (x, t, b, c, d) { return c*((t=t/d-1)*t*t + 1) + b; },
	easeInOutCubic: function (x, t, b, c, d) { if ((t/=d/2) < 1) return c/2*t*t*t + b; return c/2*((t-=2)*t*t + 2) + b;	},
	easeInQuart: function (x, t, b, c, d) { return c*(t/=d)*t*t*t + b;	},
	easeOutQuart: function (x, t, b, c, d) { return -c * ((t=t/d-1)*t*t*t - 1) + b; },
	easeInOutQuart: function (x, t, b, c, d) { if ((t/=d/2) < 1) return c/2*t*t*t*t + b; return -c/2 * ((t-=2)*t*t*t - 2) + b;	},
	easeInQuint: function (x, t, b, c, d) { return c*(t/=d)*t*t*t*t + b;	},
	easeOutQuint: function (x, t, b, c, d) { return c*((t=t/d-1)*t*t*t*t + 1) + b;	},
	easeInOutQuint: function (x, t, b, c, d) { if ((t/=d/2) < 1) return c/2*t*t*t*t*t + b; return c/2*((t-=2)*t*t*t*t + 2) + b;	},
	easeInSine: function (x, t, b, c, d) {	return -c * Math.cos(t/d * (Math.PI/2)) + c + b;	},
	easeOutSine: function (x, t, b, c, d) { return c * Math.sin(t/d * (Math.PI/2)) + b;	},
	easeInOutSine: function (x, t, b, c, d) { return -c/2 * (Math.cos(Math.PI*t/d) - 1) + b;	},
	easeInExpo: function (x, t, b, c, d) { return (t==0) ? b : c * Math.pow(2, 10 * (t/d - 1)) + b;	},
	easeOutExpo: function (x, t, b, c, d) { return (t==d) ? b+c : c * (-Math.pow(2, -10 * t/d) + 1) + b;	},
	easeInOutExpo: function (x, t, b, c, d) {
		if (t==0) return b;
		if (t==d) return b+c;
		if ((t/=d/2) < 1) return c/2 * Math.pow(2, 10 * (t - 1)) + b;
		return c/2 * (-Math.pow(2, -10 * --t) + 2) + b;
	},
	easeInCirc: function (x, t, b, c, d) { return -c * (Math.sqrt(1 - (t/=d)*t) - 1) + b;	},
	easeOutCirc: function (x, t, b, c, d) {return c * Math.sqrt(1 - (t=t/d-1)*t) + b;	},
	easeInOutCirc: function (x, t, b, c, d) { if ((t/=d/2) < 1) return -c/2 * (Math.sqrt(1 - t*t) - 1) + b;	return c/2 * (Math.sqrt(1 - (t-=2)*t) + 1) + b;	},
	easeInElastic: function (x, t, b, c, d) {
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		return -(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;
	},
	easeOutElastic: function (x, t, b, c, d) {
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d)==1) return b+c;  if (!p) p=d*.3;
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		return a*Math.pow(2,-10*t) * Math.sin( (t*d-s)*(2*Math.PI)/p ) + c + b;
	},
	easeInOutElastic: function (x, t, b, c, d) {
		var s=1.70158;var p=0;var a=c;
		if (t==0) return b;  if ((t/=d/2)==2) return b+c;  if (!p) p=d*(.3*1.5);
		if (a < Math.abs(c)) { a=c; var s=p/4; }
		else var s = p/(2*Math.PI) * Math.asin (c/a);
		if (t < 1) return -.5*(a*Math.pow(2,10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )) + b;
		return a*Math.pow(2,-10*(t-=1)) * Math.sin( (t*d-s)*(2*Math.PI)/p )*.5 + c + b;
	},
	easeInBack: function (x, t, b, c, d, s) {
		if (s == undefined) s = 1.70158;
		return c*(t/=d)*t*((s+1)*t - s) + b;
	},
	easeOutBack: function (x, t, b, c, d, s) {
		if (s == undefined) s = 1.70158;
		return c*((t=t/d-1)*t*((s+1)*t + s) + 1) + b;
	},
	easeInOutBack: function (x, t, b, c, d, s) {
		if (s == undefined) s = 1.70158;
		if ((t/=d/2) < 1) return c/2*(t*t*(((s*=(1.525))+1)*t - s)) + b;
		return c/2*((t-=2)*t*(((s*=(1.525))+1)*t + s) + 2) + b;
	},
	easeInBounce: function (x, t, b, c, d) {
		return c - jQuery.easing.easeOutBounce (x, d-t, 0, c, d) + b;
	},
	easeOutBounce: function (x, t, b, c, d) {
		if ((t/=d) < (1/2.75)) {
			return c*(7.5625*t*t) + b;
		} else if (t < (2/2.75)) {
			return c*(7.5625*(t-=(1.5/2.75))*t + .75) + b;
		} else if (t < (2.5/2.75)) {
			return c*(7.5625*(t-=(2.25/2.75))*t + .9375) + b;
		} else {
			return c*(7.5625*(t-=(2.625/2.75))*t + .984375) + b;
		}
	},
	easeInOutBounce: function (x, t, b, c, d) {
		if (t < d/2) return jQuery.easing.easeInBounce (x, t*2, 0, c, d) * .5 + b;
		return jQuery.easing.easeOutBounce (x, t*2-d, 0, c, d) * .5 + c*.5 + b;
	}
});

})( jQuery );



/*utility functions*/


(function($)
{
	"use strict";

	$.avia_utilities = $.avia_utilities || {};

	/************************************************************************
	gloabl loading function
	*************************************************************************/
	$.avia_utilities.loading = function(attach_to, delay){

		var loader = {

			active: false,

			show: function()
			{
				if(loader.active === false)
				{
					loader.active = true;
					loader.loading_item.css({display:'block', opacity:0});
				}

				loader.loading_item.stop().animate({opacity:0.7});
			},

			hide: function()
			{	
				if(typeof delay === 'undefined'){ delay = 300; }

				loader.loading_item.stop().delay( delay ).animate({opacity:0}, function()
				{
					loader.loading_item.css({display:'none'});
					loader.active = false;
				});
			},

			attach: function()
			{
				if(typeof attach_to === 'undefined'){ attach_to = 'body';}

				loader.loading_item = $('<div class="avia_loading_icon"></div>').css({display:"none"}).appendTo(attach_to);
			}
		}

		loader.attach();
		return loader;
	};

	/************************************************************************
	preload images, as soon as all are loaded trigger a special load ready event
	*************************************************************************/
	$.avia_utilities.preload_images = 0;
	$.avia_utilities.preload = function(options_passed)
	{
		var win		= $(window),
		defaults	=
		{
			container:			'body',
			maxLoops:			10,
			trigger_single:		true,
			single_callback:	function(){},
			global_callback:	function(){}

		},

		options		= $.extend({}, defaults, options_passed),

		methods		= {

			checkImage: function(container)
			{
				container.images.each(function()
				{
					if(this.complete === true)
					{
						container.images = container.images.not(this);
						$.avia_utilities.preload_images -= 1;
					}
				});

				if(container.images.length && options.maxLoops >= 0)
				{
					options.maxLoops-=1;
					setTimeout(function(){ methods.checkImage(container); }, 500);
				}
				else
				{
					$.avia_utilities.preload_images = $.avia_utilities.preload_images - container.images.length;
					methods.trigger_loaded(container);
				}
			},

			trigger_loaded: function(container)
			{
				if(options.trigger_single !== false)
				{
					win.trigger('avia_images_loaded_single', [container]);
					options.single_callback.call(container);
				}

				if($.avia_utilities.preload_images === 0)
				{
					win.trigger('avia_images_loaded');
					options.global_callback.call();
				}

			}
		};

		if(typeof options.container === 'string'){options.container = $(options.container); }

		options.container.each(function()
		{
			var container		= $(this);

			container.images	= container.find('img');
			container.allImages	= container.images;

			$.avia_utilities.preload_images += container.images.length;
			setTimeout(function(){ methods.checkImage(container); }, 10);
		});
	};



	/************************************************************************
	CSS Easing transformation table
	*************************************************************************/
	/*
	Easing transform table from jquery.animate-enhanced plugin
	http://github.com/benbarnett/jQuery-Animate-Enhanced
	*/
	$.avia_utilities.css_easings = {
			linear:			'linear',
			swing:			'ease-in-out',
			bounce:			'cubic-bezier(0.0, 0.35, .5, 1.3)',
			easeInQuad:     'cubic-bezier(0.550, 0.085, 0.680, 0.530)' ,
			easeInCubic:    'cubic-bezier(0.550, 0.055, 0.675, 0.190)' ,
			easeInQuart:    'cubic-bezier(0.895, 0.030, 0.685, 0.220)' ,
			easeInQuint:    'cubic-bezier(0.755, 0.050, 0.855, 0.060)' ,
			easeInSine:     'cubic-bezier(0.470, 0.000, 0.745, 0.715)' ,
			easeInExpo:     'cubic-bezier(0.950, 0.050, 0.795, 0.035)' ,
			easeInCirc:     'cubic-bezier(0.600, 0.040, 0.980, 0.335)' ,
			easeInBack:     'cubic-bezier(0.600, -0.280, 0.735, 0.04)' ,
			easeOutQuad:    'cubic-bezier(0.250, 0.460, 0.450, 0.940)' ,
			easeOutCubic:   'cubic-bezier(0.215, 0.610, 0.355, 1.000)' ,
			easeOutQuart:   'cubic-bezier(0.165, 0.840, 0.440, 1.000)' ,
			easeOutQuint:   'cubic-bezier(0.230, 1.000, 0.320, 1.000)' ,
			easeOutSine:    'cubic-bezier(0.390, 0.575, 0.565, 1.000)' ,
			easeOutExpo:    'cubic-bezier(0.190, 1.000, 0.220, 1.000)' ,
			easeOutCirc:    'cubic-bezier(0.075, 0.820, 0.165, 1.000)' ,
			easeOutBack:    'cubic-bezier(0.175, 0.885, 0.320, 1.275)' ,
			easeInOutQuad:  'cubic-bezier(0.455, 0.030, 0.515, 0.955)' ,
			easeInOutCubic: 'cubic-bezier(0.645, 0.045, 0.355, 1.000)' ,
			easeInOutQuart: 'cubic-bezier(0.770, 0.000, 0.175, 1.000)' ,
			easeInOutQuint: 'cubic-bezier(0.860, 0.000, 0.070, 1.000)' ,
			easeInOutSine:  'cubic-bezier(0.445, 0.050, 0.550, 0.950)' ,
			easeInOutExpo:  'cubic-bezier(1.000, 0.000, 0.000, 1.000)' ,
			easeInOutCirc:  'cubic-bezier(0.785, 0.135, 0.150, 0.860)' ,
			easeInOutBack:  'cubic-bezier(0.680, -0.550, 0.265, 1.55)' ,
			easeInOutBounce:'cubic-bezier(0.580, -0.365, 0.490, 1.365)',
			easeOutBounce:	'cubic-bezier(0.760, 0.085, 0.490, 1.365)' 
		};

	/************************************************************************
	check if a css feature is supported and save it to the supported array
	*************************************************************************/
	$.avia_utilities.supported	= {};
	$.avia_utilities.supports	= (function()
	{
		var div		= document.createElement('div'),
			vendors	= ['Khtml', 'Ms','Moz','Webkit','O'];  // vendors	= ['Khtml', 'Ms','Moz','Webkit','O'];  exclude opera for the moment. stil to buggy

		return function(prop, vendor_overwrite)
		{
			if ( div.style.prop !== undefined  ) { return ""; }
			if (vendor_overwrite !== undefined) { vendors = vendor_overwrite; }

			prop = prop.replace(/^[a-z]/, function(val)
			{
				return val.toUpperCase();
			});

			var len	= vendors.length;
			while(len--)
			{
				if ( div.style[vendors[len] + prop] !== undefined )
				{
					return "-" + vendors[len].toLowerCase() + "-";
				}
			}

			return false;
		};

	}());

	/************************************************************************
	animation function
	*************************************************************************/
	$.fn.avia_animate = function(prop, speed, easing, callback)
	{
		if(typeof speed === 'function') {callback = speed; speed = false; }
		if(typeof easing === 'function'){callback = easing; easing = false;}
		if(typeof speed === 'string'){easing = speed; speed = false;}

		if(callback === undefined || callback === false){ callback = function(){}; }
		if(easing === undefined || easing === false)	{ easing = 'easeInQuad'; }
		if(speed === undefined || speed === false)		{ speed = 400; }

		if($.avia_utilities.supported.transition === undefined)
		{
			$.avia_utilities.supported.transition = $.avia_utilities.supports('transition');
		}


		if($.avia_utilities.supported.transition !== false )
		{
			var prefix		= $.avia_utilities.supported.transition + 'transition',
				cssRule		= {},
				cssProp		= {},
				thisStyle	= document.body.style,
				end			= (thisStyle.WebkitTransition !== undefined) ? 'webkitTransitionEnd' : (thisStyle.OTransition !== undefined) ? 'oTransitionEnd' : 'transitionend';

			//translate easing into css easing
			easing = $.avia_utilities.css_easings[easing];

			//create css transformation rule
			cssRule[prefix]	=  'all '+(speed/1000)+'s '+easing;
			//add namespace to the transition end trigger
			end = end + ".avia_animate";
			
			//since jquery 1.10 the items passed need to be {} and not [] so make sure they are converted properly
			for (var rule in prop)
			{
				if (prop.hasOwnProperty(rule)) { cssProp[rule] = prop[rule]; }
			}
			prop = cssProp;
			
			
			
			this.each(function()
			{
				var element	= $(this), css_difference = false, rule, current_css;

				for (rule in prop)
				{
					if (prop.hasOwnProperty(rule))
					{
						current_css = element.css(rule);

						if(prop[rule] != current_css && prop[rule] != current_css.replace(/px|%/g,""))
						{
							css_difference = true;
							break;
						}
					}
				}

				if(css_difference)
				{
					//if no transform property is set set a 3d translate to enable hardware acceleration
					if(!($.avia_utilities.supported.transition+"transform" in prop))
					{
						prop[$.avia_utilities.supported.transition+"transform"] = "translateZ(0)";
					}
					
					element.on(end,  function(event)
					{
						if(event.target != event.currentTarget) return false;

						cssRule[prefix] = "none";

						element.off(end);
						element.css(cssRule);
						setTimeout(function(){ callback.call(element); });
					});
					
					setTimeout(function(){ element.css(cssRule);},10);
					setTimeout(function(){ element.css(prop);	},20);
				}
				else
				{
					setTimeout(function(){ callback.call(element); });
				}

			});
		}
		else // if css animation is not available use default JS animation
		{
			this.animate(prop, speed, easing, callback);
		}

		return this;
	};

})( jQuery );


/* ======================================================================================================================================================
Avia Slideshow Script rewritten
====================================================================================================================================================== */

(function($)
{
    "use strict";

	$.AviaSlider  =  function(options, slider)
	{
		var self = this;

	    this.$slider 	= $( slider );
	
		//default preload images then init slideshow
	    $.avia_utilities.preload({container: this.$slider , single_callback:  function(){ self._init( options ); }});
	}

	$.AviaSlider.defaults  = {

		//interval between autorotation switches
		interval:5,

		//autorotation active or not
		autoplay:false,

		//fade or slide animation
		animation:'slide',

		//transition speed when switching slide
		transitionSpeed:900,

		//easing method for the transition
		easing:'easeInOutQuart',

		//slide wrapper
		wrapElement: '>ul',

		//slide element
		slideElement: '>li',

		//pause if mouse cursor is above item
		hoverpause: false,
		
		//attach images as background
		bg_slider: false,
		
		//delay of miliseconds to wait before showing the next slide
		show_slide_delay: 0

	};

  	$.AviaSlider.prototype =
    {
    	_init: function( options )
    	{
			// set slider options
			this.options = this._setOptions(options);

			//slidewrap
			this.$sliderUl  = this.$slider.find(this.options.wrapElement);

			// slide elements
			this.$slides = this.$sliderUl.find(this.options.slideElement);

			// goto dots
			this.gotoButtons = this.$slider.find('.avia-slideshow-dots a');

			// slide count
			this.itemsCount = this.$slides.length;

			// current image index
			this.current = 0;

			// control if the slicebox is animating
			this.isAnimating = false;

			// mobile browser?
			this.isMobile = document.documentElement.className.indexOf('avia_mobile') !== -1 ? true : false;
			
			// css browser prefix like -webkit-, -o-, -moz-
			this.browserPrefix = $.avia_utilities.supports('transition');
			
			// css3 animation?
			this.cssActive = this.browserPrefix !== false ? true : false;
			
			// css3D animation?
			this.css3DActive = document.documentElement.className.indexOf('csstransforms3d') !== -1 ? true : false;

			//if we have a bg slider no images were preloaded yet. in that case start preloading and attaching images
			if(this.options.bg_slider == true)
			{
				//create array that holds all image urls to preload
				this.imageUrls = [];
				
				//create a preloader icon to indicate loading
				this.loader = $.avia_utilities.loading(this.$slider);
				
				//preload the images ony by one
				this._bgPreloadImages();
				
			}
			else //if it was a default slider all images are already loaded and we can start showing the slider
			{			
				//kickoff the slider: bind functions, show first slide, if active start the autorotation timer
				this._kickOff();
			}
    	},

    	//set the slider options by first merging the efault options and the passed options, then checking the slider element if any data attributes overwrite the option set
    	_setOptions: function(options)
		{
			var newOptions 	= $.extend( true, {}, $.AviaSlider.defaults, options ),
				htmlData 	= this.$slider.data(),
				i 			= "";

			//overwritte passed option set with any data properties on the html element
			for (i in htmlData)
			{
				if (htmlData.hasOwnProperty(i))
				{
					if(typeof htmlData[i] === "string" || typeof htmlData[i] === "number" || typeof htmlData[i] === "boolean")
					{
						newOptions[i] = htmlData[i];
					}
				}
			}

			return newOptions;
		},
		
		//start preloading the background images
		_bgPreloadImages : function(callback)
    	{
    		this._getImageURLS();
    		this._preloadSingle(0, function()
    		{
    			this._kickOff();
				this._preloadNext(1);
    		});
    	},
		
    	//if we are using a background image slider, fetch the images from a data attribute and preload them one by one
    	_getImageURLS: function()
    	{
    		var _self = this;
    		
    		//collect url strings of the images to preload
			this.$slides.each(function(i)
			{
				_self.imageUrls[i] = [];
				_self.imageUrls[i]['url'] = $(this).data("img-url");
				
				//if no image is passed we can set the slide to loaded
				if(typeof _self.imageUrls[i]['url'] == 'string')
				{
					_self.imageUrls[i]['status'] = false;
				}
				else
				{
					_self.imageUrls[i]['status'] = true;
				}
			});
    	},
    	
    	
    	_preloadSingle: function(key, callback)
		{
			var _self 		= this,
				objImage	= new Image();
			
			if(typeof _self.imageUrls[key]['url'] == 'string')
			{
				$(objImage).bind('load error', function()
				{ 
					_self.imageUrls[key]['status'] = true; 
					_self.$slides.eq(key).css('background-image','url(' + _self.imageUrls[key]['url'] + ')');
					if(typeof callback == 'function') callback.apply( _self, [objImage, key] );
				});
				
				objImage.src = _self.imageUrls[key]['url'];
			}
			else
			{
				if(typeof callback == 'function') callback.apply( _self, [objImage, key] );
			}
		},
		
		_preloadNext: function(key)
		{
			if(typeof this.imageUrls[key] != "undefined")
    		{
				this._preloadSingle(key, function()
	    		{
					this._preloadNext(key + 1);
	    		});
    		}
		},
    	

    	//bind click events of slide controlls to the public functions
    	_bindEvents: function()
    	{
    		var self = this,
    			win  = $( window );

    		this.$slider.on('click','.next-slide', $.proxy( this.next, this) );
    		this.$slider.on('click','.prev-slide', $.proxy( this.previous, this) );
    		this.$slider.on('click','.goto-slide', $.proxy( this.go2, this) );

    		if(this.options.hoverpause)
    		{
    			this.$slider.on('mouseenter', $.proxy( this.pause, this) );
    			this.$slider.on('mouseleave', $.proxy( this.resume, this) );
    		}

    		win.on( 'debouncedresize.aviaSlider',  $.proxy( this._setSize, this) );

    		//if its a desktop browser add arrow navigation, otherwise add touch nav
    		if(!this.isMobile)
    		{
    			this.$slider.avia_keyboard_controls();
    		}
    		else
    		{
    			this.$slider.avia_swipe_trigger();
    		}

    	},

    	//kickoff the slider by binding all functions to slides and buttons, show the first slide and start autoplay
    	_kickOff: function()
    	{
    		var self = this;
    		
    		// bind events to to the controll buttons
			self._bindEvents();
    		
    		//show the first slide
    		self.$slides.eq(0).css({visibility:'visible', opacity:0}).avia_animate({opacity:1}, function()
    		{
    			var current = $(this).addClass('active-slide');
    			self._attach_video_events(current);
    		});
    		
    		// start autoplay if active
			if( self.options.autoplay )
			{
				self._startSlideshow();
			}
    		
    	},

    	//calculate which slide should be displayed next and call the executing transition function
    	_navigate : function( dir, pos ) {

			if( this.isAnimating || this.itemsCount < 2 )
			{
				return false;
			}

			this.isAnimating = true;

			// current item's index
			this.prev = this.current;

			// if position is passed
			if( pos !== undefined )
			{
				this.current = pos;
				dir = this.current > this.prev ? 'next' : 'prev';
			}
			// if not check the boundaries
			else if( dir === 'next' )
			{
				this.current = this.current < this.itemsCount - 1 ? this.current + 1 : 0;
			}
			else if( dir === 'prev' )
			{
				this.current = this.current > 0 ? this.current - 1 : this.itemsCount - 1;
			}

			//set goto button
			this.gotoButtons.removeClass('active').eq(this.current).addClass('active');

			//set slideshow size
			this._setSize(this.current);
			
			//if we are using a background slider make sure that the image is loaded. if not preload it, then show the slide
			if(this.options.bg_slider == true)
			{
				if(this.imageUrls[this.current]['status'] == true )
				{
					this['_' + this.options.animation].call(this, dir);
				}
				else
				{
					this.loader.show();
					this._preloadSingle(this.current, function()
    				{
    					this['_' + this.options.animation].call(this, dir);
    					this.loader.hide();
    				});
				}
			}
			else //no background loader -> images are already loaded
			{
				//call the executing function. for example _slide, or _fade. since the function call is absed on a var we can easily extend the slider with new animations
				this['_' + this.options.animation].call(this, dir);
			}
		},

		//if the next slide has a different height than the current change the slideshow height
		_setSize: function()
		{
			//if images are attached as bg images the slider has a fixed height
			if(this.options.bg_slider == true) return;
		
			var self    = this,
				setTo   = Math.floor(this.$slides.eq(this.current).height()),
				current	= Math.floor(this.$sliderUl.height());

				this.$sliderUl.height(current); //make sure to set the slideheight to an actual value

				if(setTo != current)
				{
					this.$sliderUl.avia_animate({height:setTo});
				}
		},


		//slide animation: do a slide transition by css3 transform if possible. if not simply do a position left transition
		_slide: function(dir)
		{
		
			var self			= this,
				displaySlide 	= this.$slides.eq(this.current),
				hideSlide		= this.$slides.eq(this.prev),
				direction		= dir === 'next' ? -1 : 1,
				sliderWidth		= this.$slider.width(),
				property, transition = [],  transition2 = [];

				displaySlide.css({visibility:'visible', zIndex:4, opacity:1, left:0, top:0});

				//do a css3 animation
				if(this.cssActive)
				{
					property  = this.browserPrefix + 'transform';

					//do a translate 3d transformation if available, since it uses hardware acceleration
					if(this.css3DActive)
					{
						displaySlide.css(property, "translate3d(" + ( sliderWidth * direction * -1) + "px, 0, 0)");
						transition[property]  = "translate3d(" + ( sliderWidth * direction) + "px, 0, 0)";
						transition2[property] = "translate3d(0,0,0)";
					}
					else //do a 2d transform. still faster than a position "left" change
					{
						displaySlide.css(property, "translate(" + ( sliderWidth * direction * -1) + "px,0)");
						transition[property]  = "translate(" + ( sliderWidth * direction) + "px,0)";
						transition2[property] = "translate(0,0)";					}
				}
				else
				{
					displaySlide.css({left:sliderWidth * direction * -1});
					transition.left = sliderWidth * direction;
					transition2.left = 0;
				}



				hideSlide.avia_animate(transition, this.options.transitionSpeed, this.options.easing);
				
				
				var after_slide = function()
				{
					self.isAnimating = false;
					displaySlide.addClass('active-slide');
					hideSlide.css({visibility:'hidden'}).removeClass('active-slide');
					self.$slider.trigger('avia-transition-done');
				}
				
				if(self.options.show_slide_delay > 0)
				{
					setTimeout(function() { displaySlide.avia_animate(transition2, self.options.transitionSpeed, self.options.easing, after_slide); },self.options.show_slide_delay);
				}
				else
				{
					displaySlide.avia_animate(transition2, self.options.transitionSpeed, self.options.easing, after_slide);
				}

		},

		//simple fade transition of the slideshow
		_fade: function()
		{
			var self			= this,
				displaySlide 	= this.$slides.eq(this.current),
				hideSlide		= this.$slides.eq(this.prev);

			displaySlide.css({visibility:'visible', zIndex:2, opacity:0}).avia_animate({opacity:1}, this.options.transitionSpeed/3, 'linear');
			
			self._start_video(displaySlide);
			self._stop_video(hideSlide);
			
			hideSlide.avia_animate({opacity:0}, this.options.transitionSpeed/2, 'linear', function()
			{
				self.isAnimating = false;
				displaySlide.addClass('active-slide');
				hideSlide.css({visibility:'hidden'}).removeClass('active-slide');
				self.$slider.trigger('avia-transition-done');
			});
		},
		
		
		/************************************************************************
		Video functions
		*************************************************************************/
		
		//check if the current slide has a mediaplayer attached that we can control
		_get_video: function(slide)
		{
			return slide.find('video.avia_video').data('mediaelementplayer') || false;
		},
		
		//bind events to the video that tell the slider to autorotate once a video has been played
		_attach_video_events: function(slide)
		{
			var self = this, video_el = slide.find('video.avia_video');
		
			if(slide.data('avia_video_events') != true)
			{	
				slide.data('avia_video_events', true);
				if(self.current === 0) 
				{	
					video_el.bind('av-video-loaded', function() { self._start_video(slide); });
				}
				
				video_el.bind('av-video-ended', function() { self._navigate( 'next' );  self.resume(); });
			}
		},
		
		//start playing a video and pause the slideshow
		_start_video: function(slide)
		{
			var self		 = this,
				mediaElement = self._get_video(slide); 
			if( mediaElement && mediaElement.created)
			{
				self._attach_video_events(slide);				
				// mediaElement.setMuted(true);
				mediaElement.play();
				setTimeout(function(){  self.pause() }, 100); //workaround otherwise the resume would be called instantly after pause for an unknown reason
			}
		},
		
		//pause a video
		_stop_video: function(slide)
		{
			var self		 = this,
				mediaElement = this._get_video(slide);
			if( mediaElement )
			{
				mediaElement.pause();
			}
		},
		

		_timer: function(callback, delay, first)
		{	
		    var self = this, start, remaining = delay;
			
			self.timerId = 0;
			
		    this.pause = function() {
		        window.clearTimeout(self.timerId);
		        remaining -= new Date() - start;
		    };

		    this.resume = function() {
		        start = new Date();
		        self.timerId = window.setTimeout(callback, remaining);
		    };

		    this.destroy = function()
		    {
		    	window.clearTimeout(self.timerId);
		    };

		    this.resume(true);
		},

		//start autorotation
		_startSlideshow: function()
		{
			var self = this;
			
			this.isPlaying = true;
			
			this.slideshow = new this._timer( function()
			{
				self._navigate( 'next' );

				if ( self.options.autoplay )
				{
					self._startSlideshow();
				}

			}, (this.options.interval * 1000));
		},

		//stop autorotation
		_stopSlideshow: function()
		{
			if ( this.options.autoplay ) {

				this.slideshow.destroy();
				this.isPlaying = false;
				this.options.autoplay = false;
			}
		},

		// public method: shows next image
		next : function(e)
		{
			e.preventDefault();
			this._stopSlideshow();
			this._navigate( 'next' );
		},

		// public method: shows previous image
		previous : function(e)
		{
			e.preventDefault();
			this._stopSlideshow();
			this._navigate( 'prev' );
		},

		// public method: goes to a specific image
		go2 : function( pos )
		{
			//if we didnt pass a number directly lets asume someone clicked on a link that triggered the goto transition
			if(isNaN(pos))
			{
				//in that case prevent the default link behavior and set the slide number to the links hash
				pos.preventDefault();
				pos = pos.currentTarget.hash.replace('#','');
			}

			pos -= 1;

			if( pos === this.current || pos >= this.itemsCount || pos < 0 )
			{
				return false;
			}

			this._stopSlideshow();
			this._navigate( false, pos );

		},

		// public method: starts the slideshow
		// any call to next(), previous() or goto() will stop the slideshow autoplay
		play : function()
		{
			if( !this.isPlaying )
			{
				this.isPlaying = true;

				this._navigate( 'next' );
				this.options.autoplay = true;
				this._startSlideshow();
			}

		},

		// public methos: pauses the slideshow
		pause : function()
		{
			if( this.isPlaying )
			{
				this.slideshow.pause();
			}
		},

		// publiccmethos: resumes the slideshow
		resume : function()
		{
			if( this.isPlaying )
			{
				this.slideshow.resume();
			}
		},

		// public methos: destroys the instance
		destroy : function( callback )
		{
			this.slideshow.destroy( callback );
		}

    }

    //simple wrapper to call the slideshow. makes sure that the slide data is not applied twice
    $.fn.aviaSlider = function( options )
    {
    	return this.each(function()
    	{
    		var self = $.data( this, 'aviaSlider' );

    		if(!self)
    		{
    			self = $.data( this, 'aviaSlider', new $.AviaSlider( options, this ) );
    		}
    	});
    }



})( jQuery );




// -------------------------------------------------------------------------------------------
// keyboard controls
// -------------------------------------------------------------------------------------------

(function($)
{
	"use strict";

	/************************************************************************
	keyboard arrow nav
	*************************************************************************/
	$.fn.avia_keyboard_controls = function(options_passed)
	{
		var defaults	=
		{
			37: '.prev-slide',	// prev
			39: '.next-slide'	// next
		},

		methods		= {

			mousebind: function(slider)
			{
				slider.hover(
					function(){  slider.mouseover	= true;  },
					function(){  slider.mouseover	= false; }
				);
			},

			keybind: function(slider)
			{
				$(document).keydown(function(e)
				{
					if(slider.mouseover && typeof slider.options[e.keyCode] !== 'undefined')
					{
						var item;

						if(typeof slider.options[e.keyCode] === 'string')
						{
							item = slider.find(slider.options[e.keyCode]);
						}
						else
						{
							item = slider.options[e.keyCode];
						}

						if(item.length)
						{
							item.trigger('click', ['keypress']);
							return false;
						}
					}
				});
			}
		};


		return this.each(function()
		{
			var slider			= $(this);
			slider.options		= $.extend({}, defaults, options_passed);
			slider.mouseover	= false;

			methods.mousebind(slider);
			methods.keybind(slider);

		});
	};


	/************************************************************************
	swipe nav
	*************************************************************************/
	$.fn.avia_swipe_trigger = function(passed_options)
	{
		var win		= $(window),
		isMobile	= document.documentElement.ontouchstart !== undefined ? true : false,
		defaults	=
		{
			prev: '.prev-slide',
			next: '.next-slide'
		},

		methods = {

			activate_touch_control: function(slider)
			{
				var i, differenceX, differenceY;

				slider.touchPos = {};
				slider.hasMoved = false;

				slider.on('touchstart', function(event)
				{
					slider.touchPos.X = event.originalEvent.touches[0].clientX;
					slider.touchPos.Y = event.originalEvent.touches[0].clientY;
				});

				slider.on('touchend', function(event)
				{
					slider.touchPos = {};
	                if(slider.hasMoved) { event.preventDefault(); }
	                slider.hasMoved = false;
				});

				slider.on('touchmove', function(event)
				{
					if(!slider.touchPos.X)
					{
						slider.touchPos.X = event.originalEvent.touches[0].clientX;
						slider.touchPos.Y = event.originalEvent.touches[0].clientY;
					}
					else
					{
						differenceX = event.originalEvent.touches[0].clientX - slider.touchPos.X;
						differenceY = event.originalEvent.touches[0].clientY - slider.touchPos.Y;

						//check if user is scrolling the window or moving the slider
						if(Math.abs(differenceX) > Math.abs(differenceY))
						{
							event.preventDefault();

							if(slider.touchPos !== event.originalEvent.touches[0].clientX)
							{
								if(Math.abs(differenceX) > 50)
								{
									i = differenceX > 0 ? 'prev' : 'next';

									if(typeof slider.options[i] === 'string')
									{
										slider.find(slider.options[i]).trigger('click', ['swipe']);
									}
									else
									{
										slider.options[i].trigger('click', ['swipe']);
									}

									slider.hasMoved = true;
									slider.touchPos = {};
									return false;
								}
							}
						}
	                }
				});
			}
		};

		return this.each(function()
		{
			if(isMobile)
			{
				var slider	= $(this);

				slider.options	= $.extend({}, defaults, passed_options);

				methods.activate_touch_control(slider);
			}
		});
	};













}(jQuery));





