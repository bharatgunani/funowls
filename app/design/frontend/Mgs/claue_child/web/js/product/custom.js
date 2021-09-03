require(['jquery'], function ($) {

    function moveCaseBlock() {

        if ($('.case-block').length == 0) {
            return false;
        }
        if ($('.related-products').length == 0) {
            return false;
        }
        if (document.body.clientWidth < 768) 
        {

            jQuery('.related-products').insertBefore('.shipping-section');

        } else if (document.body.clientWidth > 767) {

            jQuery('.related-products').insertAfter('.case-block');
        }
        if (document.body.clientWidth < 768) 
        {

            jQuery('.case-block').insertAfter('#accordion');

        } else if (document.body.clientWidth > 767) {

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