/* 
	this prevents dom flickering for elements hidden with js, needs to be outside of dom.ready event.also adds several extra classes for better browser support 
	this is a separate file that needs to be loaded at the top of the page. other js functions are loaded before the closing body tag to make the site render faster
*/
"use strict"

document.documentElement.className += ' js_active ';
document.documentElement.className += 'ontouchstart' in document.documentElement ? ' avia_mobile ' : ' avia_desktop ';
(function(){
    var prefix = ['-webkit-','-o-','-moz-', '-ms-', ""];
    for (var i in prefix)
    { 
    	if(prefix[i]+'transform' in document.documentElement.style) document.documentElement.className += " avia_transform "; 
	}
})();