define(["jquery",'fancybox'], function($) 
{
  	"use strict";
	$(document).ready(function($)
	{
		
		/*function headerSearch() {
			$('.search-tiggle .action').click( function(){
				$(this).toggleClass('open');
				$('.nav-sections').toggleClass('open');
				$('.nav-sections .navigation').toggleClass('open');
				$('.block-search .block-content').toggle("fade");
			});
		}
		headerSearch();*/
		//$('#tips-prescription').fancybox({});
		$('#tips-prescription').fancybox({
	        infobar: false,
	        buttons: false,
	        afterLoad: function() {
	            jQuery('.fancybox-content').append('<button data-fancybox-close="" class="fancybox-close-small"></button>');
	        }
	    });
	});
});