(function($)
{
	"use strict";
	$.AviaElementBehavior = $.AviaElementBehavior || {};
	$.AviaElementBehavior.wp_media = $.AviaElementBehavior.wp_media || [];
	
 	$.AviaModal = function (options) {
        
        var defaults = {
        	
        		scope: this,					//@obj pass the "this" var of the invoking function to apply the correct callback later
        		modal_title: "",				//@string modal window title
        		modal_class: "",				//@string modal window class
        		modal_content: false,			//@string modal window content. if not specified ajax function will execute
        		modal_ajax_hook: "",			//@string name of php ajax hook that will execute the content fetching function
        		on_save: function(){},			//@function modal window callback function when the save button is hit
        		on_load: function(){},			//@function modal window callback function when the modal is open and finished loading
        		before_save: '',		        //@function modal window callback function when the save button is hit and the data is collected but before the final save is executed
        		save_param: {},					//@obj parameters that are passed to the callback function in addition to the form values
        		ajax_param: "",					//@string parameters that are passed to the ajax content fetching function
        		button: "save"					//@string parameter that tells the modal window which button to generate
        }
        
        $.AviaModal.openInstance.unshift(this); 
        
        this.instanceNr	= $.AviaModal.openInstance.length;
        this.options	= $.extend({}, defaults, options);
        this.namespace	= '.AviaModal'+ this.instanceNr;
        this.body		= $('body').addClass('avia-noscroll');
        this.wrap		= $('#wpwrap');
        this.doc		= $(document);
        this.modal		= $('<div class="avia-modal avia-style"></div>');
        this.backdrop	= $('<div class="avia-modal-backdrop"></div>');
       
        this.set_up();
        
       
    };
    
    $.AviaModal.openInstance = [];
    
   	$.AviaModal.prototype = 
   	{
   		set_up: function()
   		{
   			this.create_html();
   			this.add_behavior();
   			this.modify_binding_order();
   			this.propagate_modal_open();
   		},
   		
   		add_behavior: function()
   		{
   			var obj = this;
   			
			//save modal (execute callback)
			this.modal.on('click', '.avia-modal-save', function()
			{
				obj.execute_callback();
				return false;
			});
			
			//close modal 
			this.backdrop.add(".avia-attach-close-event",this.modal).on('click', function()
			{
				obj.close();
				return false;
			});
			
			// close modal by pressing escape key. modify_binding_order makes sure that this is fired first. 
			// bind event on keydown instead of keyup cause it will probably not interfere with other plugins
			//fire save event on ENTER (13)
			this.doc.bind('keydown'+this.namespace, function(e) 
			{
				if(obj.media_overlay_closed())
				{
					if (e.keyCode == 13 && !(e.target.tagName && e.target.tagName.toLowerCase() == "textarea"))
					{
						setTimeout( function(){ obj.execute_callback(); }, 100); 
						e.stopImmediatePropagation();
					}
					if (e.keyCode == 27)
					{ 
						setTimeout( function(){ obj.close(); }, 100); 
						e.stopImmediatePropagation();
					}
				}
			});
   		},
   		
   		modify_binding_order: function()
   		{
   			var data = jQuery.hasData( document ) && jQuery._data( document ),
   				lastItem = data.events.keydown.pop();
   				data.events.keydown.unshift(lastItem);
   		},
   			
   		
   		create_html: function()
   		{
   		
   			var content	= this.options.modal_content ? this.options.modal_content : '',
   				loading = this.options.modal_content ? "" : ' preloading ',
   				title	= '<h3 class="avia-modal-title">'+this.options.modal_title+'</h3>',
   				output  = '<div class="avia-modal-inner">';
   				output += '<div class="avia-modal-inner-header">'+title+'<a href="#close" class="avia-modal-close avia-attach-close-event">X</a></div>';
   				output += '<div class="avia-modal-inner-content '+loading+'">'+content+'</div>';
   				output += '<div class="avia-modal-inner-footer">';
   				
   				if(this.options.button == "save")
   				{
   					output += '<a href="#save" class="avia-modal-save button button-primary button-large">' + avia_modal_L10n.save + '</a>';
   				}
   				else if(this.options.button == "close")
   				{
   					output += '<a href="#close" class="avia-attach-close-event button button-primary button-large">' + avia_modal_L10n.close + '</a>';
   				}
   				else
   				{
   					output += this.options.button;
   				}
   				
   				output += '</div></div>';
   			
   			
   			//set specific modal class
   			if(this.options.modal_class) 
   			{
   				this.modal.addClass(this.options.modal_class);
   			}
   			
   			this.wrap.append(this.modal).append(this.backdrop); //changed to this.wrap instead of this.body to prevent bug with link editor popup
   			this.modal.html(output);
   			
   			//set modal margin and z-index for nested modals
   			var multiplier 	= this.instanceNr - 1,
   				z_old		= parseInt(this.modal.css('zIndex'),10);
   			
   			this.modal.css({margin: (30 * multiplier), zIndex: (z_old + multiplier + 1 )});
   			this.backdrop.css({zIndex: (z_old + multiplier)});
   			
   			
   			if(!this.options.modal_content)
   			{
   				this.fetch_ajax_content();
   			}
   			else
   			{
   				this.on_load_callback();
   			}			
   		},
   		
   		set_focus: function()
   		{
   			this.modal.find('select, input[type=text], input[type=checkbox], textarea, radio').filter(':eq(0)').focus();
   		},
   		
   		fetch_ajax_content: function()
   		{
   			var obj = this, inner = obj.modal.find('.avia-modal-inner-content');
   		
	   			$.ajax({
					type: "POST",
					url: ajaxurl,
					data: 
					{
						action: 'avia_ajax_' + this.options.modal_ajax_hook,
						params: this.options.ajax_param,
						ajax_fetch: true,
						instance: this.instanceNr
					},
					error: function()
					{
						$.AviaModal.openInstance[0].close();
						new $.AviaModalNotification({mode:'error', msg:avia_modal_L10n.ajax_error});
					},
					success: function(response)
					{
						if(response == 0)
						{
							$.AviaModal.openInstance[0].close();
							new $.AviaModalNotification({mode:'error', msg:avia_modal_L10n.login_error});
						}
						else if(response == "-1") // nonce timeout
						{
                            $.AviaModal.openInstance[0].close();
                            new $.AviaModalNotification({mode:'error', msg:avia_modal_L10n.timeout});
						}
						else
						{
							inner.html(response);
							obj.on_load_callback();
						}
					},
					complete: function(response)
					{	
						inner.removeClass('preloading');
					}
				});
   		},
   		
   		on_load_callback: function()
   		{	
   			var callbacks = this.options.on_load,
   				execute, index = 0;
   				
   			if(typeof callbacks == 'string')
   			{
   				execute = callbacks.split(", ");
   				for(index in execute)
   				{
   					if(typeof $.AviaModal.register_callback[execute[index]] != 'undefined')
   					{
   						$.AviaModal.register_callback[execute[index]].call(this);
   					}
   					else
   					{
   						avia_log('modal_on_load function "$.AviaModal.register_callback.'+execute[index]+'" not defined','error');
   						avia_log('Make sure that the modal_on_load function defined in your Shortcodes config array exists','help');
   					}
   				}
   				
   			}
   			else if(typeof callbacks == 'function')
   			{
   				callbacks.call();
   			}
   			
   			this.set_focus();
   			this.propagate_modal_content();
   		},
   		
   		close: function()
   		{
   			$.AviaModal.openInstance.shift(); //remove the first entry from the openInstance array 
   		
   			this.modal.remove();
   			this.backdrop.remove();
   			this.doc.trigger('avia_modal_close').unbind('keydown'+this.namespace); 
   			
   			if($.AviaModal.openInstance.length == 0)
   			{
   				this.body.removeClass('avia-noscroll');
   			}
   		},
   		
   		convert_values: function(a)
   		{
   			var o = {};
   			
   			$.each(a, function() 
   			{
   			
		       if (typeof o[this.name] !== 'undefined') 
		       {		       
		           if (!o[this.name].push) 
		           {
		               o[this.name] = [o[this.name]];
		           }
		           o[this.name].push(this.value || '');
		       } 
		       else 
		       {
		           o[this.name] = this.value || '';
		       }
		   });
		   return o;
   		},
   		
   		execute_callback: function()
   		{	
   			var values = this.modal.find('input, select, radio, textarea').serializeArray(), 
   				value_array = this.convert_values(values);
   				
   				//filter function for the value array in case we got a special shortcode like tables
   				if(typeof $.AviaModal.register_callback[this.options['before_save']] != 'undefined')
   				{
   					value_array = $.AviaModal.register_callback[this.options['before_save']].call(this.options.scope, value_array, this.options.save_param);
   				}
   				
   			var close_allowed = this.options['on_save'].call(this.options.scope, value_array, this.options.save_param);
   			
   			if(close_allowed !== false)
   			{
   				this.close();
   		    }
   		},
   		
   		media_overlay_closed: function()
   		{
   			return $.AviaElementBehavior.wp_media.length ? false : true;
   		},
   		
   		propagate_modal_open: function()
   		{
   			this.body.trigger('avia_modal_open', this);
   		},
   		
   		propagate_modal_content: function()
   		{
   			this.body.trigger('avia_modal_finished', this);
   		}
   	
   	}
   	
   	
   	
   	
   	
   	
   	
   	
   		
	//wrapper for small modal notifications
	
	$.AviaModalNotification = function(options)
	{
		var defaults = {
		
        		modal_content: "<div class='avia-form-element-container'>" + options.msg + "</div>",				
        		modal_class: "flexscreen",				
        		modal_title: "<span class='avia-msg-"+ options.mode +"'>" + avia_modal_L10n[options.mode] + "</span>",
        		button: "close"		
        }
		this.options = $.extend({}, defaults, options);
		return new $.AviaModal(this.options);
	}
   	
   	

   	
   	//allowed callbacks once the popup opens
   	
   	$.AviaModal.register_callback = $.AviaModal.register_callback || {};
   	
   	
   	$.AviaModal.register_callback.modal_start_sorting = function(passed_scope)
	{
		var scope	= passed_scope || this.modal,
			target	= scope.find('.avia-modal-group'),
			params	= {
					handle: '.avia-attach-modal-element-move',
					items: '.avia-modal-group-element',
					placeholder: "avia-modal-group-element-highlight",
					tolerance: "pointer",
					//axis: 'y',
					forcePlaceholderSize:true,
					start: function( event, ui ) 
					{
						$('.avia-modal-group-element-highlight').height(ui.item.outerHeight()).width(ui.item.outerWidth());
					},
					update: function(event, ui) 
					{
						//obj.updateTextarea();
					},
					stop: function( event, ui ) 
					{
						//obj.canvas.removeClass('avia-start-sorting');
					}
				};
			
			target.find('.avia-modal-group-element, .avia-insert-area').disableSelection();	
			target.sortable(params);
	}
   	
   	
   	
   	$.AviaModal.register_callback.modal_load_colorpicker = function()
	{
		var scope			= this.modal,
			colorpicker		= scope.find('.colorpicker').wpColorPicker(),
			picker_button	= scope.find('.wp-color-result');
			
			picker_button.click(function(e)
			{
				if(typeof e.originalEvent != "undefined")
				{
					var open = scope.find('.wp-picker-open').not(this).trigger('click');
				}
			});
	}
   	
   	//once a modal with tinyMCE editor is opened execute the following function

	$.AviaModal.register_callback.modal_load_tiny_mce = function(textareas)
	{
		textareas = textareas || this.modal.find('.avia-modal-inner-content .avia_tinymce');
		
		var modal    = textareas.parents('.avia-modal:eq(0)'),
			save_btn = modal.find('.avia-modal-save');
		
		textareas.each(function()
		{
			var el_id		= this.id,
				current 	= $(this), 
				parent		= current.parents('.wp-editor-wrap:eq(0)'),
				textarea	= parent.find('textarea.avia_tinymce'),
				switch_btn	= parent.find('.wp-switch-editor').removeAttr("onclick"),
				settings	= {id: this.id , buttons: "strong,em,link,block,del,ins,img,ul,ol,li,code,spell,close"};
			
			// add quicktags for text editor
			quicktags(settings);
			QTags._buttonsInit(); //workaround since dom ready was triggered already and there would be no initialization
			
			// modify behavior for html editor
			switch_btn.bind('click', function()
			{
				var button = $(this);
				
				if(button.is('.switch-tmce'))
				{
					parent.removeClass('html-active').addClass('tmce-active');
					window.tinyMCE.execCommand("mceAddControl", true, el_id);
					window.tinyMCE.get(el_id).setContent(window.switchEditors.wpautop(textarea.val()), {format:'raw'});
				}
				else
				{
					parent.removeClass('tmce-active').addClass('html-active');
					window.tinyMCE.execCommand("mceRemoveControl", true, el_id);
				}
			}).trigger('click');
			
			//make sure that when the save button is pressed the textarea gets updated and sent to the editor
			save_btn.bind('click', function()
			{
				switch_btn.filter('.switch-html').trigger('click');
			});
			
		});
		
		
		
		
		
	}
})(jQuery);	 


