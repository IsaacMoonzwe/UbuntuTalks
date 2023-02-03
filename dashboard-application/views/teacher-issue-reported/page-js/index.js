$(function () {
    var dv = '#listItems';
    searchStudents = function (frm) {
        $(dv).html(fcom.getLoader());
        var data = fcom.frmData(frm);
        fcom.ajax(fcom.makeUrl('TeacherIssueReported', 'search'), data, function (t) {
            $(dv).html(t);
        });
    };

    clearSearch = function () {
        document.frmSrch.reset();
        searchStudents(document.frmSrch);
    }

    goToSearchPage = function (page) {
        if (typeof page == undefined || page == null) {
            page = 1;
        }
        var frm = document.frmTeacherStudentsSearchPaging;
        $(frm.page).val(page);
        searchStudents(frm);
    };
    searchStudents(document.frmSrch);
});


