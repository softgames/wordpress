jQuery(document).ready(function($) {

	cart_improvement_functions();
	cart_dropdown_improvement();
	track_ajax_add_to_cart();
	
	if(jQuery.fn.avia_sc_slider)
	{								
		jQuery(".shop_slider_yes ul").avia_sc_slider({appendControlls:false, group:true, slide:'.product', arrowControll: true, autorotationInterval:'parent'});
	}
	
	
	product_add_to_cart_click();
	
	jQuery(".quantity input[type=number]").each(function()
	{
		var number = $(this),
			newNum = jQuery(jQuery('<div />').append(number.clone(true)).html().replace('number','text')).insertAfter(number);
			number.remove();
	});
	
	
	setTimeout(first_load_amount, 10);
	$('body').bind('added_to_cart', update_cart_dropdown);
		
	
		
});


//updates the shopping cart in the sidebar, hooks into the added_to_cart event whcih is triggered by woocommerce
function update_cart_dropdown(event)
{
	var menu_cart 		= jQuery('.cart_dropdown'),
		empty 			= menu_cart.find('.empty'),
		msg_success		= menu_cart.data('success'),
		product 		= jQuery.extend({name:"Product", price:"", image:""}, avia_clicked_product);
		
		if(!empty.length)
		{
			menu_cart.addClass('visible_cart');
		}
		
		if(typeof event != 'undefined')
		{
			var header		 =  jQuery('.fixed_header #header_main .cart_dropdown_first'),
				oldTemplates = jQuery('.added_to_cart_notification').trigger('avia_hide'),
				template 	 = jQuery("<div class='added_to_cart_notification'><span class='avia-arrow'></span><div class='added-product-text'><strong>\"" + product.name +"\"</strong> "+ msg_success+ "</div> " + product.image +"</div>").css( 'opacity', 0 );
			
			if(!header.length) header = 'body';
				
			template.bind('mouseenter avia_hide', function()
			{
				template.animate({opacity:0, top: parseInt(template.css('top'),10) + 15 }, function()
				{
					template.remove();
				});
				
			}).appendTo(header).animate({opacity:1},500);
			
			setTimeout(function(){ template.trigger('avia_hide'); }, 2500);
		}
}


var avia_clicked_product = {};
function track_ajax_add_to_cart()
{
	jQuery('body').on('click','.add_to_cart_button', function()
	{	
		var productContainer = jQuery(this).parents('.product').eq(0), product = {};
			product.name	 = productContainer.find('.inner_product_header h3').text();
			product.image	 = productContainer.find('.thumbnail_container img');
			product.price	 = productContainer.find('.price .amount').last().text();
			
			if(product.image.length) product.image = "<img class='added-product-image' src='" + product.image.get(0).src + "' title='' alt='' />";
			
			avia_clicked_product = product;
	});
}


//function that pre fills the amount value of the cart
function first_load_amount()
{
	var counter = 0, 
		limit = 5, 
		ms = 500,
		check = function()
		{
			var new_total = jQuery('.cart_dropdown .dropdown_widget_cart:eq(0) .total .amount');
			
			if(new_total.length)
			{
				update_cart_dropdown();
			}
			else
			{
				counter++;
				if(counter < limit)
				{
					setTimeout(check, ms);
				}
			}
		};
		
		check();
}





function product_add_to_cart_click()
{
	var jbody = jQuery('body');

	jbody.on('click', '.add_to_cart_button', function()
	{
		var product = jQuery(this).parents('.product:eq(0)').addClass('adding-to-cart-loading').removeClass('added-to-cart-check');
	})
	
	jbody.bind('added_to_cart', function()
	{
		jQuery('.adding-to-cart-loading').removeClass('adding-to-cart-loading').addClass('added-to-cart-check');
	});
	
}



// little fixes and modifications to the dom
function cart_improvement_functions()
{
	//single products are added via ajax //doesnt work currently
	//jQuery('.summary .cart .button[type=submit]').addClass('add_to_cart_button product_type_simple');
	
	//downloadable products are now added via ajax as well
	jQuery('.product_type_downloadable, .product_type_virtual').addClass('product_type_simple');
	
	//clicking tabs dont activate smoothscrooling
	jQuery('.woocommerce-tabs .tabs a').addClass('no-scroll');
	
	//connect thumbnails on single product page via lightbox
	jQuery('.single-product-main-image>.images a').attr('rel','product_images[grouped]'); 
	
	

}






//small function that improves shoping cart hover behaviour in the menu
function cart_dropdown_improvement()
{
	var dropdown = jQuery('.cart_dropdown'), subelement = dropdown.find('.dropdown_widget').css({display:'none', opacity:0});
	
	dropdown.hover(
	function(){ subelement.css({display:'block'}).stop().animate({opacity:1}); },
	function(){ subelement.stop().animate({opacity:0}, function(){ subelement.css({display:'none'}); }); }
	);
}



