$(function () {
    var dv = '#listItemsLessons';
    var url = $(location).attr('href');
    var myArray = url.split("=");
    var lesson_id = myArray[1];
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
        data=data+"&lesson_id="+lesson_id;
        fcom.ajax(fcom.makeUrl('Classes', 'search'), data, function (t) {
		
            $(dv).html(t);
        });
    };
  employeeAcccessCodes = function () {
        $.loader.show();
        $.facebox(function () {
            fcom.ajax(fcom.makeUrl('Classes', 'employeeAcccessCode'), '', function (t) {
			console.log(t);
                $.facebox(t, 'facebox-medium');
                $.loader.hide();
            });
        });
    };
    employeeAcccessCode = function () {
        $.loader.show();
        $.facebox(function () {
            fcom.ajax(fcom.makeUrl('Classes', 'employeeAcccessCode'), '', function (t) {
                $.facebox(t, 'faceboxWidth');
                $.loader.hide();
            });
        });
    };

    codeSubmit = function (frm) {
        if (!$(frm).validate()) return;
        var data = fcom.frmData(frm);
        fcom.updateWithAjax(fcom.makeUrl('Classes', 'codeSubmit'), data, function (t) {
            
            if (t.redirectUrl) {
                window.location = t.redirectUrl;
            }
            if(t.msg){
                $.mbsmessage(t.msg, false, 'alert alert--process');
            }
        });
    }
    getListingLessonPlans = function (id) {
        $(dv).html(fcom.getLoader());
        fcom.ajax(fcom.makeUrl('Classes', 'getListingLessonPlans', [id]), '', function (t) {
            searchLessons(document.frmSrch);
            $.facebox(t, 'facebox-medium');
        });
    };

    changeLessonPlan = function (id) {
        $(dv).html(fcom.getLoader());
        fcom.ajax(fcom.makeUrl('Classes', 'changeLessonPlan', [id]), '', function (t) {
            searchLessons(document.frmSrch);
            $.facebox(t, 'facebox-medium');
        });
    };

    remove = function (elem, id) {
        if (confirm(langLbl.confirmRemove)) {
            $(elem).closest('tr').remove();
            $(dv).html(fcom.getLoader());
            fcom.ajax(fcom.makeUrl('Classes', 'remove', [id]), '', function (t) {
                searchLessons(document.frmSrch);
            });
        }
    };

    assignLessonPlanToLessons = function (lessonId, planId) {
        fcom.updateWithAjax(fcom.makeUrl('Classes', 'assignLessonPlanToLessons'), 'ltp_slessonid=' + lessonId + '&ltp_tlpn_id=' + planId, function (t) {
            $.facebox.close();
            searchLessons(document.frmSrch);
        });
    };

    removeAssignedLessonPlan = function (lessonId) {
        if (confirm(langLbl.confirmRemove)) {
            fcom.updateWithAjax(fcom.makeUrl('Classes', 'removeAssignedLessonPlan'), 'ltp_slessonid=' + lessonId, function (t) {
                $.facebox.close();
                searchLessons(document.frmSrch);
            });
        }
    };

    viewAssignedLessonPlan = function (lessonId) {
        fcom.ajax(fcom.makeUrl('Classes', 'viewAssignedLessonPlan', [lessonId]), '', function (t) {
            searchLessons(document.frmSrch);
            $.facebox(t, 'facebox-medium');
        });
    };

    viewCalendar = function (frm) {
        var data = fcom.frmData(frm);
        fcom.ajax(fcom.makeUrl('Classes', 'viewCalendar'), data, function (t) {
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
    console.log('heyyy');
    // if (isUserLogged() == 0) {
    //     $.loader.hide();
    //     logInFormPopUp();
    //     return false;
    // }
    $('#lesson-status').change(function (event) {
     //   window.location.href = fcom.makeUrl('Classes', '', [], confWebRootUrl) + '#' + $('#lesson-status').val();
    });
});