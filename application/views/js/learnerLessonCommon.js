var isLessonCancelAjaxRun = false;
var isRescheduleRequest = (isRescheduleRequest) ? true : false;

lessonFeedback = function (lDetailId) {
    fcom.ajax(fcom.makeUrl('Classes', 'lessonFeedback', [lDetailId]), '', function (t) {
        $.facebox(t, 'facebox-medium');
    });
};

setupLessonFeedback = function (frm) {
    if (!$(frm).validate()) {
        return false;
    }

    var data = fcom.frmData(frm);

    fcom.updateWithAjax(fcom.makeUrl('Classes', 'setupLessonFeedback'), data, function (t) {
        $.facebox.close();
        window.location.href = fcom.makeUrl('Classes') + '#' + statusCompleted;
        location.reload();
    });
};

requestReschedule = function (id) {
    fcom.ajax(fcom.makeUrl('Classes', 'requestReschedule', [id]), '', function (t) {
        $.facebox(t, 'facebox-large booking-calendar-pop-js');
    });
};

requestRescheduleSetup = function (frm) {
    if (!$(frm).validate())
        return;
    var data = fcom.frmData(frm);
    fcom.updateWithAjax(fcom.makeUrl('Classes', 'requestRescheduleSetup'), data, function (t) {
        $.facebox.close();
        window.location.href = fcom.makeUrl('Classes') + '#' + statusScheduled;
        location.reload();
    });
};

viewBookingCalendar = function (id, action = '') {
    var data = { 'action': action };
    $.mbsmessage(langLbl.processing, false, 'alert alert--process');
    fcom.ajax(fcom.makeUrl('Classes', 'viewBookingCalendar', [id]), data, function (t) {
        $.mbsmessage.close();
        $.facebox(t, 'facebox-large booking-calendar-pop-js');
        $('body').addClass('calendar-facebox');
    });
};

var slot = 0;

setUpLessonSchedule = function (teacherId, lDetailId, startTime, endTime, date) {
    rescheduleReason = '';
    if (isRescheduleRequest) {
        var rescheduleReason = $('#reschedule-reason-js').val();
        if ($.trim(rescheduleReason) == "") {
            alert(langLbl.requriedRescheduleMesssage);
            $('.booking-calendar-pop-js').animate({
                scrollTop: $("#loaderCalendar").offset().top
            }, 500);
            return false;
        }
    }
    $.mbsmessage.close();
    $.loader.show();
    $.mbsmessage(langLbl.requestProcessing, false, 'alert alert--process');
    fcom.ajax(fcom.makeUrl('Classes', 'isSlotTaken'), 'teacherId=' + teacherId + '&startTime=' + startTime + '&endTime=' + endTime + '&date=' + date, function (t) {
        t = JSON.parse(t);
        slot = t.count;

        var ajaxData = 'teacherId=' + teacherId + '&lDetailId=' + lDetailId + '&startTime=' + startTime + '&endTime=' + endTime + '&date=' + date;

        if (isRescheduleRequest) {
            ajaxData += '&rescheduleReason=' + rescheduleReason + '&isRescheduleRequest=' + isRescheduleRequest;

        }

        if (slot > 0) {
            $.mbsmessage.close();
            $.loader.hide();
            $.confirm({
                title: langLbl.Confirm,
                content: langLbl.bookedSlotAlert,
                buttons: {
                    Proceed: {
                        text: langLbl.Proceed,
                        btnClass: 'btn btn--primary',
                        keys: ['enter', 'shift'],
                        action: function () {
                            $.loader.show();
                            $.mbsmessage(langLbl.requestProcessing, false, 'alert alert--process');
                            fcom.updateWithAjax(fcom.makeUrl('Classes', 'setUpLessonSchedule'), ajaxData, function (doc) {
                                window.location.href = fcom.makeUrl('Classes') + '#' + statusScheduled;
                                location.reload();
                            });
                        }
                    },
                    Quit: {
                        text: langLbl.Quit,
                        btnClass: 'btn btn--secondary',
                        keys: ['enter', 'shift'],
                        action: function () {
                        }
                    }
                }
            });
        } else {

            fcom.updateWithAjax(fcom.makeUrl('Classes', 'setUpLessonSchedule'), ajaxData, function (doc) {
                window.location.href = fcom.makeUrl('Classes') + '#' + statusScheduled;
                location.reload();
            });
        }

    });
};

cancelLesson = function (id) {
    isLessonCancelAjaxRun = false;
    fcom.ajax(fcom.makeUrl('Classes', 'cancelLesson', [id]), '', function (t) {
        $.facebox(t, 'facebox-medium cancelLesson');
    });
};

closeCancelLessonPopup = function (obj) {
    $.facebox.close();
    isLessonCancelAjaxRun = false;
}

cancelLessonSetup = function (frm) {
    if (isLessonCancelAjaxRun) {
        return false;
    }

    if (!$(frm).validate()){
        return;
    }
           
    isLessonCancelAjaxRun = true;
    
    var data = fcom.frmData(frm);
    fcom.ajax(fcom.makeUrl('Classes', 'cancelLessonSetup'), data, function (ans) {
        isLessonCancelAjaxRun = false;
        if (ans.status != 1) {
            $(document).trigger('close.mbsmessage');
            $.mbsmessage(ans.msg, true, 'alert alert--danger');
            /* Custom Code[ */
            if (ans.redirectUrl) {
                setTimeout(function () {
                    window.location.href = ans.redirectUrl
                }, 3000);
            }
            /* ] */
            return;
        }
        $.mbsmessage(ans.msg, true, 'alert alert--success');
        $.facebox.close();
        window.location.href = fcom.makeUrl('Classes') + '#' + statusUpcoming;
        location.reload();
    }, { fOutMode: 'json' });

    // fcom.updateWithAjax(fcom.makeUrl('Classes', 'cancelLessonSetup'), data , function(t) {
    // 		$.facebox.close();
    // 		location.reload();
    // });
};

issueReported = function (id) {
    fcom.ajax(fcom.makeUrl('ReportIssue', 'form', [id]), '', function (res) {
        if (isJson(res)) {
            var response = JSON.parse(res);
            if (response.status == 1) {
                $.mbsmessage(response.msg, true, 'alert alert--success');
            } else {
                $.mbsmessage(response.msg, true, 'alert alert--danger');
            }
        } else {
            $.facebox(res, 'facebox-medium');
        }
    });
};

issueReportedSetup = function (frm) {
    if (!$(frm).validate()) {
        return;
    }
    var action = fcom.makeUrl('ReportIssue', 'setup');
    fcom.updateWithAjax(action, fcom.frmData(frm), function (response) {
        $.facebox.close();
        if (response.status == 0) {
            return $.mbsmessage(response.msg, true, 'alert alert--danger');
        }
        $.mbsmessage(response.msg, true, 'alert alert--success');
        $("#lesson-status").length ? $("#lesson-status").val(statusIssueReported).trigger('change') : window.location.reload();
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

esclateForm = function (id) {
    fcom.ajax(fcom.makeUrl('ReportIssue', 'esclateForm', [id]), '', function (res) {
        if (isJson(res)) {
            var response = JSON.parse(res);
            if (response.status == 1) {
                $.mbsmessage(response.msg, true, 'alert alert--success');
            } else {
                $.mbsmessage(response.msg, true, 'alert alert--danger');
            }
        } else {
            $.facebox(res, 'facebox-medium');
        }
    });
};

esclateSetup = function (frm) {
    if (!$(frm).validate()) {
        return;
    }
    var action = fcom.makeUrl('ReportIssue', 'esclateSetup');
    fcom.updateWithAjax(action, fcom.frmData(frm), function (response) {
        $.facebox.close();
        if (response.status == 1) {
            $.mbsmessage(response.msg, true, 'alert alert--success');
        } else {
            $.mbsmessage(response.msg, true, 'alert alert--danger');
        }
    });
};
