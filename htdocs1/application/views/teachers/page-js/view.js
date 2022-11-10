function isUserLogged() {
    var isUserLogged = 0;
    $.ajax({
        url: fcom.makeUrl('GuestUser', 'checkAjaxUserLoggedIn'),
        async: false,
        dataType: 'json',
    }).done(function (ans) {
        isUserLogged = parseInt(ans.isUserLogged);
    });
    return isUserLogged;
}


function getSortedReviews(elm) {
    if ($(elm).length) {
        var sortBy = $(elm).data('sort');
        if (sortBy) {
            $(elm).parent().siblings().removeClass('is-active');
            $(elm).parent().addClass('is-active');
        }
    }
    reviews(document.frmReviewSearch);
}
function contactSubmit(frm) {
    if (!$(frm).validate()) return;
    var data = fcom.frmData(frm);
    fcom.updateWithAjax(fcom.makeUrl('teachers', 'contactSubmit'), data, function (t) {
        if (t.redirectUrl) {
            window.location = window.location;
        }
    });
}

$("document").ready(function () {

    loadOneThirdSlick();

    $('.toggle-dropdown__link-js').each(function () {
        $(this).click(function () {
            $(this).parent('.toggle-dropdown').toggleClass("is-active");
        });
    })

    $('html').click(function () {
        if ($('.toggle-dropdown').hasClass('is-active')) {
            $('.toggle-dropdown').removeClass('is-active');
        }
    });

    $('.toggle-dropdown').click(function (e) {
        e.stopPropagation();
    });

    $('.tab-a').click(function () {
        $(".tab").removeClass('tab-active');
        $(".tab[data-id='" + $(this).attr('data-id') + "']").addClass("tab-active");
        $(".tab-a").parent('li').removeClass('is-active');
        $(this).parent().addClass('is-active');
    });

    /* FUNCTION FOR LEFT COLLAPSEABLE LINKS */
    if ($(window).width() < 767) {
        $('.box__head-trigger-js').click(function () {
            if ($(this).hasClass('is-active')) {
                $(this).removeClass('is-active');
                $(this).siblings('.box__body-target-js').slideUp();
                return false;
            }

            $('.box__head-trigger-js').removeClass('is-active');
            $(this).addClass("is-active");
            $('.box__body-target-js').slideUp();
            $(this).siblings('.box__body-target-js').slideDown();
        });
    }

    reviews(document.frmReviewSearch);
    if ($(window).width() > 1199) {
        $('.scrollbar-js').enscroll({
            verticalTrackClass: 'scrollbar-track',
            verticalHandleClass: 'scrollbar-handle'
        });
    }


    
    $(document).on('change', '#teachLang', function () {
        $(".slider--onethird").hide();
        $('div[data-lang-id="' + $(this).val() + '"]').show();
        $($('div[data-lang-id="' + $(this).val() + '"]')).slick('setPosition');
    });

    $('.countdowntimer').each(function (i) {
        $(this).countdowntimer({
            startDate: $(this).data('starttime'),
            dateAndTime: $(this).data('endtime'),
            size: "sm",
        });
    });

    $('select[name="orderBy"]').change(function () {
        var frm = document.frmReviewSearch;
        $(frm.page).val(1);
        var dv = '#itemRatings';
        $(dv).html('');
        reviews(document.frmReviewSearch);
    })

});

function viewCalendar(teacherId, languageId, action = '') {
    var dv = $('#availbility');
    if (action == 'free_trial') {
        if (isUserLogged() == 0) {
            $.loader.hide();
            logInFormPopUp();
            return false;
        }
    }
    fcom.ajax(fcom.makeUrl('Teachers', 'viewCalendar', [teacherId, languageId]), 'action=' + action, function (t) {
        if (action == 'free_trial') {
            $.facebox(t, 'facebox-large');
            $('body').addClass('calendar-facebox');
        } else {
            $(dv).html(t);
        }
    });
}

function searchQualifications(user_id) {
    var dv = $('#qualificationsList');
    $(dv).html(fcom.getLoader());

    var data = 'user_id=' + user_id;
    fcom.ajax(fcom.makeUrl('Teachers', 'qualificationList'), data, function (ans) {
        $(dv).html(ans);
    });
}

function getReviewDiv(data) {
    return `
	<div class="row">
	<div class="avatar avatar-md" data-title="{data.fChar}">
	<h6>{data.lessonLanguage}<span>' + val.lessonCount + '</span></h6>
	</div>
	</div>
	`;
}

reviews = function (frm) {
    var html = '';
    var dv = '#itemRatings';
    var data = fcom.frmData(frm);
    data += '&OrderBy=' + $('select[name="orderBy"]').val();
    fcom.updateWithAjax(fcom.makeUrl('Teachers', 'getTeacherReviews'), data, function (ans) {
        if (!ans.records) {
            return;
        }
        $.each(ans.records, function (key, val) {
            // html+= getReviewDiv(val);
            html += '<div class="row">';
            html += '<div class="col-xl-4 col-lg-4 col-sm-4">';
            html += '<div class="review-profile">';
            html += '<div class="avatar avatar-md" data-title="' + val.fChar + '">';
            if (val.img) {
                html += '<img src="' + val.img + '" alt="">';
            }
            html += '</div>';
            html += '<div class="user-info">';
            html += '<b>' + val.lname + '</b>';
            html += '<p>' + val.tlreview_posted_on + '</p>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '<div class="col-xl-8 col-lg-8 col-sm-8">';
            html += '<div class="review-content">';
            html += '<div class="review-content__head">';
            html += '<h6>' + val.lessonLanguage + '<span>' + val.lessonCount + '</span></h6>';
            html += '<div class="info-wrapper">';
            html += '<div class="info-tag ratings">';
            html += '<svg class="icon icon--rating"><use xlink:href="' + val.iconSrc + '"></use></svg>';
            html += '<span class="value">' + val.prod_rating + '</span>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '<div class="review-content__body">';
            html += '<p>' + val.tlreview_description + '</p>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';

        });
        $("#recordToDisplay").html(ans.displayRecords);
        $(dv).append(html);
        $('.show-more').html(ans.loadMoreBtnHtml);
    });
};

goToLoadMoreReviews = function (page) {
    if (typeof page == undefined || page == null) {
        page = 1;
    }
    var frm = document.frmReviewSearch;
    $(frm.page).val(page);
    reviews(frm);
};

loadOneThirdSlick = function () {
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
};