$(function () {
    var load = false;
    var offset = $('.box:last').offset();
    var loader = $('div#loadmoreajaxloader');

    $(window).scroll(function() {
        //Asynchron announcements loading
        if ((offset.top-$(window).height() <= $(window).scrollTop()) && load === false) {
            load = true;
            loader.show();

            var page = parseInt(loader.attr('data-page')) + 1;

            $.ajax({
                type: "GET",
                url: loader.attr('data-url'),
                data: {'page' : page},
                success: function(msg){
                    if (msg.length > 0) {
                        $('div#loadmoreajaxloader').hide();
                        $('div#job-container').append(msg);
                        $('#loadmoreajaxloader').attr('data-page', page.toString());
                        offset = $('.box:last').offset();
                        load = false;
                    } else {
                        $('div#loadmoreajaxloader').html(loader.attr('data-empty'));
                    }
                }
            });
        }

        //Back to top button
        if ($(this).scrollTop() > 220) {
            $('.back-to-top').fadeIn(500);
        } else {
            $('.back-to-top').fadeOut(500);
        }

    });

    $('.back-to-top').on('click', function(event) {
        event.preventDefault();
        $('html,body').animate({scrollTop: 0});
        event.stopPropagation();
    });

    $('.filter a[data-relative]').click(function(e){
        e.preventDefault();
        var $target = $('#'+$(this).data('relative'));
        $target.val($(this).data('relative-value'));
        $target.closest('form').submit();
    });
});
