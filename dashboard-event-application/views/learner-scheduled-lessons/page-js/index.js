$(function () {
    var dv = '#listItemsLessons';

    getLessonsByStatus = function (lStatus) {
        $('[name=status]').val(lStatus);
        searchLessons(document.frmSrch);
    };
    searchAllStatusLessons = function (frm) {
      
        searchLessons(frm);
    };

    searchLessons = function (frm) {
        $('.calender-js').removeClass('is-active');
        $('.list-js').addClass('is-active');
        $(dv).html(fcom.getLoader());
        var data = fcom.frmData(frm);
        fcom.ajax(fcom.makeUrl('LearnerScheduledLessons', 'search'), data, function (t) {
            $(dv).html(t);
        });
    };

    getListingLessonPlans = function (id) {
        $(dv).html(fcom.getLoader());
        fcom.ajax(fcom.makeUrl('LearnerScheduledLessons', 'getListingLessonPlans', [id]), '', function (t) {
            searchLessons(document.frmSrch);
            $.facebox(t, 'facebox-medium');
        });
    };

    changeLessonPlan = function (id) {
        $(dv).html(fcom.getLoader());
        fcom.ajax(fcom.makeUrl('LearnerScheduledLessons', 'changeLessonPlan', [id]), '', function (t) {
            searchLessons(document.frmSrch);
            $.facebox(t, 'facebox-medium');
        });
    };

    remove = function (elem, id) {
        if (confirm(langLbl.confirmRemove)) {
            $(elem).closest('tr').remove();
            $(dv).html(fcom.getLoader());
            fcom.ajax(fcom.makeUrl('TeacherLessonsPlan', 'remove', [id]), '', function (t) {
                searchLessons(document.frmSrch);
            });
        }
    };

    assignLessonPlanToLessons = function (lessonId, planId) {
        fcom.updateWithAjax(fcom.makeUrl('LearnerScheduledLessons', 'assignLessonPlanToLessons'), 'ltp_slessonid=' + lessonId + '&ltp_tlpn_id=' + planId, function (t) {
            $.facebox.close();
            searchLessons(document.frmSrch);
        });
    };

    removeAssignedLessonPlan = function (lessonId) {
        if (confirm(langLbl.confirmRemove)) {
            fcom.updateWithAjax(fcom.makeUrl('LearnerScheduledLessons', 'removeAssignedLessonPlan'), 'ltp_slessonid=' + lessonId, function (t) {
                $.facebox.close();
                searchLessons(document.frmSrch);
            });
        }
    };

    viewAssignedLessonPlan = function (lessonId) {
        fcom.ajax(fcom.makeUrl('LearnerScheduledLessons', 'viewAssignedLessonPlan', [lessonId]), '', function (t) {
            searchLessons(document.frmSrch);
            $.facebox(t, 'facebox-medium');
        });
    };

    viewCalendar = function (frm) {
        var data = fcom.frmData(frm);
        fcom.ajax(fcom.makeUrl('LearnerScheduledLessons', 'viewCalendar'), data, function (t) {
            $(dv).html(t);
            $('body').addClass('calendar-facebox');
        });
    };

    clearSearch = function () {
        document.frmSrch.reset();
        searchLessons(document.frmSrch);
    };

    goToSearchPage = function (page) {
        if (typeof page == undefined || page == null) {
            page = 1;
        }
        var frm = document.frmSLnsSearchPaging;
        $(frm.page).val(page);
        searchLessons(frm);
    };

    loadLessonsTab = function () {
        let urlHashVal = window.location.hash.replace('#', '');
        let activeTab = urlHashVal ? urlHashVal : '';
        $('#lesson-status option[value="'+activeTab+'"]').prop('selected', true);
		searchLessons(document.frmSrch);
    }
	loadLessonsTab();
});


$(document).on('click', '.tab-switch a', function () {
    $('.tab-switch a').removeClass('is-active');
    $(this).addClass('is-active');
});
$(document).ready(function () {
    $('#lesson-status').change(function (event) {
        window.location.href = fcom.makeUrl('LearnerScheduledLessons', '', [], confWebRootUrl) + '#' + $('#lesson-status').val();
    });
});