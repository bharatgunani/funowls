require(['jquery'], function ($) {

    function moveCaseBlock() 
    {
    	/*if ($('.case-block').length == 0) {
            return false;
        }
        if ($('.related-products').length == 0) {
            return false;
        }*/
     	if ($(window).width() < 768) {
		   jQuery('.related-products').insertBefore('.shipping-section');
		}
		else {
		   jQuery('.related-products').insertAfter('.case-block');
		}
	    if ($(window).width() < 768) {
		   jQuery('.case-block').insertAfter('#accordion');
		}
		else {
		   jQuery('.case-block').insertAfter('.product_description_1');
		}   
    }
    window.addEventListener('resize', function(event) {
        moveCaseBlock();
    }, true);
    $(document).ready(function(){
        moveCaseBlock();
    });
});