
$("document").ready(function () {
    var frm = document.frmSrch;
    search(frm);
    $("input[name='filterWeekDays[]']").change(function () {
        var id = $(this).closest("label").attr('id');
        if ($(this).is(":checked")) {
            addFilter(id, this);
            search(frm);
        } else {
            removeFilter(id, this);
        }
    });
    $("input[name='filterTimeSlots[]']").change(function () {
        var id = $(this).closest("label").attr('id');
        if ($(this).is(":checked")) {
            addFilter(id, this);
            search(frm);
        } else {
            removeFilter(id, this);
        }
    });
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
    removeFilter = function (id, obj) {
        $('.' + id).parent("li").remove();
        $('#' + id).find('input[type=\'checkbox\']').prop('checked', false);
        hideAppliedFilterSection();
        search(document.frmSrch);
    };



    $(document).on('change', '[name=language],[name=custom_filter],[name=status]', function () {
        search(frm);
    });

    $('.search-group-class-js').click(function () {
        search(frm);
    });

    $('#teachLang').change(function () {
        search(frm);
    });

    $('.filter-trigger-js').click(function (event) {
        event.stopPropagation();
        if ($(this).hasClass('is-active')) {
            $(this).removeClass('is-active');
            $(this).siblings('.filter-target-js').slideUp(); return false;
        }
        $('.filter-trigger-js').removeClass('is-active');
        $(this).addClass("is-active");
        $('.filter-target-js').slideUp();
        $(this).siblings('.filter-target-js').slideDown();
    });

    $('.select-teach-lang-js').click(function () {
        var langId = parseInt($(this).attr('data-id'));
        var langName = $(this).html();
        if (1 > langId) {
            langName = '';
            langId = '';
        }
        $('.select-teach-lang-js').parent('li').removeClass('is--active');
        $(this).parent('li').addClass('is--active');
        $('#language').val(langId);
        $("#teachLang").val(langName);
        $('.filter-trigger-js').removeClass('is-active');
        $('.filter-target-js').slideUp();
        search(frm);
    });

    $('.ages_group').click(function () {
        var langId = parseInt($(this).attr('data-id'));
        var langName = $(this).html();
        if (1 > langId) {
            langName = '';
            langId = '';
        }
        $('.ages_group').parent('li').removeClass('is--active');
        $(this).parent('li').addClass('is--active');
        // $('#language').val(langId);
        $("#techAges").val(langName);
        $('.filter-trigger-js').removeClass('is-active');
        $('.filter-target-js').slideUp();
        search(frm);
    });

    $(document).on('keyup', "input[name='keyword']", function (e) {
        var code = e.which;
        if (code == 13) {
            e.preventDefault();
            var frm = document.frmSrch;
            search(frm);
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
    $('#btnSrchSubmit').click(function () {
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
    $(document).on('click', '.select-teach-lang-js', function () {
        var langName = $(this).html();
        var langId = $(this).attr('teachLangId');
        $('input[name=\'teachLangId\']').val(langId);
        $("input[name=\'language\']").val(langName);
        $('.filter-trigger-js').removeClass('is-active');
        $('.filter-target-js').slideUp();
        $('#frm_fat_id_frmSrch').submit();
        $('.language_keyword').parent("li").remove();
        $('#searched-filters').find('ul').append("<li class='filter-li-js'><a href='javascript:void(0);' class= 'language_keyword tag__clickable' onclick='removeFilterCustom(\"language_keyword\",this)' >" + langLbl.language + " : " + langName + "</a></li>");
        showAppliedFilterSection();

    });
    $(document).on('click', '.ages_group', function () {
        var langName = $(this).html();
        var langId = $(this).attr('teachLangId');
        $('input[name=\'ageId\']').val(langId);
        $("input[name=\'techAge\']").val(langName);
        var id = $(this).closest("label").attr('id');
        var filterCaption = htmlEncode($("#techAge").parents('.filter').find(".filter__trigger-label").text());
        // $('.filter-trigger-js').removeClass('is-active');
        // $('.filter-target-js').slideUp();
        $('#frm_fat_id_frmSrch').submit();
        $('.Age_keyword').parent("li").remove();
        $('#searched-filters').find('ul').append("<li class='filter-li-js'><a href='javascript:void(0);' class= 'Age_keyword tag__clickable' onclick='removeAgeCustom(\"Age_keyword\",this)' >" + "Ages" + " : " + langName + "</a></li>");
        // addFilter(id,this);
        showAppliedFilterSection();

    });

});

function viewCalendar(teacherId, action) {
    fcom.ajax(fcom.makeUrl('Teachers', 'viewCalendar', [teacherId]), 'action=' + action, function (t) {
        $.facebox(t, 'facebox-large');
        $('body').addClass('calendar-facebox');
    });
}

(function () {
    updateRange = function (from, to) {
        range.update({
            from: from,
            to: to
        });
    };

    removeNameValidation = function () {
        if ($("#keyword").hasClass('error')) {
            $("#keyword").val('');
            $("#keyword").next('ul.errorlist').remove();
            $("#keyword").removeClass('error');
        }
    };

    search = function (frm) {
        removeNameValidation();
        var data = fcom.frmData(frm);
        var dv = $("#listingContainer");
        // $(dv).html(fcom.getLoader());
        if (typeof $("input[name=priceFilterMinValue]").val() != "undefined") {
            data = data + "&minPriceRange=" + $("input[name=priceFilterMinValue]").val();
        }
        if (typeof $("input[name=priceFilterMaxValue]").val() != "undefined") {
            data = data + "&maxPriceRange=" + $("input[name=priceFilterMaxValue]").val();
        }
        $(dv).html(fcom.getLoader());
        fcom.ajax(fcom.makeUrl('Kids', 'search'), data, function (t) {
            ;
            $(dv).html(t);
        });

    };

    // $("input[name='filterWeekDays[]']").change(function () {

    //     console.log('sbdk1212');
    //     var id = $(this).closest("label").attr('id');
    //     if ($(this).is(":checked")) {
    //         console.log('sbdk1212');
    //         addFilter(id, this);
    //         search(frm);
    //     } else {
    //         removeFilter(id, this);
    //     }
    // });
    // $("input[name='filterTimeSlots[]']").change(function () {
    //     var id = $(this).closest("label").attr('id');
    //     if ($(this).is(":checked")) {
    //         addFilter(id, this);
    //         searchTeachers(frm);
    //     } else {
    //         removeFilter(id, this);
    //     }
    // });
    // addFilter = function (id, obj, from) {
    //     $('.filter-tags').show();
    //     var click = "onclick=removeFilter('" + id + "',this)";
    //     var filter = htmlEncode($(obj).parents(".filter-colum, .filter-group__inner").find(".filter__trigger-label").text());
    //     $filterVal = htmlEncode($(obj).parents(".selection-tabs__label, label").find(".name").text());
    //     if (!$('#searched-filters').find('a').hasClass(id)) {
    //         id += ' tag__clickable';
    //         $('#searched-filters').find('ul').append("<li class='filter-li-js'><a href='javascript:void(0);' class=\' " + id + " \'" + click + ">" + filter + ": " + $filterVal + "</a></li>");
    //         showAppliedFilterSection();
    //     }
    // };

    setTeachLangId = function (el, id, name) {
        if (typeof page == undefined || page == null) {
            page = 1;
        }
        var frm = document.frmSearchPaging;
        $(frm.page).val(page);
        search(frm);
    };

    goToSearchPage = function (page) {
        if (typeof page == undefined || page == null) {
            page = 1;
        }
        var frm = document.frmSearchPaging;
        $(frm.page).val(page);
        search(frm);
    };

    resetSearchFilters = function () {
        searchArr = [];
        document.frmSrch.reset();
        document.frmSrch.reset();
        search(document.frmSrch);
    };
    hideAppliedFilterSection = function (id = '') {

        var defLang = ($('.language_keyword').text()).split(": ")[1];
        var ageFilter = ($('.Age_keyword').text()).split(": ")[1];
        if (id == 'Age_keyword') {
            if (id == 'Age_keyword' || ageFilter == 'All Ages') {
                if ($('li.filter-li-js').length <= 1) {
                    $('.filter-tags').addClass('d-none');
                    $('.Age_keyword').parent().addClass('d-none');
                    $('.clear-filter').addClass('d-none');
                }
                else if ($('li.filter-li-js').length <= 2 && ageFilter == 'All Ages' && defLang == 'All Language') {
                    $('.filter-tags').addClass('d-none');
                    $('.language_keyword').parent().addClass('d-none');
                    $('.Age_keyword').parent().addClass('d-none');
                    $('.clear-filter').addClass('d-none');
                }
                else {
                    console.log("Hello2");
                    $('.Age_keyword').parent().addClass('d-none');
                }
            }
            else if (2 == $('li.filter-li-js').length && (id == 'Age_keyword' || ageFilter == 'All Ages')) {
                console.log("Hello1");
                $('.filter-tags').addClass('d-none');
                $('.Age_keyword').parent().addClass('d-none');
                $('.clear-filter').addClass('d-none');
            }

            else if (1 == $('li.filter-li-js').length) {

            }
        }
        else if (id == 'language_keyword') {
            if (id == 'language_keyword' || defLang == 'All Language') {
                if ($('li.filter-li-js').length <= 1) {
                    $('.filter-tags').addClass('d-none');
                    $('.language_keyword').parent().addClass('d-none');
                    $('.clear-filter').addClass('d-none');
                }
                else if ($('li.filter-li-js').length <= 2 && ageFilter == 'All Ages' && defLang == 'All Language') {
                    $('.filter-tags').addClass('d-none');
                    $('.language_keyword').parent().addClass('d-none');
                    $('.Age_keyword').parent().addClass('d-none');
                    $('.clear-filter').addClass('d-none');
                }
                else {
                    console.log("Hello5", ageFilter + " " + defLang);
                    $('.language_keyword').parent().addClass('d-none');
                }
            }
            else if (2 == $('li.filter-li-js').length && (id == 'language_keyword' || defLang == 'All Language')) {
                console.log("Hello4");
                $('.filter-tags').addClass('d-none');
                $('.language_keyword').parent().addClass('d-none');
                $('.clear-filter').addClass('d-none');
            }

        }
        
        else if (id=='All'){
            $('.filter-tags').addClass('d-none');
                $('.clear-filter').addClass('d-none'); 
        }
        else {
            if ((1 > $('li.filter-li-js').length) ) {
                console.log("Hello6");
                $('.filter-tags').addClass('d-none');
                $('.clear-filter').addClass('d-none');
            }
            else if (1== $('li.filter-li-js').length &&  defLang=='All Language' || ageFilter=='All Ages'){
                $('.filter-tags').addClass('d-none');
                $('.clear-filter').addClass('d-none');
            }
            else if(2== $('li.filter-li-js').length &&  defLang=='All Language' && ageFilter=='All Ages'){
                $('.filter-tags').addClass('d-none');
                $('.clear-filter').addClass('d-none');
            }
        }

    };

    showAppliedFilterSection = function () {
        var defLang = ($('.language_keyword').text()).split(": ")[1];
        var ageFilter = ($('.Age_keyword').text()).split(": ")[1];
        if(defLang=='All Language'){
            $('.language_keyword').parent().addClass('d-none');
        }
        if(ageFilter=='All Ages'){
            $('.Age_keyword').parent().addClass('d-none');
        }
        if ($('li.filter-li-js').length > 0) {
           
           
                $('.filter-tags').removeClass('d-none');
                $('.clear-filter').removeClass('d-none');
            
            
        }
    };

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
    };
    removeFilter = function (id, obj) {
        $('.' + id).parent("li").remove();
        $('#' + id).find('input[type=\'checkbox\']').prop('checked', false);
        hideAppliedFilterSection();
        search(document.frmSrch);
    };

    removeAgeCustom = function (id, obj) {
        $('.' + id).parent("li").remove();
        $('input[name=\'ageId\']').val('');
        $('input[name=\'techAge\']').val('');
        $('#ages_group option:selected').remove();

        $(".ages ul li").removeClass("is--active");
        $(".ages ul li:first-child").addClass("is--active");
        $('.ages_filter').attr('placeHolder', 'All Ages');
        $('.ages_filter').attr('value', 'All Ages');
        $(".ages ul li:first a").click();

        hideAppliedFilterSection(id);
        search(document.frmSrch);
    };
    removeFilterCustom = function (id, obj) {
        $('.' + id).parent("li").remove();
        $('input[name=\'teachLangId\']').val('');
        $('input[name=\'language\']').val('');
        $('#language option:selected').remove();
        $(".languages ul li").removeClass("is--active");
        $(".languages ul li:first-child").addClass("is--active");
        $('.languages_input').attr('placeHolder', 'All Language');
        $('.languages_input').attr('value', 'All Language');
        $(".languages ul li:first a").click();

        hideAppliedFilterSection(id);
        search(document.frmSrch);
    };

    removeAllFilters = function () {
        $('input:checkbox').removeAttr('checked');
        $('input[name=\'teachLangId\']').val('');
        $('input[name=\'language\']').val('');
        $('input[name=\'keyword\']').val('');
        $('li.filter-li-js').remove();
        $(".languages ul li").removeClass("is--active");
        $(".languages ul li:first-child").addClass("is--active");
        $('.languages_input').attr('placeHolder', 'All Language');
        $('.languages_input').attr('value', 'All Language');
        $(".languages ul li:first a").click();
        
        $('#ages_group option:selected').remove();

        $(".ages ul li").removeClass("is--active");
        $(".ages ul li:first-child").addClass("is--active");
        $('.ages_filter').attr('placeHolder', 'All Ages');
        $('.ages_filter').attr('value', 'All Ages');
        $(".ages ul li:first a").click();

        updatePriceFilter();
        hideAppliedFilterSection('All');
        search(document.frmSrch);
    };

    removeFilterUser = function (id, obj) {
        $('.' + id).parent("li").remove();
        $('input[name=\'keyword\']').val('');
        hideAppliedFilterSection();
        search(document.frmSrch);
    };
    removePriceFilter = function () {
        updatePriceFilter();
        search(document.frmSrch);
        $('.price').parent("li").remove();
        hideAppliedFilterSection();
    };

    $("input[name='filterWeekDays[]']").change(function () {
        var id = $(this).closest("label").attr('id');
        if ($(this).is(":checked")) {
            addFilter(id, this);
            search(frm);
        } else {
            removeFilter(id, this);
        }
    });
    $("input[name='filterTimeSlots[]']").change(function () {
        var id = $(this).closest("label").attr('id');
        if ($(this).is(":checked")) {
            addFilter(id, this);
            search(frm);
        } else {
            removeFilter(id, this);
        }
    });
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
    };

    $("input[name='priceFilterMinValue']").keyup(function (e) {
        var code = e.which;
        if (code == 13) {
            e.preventDefault();
            addPricefilter();
        }
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


})();

