$(document).ready(function () {

    searchOrderPuchasedLessons(document.orderPurchasedLessonsSearchForm);

    $(document).on('click', function () {
        $('.autoSuggest').empty();
    });



    // $('#teacher,#learner').blur(function () {
    // 		$(this).next('.dropdown-menu').hide();
    // });
    $('.menutrigger').click(function () {
        $('.dropdown-menu').hide();
    });

    $('input[name=\'teacher\']').keyup(function () {
        $('input[name=\'op_teacher_id\']').val('');
    });



    $('input[name=\'learner\']').keyup(function () {
        $('input[name=\'order_user_id\']').val('');
    });

    //redirect user to login page
    $(document).on('click', 'ul.linksvertical li a.redirect--js', function (event) {
        event.stopPropagation();
    });

});

(function () {
    var currentPage = 1;
    var transactionUserId = 0;
    var active = 1;
    var inActive = 0;

    goToSearchPage = function (page) {
        if (typeof page == undefined || page == null) {
            page = 1;
        }
        var frm = document.frmUserSearchPaging;
        $(frm.page).val(page);
        searchOrderPuchasedLessons(frm);
    };

    searchOrderPuchasedLessons = function (form, page) {
        if (!page) {
            page = currentPage;
        }
        currentPage = page;
        /*[ this block should be before dv.html('... anything here.....') otherwise it will through exception in ie due to form being removed from div 'dv' while putting html*/
        var data = '';
        if (form) {
            data = fcom.frmData(form);
        }
        /*]*/

        $("#userListing").html(fcom.getLoader());

        fcom.ajax(fcom.makeUrl('DonationInformation', 'search'), data, function (res) {
            $("#userListing").html(res);
        });
    };

    clearUserSearch = function () {
        document.orderPurchasedLessonsSearchForm.reset();
        document.orderPurchasedLessonsSearchForm.order_user_id.value = '';
        document.orderPurchasedLessonsSearchForm.op_teacher_id.value = '';
        searchOrderPuchasedLessons(document.orderPurchasedLessonsSearchForm);
    };

    updateOrderStatus = function (obj, id, value, oldValue) {
        var currentValue = $(obj).val();
        console.log(currentValue, 'currentValue');
        if (!confirm("Do you really want to update status?")) {
            $(obj).val(oldValue);
            return false;
        }
        if (id === null) {
            $.mbsmessage('Invalid Request!');
            return false;
        }
        fcom.ajax(fcom.makeUrl('DonationInformation', 'updateOrderStatus'), {"order_id": id, "order_is_paid": value}, function (json) {
            res = $.parseJSON(json);
            if (res.status == "1") {
                $.mbsmessage(res.msg, true, 'alert alert--success');
                searchOrderPuchasedLessons(document.orderPurchasedLessonsSearchForm);
            } else {
                $(obj).val(oldValue);
                $.mbsmessage(res.msg, true, 'alert alert--danger');
            }
        });
    };

})();

$(document).ready(function () {
    $('input[name=\'teacher\']').autocomplete({
        'source': function (request, response) {
            $.ajax({
                url: fcom.makeUrl('Users', 'autoCompleteJson'),
                data: {keyword: request, fIsAjax: 1},
                dataType: 'json',
                type: 'post',
                success: function (json) {
                    response($.map(json, function (item) {
                        return {label: item['name'] + '(' + item['username'] + ')', value: item['id'], name: item['username']};
                    }));
                },
            });
        },
        'select': function (item) {
            $("input[name='op_teacher_id']").val(item['value']);
            $("input[name='teacher']").val(item['name']);
        }
    });
    $('input[name=\'learner\']').autocomplete({
        'source': function (request, response) {
            $.ajax({
                url: fcom.makeUrl('Users', 'autoCompleteJson'),
                data: {keyword: request, fIsAjax: 1},
                dataType: 'json',
                type: 'post',
                success: function (json) {
                    response($.map(json, function (item) {
                        return {label: item['name'] + '(' + item['username'] + ')', value: item['id'], name: item['username']};
                    }));
                },
            });
        },
        'select': function (item) {
            $("input[name='order_user_id']").val(item['value']);
            $("input[name='learner']").val(item['name']);
        }
    });
    $('input[name=\'learner\']').keyup(function () {
        $('input[name=\'order_user_id\']').val('');
    });
    $('input[name=\'teacher\']').keyup(function () {
        $('input[name=\'op_teacher_id\']').val('');
    });
});
