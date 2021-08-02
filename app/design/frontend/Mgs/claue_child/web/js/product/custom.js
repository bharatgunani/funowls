require(['jquery'], function ($) {

    function moveCaseBlock() {

        if ($('.case-block').length == 0) {
            return false;
        }

        if (document.body.clientWidth < 768) {

            jQuery('.case-block').insertAfter('#accordion');

        } else if (document.body.clientWidth > 767) {

            jQuery('.case-block').insertAfter('.product_description');
        }
    }
    window.addEventListener('resize', function(event) {
        moveCaseBlock();
    }, true);
    $(document).ready(function(){
        moveCaseBlock();
    });
});