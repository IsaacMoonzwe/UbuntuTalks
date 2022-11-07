var div = '#commisionReportList';
searchCommissionReport = function (form) {
    var data = fcom.frmData(form);
    fcom.ajax(fcom.makeUrl('commissionReport', 'search'), data, function (res) {
        $(div).html(res)
    });

}

goToSearchPage = function (page) {
    if (page == null || typeof page == undefined) {
        page = 1;
    }
    form = document.commissionReportSearchPaging;
    $(form.page).val(page);
    searchCommissionReport(form);
}

clear_search = function () {
    document.comssionReportForm.reset();
    searchCommissionReport(document.comssionReportForm);
}

$(document).ready(function () {
    searchCommissionReport(document.comssionReportForm);
})