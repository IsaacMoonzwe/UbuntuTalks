var lastValue = weekDayNames.longName[6];
weekDayNames.longName.pop();
weekDayNames.longName.unshift(lastValue);

var lastValue = weekDayNames.shortName[6];
weekDayNames.shortName.pop();
weekDayNames.shortName.unshift(lastValue);

defaultsValue = {
    monthNames: monthNames.longName,
    monthNamesShort: monthNames.shortName,
    dayNamesMin: weekDayNames.shortName,
    dayNamesShort: weekDayNames.shortName,
    currentText: langLbl.today,
    closeText: langLbl.done,
    prevText: langLbl.prev,
    nextText: langLbl.next,
    isRTL : (layoutDirection == 'rtl')
}

$.datepicker.regional[''] = $.extend(true, {}, defaultsValue);
$.datepicker.setDefaults($.datepicker.regional['']);


$(document).ready(function () {
    searchCredits(document.frmCreditSrch);
});
(function () {
    var dv = '#creditListing';

    searchCredits = function (frm) {
        var data = fcom.frmData(frm);
        $(dv).html(fcom.getLoader());

        fcom.ajax(fcom.makeUrl('Wallet', 'search'), data, function (res) {
            $(dv).html(res);
        });
    };

    goToCreditSearchPage = function (page) {
        if (typeof page == undefined || page == null) {
            page = 1;
        }
        var frm = document.frmCreditSrchPaging;
        $(frm.page).val(page);
        searchCredits(frm);
    };

    clearSearch = function () {
        document.frmCreditSrch.reset();
        searchCredits(document.frmCreditSrch);
    };

    closeForm = function () {
        $(document).trigger('close.facebox');
    }

    setUpWalletRecharge = function (frm) {
        if (!$(frm).validate()) return;
        var data = fcom.frmData(frm);
        fcom.updateWithAjax(fcom.makeUrl('Wallet', 'setUpWalletRecharge'), data, function (t) {
            if (t.redirectUrl) {
                window.location = t.redirectUrl;
            }
        });
    }

    redeemGiftcardForm = function () {
        $.loader.show();
        $.facebox(function () {
            fcom.ajax(fcom.makeUrl('Wallet', 'giftcard-redeem-form'), '', function (t) {
                $.facebox(t, 'faceboxWidth');
                $.loader.hide();
            });
        });
    };

    rechargeWalletForm = function () {
        $.loader.show();
        $.facebox(function () {
            fcom.ajax(fcom.makeUrl('Wallet', 'rechargeWalletForm'), '', function (t) {
                $.facebox(t, 'faceboxWidth');
                $.loader.hide();
            });
        });
    };

    giftcardRedeem = function (frm1) {
        if (!$(frm1).validate()) return;
        var data1 = fcom.frmData(frm1);

        fcom.updateWithAjax(fcom.makeUrl('Wallet', 'reedemGiftcard'), data1, function (res1) {
            $(document).trigger('close.facebox');
            //searchCredits(document.frmCreditSrch);
            document.location.reload();
        });
    };

    withdrwalRequestForm = function (methodId) {
        $.loader.show();
        var postData = '';
        if (typeof methodId != 'undefined') {
            postData = 'methodId=' + methodId;
        }
        $.facebox(function () {
            fcom.ajax(fcom.makeUrl('Wallet', 'requestWithdrawal'), postData, function (t) {
                $.facebox(t, 'facebox-medium request-Withdrawal-js');
                $.loader.hide();
            });
        });
    };

    getWithdrwalRequestForm = function (methodId) {
        $('.request-Withdrawal-js').html(fcom.getLoader());
        postData = '';
        if (typeof methodId != 'undefined') {
            postData = 'methodId=' + methodId;
        }
        fcom.ajax(fcom.makeUrl('Wallet', 'requestWithdrawal'), postData, function (t) {
            $('.request-Withdrawal-js').html(t);
        });
    };


    setupWithdrawalReq = function (frm) {
        if (!$(frm).validate()) return;
        $.loader.show();
        var data = fcom.frmData(frm);
        fcom.updateWithAjax(fcom.makeUrl('Wallet', 'setupRequestWithdrawal'), data, function (t) {
            $(document).trigger('close.facebox');
            searchCredits(document.frmCreditSrch);
            $.loader.hide();
        });
    };


})();
