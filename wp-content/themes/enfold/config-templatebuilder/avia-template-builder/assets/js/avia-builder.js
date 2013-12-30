/* this prevents dom flickering, needs to be outside of dom.ready event.also adds several extra classes for better browser support */
document.documentElement.className += ' js_active ';
document.documentElement.className += 'ontouchstart' in document.documentElement ? ' avia_mobile ' : ' avia_desktop ';
(function()
{
    var prefix = ['-webkit-','-o-','-moz-', '-ms-', ""];
    for (var i in prefix)
    {
        if(prefix[i]+'transform' in document.documentElement.style) document.documentElement.className += " avia_transform ";
    }
})();

//global logging helper
function avia_log(text, type)
{
	if(typeof console === 'undefined') return;
	if(typeof type === 'undefined') type = "log"
	type = "AVIA-" + type.toUpperCase();
	console.log("["+type+"] " +text);
}
//global newline helper
function avia_nl2br (str, is_xhtml) 
{
	var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br ' + '/>' : '<br>';
	return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}










//main builder js
(function($)
{
	"use strict";

	$.AviaBuilder = function () 
	{
		// the canvas we use to display the interface
        this.canvas				= $('#aviaLayoutBuilder');
        
        // box wrapping the canvas
        this.canvasParent		= this.canvas.parents('.postbox:eq(0)');
        
        //wether the layout builder is currently active or the wordpress default editor is
        this.activeStatus		= this.canvasParent.find('#aviaLayoutBuilder_active');
        
        //list of available shortcode buttons
        this.shortcodes 		= $.AviaBuilder.shortcodes || {};
        
        //if safemode is true the wordpress default editor will not show the shortcodes
        this.safemode			= avia_globals.builderMode || false;
        
        //wether tinymce is available
        this.tiny_active		= typeof window.tinyMCE == 'undefined' ? false : true;
        
        //wordpress tiny_mce editor
        this.classic_editor 	= $('#postdivrich');
        
        //wrapper arroudn tinymce editor
        this.classic_editor_wrap= $('#postdivrich_wrap');
        
        //button to switch between wordpress editor and avia builder
        this.switch_button      = this.classic_editor_wrap.find('.avia-builder-button');
        
        //fallback html textarea for the wordpress tinymce editor
        this.classic_textarea 	= $('#content.wp-editor-area');
        
        //field that stores all our content
        this.secureContent		= $('#_aviaLayoutBuilderCleanData');
        
        //textarea elements for each builder element that holds the actual shortcode + content
        this.datastorage		= 'textarea[data-name="text-shortcode"]';
        
        //wrapper arround all the shortcode buttons
        this.shortcode_wrap 	= $('.shortcode_button_wrap');
        
        //wether we are in target insert mode or not, if set the var holds an element that gets inserted into the canvas on click
        this.targetInsert		= false;
        
        //if we only insert one item (cut&move: deprecated) or multiple
        this.only_one_insert	= false;
        
        //var that holds the function to update the editor once updateTextarea() was executed, since updating the tinymce field requires too much resources on big sites
        this.update_timout	= false;
        
        //activate the plugin
        this.set_up();
    };
    
   	$.AviaBuilder.prototype = {
		
		/**
		* Sets up the whole interface
		*
		*/
		set_up: function()
		{
		    this.place_top();
			this.convertTextToInterface();
			this.add_behavior();
		},
		
		/**
		* Moves the editor to the fist position and triggers post box saving, in case it is no already at the first pos :)
		*
		*/
		place_top: function()
		{
            var meta_boxe_container = $('#normal-sortables'),
                meta_boxes          = meta_boxe_container.find('.postbox');
               
            if(this.canvasParent.length && meta_boxes.index(this.canvasParent) !== 0)
            {
                this.canvasParent.prependTo(meta_boxe_container);
                window.postboxes.save_order(pagenow);
            }
		},
		

		/**
		* all event binding goes here
		*
		*/
		add_behavior: function()
		{
			var obj = this, $body = $('body');
			
			//switch between normal text editor and advanced layout editor
			this.switch_button.on('click', function(e)
			{
                e.preventDefault();
				obj.switch_layout_mode();
			});
			
			
			//add a new element to the AviaBuilder canvas
			this.shortcode_wrap.on('click', '.shortcode_insert_button', function()
			{
				var parent 			= $(this).parents('.shortcode_button_wrap'),
					execute			= this.hash.replace('#',''),
					target			= "instant_insert", // this.className.indexOf('avia-target-insert') !== -1 ? "target_insert" : "instant_insert",
					already_active	= this.className.indexOf('avia-active-insert') !== -1 ? true : false;
				
				/*
				parent.find('.avia-active-insert').removeClass('avia-active-insert');
				
				if(already_active)
				{
					 obj.targetInsertInactive();
					 return false;
				}
				
				if(target == "target_insert")
				{
					this.className += " avia-active-insert"
				}
				else
				{
					obj.targetInsertInactive();
				}	
				*/
				
				obj.shortcodes.fetchShortcodeEditorElement(execute, target, obj);
				
				return false;
			});
			
			//remove element from canvas
			this.canvas.on('click', 'a.avia-delete', function()
			{
				obj.shortcodes.deleteItem(this, obj);
				return false;
			});
			
			
			//change size of column element
			this.canvas.on('click', 'a.avia-change-col-size', function()
			{
				obj.shortcodes.changeSize(this, obj);
				return false;
			});
	
			
			//edit item via modal window
			$body.on('click', '.avia-edit-element', function()
			{
				var parent				  = $(this).parents('.avia_sortable_element:eq(0)');
				if(!parent.length) parent = $(this).parents('.avia_layout_section:eq(0)');
				
					var params			= parent.data(), modal;
					params.scope		= obj;
					params.on_load		= parent.data('modal_on_load');
					params.before_save	= parent.data('before_save');
					params.on_save		= obj.send_to_datastorage;
					params.save_param	= parent;
					params.ajax_param	= {extract: true, shortcode: parent.find('>.avia_inner_shortcode>'+ obj.datastorage + ':eq(0)').val(), allowed: params.allowedShortcodes};
				
				var modal = new $.AviaModal(params);
				return false;
			});
			
			
			//edit item in modal window via sub modal window
			$body.on('click', '.avia-modal-group-element-inner', function() 
			{
				var parent				= $(this).parents('.avia-modal-group-element:eq(0)'),
					params				= parent.data(), modal;
					
					params.scope		= obj;
					params.on_load		= parent.data('modal_on_load');
					params.before_save	= parent.data('before_save');
					params.on_save		= obj.send_to_datastorage;
					params.save_param	= parent;
					params.ajax_param	= {subelement: true, extract: true, shortcode: parent.find(obj.datastorage + ':eq(0)').val()};
					
				var modal = new $.AviaModal(params);
				return false;
			});
			
			//delete sub items in modal window
			$body.on('click', '.avia-attach-modal-element-delete', function(e)
			{
				obj.shortcodes.deleteModalSubItem(this, e);
			});
			
			//add sub item modal window
			$body.on('click', '.avia-attach-modal-element-add', function(e)
			{
				obj.shortcodes.appendModalSubItem(this, e);
			});
			

			//copy item
			this.canvas.on('click', 'a.avia-clone', function()
			{
				obj.cloneElement(this, obj);
				return false;
			});

			//recalc shortcode when select elements change
			this.canvas.on('change', 'select.avia-recalc-shortcode', function()
			{
				var container = $(this).parents('.avia_sortable_element:eq(0)');
				obj.recalc_shortcode(container);
				return false;
			});
			
			
			//re activate sorting and dropping after undo and redo changes
			this.canvas.on('avia-history-update', function()
			{
				obj.activate_element_dragging(this.canvasParent,"");
				obj.activate_element_dropping(this.canvasParent,"");
			});
			
			
			
						
			//target mode functions: currently disabled. somehow they appear less usefull with the new drag and drop system
			
			//move item
			/*
			this.canvas.on('click', 'a.avia-new-target', function()
						{
							obj.targetCutItem(this, obj);
							return false;
						});
			
			
			//target insert of an element
			this.canvasParent.on('click', '.av_drop', function()
			{
				if(obj.targetInsert)
				{
					obj.doTargetInsert(this, obj);
					return false;
				}
			});
			
			//disable target mode if right mouse button is clicked
			$body.on("contextmenu",function(e)
			{	
				if(obj.targetInsert)
				{
					obj.targetInsertInactive();
					return false;
				}
			}); 
			
			//keboard buttons for target inserting
			$(document).keydown(function(e) 
			{
				if(obj.targetInsert)
				{
					//escape button pressed when in target insert mode: deactivate target insert mode
			  		if (e.keyCode == 27) { setTimeout( function(){ obj.targetInsertInactive(); }, 150);  }
			  		
			  		//move target insert element to prev next item. 37 = left, 39 = right
			  		if (e.keyCode == 37 || e.keyCode == 39 ) 
			  		{ 
			  			e.preventDefault();
			  			setTimeout( function(){ obj.changeTargetingShortcode(e.keyCode); }, 150);  
			  		}	
			  	}	
			});
			*/
			
			
			
			
		},
		

		
		// ------------------------------------------------------------------------------------------------------------
		// main interface drag and drop implementation
		// ------------------------------------------------------------------------------------------------------------
		
		activate_element_dragging: function(passed_scope, exclude)
		{
			//drag
			var obj		= this,
				scope  	= passed_scope || this.canvasParent,
				params 	= 
				{ 
					appendTo: "body",
					handle: '>.avia_sorthandle',
					helper: "clone",
					scroll: true,
					zIndex: 1000,
					cursorAt: { left: 20 },
					start: function( event, ui )
					{
						var current = $(event.target);
						
						//reduce elements opacity so user got a visual feedback on what he is editing
						current.css({opacity:0.4});
						
						//remove all previous hover elements
						$('.avia-hover-active').removeClass('avia-hover-active');
						
						//add a class to the container element that highlights all possible drop targets
						obj.canvas.addClass('avia-select-target-' + current.data('dragdrop-level'));
					},
					
					stop: function(event, ui )
					{
						//return opacity of element to normal
						$(event.target).css({opacity:1});
						
						//remove hover class from all elements
						$('.avia-hover-active').removeClass('avia-hover-active');
						
						//reset highlight on container class
						obj.canvas.removeClass('avia-select-target-1 avia-select-target-2 avia-select-target-3 avia-select-target-4');
					}
				};
			
			
			if(typeof exclude == "undefined") exclude = ":not(.ui-draggable)";
			scope.find('.av_drag'+exclude).draggable(params);
			
			params.cursorAt = { left: 33, top:33 };
			params.handle   = false;
			scope.find('.shortcode_insert_button').not('.ui-draggable').draggable(params);
		},
		
		
		
		activate_element_dropping: function(passed_scope, exclude)
		{
			//drag
			var obj		= this,
				scope  	= passed_scope || this.canvasParent,
				params 	= 
				{ 
					tolerance: 'pointer',
					greedy: true,
				
					over: function(event, ui)
					{
						var dropable = $(this);
						if(obj.droping_allowed(ui.helper, dropable))
						{
							dropable.addClass('avia-hover-active');
						}
					},
					
					out: function(event, ui)
					{
						$(this).removeClass('avia-hover-active');
					},
					
					drop: function(event, ui)
					{
						// this = the target that we dropped the draggable onto
						var dropable = $(this);
						
						//check if the previous check for droping_allowed returend true, otherwise do nothing
						if(!dropable.is('.avia-hover-active')) return false;
						
						//get all items within the dropable and check their position so we know where exactly to add the dragable
						var elements = dropable.find('>.av_drag'), offset = {}, method = 'after', toEl = false, position_array = [], last_pos, max_height;
						
						//avia_log("dragging:" + ui.draggable.find('h2').text() +" to position: "+ui.offset.top + "/" +ui.offset.left);
						
						//iterate over all elements and check their positions
						for (var i=0; i < elements.length; i++)
						{ 
							var current = elements.eq(i);
							offset = current.offset();
							
							if(offset.top < ui.offset.top)
							{
								toEl = current;
								last_pos = offset;
								//save all items before the draggable to a position array so we can check if the right positioning is important
								if(!position_array["top_"+offset.top]) 
								{ 	
									max_height = 0;
									position_array["top_"+offset.top] = []; 
								}
								max_height = max_height > current.outerHeight() +offset.top ? max_height : current.outerHeight() +offset.top;
								position_array["top_"+offset.top].push({left: offset.left, top: offset.top, index: i, height: current.outerHeight(), maxheight: current.outerHeight() +offset.top});
								
								//avia_log(current.find('h2').text() + " element offset:" +offset.top + "/" +offset.left);
							}
							else
							{
								break;
							}
						}
						
						//if we got multiple matches that all got the same top position we also need to check for the left position
						if(last_pos && position_array["top_"+last_pos.top].length > 1 && max_height -40 > ui.offset.top)
						{
							var real_element = false;
							
							//avia_log("checking right positions:");
							
							for (var i=0; i < position_array["top_"+last_pos.top].length; i++)
							{
								//console.log(position_array["top_"+last_pos.top][i]);
							
								if(position_array["top_"+last_pos.top][i].left < ui.offset.left)
								{
									real_element = position_array["top_"+last_pos.top][i].index;
								}
								else
								{
									break;
								}
							}
							
							//if we got an index get that element from the list, else delete the toEL var because we need to append the draggable to the start and the next check will do that for us
							if(real_element === false)
							{
								//avia_log("No right pos element found, using first element");
								real_element = position_array["top_"+last_pos.top][0].index;
								method = 'before';
							}
							
							toEl = elements.eq(real_element);
						}
						
						
						//if no element with higher offset were found there either are no at all or the new position is at the top so we change the params accordingly
						if(toEl === false)
						{
							//avia_log('no el found');
							toEl = dropable;
							method = 'prepend';
						}
						
						//avia_log( ui.draggable.find('h2').text() + " dragable top:" +ui.offset.top + "/" +ui.offset.left);
						
						//if the draggable and the new el are the same do nothing
						if(toEl[0] == ui.draggable[0])
						{
							 //avia_log("same element selected: stopping script");
							 return;
						}
						
						//if we got a hash on the draggable we are not dragging an existing element but a new one via shortcode button so we need to fetch an empty shortcode template
						if(ui.draggable[0].hash)
						{
							var shortcode 	= ui.draggable.get(0).hash.replace('#',''),
								template 	= $($("#avia-tmpl-"+shortcode).html());
							
							ui.draggable = template;
						}
						
        				//before finaly moving the element, save the former parent of the draggable to a var so we can check later if we need to update that parent as well
						var formerParent = ui.draggable.parents('.av_drag:last');
						
						
						//move the real dragable element to the new position
						toEl[method](ui.draggable);
						
						
						//avia_log("Appended to: " + toEl.find('h2').text());
						
						//if the element got a former parent we need to update that as well
						if(formerParent.length)
						{
							obj.updateInnerTextarea(false, formerParent); 
						}
						
						
						//get the element that the new element was inserted into. This has to be the parrent of the current toEL since we usualy insert the new element outside of toEL with the "after" method
						//if method != 'after' the element was inserted with prepend directly to the toEL and toEL should therefore also the insertedInto element
						
						var insertedInto = method == 'after' ? toEl.parents('.av_drop') : toEl;
						
						if(insertedInto.data('dragdrop-level') !== 0)
						{	
							//avia_log("Inner update necessary. Level:" + insertedInto.data('dragdrop-level'));
							obj.updateTextarea();//<-- actually only necessary because of column first class. optimize that so we can remove the costly function of updating all elements
      						obj.updateInnerTextarea(ui.draggable);
						}
						
						//everything is fine, now do the re sorting and textfield updating
						obj.updateTextarea();
						
						//if we were in target mode deactivate that
						obj.targetInsertInactive();
						
						//apply dragging and dropping in case we got a new element
						if(typeof template != "undefined")
						{
							obj.canvas.removeClass('ui-droppable').droppable('destroy');
							obj.activate_element_dragging();
							obj.activate_element_dropping();
						}
						
						obj.do_history_snapshot();
						//avia_log("-----------------------------");
					}

				};
			
			if(typeof exclude == "undefined") exclude = ":not(.ui-droppable)";
			
			//if exclude is set to destroy remove all dropables and then re-apply
			if("destroy" == exclude)
			{
				scope.find('.av_drop').droppable('destroy');
				exclude = "";
			}
			
			scope.find('.av_drop'+exclude).droppable(params);		
		},
		
		//compares the drop levels of the 2 elments. if the dragable has a higher drop level it may be dropped upon the droppable
		droping_allowed: function(dragable, droppable)
		{
			if(dragable.data('dragdrop-level') > droppable.data('dragdrop-level'))
			{
				return true;
			}
			
			return false;
		},

			
		/**
		* Switches between the wordpress editor and the AviaBuilder editor
		*
		*/
		switch_layout_mode: function()
		{
			var self = this, editor = this.tiny_active ? window.tinyMCE.get('content') : false;
			
			if(this.activeStatus.val() != 'active')
			{
				$('#content-html').trigger('click');
				self.classic_editor_wrap.addClass('avia-hidden-editor');
				self.switch_button.addClass('avia-builder-active').text(self.switch_button.data('active-button'));
				self.activeStatus.val('active');
				self.canvasParent.removeClass('avia-hidden');
					
				setTimeout(function()
				{
					$('#content-tmce').trigger('click');
					self.convertTextToInterface();
					
					/*
					if(self.safemode && this.safemode == 'safe' && self.secureContent.val() != "")
					{	
						if(editor) editor.setContent(self.secureContent.val(), {format:'html'});
						self.classic_textarea.val(self.secureContent.val());
					}
					*/
					
				},10);
			}
			else
			{
				this.classic_editor_wrap.removeClass('avia-hidden-editor');
				this.switch_button.removeClass('avia-builder-active').text(this.switch_button.data('inactive-button'));
				this.activeStatus.val("");
				this.canvasParent.addClass('avia-hidden');
				this.canvas.addClass('preloading').find('>*:not(.avia-controll-bar, .avia-insert-area)').remove();
				

				if(this.safemode && this.safemode == 'safe' && this.secureContent.val().indexOf('[') !== -1)
				{
					avia_log('Switching to Classic Editor. Template Builder is in safe mode and will empty the textarea so user cant edit shortcode directly');
					if(editor) editor.setContent("", {format:'html'});
					this.classic_textarea.val("");
				}
			}
			
			return false;
		},
		
		
		/**
		* Send element(s) to the AviaBuilder Canvas
		* Gets executed on page load to display all elements and when a single item is fetched via AJAX or HTML5 Storage
		*/
		sendToAdvancedEditor: function( text )
		{
			var add = $(text);
		
			this.canvas.append(add);
			this.activate_element_dragging();
			this.activate_element_dropping();
		},
		
		
		/**
		* Updates the Textarea that holds the shortcode + values when located in a nested enviroment like columns
		*/
		
		updateInnerTextarea: function(element , container )
		{
		    //if we dont have a container passed but an element try to fetch the outer most possible container that wraps that element: A section
            if(typeof container == "undefined")
			{
                container = $(element).parents('.avia_layout_section:eq(0)');
            }
            
            //if we got no section and no container yet check if the container is a column
            if(!container.length)
			{
                container = $(element).parents('.avia_layout_column:eq(0)');
			}
            
            //stil no container? no need for an inner update
            if(!container.length)
			{
                return;
			}
            
            //if we are in a section iterate over all columns inside and set the value before setting the section value
            if(container.is('.avia_layout_section'))
            {
                var columns = container.find('.avia_layout_column');
                for (var i = 0; i < columns.length; i++) 
    			{   
    				this.updateInnerTextarea(false, $(columns[i]));
    			}
    			
    			var main_storage	= container.find('>.avia_inner_shortcode >' + this.datastorage),
                    content_fields	= container.find('>.avia_inner_shortcode > div ' +this.datastorage + ':not(.avia_layout_column .avia_sortable_element '+this.datastorage+')'),
                    content			= "",
				    currentName		= container.data('shortcodehandler'),
				    open_tag        = main_storage.val().match(new RegExp("\\["+currentName+".*?\\]"));
				    
    				for (var i = 0; i < content_fields.length; i++) 
        			{
        				content	+= $(content_fields[i]).val();
        			}

        			
        			content = open_tag[0]+"\n\n" + content + "[/"+ currentName +"]";
        			main_storage.val(content);
            }
            
            
            
            if(container.is('.avia_layout_column'))
            {
                var main_storage	= container.find('>.avia_inner_shortcode >' + this.datastorage),
                    content_fields	= container.find('.avia_sortable_element ' + this.datastorage),
                    content			= "",
				    currentSize		= container.data('width'),
				    currentFirst	= container.is('.avia-first-col') ? " first" : "";
				    
				for (var i = 0; i < content_fields.length; i++) 
    			{
    				content	+= $(content_fields[i]).val();
    			}
    			
    			content = "["+currentSize+currentFirst+"]\n\n" + content + "[/"+ currentSize +"]";
    			main_storage.val(content);
            }
            
            
            
            
		},
		
		
		/**
		* Updates the Textarea that holds the shortcode + values when element is on the first level and not nested
		*/
		updateTextarea: function(scope)
		{	
			//check if the user uses the layout builder (activeStatus.val() will be active) or the shortcode editor. 
			//if its the shortcode editor then the user is using a nested modal field and we dont want a textarea update
			if(this.activeStatus.val() != 'active') return;
			
		    if(!scope) 
		    {
		        var obj = this;
		        //if this was called without predefined scope iterate over all sections and calculate the columns withs in there, afterwards calculate the column outside
		        $('.avia_layout_builder').find('.avia_layout_section').each(function()
		        {
                    obj.updateTextarea($(this).find('>.avia_inner_shortcode > div > .avia_inner_shortcode'));
		        });
		        
                scope = $('.avia_layout_builder > div > .avia_inner_shortcode');
		    }
		    
			
			var content_fields 	= scope.find('>'+this.datastorage),
				content 		= "",
				sizeCount		= 0,
				currentField, currentContent, currentParent, currentSize,
				sizes			= { 
									'av_one_full'		:   1		, 
									'av_three_fourth'	:   0.75	,
									'av_two_third'		:   0.66	, 
									'av_one_half'		:   0.5		, 
									'av_one_third'		:   0.33	, 
									'av_one_fourth'		:   0.25	, 
									'av_one_fifth'		:	0.2	 
								};
			
			for (var i = 0; i < content_fields.length; i++) 
			{
			    currentField	= $(content_fields[i]);
			    currentParent	= currentField.parents('.avia_layout_column:eq(0)');
				currentContent	= currentField.val();
				
				//if we are checking a column we need to make sure to add/remove the first class
				if(currentParent.length)
				{
					currentSize = currentParent.data('width');
					sizeCount += sizes[currentSize];
					
					if(sizeCount > 1 || i == 0)
					{
						if(!currentParent.is('.avia-first-col'))
						{
							currentParent.addClass('avia-first-col');
							currentContent = currentContent.replace(new RegExp("^\\[" + currentSize), "[" + currentSize + " first");
							currentField.val(currentContent);
						}
						sizeCount = sizes[currentSize];
					}
					else if(currentParent.is('.avia-first-col'))
					{
						currentParent.removeClass('avia-first-col');
						currentContent = currentContent.replace(" first", "");
						currentField.val(currentContent);
					}
				}
				else
				{
					sizeCount = 1;
				}
				
				content += currentContent;
			}
			
			
			var editor = this.tiny_active ? window.tinyMCE.get('content') : undefined;
			
			if(typeof editor != "undefined")
			{	
				clearTimeout(this.update_timout);
				
				this.update_timout = setTimeout(function()
				{
					editor.setContent(window.switchEditors.wpautop(content), {format:'html'}); //<-- slows the whole process considerably
				}
				,500);
			}
			
			this.classic_textarea.val(content);
			this.secureContent.val(content);
		},
		
		// create a snapshot for the undoredo function. timeout it so javascript has enough time to remove animation classes and hover states
		do_history_snapshot: function(timeout)
		{
			var self = this;
			
			if(!timeout) timeout = 150;
			
			setTimeout(function()
			{
				self.canvas.trigger('avia-storage-update');
			}, timeout );
            
		},
		
		
		/**
		* takes some text in shortcode format (eg: [avia_textblock]this is test[/avia_textblock]) and converts it to an editable element on 
		* the AviaBuilder canvas. only executed at page load or when the editor is switched from default wordpress to avia builder
		*/
		convertTextToInterface: function(text)
		{	
            if(this.activeStatus.val() != "active") return;
		
			var obj = this, editor;
		
			if(typeof text == "undefined")
			{
				text = this.secureContent.val(); //testdrive: val() to html()
				if(text.indexOf('[') === -1)
				{
                	text = this.classic_textarea.val(); //testdrive: val() to html()
                	this.secureContent.val(text);
				}
			}
			
			if(this.tiny_active) text = window.switchEditors._wp_Nop(text);
			
			//sends the request. calls the the wp_ajax_avia_ajax_fetch_shortcode php function
			$.ajax({
			type: "POST",
			url: ajaxurl,
			data: 
			{
				action: 'avia_ajax_text_to_interface',
				text: text
			},
			success: function(response)
			{
				$('#content-tmce').trigger('click');
				obj.sendToAdvancedEditor(response);
				//obj.updateTextarea(); //dont update textarea on load, only when elements got edited
				obj.canvas.removeClass('preloading');
				obj.do_history_snapshot();
			}
		});
			
			
		},
		
		/**
		* activates the target mode. highlights all avialable targets like top/bottom insert and columns
		*/
		targetInsertActive: function(response, target_class)
		{
            if(!target_class) 
            { 
            	target_class = 'avia-hover-target avia-select-target-' + $(response).data('dragdrop-level')
            }
			
			$('.avia-hover').removeClass('avia-hover');
			this.canvas.addClass(target_class);
			this.targetInsert = response;
		},
		
		/**
		* deactivates the target mode.
		*/
		targetInsertInactive: function()
		{
			$('.shortcode_button_wrap').find('.avia-active-insert').removeClass('avia-active-insert');
			this.canvas.removeClass('avia-hover-target avia-select-target-1 avia-select-target-2 avia-select-target-3 avia-select-target-4');
			this.targetInsert = "";
			this.only_one_insert = false;
		},
		
		/**
		* ones the target mode is active for NEW items via targetInsertActive, 
		* this function is executed as soon as the user clicks on a valid element for insertion.
		* The new element is then added to the AviaBuilder Canvas
		*/
		doTargetInsert: function(element, obj, insertMethod)
		{
		    var current 	= $(element),
		    	test_insert = typeof obj.targetInsert == 'object' ? obj.targetInsert : $(obj.targetInsert);
		    
		    //stop if we try to insert element with low level into higher level
		    if(!obj.droping_allowed(test_insert, current)) return;
		    

			if(typeof insertMethod == "undefined") insertMethod = "append";
			
			if(insertMethod == "append")
			{
				current.append(obj.targetInsert);
			}
			
			if(insertMethod == "prepend")
			{
				current.prepend(obj.targetInsert);
			}
			
			//if we are modifying an inner object update the inner textarea. inner objects are not jQuery elements and therefore got no length
			if(!element.length)
			{
				obj.updateInnerTextarea(element);
			}


			var section = current.parents('.avia_layout_section:eq(0)');
			if(section.length)
			{
				obj.updateTextarea();
				obj.updateInnerTextarea(false, section);
			}
			
			if(this.only_one_insert != false)
			{
				obj.updateInnerTextarea(false, this.only_one_insert);
				obj.targetInsertInactive();
			}
			
			obj.activate_element_dragging();
			obj.activate_element_dropping();
			obj.updateTextarea();
			obj.do_history_snapshot();
			
		},
		
		/**
		* function that changes the currently active shortcode by pressing the left and right buttons
		*/
		changeTargetingShortcode: function(keycode)
		{
			var direction 	= keycode == 37 ? -1 : 1,
				container	= $('.shortcode_button_wrap:last'),
				buttons		= container.find('.avia-target-insert'),
				active		= buttons.filter('.avia-active-insert'),
				index		= buttons.index(active),
				next		= buttons.filter(':eq('+(index + direction)+')');
				
			if(!next.length)
			{
				next = direction == 1 ? buttons.filter(':first') : buttons.filter(':last');
			}	
			
			next.trigger('click');
			
			if(!next.is(':visible'))
			{
				var tabcontainer = container.find('.avia-tab'),
					current_cont = next.parent(),
					tabIndex	 = tabcontainer.index(current_cont),
					tabButton	 = container.find('.avia-tab-title-container a:eq('+tabIndex+')').trigger('click');
			}
				
			return false;
		},
		
		
		/**
		* function that gets executed if a user clicks the scissor icon and moves an EXISTING element on the AviaBuilder Canvas to another place
		*/
		/*
		targetCutItem: function(element, obj)
		{
			var el = $(element),
				text = el.parents('.avia_sortable_element, .avia_layout_column, .avia_layout_section').filter(':eq(0)'),
				also_update = el.parents('.avia_layout_column:eq(0), .avia_layout_section:eq(0)'),
				target_class = 'avia-select-target';
			
			if(also_update.length > 1) also_update = also_update.filter('.avia_layout_section');
			
			this.only_one_insert = also_update;
			
			if(text.is('.avia_layout_column')) { target_class = 'avia-select-target-no-col';} else 
			if(text.is('.avia_layout_section')){ target_class = 'avia-top-bottom-insert'; }
				
			obj.targetInsertActive(text, target_class);
		},
		*/
		
		/**
		* function that gets executed if a user clicks the clone icon. the current element is cloned and appended after the item that was clicked on
		*/
		cloneElement: function(element, obj)
		{
			var el = $(element),
				parent = el.parents('.avia_sortable_element:eq(0)');
			
			if(!parent.length) parent = el.parents('.avia_layout_column:eq(0)');
			if(!parent.length) parent = el.parents('.avia_layout_section:eq(0)');
			
			//make sure that the elments actual html code matches the value so cloning works properly
			parent.find('textarea').each(function()
			{
				this.innerHTML = this.value;
			});
			
			
			var cloned = parent.clone();
			
			//remove all previous drag/drop classes so we can apply new ones
			cloned.removeClass('ui-draggable ui-droppable').find('.ui-draggable, .ui-droppable').removeClass('ui-draggable ui-droppable');
			cloned.insertAfter(parent);
			
			var wrap = parent.parents('.avia_layout_section, .avia_layout_column');
			
			if(parent.is('.avia_layout_column') || parent.is('.avia_layout_section') || wrap.length)
			{    
			    if(wrap.length) obj.updateTextarea();
			    obj.updateInnerTextarea(parent);
			}	
			
			obj.activate_element_dragging();
			obj.activate_element_dropping();
			obj.updateTextarea();
			obj.do_history_snapshot();
		},
		
		
		/**
		* function that gets executed if an element on the AviaBuilder Canvas has no popup modal but is managed via directly attached
		* form elements (eg: sidebar with dropdown)
		*/
		recalc_shortcode: function(element_container)
		{
			var recalcs		= element_container.find('select.avia-recalc-shortcode'), 
				currentEl	= false,
				values		= [];
				
				for (var i = 0; i < recalcs.length; i++) 
				{
					currentEl = $(recalcs[i]);
					values[currentEl.data("attr")] = currentEl.val();
				}
				
				this.send_to_datastorage(values, element_container);
		},
		
		
		/**
		* function that gets executed once we know how a shortcode should look like and need to insert it into the textarea that holds the shortcode
		* called by either recalc_shortcode or on modal box saving
		*
		* also checks for elements with the data attribute: "data-update_with". if such an attribute happens to have the same name as a shortcode
		* arg or content key the elements html is updated. 
		* if a data-update_template is available instead of just inserting the new value insert the new value into the update template and then add the template
		*
		* also checks for elements with the data attribute: "data-update_class_with". if such an attribute happens to have the same name as a shortcode
		* arg or content key the elements classname is updated
		*/
		send_to_datastorage: function(values, element_container)
		{
			var selector_string = element_container.is('.avia-modal-group-element') ? this.datastorage + ':eq(0)' : '>.avia_inner_shortcode>'+ this.datastorage + ':eq(0)',
				saveTo = element_container.find(selector_string),
				column = element_container.parents('.avia_layout_column:eq(0)'),
				section = element_container.parents('.avia_layout_section:eq(0)'),
				shortcode = element_container.data('shortcodehandler'),
				output = "", tags = {};
			
		  
			//if we got a string value passed insert the string, otherwise calculate the shortcode
			if(typeof values == 'string')
			{
				output = values;
			}
			else
			{
				var return_val = this.update_builder_html(element_container, values);
				
				output = return_val.output;
				tags = return_val.tags;
			}
				
			//if we are working inside a section only update the shortcode open tag
			if(element_container.is('.avia_layout_section'))
			{
			    saveTo.val(saveTo.val().replace(new RegExp("^\\["+shortcode+".*?\\]"), tags.open));
			}
			else //if we are not editing a section update everything
			{
			    saveTo.val(output);
			}
			
			
			if(section.length)
			{
				this.updateInnerTextarea(false, section);
			}
			else if(column.length)
			{
				this.updateInnerTextarea(false, column);
			}
			
			this.updateTextarea();
			
			this.do_history_snapshot();
			element_container.trigger('update');
				
		},
		
		update_builder_html: function(element_container, values)
		{
			var output = "",
				key, 
				subkey,
				new_key;
				
					// filter keys for the "aviaTB" string prefix and re-modify the key that was edited in the php html helper class
					for (key in values)
					{
						if (values.hasOwnProperty(key)) 
						{
							new_key = key.replace(/aviaTB/g,"");
							if(new_key != key)
							{
								values[new_key] = values[key];
								delete values[key];
							}
						}
					}
					
					// replace all single quotes  with "real" single quotes so we dont break the shortcode. not necessary in the content field
					for (key in values)
					{
						if (values.hasOwnProperty(key)) 
						{
							if('content' != key)
							{
								if(typeof values[key] == "string")
								{
									values[key] = values[key].replace(/'(.+?)'/g,'‘$1’').replace(/'/g,'’');
								}
								else if(typeof values[key] == "object")
								{
									for (subkey in values[key])
									{
										values[key][subkey] = values[key][subkey].replace(/'(.+?)'/g,'‘$1’').replace(/'/g,'’');
									}
								}
								
							}
						}
					}

				
					var shortcode		= element_container.data('shortcodehandler'),
						visual_updates	= element_container.find("[data-update_with]"),
						class_updates	= element_container.find("[data-update_class_with]").attr('class',''),
						visual_key 		= "",
						visual_el		= "",
						visual_template	= "",
						update_html		= "",
						replace_val		= "";
						
						if(!element_container.is('.avia-no-visual-updates'))
						{
							//update elements on the AviaBuilder Canvas like text elements to reflect those changes instantly
							visual_updates.each(function()
							{
								visual_el	= $(this);
								visual_key	= visual_el.data('update_with');
								visual_template = visual_el.data('update_template');
								
								if(typeof values[visual_key] === "string" || typeof values[visual_key] === "number")
								{
									replace_val = values[visual_key];
									//apply autop to content
									if(visual_key === "content")
									{
										if(typeof window.switchEditors != 'undefined')
										{
											replace_val = window.switchEditors.wpautop(values[visual_key]);
										}
										else
										{
											//if visual editor is disabled convert newlines to br for the canvas preview
											replace_val = avia_nl2br(values[visual_key]);
										}
									}
									
									//check for a template
									if(visual_template)
									{
										update_html = visual_template.replace("{{"+visual_key+"}}", replace_val);
									}
									else
									{
										update_html = replace_val;
									}
									
									//update all elements
									visual_el.html(update_html);
								}
							});
							
							
							//update element classnames on the AviaBuilder Canvas to reflect visual changes instantly
							class_updates.each(function()
							{
								visual_el	= $(this);
								visual_key	= visual_el.data('update_class_with').split(',');
								
								for (var i = 0; i < visual_key.length; i++) 
								{
									if(typeof values[visual_key[i]] === "string")
									{
										visual_el.get(0).className += ' avia-' + visual_key[i] + '-' + values[visual_key[i]];
									}
								}
							});
						}
						//remove fake argumens that were only used for nicer look of backend
						for (key in values)
						{
							if (values.hasOwnProperty(key) && key.indexOf('_fakeArg') !== -1) 
							{
								delete values[key];
							}
						}
						
						//create the shortcode string out of the arguments and save it to the data storage textarea
						var tags = {}, return_val = {};
						return_val.output = this.createShortcode(values, shortcode, tags);
						return_val.tags = tags;

						return return_val;
		},
		
		
		
		/**
		* function that gets executed by send_to_datastorage and creates the actual shortcode string out of the arguments and content
		*/
		createShortcode: function(values, shortcode, tag)
		{
		
			var key, output = "", attr = "", content = "", i, array_seperator = ",", line_break = "\n";
			if(!tag) tag = {};
			
			//create content var 
			if(typeof values.content != 'undefined')
			{
				//check if the content var is an array of items
				if(typeof values.content == 'object')
				{
					//if its an array check if its an array of sub-shortcodes eg (contact form fields), if so switch the array_seperator to line break
					if( values.content[0].indexOf('[') != -1) array_seperator = line_break;
					
					//trim spaces and line breaks from the array
					for(i = 0; i < values.content.length; i++)
					{
						values.content[i] = $.trim(values.content[i]);
					}
					
					//join the array into a single string
					content =  values.content.join(array_seperator);
				}
				else
				{
					content = values.content ;
				}
				
				content = line_break + content + line_break ;
				delete values.content;
			}
			
			//create attr string
			for (key in values)
			{
				if (values.hasOwnProperty(key)) 
				{
					if(typeof values[key] === 'object') values[key] = values[key].join(',');
			        attr += key + "='" + values[key] + "' ";
			    }
			}
			
			tag.open = "["+shortcode+" "+ $.trim(attr) +"]";
			output = tag.open;
			if(content)
			{
				if($.trim(content) == "") content = "";
				
				tag.close = "[/"+shortcode+"]";				
				output += content + tag.close;
			}
			
			output += line_break + line_break;
			
			return output;
		}
	}
	
	$(document).ready(function () 
	{
    	$.avia_builder = new $.AviaBuilder();
	});
	
	
	
	
})(jQuery);	 



(function($)
{
	"use strict";
	
    $.AviaBuilder.shortcodes = $.AviaBuilder.shortcodes || {};
    
    $.AviaBuilder.shortcodes.fetchShortcodeEditorElement = function(shortcode, insert_target, obj)
	{
		var template = $("#avia-tmpl-"+shortcode);
		
		if(template.length)
		{
			if(insert_target == 'instant_insert')
			{
				obj.sendToAdvancedEditor(template.html());
				obj.updateTextarea();
				obj.do_history_snapshot(0);
			}
			else
			{
				obj.targetInsertActive(template.html());
			}
				
			return;
		}
	}
	
	$.AviaBuilder.shortcodes.deleteItem = function(clicked, obj)
	{
		var $_clicked = $(clicked),
			item      = $_clicked.parents('.avia_sortable_element:eq(0)'), parent = false;
		
		//check if it is a column	
		if(!item.length) 
		{
			item = $_clicked.parents('.avia_layout_column:eq(0)');	
			parent = $_clicked.parents('.avia_layout_section:eq(0)>.avia_inner_shortcode');
			
			//check if it is a section
			if(!item.length)
			{
			    item = $_clicked.parents('.avia_layout_section:eq(0)');	
			    parent = false;
			}
			
		}
		else
		{
			parent = $_clicked.parents('.avia_inner_shortcode:eq(0)');
		}
		
		obj.targetInsertInactive();
		
		item.hide(200, function()
		{ 
			item.remove();
			if(parent && parent.length) obj.updateInnerTextarea(parent);
			
			obj.updateTextarea();
			
			//bugfix for column delete that renders the canvas undropbable for unknown reason
			if(obj.secureContent.val() == "") { obj.activate_element_dropping(obj.canvasParent, "destroy"); }
			
			
			obj.do_history_snapshot();
		});
	}
	
	$.AviaBuilder.shortcodes.deleteModalSubItem = function(clicked, e)
	{
		e.stopImmediatePropagation();
		
		var $_clicked = $(clicked),
			item      = $_clicked.parents('.avia-modal-group-element:eq(0)');
		
		item.slideUp(200, function()
		{
			item.remove();
		});
	}
	
	$.AviaBuilder.shortcodes.appendModalSubItem = function(clicked, e)
	{	
		e.preventDefault();
				
		var $_clicked	= $(clicked),
			wrap		= $_clicked.parents('.avia-modal-group-wrapper:eq(0)'),
			parent		= wrap.find('.avia-modal-group'),
			template	= wrap.find('.avia-tmpl-modal-element'),
			newTemplate = $(template.html()).appendTo(parent).css({display:"none"});
		
			newTemplate.slideDown(200);
	}
	
	
	$.AviaBuilder.shortcodes.changeSize = function(clicked, obj)
	{
		var item		= $(clicked),
			container	= item.parents('.avia_layout_column:eq(0)'),
			section     = container.parents('.avia_layout_section:eq(0)'),
			currentSize	= container.data('width'),
			nextSize	= [],
			direction	= item.is('.avia-bigger') ? 1 : -1,
			sizeString	= container.find('.avia-col-size'),
			dataStorage	= container.find('> .avia_inner_shortcode > '+obj.datastorage),
			dataString	= dataStorage.val(),
			sizes		= [ 
							['av_one_full'	,		'1/1'], 
							['av_three_fourth',		'3/4'],
							['av_two_third',		'2/3'], 
							['av_one_half',			'1/2'], 
							['av_one_third',		'1/3'], 
							['av_one_fourth',		'1/4'], 
							['av_one_fifth',		'1/5'] 
						];
		
		for (var i = 0; i < sizes.length; i++) 
		{
		    if(sizes[i][0] == currentSize)
		    {
		    	nextSize =  sizes[i - direction];
		    }
		}
		
		if(typeof nextSize != 'undefined')
		{
			dataString = dataString.replace(new RegExp("^\\[" + currentSize, 'g'), "[" + nextSize[0]);
			dataString = dataString.replace(new RegExp( currentSize + "\\]", 'g'), nextSize[0] + "]");
			
			dataStorage.val(dataString);
			container.removeClass(currentSize).addClass(nextSize[0]);
			container.attr('data-width',nextSize[0]).data('width',nextSize[0]); //make sure to also set the data attr so html() functions fetch the correct value
			sizeString.text(nextSize[1]);
			
			obj.updateTextarea();
            if(section.length){ obj.updateInnerTextarea(false, section); obj.updateTextarea(); } 
            obj.do_history_snapshot(0);
		}
		
	}
	
})(jQuery);	 



