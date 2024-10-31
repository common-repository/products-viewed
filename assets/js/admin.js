(function($) {
    jQuery(document).ready(function($) { 
        $(window).load(function() {
            $('.lbk-lightbox-wrapper').click(function(e){   
                $('#lbk-fc-lightbox').toggleClass('active');
            });
        });

        $('#lbk-fc-lightbox').click(function(e) 
        {
            var container = $("#lbk-fc-lightbox .lbk-lightbox");

            // if the target of the click isn't the container nor a descendant of the container
            if (!container.is(e.target) && container.has(e.target).length === 0) {
                $('#lbk-fc-lightbox').removeClass('active');
            }
        });
    })
})(jQuery);