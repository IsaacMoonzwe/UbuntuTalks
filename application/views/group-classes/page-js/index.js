$("document").ready(function () {
    var frm = document.frmTeacherSrch;
    search(frm);

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

    $(document).on('keyup', "input[name='keyword']", function (e) {
        var code = e.which;
        if (code == 13) {
            e.preventDefault();
            var frm = document.frmTeacherSrch;
            search(frm);
        }
    });

});

(function () {
    search = function (frm) {
        var data = fcom.frmData(frm);
        var dv = $("#listingContainer");
        $(dv).html(fcom.getLoader());
        console.log(data);

        fcom.ajax(fcom.makeUrl('GroupClasses', 'search'), data, function (t) {
            $(dv).html(t);
        });
    };

    groupClassesForm = function () {
        $.loader.show();
        $.facebox(function () {
            fcom.ajax(fcom.makeUrl('GroupClasses', 'groupClassesForm'), '', function (t) {
                $.facebox(t, 'faceboxWidth');
                $.loader.hide();
            });
        });
    };

    contactSubmit = function (frm) {

        //if (!$(frm).validate()) return;
        $.loader.show();
        var data = fcom.frmData(frm);
        var country_name = '';
        var timezone = '';
        $('.select2-selection__rendered li').each(function (i) {
            var name1 = $(this).attr('title');
            if (name1 == undefined) {
                console.log("hi");
            }
            else {
                country_name = country_name +  $(this).attr('title') +','; // This is your rel value
            }
        });

        var count = country_name.split('(')[0];
        const alltimezones = country_name.slice(country_name.indexOf('(') + 1);
        console.log(alltimezones);
        timezone = alltimezones;
        data = data + '&country=' + count + '&timeZone=' + timezone;
        console.log("data==", data);
        fcom.updateWithAjax(fcom.makeUrl('GroupClasses', 'contactSubmit'), data, function (t) {

            if (t.redirectUrl) {
                $.loader.hide();
                window.location.href = window.location.href;
            }
        });
    }



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

})();