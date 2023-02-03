searchLessons = null;
var isLessonCancelAjaxRun = false;
requestReschedule = function (id) {
    fcom.ajax(fcom.makeUrl('TeacherScheduledLessons', 'requestReschedule', [id]), '', function (t) {
        $.facebox(t, 'facebox-medium');
    });
};

requestRescheduleSetup = function (frm) {
    if (!$(frm).validate())
        return;
    var data = fcom.frmData(frm);
    fcom.updateWithAjax(fcom.makeUrl('TeacherScheduledLessons', 'requestRescheduleSetup'), data, function (t) {
        $.facebox.close();
        location.reload();
    });
};

issueReported = function (id) {
    fcom.ajax(fcom.makeUrl('TeacherScheduledLessons', 'issueReported', [id]), '', function (t) {
        $.facebox(t, 'facebox-medium');
    });
};

issueReportedSetup = function (frm) {
    if (!$(frm).validate())
        return;
    var data = fcom.frmData(frm);
    fcom.updateWithAjax(fcom.makeUrl('TeacherScheduledLessons', 'issueReportedSetup'), data, function (t) {
        $.facebox.close();
        location.reload();
    });
};

cancelLesson = function (id) {
    fcom.ajax(fcom.makeUrl('TeacherScheduledLessons', 'cancelLesson', [id]), '', function (t) {
        $.facebox(t, 'facebox-medium');
    });
};

cancelLessonSetup = function (frm) {
    if (isLessonCancelAjaxRun) {
        return false;
    }
    if (!$(frm).validate()){
        return;
    }
    

    isLessonCancelAjaxRun = true;
  
    var data = fcom.frmData(frm);
    fcom.updateWithAjax(fcom.makeUrl('TeacherScheduledLessons', 'cancelLessonSetup'), data, function (t) {
        $.facebox.close();
        location.reload();
    });
};

viewBookingCalendar = function (id) {
    $.mbsmessage(langLbl.processing, true, 'alert alert--process');
    fcom.ajax(fcom.makeUrl('TeacherScheduledLessons', 'viewBookingCalendar', [id]), '', function (t) {
        $.mbsmessage.close();
        $.facebox(t, 'facebox-medium');
        $('body').addClass('calendar-facebox');
    });
};

listLessonPlans = function (id) {
    fcom.ajax(fcom.makeUrl('TeacherScheduledLessons', 'listLessonPlans', [id]), '', function (t) {
        $.facebox(t, 'facebox-medium');
    });
};

changeLessonPlan = function (id) {
    fcom.ajax(fcom.makeUrl('TeacherScheduledLessons', 'changeLessonPlan', [id]), '', function (t) {
        $.facebox(t, 'facebox-medium');
    });
};

assignLessonPlanToLessons = function (lessonId, planId) {
    var data = 'ltp_slessonid=' + lessonId + '&ltp_tlpn_id=' + planId;
    fcom.updateWithAjax(fcom.makeUrl('TeacherScheduledLessons', 'assignLessonPlanToLessons'), data, function (t) {
        $.facebox.close();
        if (searchLessons != null) {
            searchLessons(document.frmSrch);
        } else {
            window.location.reload();
        }
    });
};

removeAssignedLessonPlan = function (lessonId) {
    if (confirm(langLbl.confirmRemove)) {
        fcom.updateWithAjax(fcom.makeUrl('TeacherScheduledLessons', 'removeAssignedLessonPlan'), 'ltp_slessonid=' + lessonId, function (t) {
            $.facebox.close();
            if (searchLessons != null) {
                searchLessons(document.frmSrch);
            } else {
                window.location.reload();
            }
        });
    }
};

viewAssignedLessonPlan = function (lessonId) {
    fcom.ajax(fcom.makeUrl('TeacherScheduledLessons', 'viewAssignedLessonPlan', [lessonId]), '', function (t) {
        $.facebox(t, 'facebox-medium');
    });
};

scheduleLessonSetup = function (lessonId, startTime, endTime, date) {
    fcom.ajax(fcom.makeUrl('TeacherScheduledLessons', 'scheduleLessonSetup'), 'lessonId=' + lessonId + '&startTime=' + startTime + '&endTime=' + endTime + '&date=' + date, function (doc) {
        $.facebox.close();
        location.reload();
    });
};

issueDetails = function (id) {
    $.mbsmessage(langLbl.processing, true, 'alert alert--process');
    fcom.ajax(fcom.makeUrl('ReportIssue', 'detail', [id]), '', function (response) {
        if (isJson(response)) {
            var res = JSON.parse(response);
            if (res.status == 1) {
                $.mbsmessage(res.msg, true, 'alert alert--success');
            } else {
                $.mbsmessage(res.msg, true, 'alert alert--danger');
            }
        } else {
            $.mbsmessage.close();
            $.facebox(response, 'facebox-medium issueDetailPopup');
        }
    });
};

resolveForm = function (id) {
    $.mbsmessage(langLbl.processing, true, 'alert alert--process');
    fcom.ajax(fcom.makeUrl('ReportIssue', 'resolveForm', [id]), '', function (response) {
        if (isJson(response)) {
            var res = JSON.parse(response);
            if (res.status == 1) {
                $.mbsmessage(res.msg, true, 'alert alert--success');
            } else {
                $.mbsmessage(res.msg, true, 'alert alert--danger');
            }
        } else {
            $.mbsmessage.close();
            $.facebox(response, 'facebox-medium issueDetailPopup');
        }
    });
};

resolveSetup = function (frm) {
    if (!$(frm).validate()) {
        return false;
    }
    fcom.updateWithAjax(fcom.makeUrl('ReportIssue', 'resolveSetup'), fcom.frmData(frm), function (t) {
        $.facebox.close();
        if (t.status == 0) {
            return $.mbsmessage(t.msg, true, 'alert alert--danger');
        }
        $.mbsmessage(t.msg, true, 'alert alert--success');
        window.location.reload();
    });
};

issueResolveSetup = function (frm) {
    if (!$(frm).validate())
        return;
    var issue_id = $('input[name="issue_id"]').val();
    var slesson_id = $('input[name="slesson_id"]').val();
    var data = fcom.frmData(frm);

    fcom.updateWithAjax(fcom.makeUrl('TeacherIssueReported', 'issueResolveSetup'), data, function (t) {
        $.facebox.close();
        window.location.reload();
    });

};

issueResolveStepTwo = function (issueId, slesson_id) {
    fcom.ajax(fcom.makeUrl('TeacherIssueReported', 'issueResolveStepTwo', [issueId, slesson_id]), '', function (t) {
        $.facebox(t, 'facebox-medium');
    });
};

issueResolveSetupStepTwo = function (frm) {
    if (!$(frm).validate())
        return;
    var data = fcom.frmData(frm);
    fcom.updateWithAjax(fcom.makeUrl('TeacherIssueReported', 'issueResolveSetupStepTwo'), data, function (t) {
        $.facebox.close();
        window.location.reload();
    });
};
reportIssueToAdmin = function (issueId, lessonId, escalated_by) {
    fcom.updateWithAjax(fcom.makeUrl('LearnerScheduledLessons', 'reportIssueToAdmin', [issueId, lessonId, escalated_by]), '', function (t) {
        $.facebox.close();
        location.reload();
    });
};


$(document).ready(function () {
    $(document).on("click", '.iss_accordion', function () {
        this.classList.toggle("active");
        var panel = this.nextElementSibling;
        if (panel.style.display === "block") {
            panel.style.display = "none";
        } else {
            panel.style.display = "block";
        }
    });
});