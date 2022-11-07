$("document").ready(function () {


    $('.caraousel--single-js').slick({
        autoplay: true,
        arrows: false,
        dots: true,
        fade: true,
        rtl: (langLbl.layoutDirection == 'rtl') ? true : false,
        responsive: [{
                breakpoint: 767,
                settings: {
                    arrows: false,
                    dots: true
                }
            }]
    });

    $("input[name='language']").autocomplete({
        'autoFocus': true,
        'minLength': 0,
        'source': $.map(LANGUAGES, function (item) {
            return {
                value: item['name'],
                id: item['id'],
                slug: item['slug'],
            };
        }),
        'select': function (event, ui) {
            event.preventDefault();
            $('input[name=\'language\']').val(ui.item.label);
            $('input[name=\'teachLangId\']').val(ui.item.id);
            $('input[name=\'teachLangSlug\']').val(ui.item.slug);
            $('#homeSearchForm').attr('action', fcom.makeUrl('Teachers', 'languages', [ui.item.slug]));
            $('#homeSearchForm').submit();
        }
    }).bind('focus', function () {
        $(this).autocomplete("search");
    });

    /* Common Carousel */
    var _carousel = $('.js-carousel');
    _carousel.each(function () {

        var _this = $(this),
                _slidesToShow = (_this.data("slides")).toString().split(',');

        /* slick common carousel init */
        _this.slick({
            slidesToShow: parseInt(_slidesToShow.length > 0 ? _slidesToShow[0] : "3"),
            slidesToScroll: 1,
            rtl: (langLbl.layoutDirection == 'rtl') ? true : false,
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
    /* End of Common Carousel */

    $('.vert-carousel').slick({
        slidesToShow: 3,
        rtl: (langLbl.layoutDirection == 'rtl') ? true : false,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 2000,
        arrow: true,
        vertical: true
    });

    $('.slideshow-js').slick({
        dots: true,
        infinite: true,
        speed: 500,
        fade: true,
        cssEase: 'linear',
        arrows: false,
        autoplay: true,
        rtl: (langLbl.layoutDirection == 'rtl') ? true : false,
        responsive: [
            {
                breakpoint: 576,
                settings: {
                    slidesToShow: 1,
                    arrows: false,
                    dots: true
                }
            }
        ]
    });

    $('.slider-onethird-js').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        infinite: false,
        rtl: (langLbl.layoutDirection == 'rtl') ? true : false,
        arrows: true,
        adaptiveHeight: true,
        dots: false,
        prevArrow: '<button class="slick-prev cursor-hide" aria-label="Previous" type="button">Previous</button>',
        nextArrow: '<button class="slick-next cursor-hide" aria-label="Next" type="button">Next</button>',
        responsive: [
            {
                breakpoint: 1199,
                settings: {
                    slidesToShow: 2,
                    arrows: false,
                    dots: true
                }
            },
            {
                breakpoint: 1023,
                settings: {
                    slidesToShow: 2,
                    arrows: false,
                    dots: true
                }
            },
            {
                breakpoint: 767,
                settings: {
                    slidesToShow: 2,
                    arrows: false,
                    dots: true
                }
            },

            {
                breakpoint: 576,
                settings: {
                    slidesToShow: 1,
                    arrows: false,
                    dots: true
                }
            }

        ]
    });

    $('.step-slider-js').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        rtl: (langLbl.layoutDirection == 'rtl') ? true : false,
        arrows: false,
        dots: true,
        asNavFor: '.slider-tabs--js'
    });

    $('.slider-tabs--js').slick({
        slidesToShow: 3,
        slidesToScroll: 1,
        asNavFor: '.step-slider-js',
        rtl: (langLbl.layoutDirection == 'rtl') ? true : false,
        dots: true,
        centerMode: true,
        focusOnSelect: true
    });

    $('.slider-quote-js').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: true,
        rtl: (langLbl.layoutDirection == 'rtl') ? true : false,
        autoplay: false,
        adaptiveHeight: true,
        dots: false,
        prevArrow: '<button class="slick-prev cursor-hide" aria-label="Previous" type="button">Previous</button>',
        nextArrow: '<button class="slick-next cursor-hide" aria-label="Next" type="button">Next</button>',
        responsive: [
            {
                breakpoint: 1199,
                settings: {
                    fade: false,
                    infinite: true,

                    centerMode: true,
                    centerPadding: '15%',

                    arrows: false,
                    dots: true
                }
            },

            {
                breakpoint: 1023,
                settings: {
                    fade: false,
                    infinite: true,

                    centerMode: true,
                    centerPadding: '15%',

                    arrows: false,
                    dots: true
                }
            },
            {
                breakpoint: 767,
                settings: {
                    fade: false,
                    infinite: true,

                    centerMode: true,
                    centerPadding: '15%',

                    arrows: false,
                    dots: true

                }
            },
            {
                breakpoint: 576,
                settings: {
                    fade: false,
                    infinite: true,

                    centerMode: true,
                    centerPadding: '5%',

                    arrows: false,
                    dots: true

                }
            }
        ]
    });

    $('.slider-onehalf-js').slick({
        slidesToShow: 2,
        slidesToScroll: 1,
        infinite: false,
        rtl: (langLbl.layoutDirection == 'rtl') ? true : false,
        arrows: true,
        adaptiveHeight: true,
        dots: false,

        responsive: [

            {
                breakpoint: 1199,
                settings: {
                    slidesToShow: 2,
                    dots: true,
                    arrows: false,
                }
            },
            {
                breakpoint: 1023,
                settings: {
                    slidesToShow: 1,
                    dots: true,
                    arrows: false,
                }
            },
            {
                breakpoint: 767,
                settings: {
                    slidesToShow: 1,
                    dots: true,
                    arrows: false,
                }
            },
        ]
    });

    $('.countdowntimer').each(function (i) {
        $(this).countdowntimer({
            startDate: $(this).data('starttime'),
            dateAndTime: $(this).data('endtime'),
            size: "sm",
        });
    });



    var slideWrapper = $(".main-slider"),
    iframes = slideWrapper.find('.embed-player'),
    lazyImages = slideWrapper.find('.slide-image'),
    lazyCounter = 0;

// POST commands to YouTube or Vimeo API
function postMessageToPlayer(player, command){
  if (player == null || command == null) return;
  player.contentWindow.postMessage(JSON.stringify(command), "*");
}

// When the slide is changing
function playPauseVideo(slick, control){
  var currentSlide, slideType, startTime, player, video;

  currentSlide = slick.find(".slick-current");
  slideType = currentSlide.attr("class").split(" ")[1];
  player = currentSlide.find("iframe").get(0);
  startTime = currentSlide.data("video-start");

  if (slideType === "youtube") {
    switch (control) {
      case "play":
        postMessageToPlayer(player, {
          "event": "command",
          "func": "mute"
        });
        postMessageToPlayer(player, {
          "event": "command",
          "func": "playVideo"
        });
        break;
      case "pause":
        postMessageToPlayer(player, {
          "event": "command",
          "func": "pauseVideo"
        });
        break;
    }
  } 
}

// Resize player
function resizePlayer(iframes, ratio) {
  if (!iframes[0]) return;
  var win = $(".main-slider"),
      width = win.width(),
      playerWidth,
      height = win.height(),
      playerHeight,
      ratio = ratio || 16/9;

  iframes.each(function(){
    var current = $(this);
    if (width / ratio < height) {
      playerWidth = Math.ceil(height * ratio);
      current.width(playerWidth).height(height).css({
        left: (width - playerWidth) / 2,
         top: 0
        });
    } else {
      playerHeight = Math.ceil(width / ratio);
      current.width(width).height(playerHeight).css({
        left: 0,
        top: (height - playerHeight) / 2
      });
    }
  });
}

// DOM Ready
$(function() {
  // Initialize
  slideWrapper.on("init", function(slick){
    slick = $(slick.currentTarget);
    setTimeout(function(){
      playPauseVideo(slick,"play");
    }, 1000);
    resizePlayer(iframes, 16/9);
  });
  slideWrapper.on("beforeChange", function(event, slick) {
    slick = $(slick.$slider);
    playPauseVideo(slick,"pause");
  });
  slideWrapper.on("afterChange", function(event, slick) {
    slick = $(slick.$slider);
    playPauseVideo(slick,"play");
  });
  slideWrapper.on("lazyLoaded", function(event, slick, image, imageSource) {
    lazyCounter++;
    if (lazyCounter === lazyImages.length){
      lazyImages.addClass('show');
      // slideWrapper.slick("slickPlay");
    }
  });

  //start the slider
  slideWrapper.slick({
    // fade:true,
    autoplaySpeed:4000,
    lazyLoad:"progressive",
    speed:600,
    arrows:false,
    dots:true,
    cssEase:"cubic-bezier(0.87, 0.03, 0.41, 0.9)"
  });
});

// Resize event
$(window).on("resize.slickVideoPlayer", function(){  
  resizePlayer(iframes, 16/9);
});

var dv = $("#listingContainer");
$(dv).html(fcom.getLoader());
fcom.ajax(fcom.makeUrl('Home', 'search'), [], function (t) {
    $(dv).html(t);
});

});
