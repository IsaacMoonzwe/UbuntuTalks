var timeInterval;
var confFrontEndUrl='https://ubuntutalks.com/';
var FatEventCalendar = function (teacherId) {
    this.teacherId = teacherId;
    var seconds = 2;
    teacherId = teacherId;
    this.calDefaultConf = {
        initialView: 'timeGridWeek',
        headerToolbar: {
            left: 'time',
            center: 'title',
            right: 'prev,next today'
        },
        slotDuration: '00:15',
        slotLabelFormat: {
            hour12: true,
            hour: '2-digit',
            minute: '2-digit',
            meridiem: 'short'
        },
        buttonText: {
            today: langLbl.today,
        },
        views: {
            timeGridWeek: {
                titleFormat: { month: 'short', day: '2-digit', year: 'numeric' }
            }
        },
        direction: layoutDirection,
        nowIndicator: true,
        navLinks: false, 
        eventOverlap: false,
        slotEventOverlap: false,
        selectable: false,
        editable: false,
        selectLongPressDelay: 50,
        eventLongPressDelay: 50,
        longPressDelay: 50,
        allDaySlot: false,
        eventTimeFormat: 'hh:mm a',
        slotLabelFormat: '{hh:mm{a}}',
        loading: function (isLoading) {
            if (isLoading == true) {
                jQuery("#loaderCalendar").show();
            } else {
                jQuery("#loaderCalendar").hide();
            }
        }
    };

    updateTime = function (time, calendarObj) {
        currentTimeStr = moment(time).add(seconds, 'seconds').format('YYYY-MM-DD HH:mm:ss');
        currentTimeStr = calendarObj.formatDate(currentTimeStr, 'hh:mm:ss a')
        jQuery('body').find(".fc-toolbar-ltr h6 span.timer").html(currentTimeStr);
    };

    this.setLocale = function (locale) {
        this.calDefaultConf.locale = locale;
    };

    this.startTimer = function (current_time, calendarObj) {
        clearInterval(timeInterval);
        timeInterval = setInterval(function () {
            this.updateTime(current_time, calendarObj);
            seconds++;
        }, 1000);
    };

    isColliding = function (div1, div2) {
        let d1ffset = div1.offset();
        let d1Top = d1ffset.top + div1.outerHeight(true);

        let d2ffset = div2.offset();
        let d2Top = d2ffset.top + div2.outerHeight(true);
        return (d1ffset.top <= d2ffset.top && d1Top >= d2Top);
    };

    getSlotBookingConfirmationBox = function (calEvent, jsEvent, calendar) {
      
        var startDateTime = calendar.formatDate(calEvent.start, 'LLLL, d, yyyy');

        var start = calendar.formatDate(calEvent.start, 'hh:mm {a}');
        var end = calendar.formatDate(calEvent.end, 'hh:mm {a}');

        var selectedStartDateTime = moment(calEvent.start).format('YYYY-MM-DD HH:mm:ss');
        var selectedEndDateTime = moment(calEvent.end).format('YYYY-MM-DD HH:mm:ss');

        var tooltip = jQuery('.tooltipevent-wrapper-js').html();
        tooltip = tooltip.replace('{{displayEventDate}}', startDateTime);
        tooltip = tooltip.replace('{{displayEventTime}}', start + ' - ' + end);
        tooltip = tooltip.replace('{{selectedStartDateTime}}', selectedStartDateTime);
        tooltip = tooltip.replace('{{selectedEndDateTime}}', selectedEndDateTime);
        tooltip = tooltip.replace('{{selectedDate}}', moment(calEvent.start).format('YYYY-MM-DD'));
        jQuery("body").append(tooltip);
        let tooltipTop = 0, tooltipLeft = 0;
        if (jsEvent.changedTouches) {
            tooltipTop = jsEvent.changedTouches[jsEvent.changedTouches.length - 1].clientY - 110;
            tooltipLeft = jsEvent.changedTouches[jsEvent.changedTouches.length - 1].clientX - 100;
            jQuery('.tooltipevent').css('position', 'fixed');
        } else {
            tooltipTop = jsEvent.pageY - 110;
            tooltipLeft = jsEvent.pageX - 100;
        }
        jQuery('.tooltipevent').css('top', tooltipTop);
        jQuery('.tooltipevent').css('left', tooltipLeft);

        jQuery(this).mouseover(function (e) {
            jQuery(this).css('z-index', 10000);
            jQuery('.tooltipevent').fadeIn('500');
            jQuery('.tooltipevent').fadeTo('10', 1.9);
        });
    };

    eventMerging = function (info, events) {
        var start = info.event.start;
        var end = info.event.end;

        for (i in events) {

            if (events[i]._instance.instanceId == info.oldEvent._instance.instanceId && events[i]._instance.defId == info.oldEvent._instance.defId) {
                continue;
            }

            if (moment(end) >= moment(events[i].start) && moment(start) <= moment(events[i].end)) {

                if (moment(start) > moment(events[i].start)) {
                    start = events[i].start;
                    info.event.setStart(events[i].start);
                }

                if (moment(end) < moment(events[i].end)) {
                    end = events[i].end;
                    info.event.setEnd(events[i].end);
                }

                events[i].remove();
            }
        }
    }

};

FatEventCalendar.prototype.validateSelectedSlot = function (arg, current_time, duration, bookingBefore) {
    var start = arg.startStr;
    var end = arg.endStr;
    var validSelectDateTime = moment(current_time).add(bookingBefore, 'hours').format('YYYY-MM-DD HH:mm:ss');
    var selectedDateTime = moment(start).format('YYYY-MM-DD HH:mm:ss');
    var duration = moment.duration(moment(end).diff(moment(start)));
    var minutesDiff = duration.asMinutes();
    var minutes = duration;
    if (minutesDiff > minutes) {
        return false;
    }
    if (selectedDateTime < validSelectDateTime) {
        return false;
    }

    if (moment(current_time).diff(moment(start)) >= 0 || moment(start).format('YYYY-MM-DD HH:mm:ss') > moment(end).format('YYYY-MM-DD HH:mm:ss')) {
        return false;
    }

    return true;
};

FatEventCalendar.prototype.WeeklyBookingCalendar = function (current_time, duration, bookingBefore) {
    var fecal = this;
    var calConf = {
        now: current_time,
        selectable: true,
        views: {
            timeGridWeek: {
                titleFormat: '{LLL {d}}, yyyy'
            }
        },
        dayHeaderFormat: '{EEE {L/d}}',
        eventSources: [
            {
                events: function (fetchInfo, successCallback, failureCallback) {
                    postData = "start=" + moment(fetchInfo.start).format('YYYY-MM-DD HH:mm:ss') + "&end=" + moment(fetchInfo.end).format('YYYY-MM-DD HH:mm:ss') + "&bookingBefore=" + bookingBefore;

                    fcom.ajax(fcom.makeUrl('Teachers', 'getTeacherWeeklyScheduleJsonData', [fecal.teacherId],confFrontEndUrl), postData, function (docs) {
                        for (i in docs) {
                            docs[i].selectable = false;
                            if ((parseInt(docs[i].classType))) {
                                docs[i].display = 'background';
                                docs[i].selectable = true;
                            }
                            docs[i].editable = false;
                        }
                        successCallback(docs);
                    }, { fOutMode: 'json' });
                }
            },
            {
                events: function (fetchInfo, successCallback, failureCallback) {
                    postData = "start=" + moment(fetchInfo.start).format('YYYY-MM-DD HH:mm:ss') + "&end=" + moment(fetchInfo.end).format('YYYY-MM-DD HH:mm:ss');
                    fcom.ajax(fcom.makeUrl('Teachers', 'getTeacherScheduledLessonData', [fecal.teacherId], confFrontEndUrl), postData, function (docs) {
	                   successCallback(docs);
                    }, { fOutMode: 'json' });
                }
            },
        ],
        select: function (arg) {
            let slotAvailableEl = $(arg.jsEvent.target).parents('.fc-timegrid-col-frame').find('.slot_available');
            if (slotAvailableEl.length == 0) {
                calendar.unselect();
                return false;
            }

            // if(!isColliding($(slotAvailableEl).parent(), $(arg.jsEvent.target))){
            //     calendar.unselect();
            //     return false;
            // }

            jQuery('body #admin_calendar .closeon').click();
            jQuery("#loaderCalendar").show();
            if (checkSlotAvailabiltAjaxRun) {
                calendar.unselect();
                return false;
            }

            var time_diff = arg.end - arg.start;
            var durationMS = FullCalendar.createDuration(duration).milliseconds;
            
            if (time_diff <= durationMS) {
                arg.end = new Date(arg.start);
                var ms = arg.end.getTime() + durationMS;
                arg.end.setTime(ms);
                arg.endStr = moment(arg.end).format('YYYY-MM-DDTHH:mm:ssZ');
            }else{
                jQuery("#loaderCalendar").hide();
                jQuery("body").css({ "cursor": "default" });
                jQuery("body").css({ "pointer-events": "initial" });
                calendar.unselect();
                return false;
            }

            if (!fecal.validateSelectedSlot(arg, current_time, duration, bookingBefore)) {
                jQuery("#loaderCalendar").hide();
                jQuery("body").css({ "cursor": "default" });
                jQuery("body").css({ "pointer-events": "initial" });
                calendar.unselect();
                return false;
            }

            checkSlotAvailabiltAjaxRun = true;
            var newEvent = { start: moment(arg.startStr).format('YYYY-MM-DD HH:mm:ss'), end: moment(arg.endStr).format('YYYY-MM-DD HH:mm:ss') };
            fcom.ajax(fcom.makeUrl('Teachers', 'checkCalendarTimeSlotAvailability', [fecal.teacherId], confFrontEndUrl), newEvent, function (doc) {
                checkSlotAvailabiltAjaxRun = false;
                jQuery("#loaderCalendar").hide();
                jQuery("body").css({ "cursor": "default" });
                jQuery("body").css({ "pointer-events": "initial" });
                var res = JSON.parse(doc);
				console.log(res);
                if (res.status == 1) {
                    this.getSlotBookingConfirmationBox(newEvent, arg.jsEvent, calendar);
                }
                if (res.status == 0) {
                    jQuery('body > .tooltipevent').remove();
                    calendar.unselect();
                }
                if (res.msg && res.msg != "") {
                    jQuery.mbsmessage(res.msg, true, 'alert alert--danger');
                }
            });
        }
    }
    var defaultConf = this.calDefaultConf;
    var conf = { ...defaultConf, ...calConf };

    var calendarEl = document.getElementById('admin_calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, conf);
    if ($(window).width() <= 767) {
        // calendar.setOption('height', 'auto');
    }
    calendar.render();

    jQuery('body').find(".fc-time-button").parent().html("<h6><span>" + langLbl.myTimeZoneLabel + " :-</span> <span class='timer'>" + calendar.formatDate(current_time, 'hh:mm:ss {a}') + "</span><span class='timezoneoffset'>(" + langLbl.timezoneString + " " + timeZoneOffset + ")</span></h6>");
    seconds = 2;
    this.startTimer(current_time, calendar);

    jQuery(".fc-today-button,button.fc-prev-button,button.fc-next-button").click(function () {
        jQuery('body > .tooltipevent').remove();
    });
    jQuery(document).bind('close.facebox', function () {
        jQuery('body > .tooltipevent').remove();
    });


};

FatEventCalendar.prototype.LearnerMonthlyCalendar = function (current_time) {
    var calConf = {
        initialView: '',
        now: current_time,
        dayMaxEvents: 3,
        headerToolbar: {
            left: 'time',
            center: 'title',
            right: 'prev,next'
        },
        views: {
            dayGridMonth: {
                titleFormat: '{LLL}, yyyy'
            }
        },
        dayHeaderFormat: '{EEE}',
        moreLinkText: moreLinkTextLabel,
        events: function (fetchInfo, successCallback, failureCallback) {
            postData = "start=" + moment(fetchInfo.start).format('YYYY-MM-DD HH:mm:ss') + "&end=" + moment(fetchInfo.end).format('YYYY-MM-DD HH:mm:ss');

            fcom.ajax(fcom.makeUrl('LearnerScheduledLessons', 'calendarJsonData'), postData, function (docs) {
                successCallback(docs);
            }, { fOutMode: 'json' });
        },
        select: function (arg) {
            var start = arg.start;
            var end = arg.end;
            if (moment(start).format('d') != moment(end).format('d')) {
                calender.unselect();
                return false;
            }
        }
    }
    var defaultConf = this.calDefaultConf;
    var conf = { ...defaultConf, ...calConf };

    var calendarEl = document.getElementById('admin_calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, conf);

    calendar.render();
};

FatEventCalendar.prototype.TeacherMonthlyCalendar = function (current_time, dayMaxEvents) {
    var calConf = {
        initialView: 'dayGridMonth',
        now: current_time,
        headerToolbar: {
            left: 'time',
            center: 'title',
            right: 'prev,next'
        },
        views: {
            dayGridMonth: {
                titleFormat: '{LLL}, yyyy'
            }
        },
        dayHeaderFormat: '{EEE}',
        moreLinkText: moreLinkTextLabel,
        eventColor: 'green',
        events: function (fetchInfo, successCallback, failureCallback) {
            postData = "start=" + moment(fetchInfo.start).format('YYYY-MM-DD HH:mm:ss') + "&end=" + moment(fetchInfo.end).format('YYYY-MM-DD HH:mm:ss');

            fcom.ajax(fcom.makeUrl('TeacherScheduledLessons', 'calendarJsonData'), postData, function (docs) {
                successCallback(docs);
            }, { fOutMode: 'json' });
        },
        select: function (arg) {
            var start = arg.start;
            var end = arg.end;
            if (moment(start).format('d') != moment(end).format('d')) {
                calender.unselect();
                return false;
            }
        },
        dayMaxEvents: (dayMaxEvents) ? dayMaxEvents : 3
    }
    var defaultConf = this.calDefaultConf;
    var conf = { ...defaultConf, ...calConf };

    var calendarEl = document.getElementById('admin_calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, conf);

    calendar.render();
};

FatEventCalendar.prototype.TeacherGeneralAvailaibility = function (current_time) {
    var calConf = {
        selectable: true,
        editable: true,
        initialDate: '2018-01-07',
        slotEventOverlap: false,
        now: current_time,
        headerToolbar: {
            left: 'time',
            center: '',
            right: ''
        },
        firstDay: 0,
        dayHeaderFormat: '{EEE}',
        eventSources: [
            {
                events: function (fetchInfo, successCallback, failureCallback) {
                    postData = "start=" + moment(fetchInfo.start).format('YYYY-MM-DD HH:mm:ss') + "&end=" + moment(fetchInfo.end).format('YYYY-MM-DD HH:mm:ss');
                    fcom.ajax(fcom.makeUrl('Teachers', 'getTeacherGeneralAvailabilityJsonData', [fecal.teacherId], confFrontEndUrl), postData, function (docs) {
                        successCallback(docs);
                    }, { fOutMode: 'json' });
                }
            }
        ],
        select: function (arg) {
            var start = arg.start;
            var end = arg.end;

            if (moment(start).format('d') != moment(end).format('d') && moment(end).format('YYYY-MM-DD HH:mm') != moment(start).add(1, 'days').format('YYYY-MM-DD 00:00')) {
                calendar.unselect();
                return false;
            }
            var newEvent = new Object();
            newEvent.start = start;//moment(start).format('YYYY-MM-DD')+"T"+moment(start).format('HH:mm:ss');
            newEvent.end = end;//moment(end).format('YYYY-MM-DD')+"T"+moment(end).format('HH:mm:ss'),
            newEvent.classType = 1,
                // newEvent.allday = false;
                newEvent.overlap = false;
            var events = calendar.getEvents();
            for (i in events) {
                if (moment(end) >= moment(events[i].start) && moment(start) <= moment(events[i].end)) {

                    if (moment(start) > moment(events[i].start)) {
                        newEvent.start = moment(events[i].start).format('YYYY-MM-DD') + "T" + moment(events[i].start).format('HH:mm:ss');
                    }

                    if (moment(end) < moment(events[i].end)) {
                        newEvent.end = moment(events[i].end).format('YYYY-MM-DD') + "T" + moment(events[i].end).format('HH:mm:ss');
                    }
                    events[i].remove();
                }
            }

            calendar.addEvent({
                title: '',
                start: newEvent.start,
                end: newEvent.end,
                className: 'slot_available',
                allDay: arg.allDay
            });
        },
        eventDrop: function (info) {
            let calendarStartDateTime = calendar.view.currentStart;
            let calendarEndDateTime = calendar.view.currentEnd;

            if (moment(calendarStartDateTime) > moment(info.event.end) || moment(calendarEndDateTime) < moment(info.event.start)) {
                info.event.remove();
                return;
            }

            if (moment(calendarStartDateTime) > moment(info.event.start)) {

                info.event.setStart(calendarStartDateTime);
            }

            if (moment(calendarEndDateTime) < moment(info.event.end)) {
                info.event.setEnd(calendarEndDateTime);
            }

            var events = calendar.getEvents();
            eventMerging(info, events);
        },

        eventResize: function (info) {
            let calendarStartDateTime = calendar.view.currentStart;
            let calendarEndDateTime = calendar.view.currentEnd;

            if (moment(calendarStartDateTime) > moment(info.event.end) || moment(calendarEndDateTime) < moment(info.event.start)) {
                info.event.remove();
                return;
            }

            if (moment(calendarStartDateTime) > moment(info.event.start)) {

                info.event.setStart(calendarStartDateTime);
            }

            if (moment(calendarEndDateTime) < moment(info.event.end)) {
                info.event.setEnd(calendarEndDateTime);
            }

            var events = calendar.getEvents();
            eventMerging(info, events);

        },
        eventDidMount: function (arg) {
            let calendarStartDateTime = calendar.view.currentStart;
            let calendarEndDateTime = calendar.view.currentEnd;
            let event = arg.event;

            if (moment(calendarStartDateTime) > moment(event.end) || moment(calendarEndDateTime) < moment(event.start)) {
                event.remove();
                return;
            }

            if (moment(calendarStartDateTime) > moment(event.start)) {
                event.setStart(calendarStartDateTime);
            }

            if (moment(calendarEndDateTime) < moment(event.end)) {
                event.setEnd(calendarEndDateTime);
            }

            element = arg.el;

            $(element).find(".fc-event-main-frame").prepend("<span class='closeon'>X</span>");
            $(element).find(".closeon").click(function () {
                if (confirm(langLbl.confirmRemove)) {
                    event.remove();
                }
            });
        }
    }
    var defaultConf = this.calDefaultConf;
    var conf = { ...defaultConf, ...calConf };

    var calendarEl = document.getElementById('ga_calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, conf);

    calendar.render();
    jQuery('body').find(".fc-time-button").parent().html("<h6><span>" + langLbl.myTimeZoneLabel + " :-</span> <span class='timer'>" + calendar.formatDate(current_time, 'hh:mm:ss {a}') + "</span><span class='timezoneoffset'>(" + langLbl.timezoneString + " " + timeZoneOffset + ")</span></h6>");
    seconds = 2;
    this.startTimer(current_time, calendar);
    return calendar;
};

FatEventCalendar.prototype.TeacherWeeklyAvailaibility = function (current_time) {
    var calConf = {
        selectable: true,
        editable: true,
        now: current_time,
        dayHeaderFormat: '{EEE {L/d}}',
        views: {
            timeGridWeek: {
                titleFormat: '{LLL {d}}, yyyy'
            }
        },
        events: function (fetchInfo, successCallback, failureCallback) {
            postData = "start=" + moment(fetchInfo.start).format('YYYY-MM-DD HH:mm:ss') + "&end=" + moment(fetchInfo.end).format('YYYY-MM-DD HH:mm:ss') + "&bookingBefore=0";

            fcom.ajax(fcom.makeUrl('Teachers', 'getTeacherWeeklyScheduleJsonData', [fecal.teacherId], confFrontEndUrl), postData, function (docs) {
                for (i in docs) {
                    docs[i].overlap = false;
                    docs[i].extendedProps = {};
                    docs[i].extendedProps._id = (docs[i]._id) ? docs[i]._id : 0;
                    docs[i].extendedProps.action = docs[i].action;
                    docs[i].extendedProps.classType = docs[i].classType;
                    docs[i].extendedProps.className = docs[i].className;
                }
                successCallback(docs);
            }, { fOutMode: 'json' });
        },
        select: function (arg) {
            var start = arg.start;
            var end = arg.end;
            if (moment(current_time).diff(moment(start)) >= 0) {
                calendar.unselect();
                return false;
            }
            if (moment(start).format('d') != moment(end).format('d') && moment(end).format('YYYY-MM-DD HH:mm') != moment(start).add(1, 'days').format('YYYY-MM-DD 00:00')) {
                calendar.unselect();
                return false;
            }

            var newEvent = new Object();
            newEvent.title = '';
            newEvent.start = moment(start).format('YYYY-MM-DD') + "T" + moment(start).format('HH:mm:ss');
            newEvent.end = moment(end).format('YYYY-MM-DD') + "T" + moment(end).format('HH:mm:ss'),
                newEvent.startTime = moment(start).format('HH:mm:ss');
            newEvent.endTime = moment(end).format('HH:mm:ss'),
                newEvent.daysOfWeek = moment(start).format('d'),
                newEvent.extendedProps = {};
            newEvent.extendedProps._id = 0;
            newEvent.extendedProps.className = 'slot_available',
                newEvent.extendedProps.classType = 1,
                newEvent.extendedProps.action = 'fromGeneralAvailability',
                newEvent.className = 'slot_available',
                newEvent.allday = false;
            newEvent.overlap = false;

            var events = calendar.getEvents();
            for (i in events) {
                if (moment(end) >= moment(events[i].start) && moment(start) <= moment(events[i].end)) {
                    newEvent.extendedProps._id = events[i].extendedProps_id;

                    if (moment(start) > moment(events[i].start)) {
                        newEvent.start = moment(events[i].start).format('YYYY-MM-DD') + "T" + moment(events[i].start).format('HH:mm:ss');
                    }

                    if (moment(end) < moment(events[i].end)) {
                        newEvent.end = moment(events[i].end).format('YYYY-MM-DD') + "T" + moment(events[i].end).format('HH:mm:ss');
                    }
                    events[i].remove();
                }

            }
            // calendar.addEvent(newEvent);
            calendar.addEvent({
                title: '',
                start: newEvent.start,
                overlap: false,
                className: 'slot_available',
                end: newEvent.end,
                allDay: arg.allDay,
                extendedProps: newEvent.extendedProps
            });
        },
        eventDrop: function (info) {
            var events = calendar.getEvents();
            eventMerging(info, events);
        },

        eventResize: function (info) {
            var events = calendar.getEvents();
            eventMerging(info, events);

        },
        eventDidMount: function (arg) {
            let element = arg.el;
            $(element).find(".fc-event-main-frame").prepend("<span class='closeon'>X</span>");
            $(element).find(".closeon").on("click", function (evt) {
                let event = arg.event;
                if (parseInt(event.extendedProps._id) > 0) {
                    let element = arg.el;
                    let confirmMsg = langLbl.disableSlot;
                    let newClassType = 0;
                    let className = "slot_unavailable";
                    if (parseInt(event.extendedProps.classType) == 0) {
                        confirmMsg = langLbl.enableSlot;
                        newClassType = 1;
                        className = "slot_available";
                    }

                    if (confirm(confirmMsg)) {
                        event.setExtendedProp('classType', newClassType);
                        $(element).addClass(className);
                        $(element).removeClass(event.extendedProps.className);
                        event.setExtendedProp('className', className);
                    }
                } else {
                    event.remove();
                }
            });



        }
    }
    var defaultConf = this.calDefaultConf;
    var conf = { ...defaultConf, ...calConf };

    var calendarEl = document.getElementById('w_calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, conf);

    calendar.render();
    jQuery('body').find(".fc-time-button").parent().html("<h6><span>" + langLbl.myTimeZoneLabel + " :-</span> <span class='timer'>" + calendar.formatDate(current_time, 'hh:mm:ss {a}') + "</span><span class='timezoneoffset'>(" + langLbl.timezoneString + " " + timeZoneOffset + ")</span></h6>");
    seconds = 2;
    this.startTimer(current_time, calendar);
    return calendar;
};