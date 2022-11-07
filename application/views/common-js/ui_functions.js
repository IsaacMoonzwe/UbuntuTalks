/* FOR NAV TOGGLES 

/* FOR LANGUAGE/CURRENCY
$('.nav__item-settings-js').click(function () {
    $(this).toggleClass("is-active");
    $('html').toggleClass("show-setting-js");
});
*/

/* FOR STICKY HEADER
if ($(window).width() > 767) {
    $(window).scroll(function () {
        body_height = $(".body").position();
        scroll_position = $(window).scrollTop();
        if (body_height.top < scroll_position)
            $(".header").addClass("is-fixed");
        else
            $(".header").removeClass("is-fixed");
    });
}
*/

/* FOR HEADER TOGGLES */
if ($(window).width() < 1200) {
    $('.nav__dropdown-trigger-js').click(function () {
        $('html').toggleClass("show-dropdown-js");

        if ($(this).hasClass('is-active')) {
            $(this).removeClass('is-active');
            $(this).siblings('.nav__dropdown-target-js').slideUp();
            return false;
        }
        $('.nav__dropdown-trigger-js').removeClass('is-active');
        $(this).addClass("is-active");
        $('.nav__dropdown-target-js').slideUp();
        $(this).siblings('.nav__dropdown-target-js').slideDown();
    });
}


/* FOR COMMON DROPDOWN */
$('.nav__dropdown-js').each(function () {
    $(this).click(function () {
        $(this).parent('.nav__item').toggleClass("is-active");
        $("html").toggleClass("toggled-user");
        return false;
    });
})
$('html').click(function () {
    if ($('.nav__item').hasClass('is-active')) {
        $('.nav__item').removeClass('is-active');
    }
});
$('.nav__item').click(function (e) {
    e.stopPropagation();
});

/* FOR MOBILE CANVAS MENU */
$(".nav__toggle-js").click(function () {
    $("html").toggleClass("show--mobile-nav");
    return false;
});


/* FOR MOBILE DROPDOWN MENU
$('.nav__dropdown-trigger').click(function () {
    if ($(this).hasClass('is-active')) {
        $(this).removeClass('is-active');
        $(this).next('.nav__dropdown-target-js').find('.nav__dropdown-trigger').removeClass('is-active');
        $(this).next('.nav__dropdown-target-js').slideUp();
        $(this).next('.nav__dropdown-target-js').find('.nav__dropdown-target-js').slideUp();
        return false;
    }
    $('.nav__dropdown-trigger').removeClass('is-active');
    $(this).addClass("is-active");
    $(this).parents('ul').each(function () {
        $(this).siblings('span').addClass('is-active');
    });
    $(this).closest('ul').find('li .nav__dropdown-target-js').slideUp();
    $(this).next('.nav__dropdown-target-js').slideDown();
});

*/

/* FOR DESKTOP NAVIGATION */
if ($(window).width() > 1200) {
    var elBody = $("html");
    $('.nav--primary > ul > li.nav__has-child').mouseenter(function () {
        $(this).toggleClass("is-active");
        elBody.toggleClass("show--main-nav");
        return false;
    }).mouseleave(function () {
        $(this).toggleClass("is-active");
        elBody.toggleClass("show--main-nav");
        return false;
    });
}



/* FUNCTION FOR COMMON DROPDOWN */
jQuery(document).ready(function (e) {
    function t(t) {
        e(t).bind("click", function (t) {
            t.preventDefault();
            e(this).parent().fadeOut()
        })
    }
    e(".toggle__trigger-js").click(function () {
        var t = e(this).parents(".toggle-group").children(".toggle__target-js").is(":hidden");
        e(".toggle-group .toggle__target-js").hide();
        e(".toggle-group .toggle__trigger-js").removeClass("is-active");
        if (t) {
            e(this).parents(".toggle-group").children(".toggle__target-js").toggle().parents(".toggle-group").children(".toggle__trigger-js").addClass("is-active")
        }

    });
    e(document).bind("click", function (t) {
        var n = e(t.target);
        if (!n.parents().hasClass("toggle-group")) e(".toggle-group .toggle__target-js").hide();
    });
    e(document).bind("click", function (t) {
        var n = e(t.target);
        if (!n.parents().hasClass("toggle-group")) e(".toggle-group .toggle__trigger-js").removeClass("is-active");
    })
});


/* FOR BACK TO TOP
$(function () {
    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $('.gototop').addClass("isvisible");
        } else {
            $('.gototop').removeClass("isvisible");
        }
    });

    // scroll body to 0px on click
    $('.gototop').click(function () {
        $('body,html').animate({
            scrollTop: 0
        }, 800);
        return false;
    });
});
*/

var _carousel = $('.js-carousel');
_carousel.each(function () {

    var _this = $(this),
        _slidesToShow = (_this.data("slides")).toString().split(',');

    _this.slick({
        slidesToShow: parseInt(_slidesToShow.length > 0 ? _slidesToShow[0] : "3"),
        slidesToScroll: 1,
        arrows: _this.data("arrows"),
        dots: _this.data("dots"),
        infinite: true,
        autoplay: true,
        pauseOnHover: true,
        responsive: [
            {
                breakpoint: 1200,
                settings: {
                    slidesToShow: parseInt(parseInt(_slidesToShow.length > 1 ? _slidesToShow[1] : "2"))
                }
                           },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: parseInt(parseInt(_slidesToShow.length > 2 ? _slidesToShow[2] : "1"))
                }
                           },
            {
                breakpoint: 576,
                settings: {
                    slidesToShow: parseInt(parseInt(_slidesToShow.length > 3 ? _slidesToShow[3] : "1"))
                }
                           }
                         ]
    });

});

$('.vert-carousel').slick({
    slidesToShow: 3,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 2000,
    arrow: true,
    vertical: true
});

$.loader = {
    selector: '.loading-wrapper',
    show: function() {
        $(this.selector).show();
    },
    hide: function() {
        $(this.selector).hide();
    }
};