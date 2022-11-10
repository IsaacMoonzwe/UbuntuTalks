var searchArr = [];
$("document").ready(function () {
    $(document).on('click', '.btn--filters-js', function () {
        $(this).toggleClass("is-active");
        $('html').toggleClass("show-filters-js");
    });


    $('.close--filters-js').click(function () {
        $(this).removeClass("is-active");
        $('html').removeClass("show-filters-js");
    });

    $('input[name="teach_language_name"]').keyup(function () {
        var text = $(this).val();
        $('.select-teach-lang-js').parent().hide();
        $('.select-teach-lang-js:contains("' + text + '")').parent().show();
    });

    var frm = document.frmSrch;

    search(frm);

    $("input[name='filterSpokenLanguage[]']").change(function () {
        var id = $(this).closest("label").attr('id');
        if ($(this).is(":checked")) {
            addFilter(id, this);
        } else {
            removeFilter(id, this);
        }
        search(frm);
    });

    $(document).on('click', '.select-teach-lang-js', function () {
        var langName = $(this).html();
        var langId = $(this).attr('teachLangId');
        $('input[name=\'teachLangId\']').val(langId);
        $("input[name='teach_language_name']").val(langName);
        $('.filter-trigger-js').removeClass('is-active');
        $('.filter-target-js').slideUp();
        $('#frm_fat_id_frmSrch').submit();
        $('.language_keyword').parent("li").remove();
        $('#searched-filters').find('ul').append("<li class='filter-li-js'><a href='javascript:void(0);' class= 'language_keyword tag__clickable' onclick='removeFilterCustom(\"language_keyword\",this)' >" + langLbl.language + " : " + langName + "</a></li>");
        showAppliedFilterSection();

    });

    $("input[name='filterPreferences[]']").change(function () {
        var id = $(this).closest("label").attr('id');
        if ($(this).is(":checked")) {
            addFilter(id, this);
        } else {
            removeFilter(id, this);
        }
        search(frm);
    });

    $("input[name='filterWeekDays[]']").change(function () {
        var id = $(this).closest("label").attr('id');
        if ($(this).is(":checked")) {
            addFilter(id, this);
        } else {
            removeFilter(id, this);
        }
        search(frm);
    });

    $('input[name=\'keyword\']').change(function (e) {
        if (!$(document.frmSrch).validate()) {
            e.preventDefault();
            return;
        }

    });
    $("#keyword").keyup(function (event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            let keywordValue = $("#keyword").val();
            $('.userKeyword').parent().remove();
            if (keywordValue != '') {
                $('#searched-filters').find('ul').append("<li class='filter-li-js'><a href='javascript:void(0);' class='userKeyword tag__clickable' onclick='removeFilterUser(\"userKeyword\",this)' >" + langLbl.userFilterLabel + " : " + keywordValue + "</a></li>");
                showAppliedFilterSection();
            } else {
                hideAppliedFilterSection();
            }
            search(frm);
        }
    });

    $('#btnTeacherSrchSubmit').click(function () {

        let keywordValue = $("#keyword").val();
        $('.userKeyword').parent().remove();
        if (keywordValue != '') {
            $('#searched-filters').find('ul').append("<li class='filter-li-js'><a href='javascript:void(0);' class='userKeyword tag__clickable' onclick='removeFilterUser(\"userKeyword\",this)' >" + langLbl.userFilterLabel + " : " + keywordValue + "</a></li>");
            showAppliedFilterSection();
        } else {
            hideAppliedFilterSection();
        }
        search(frm);
    });



    $(document).on('click', '.panel-action', function () {

        $(this).parents('.panel-box').find('.panel-content').hide();
        $(this).parents('.panel-box').find('.' + $(this).attr('content')).show();

        $(this).parent().siblings().removeClass('is--active');
        $(this).parent().addClass('is--active');

    });


    var priceFilterMinValue = $("input[name='priceFilterMinValue']").val();
    var priceFilterMaxValue = $("input[name='priceFilterMaxValue']").val();
    $("input[name='priceFilterMinValue'], input[name='priceFilterMaxValue']").focus(function () {
        priceFilterMinValue = $("input[name='priceFilterMinValue']").val();
        priceFilterMaxValue = $("input[name='priceFilterMaxValue']").val();
        $(this).val('');
    }).blur(function () {
        if ($(this).val() == "") {
            $("input[name='priceFilterMinValue']").val(priceFilterMinValue);
            $("input[name='priceFilterMaxValue']").val(priceFilterMaxValue);
        }
        // $(this).parent('li').find('.rsText').show(500);
    })

    $("input[name='filterTimeSlots[]']").change(function () {
        var id = $(this).closest("label").attr('id');
        if ($(this).is(":checked")) {
            addFilter(id, this);
        } else {
            removeFilter(id, this);
        }
        search(frm);
    });

    $("input[name='filterFromCountry[]']").change(function () {

        var id = $(this).closest("label").attr('id');
        if ($(this).is(":checked")) {
            addFilter(id, this, 'filterfromCountry');
        } else {
            removeFilter(id, this);
        }
        search(frm);
    });

    $("input[name='filterGender[]']").change(function () {
        var id = $(this).closest("label").attr('id');
        if ($(this).is(":checked")) {
            addFilter(id, this);
        } else {
            removeFilter(id, this);
        }
        search(frm);
    });

    $(document).on('change', "select[name='filterSortBy']", function () {
        search(frm);
    });

    $("input[name='priceFilterMinValue']").keyup(function (e) {
        var code = e.which;
        if (code == 13) {
            e.preventDefault();
            addPricefilter();
        }
    });

    $("input[name='keyword']").keyup(function (e) {
        var code = e.which;
        if (code == 13) {
            e.preventDefault();
            search(frm);
        }
    });

    $("input[name='priceFilterMaxValue']").keyup(function (e) {
        var code = e.which;
        if (code == 13) {
            e.preventDefault();
            addPricefilter();
        }
    });



    $('input[name=\'teach_language_name\']').click(function () {
        $('input[name=\'teach_language_id\']').val('');
    });

    $('.form__input-js').click(function () {
        $(this).toggleClass("is-active");
        $('.section--listing-js').toggleClass("section-invisible");
        $('.form__element-js').toggleClass("form-target-visible");
    });

    $('html').click(function () {
        if ($('.section--listing-js').hasClass('section-invisible')) {
            $('.section--listing-js').removeClass('section-invisible');
        }
        if ($('.form__element-js').hasClass('form-target-visible')) {
            $('.form__element-js').removeClass('form-target-visible');
        }
    });
    $('.form-filters').click(function (e) {
        e.stopPropagation();
    });
    /* FOR NAV TOGGLES */


    /* FUNCTION FOR COLLAPSEABLE LINKS */
    $('.filter-trigger-js').click(function () {
        if ($(this).hasClass('is-active')) {
            $(this).removeClass('is-active');
            $(this).siblings('.filter-target-js').slideUp(); return false;
        }
        $('.filter-trigger-js').removeClass('is-active');
        $(this).addClass("is-active");
        $('.filter-target-js').slideUp();
        $(this).siblings('.filter-target-js').slideDown();
    });

});

function viewCalendar(teacherId, action) {
    fcom.ajax(fcom.makeUrl('Teachers', 'viewCalendar', [teacherId]), 'action=' + action, function (t) {
        $.facebox(t, 'facebox-large');
        $('body').addClass('calendar-facebox');
    });
}

function htmlEncode(value) {
    return $('<div/>').text(value).html();
}

(function () {


    updateRange = function (from, to) {
        range.update({
            from: from,
            to: to
        });
    };

    removeTecherNameValidation = function () {
        if ($("#keyword").hasClass('error')) {
            $("#keyword").val('');
            $("#keyword").next('ul.errorlist').remove();
            $("#keyword").removeClass('error');
        }
    };

    search = function (frm) {
        removeTecherNameValidation();
        $('#start_record').parent('p').addClass('d-none');
        var data = fcom.frmData(frm);
        //alert( data );
        var dv = $("#teachersListingContainer");


        /* spoken language filters[ */
        var spokenLanguages = [];
        $.each($("input[name='filterSpokenLanguage[]']:checked"), function () {
            var id = $(this).closest("label").attr('id');
            addFilter(id, this);
            spokenLanguages.push($(this).val());
        });
        if (spokenLanguages.length) {
            data = data + "&spokenLanguage=" + [spokenLanguages];
        }
        /* ] */

        /* preference filters[ */
        var preferenceFilters = [];
        $.each($("input[name='filterPreferences[]']:checked"), function () {
            var id = $(this).closest("label").attr('id');
            addFilter(id, this);
            preferenceFilters.push($(this).val());
        });
        if (preferenceFilters.length) {
            data = data + "&preferenceFilter=" + [preferenceFilters];
        }
        /* ] */

        /* from country filter[ */
        var fromCountry = [];
        $.each($("input[name='filterFromCountry[]']:checked"), function () {

            var id = $(this).closest("label").attr('id');
            addFilter(id, this);
            fromCountry.push($(this).val());
        });
        if (fromCountry.length) {
            data = data + "&fromCountry=" + [fromCountry];
        }
        /* ] */

        /* gender filter[ */
        var gender = [];
        $.each($("input[name='filterGender[]']:checked"), function () {
            var id = $(this).closest("label").attr('id');
            addFilter(id, this);
            gender.push($(this).val());
        });
        if (gender.length) {
            data = data + "&gender=" + [gender];
        }
        /* ] */

        /* price filter value pickup[ */
        if (typeof $("input[name=priceFilterMinValue]").val() != "undefined") {
            data = data + "&minPriceRange=" + $("input[name=priceFilterMinValue]").val();
        }
        if (typeof $("input[name=priceFilterMaxValue]").val() != "undefined") {
            data = data + "&maxPriceRange=" + $("input[name=priceFilterMaxValue]").val();
        }
        /* sort by[ */
        var sortOrder = $("select[name='filterSortBy']").val();
        if (sortOrder) {
            data = data + "&sortOrder=" + sortOrder;
        } else {
            data = data + "&sortOrder=popularity_desc";

        }
        /* ] */
        $(dv).html(fcom.getLoader());

        fcom.ajax(fcom.makeUrl('Teachers', 'teachersList'), data, function (ans) {
            $.mbsmessage.close();
            $(dv).html(ans);
            window.scroll(0, 0);
        });
    };

    goToSearchPage = function (page) {
        if (typeof page == undefined || page == null) {
            page = 1;
        }
        var frm = document.frmTeacherSearchPaging;
        $(frm.page).val(page);
        search(frm);
    };

    resetSearchFilters = function () {
        searchArr = [];
        document.frmSrch.reset();
        document.frmSrch.reset();
        search(document.frmSrch);
    };

    addFilter = function (id, obj, from) {
        $('.filter-tags').show();
        var click = "onclick=removeFilter('" + id + "',this)";
        var filter = htmlEncode($(obj).parents(".filter-colum, .filter-group__inner").find(".filter__trigger-label").text());
        $filterVal = htmlEncode($(obj).parents(".selection-tabs__label, label").find(".name").text());
        if (!$('#searched-filters').find('a').hasClass(id)) {
            id += ' tag__clickable';
            $('#searched-filters').find('ul').append("<li class='filter-li-js'><a href='javascript:void(0);' class=\' " + id + " \'" + click + ">" + filter + ": " + $filterVal + "</a></li>");
            showAppliedFilterSection();
        }
    };
    hideAppliedFilterSection = function () {
        console.log('hello');
        if (1 > $('li.filter-li-js').length) {
            $('.filter-tags').addClass('d-none');
            $('.clear-filter').addClass('d-none');
        }
    }
    showAppliedFilterSection = function () {
        if ($('li.filter-li-js').length > 0) {
            $('.filter-tags').removeClass('d-none');
            $('.clear-filter').removeClass('d-none');
        }
    }

    removeFilter = function (id, obj) {
        $('.' + id).parent("li").remove();
        $('#' + id).find('input[type=\'checkbox\']').prop('checked', false);
        hideAppliedFilterSection();
        search(document.frmSrch);
    }

    removeFilterCustom = function (id, obj) {
        $('.' + id).parent("li").remove();
        $('input[name=\'teachLangId\']').val('');
        $('input[name=\'teach_language_name\']').val('');
        hideAppliedFilterSection();
        search(document.frmSrch);
    }
    removeAllFilters = function () {
        $('input:checkbox').removeAttr('checked');
        $('input[name=\'teachLangId\']').val('');
        $('input[name=\'teach_language_name\']').val('');
        $('input[name=\'keyword\']').val('');
        $('li.filter-li-js').remove();
        hideAppliedFilterSection();
        search(document.frmSrch);
    }

    removeFilterUser = function (id, obj) {
        $('.' + id).parent("li").remove();
        $('input[name=\'keyword\']').val('');
        hideAppliedFilterSection();
        search(document.frmSrch);
    }

    addPricefilter = function () {
        $('.filter-tags').show();
        $('.price').parent("li").remove();
        if (!$('#searched-filters').find('a').hasClass('price')) {
            var filterCaption = htmlEncode($("#price_range").parents('.filter').find(".filter__trigger-label").text());
            var varcurrencySymbolLeft = $('<textarea />').html(currencySymbolLeft).text();
            var varcurrencySymbolRight = $('<textarea />').html(currencySymbolRight).text();
            $('#searched-filters').find('ul').append('<li class="filter-li-js"><a href="javascript:void(0)" class="price tag__clickable" onclick="removePriceFilter(this)" >' + filterCaption + ': ' + varcurrencySymbolLeft + $("input[name=priceFilterMinValue]").val() + varcurrencySymbolRight + ' - ' + varcurrencySymbolLeft + $("input[name=priceFilterMaxValue]").val() + varcurrencySymbolRight + '</a></li>');
            showAppliedFilterSection();
        }
        var frm = document.frmSrch;
        search(frm);
    }

    removePriceFilter = function () {
        updatePriceFilter();
        search(document.frmSrch);
        $('.price').parent("li").remove();
        hideAppliedFilterSection();
    }
    removePriceFilterCustom = function (e, minPrice, maxPrice) {
        $('input[name="priceFilterMinValue"]').val(minPrice);
        $('input[name="priceFilterMaxValue"]').val(maxPrice);
        var $range = $("#price_range");
        range = $range.data("ionRangeSlider");
        updateRange(minPrice, maxPrice);
        range.reset();
        $('.price').parent("li").remove();
        hideAppliedFilterSection();
        search(document.frmSrch);
    }


    updatePriceFilter = function (minPrice, maxPrice) {
        if (typeof minPrice == 'undefined' || typeof maxPrice == 'undefined') {
            minPrice = $("#filterDefaultMinValue").val();
            maxPrice = $("#filterDefaultMaxValue").val();
        } else {
            addPricefilter();
        }

        $('input[name="priceFilterMinValue"]').val(minPrice);
        $('input[name="priceFilterMaxValue"]').val(maxPrice);
        var $range = $("#price_range");
        range = $range.data("ionRangeSlider");
        updateRange(minPrice, maxPrice);
        range.reset();
    }

})();
