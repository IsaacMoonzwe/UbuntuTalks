(function () {

	var dv = '#availability-calendar-js';
	teacherGeneralAvailability = function () {
		$(dv).html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('Teacher', 'teacherGeneralAvailability'), '', function (t) {
			$(dv).html(t);
			getTeacherProfileProgress();
		});
	};

	setupTeacherWeeklySchedule = function (frm) {
		$(dv).html(fcom.getLoader());
		fcom.updateWithAjax(fcom.makeUrl('Teacher', 'setupTeacherWeeklySchedule'), 'data=' + frm, function (t) {
			teacherWeeklySchedule()
		});
	};

	setUpWeeklyAvailability = function () {
		var json = JSON.stringify(calendar.getEvents().map(function (e) {

			return {
				start: moment(e.start).format('HH:mm:ss'),
				end: moment(e.end).format('HH:mm:ss'),
				date: moment(e.start).format('YYYY-MM-DD'),
				endDate: moment(e.end).format('YYYY-MM-DD'),
				_id: e.extendedProps._id,
				action: e.extendedProps.action,
				classtype: e.extendedProps.classType,
			};
		}));

		setupTeacherWeeklySchedule(json);
	};


	setupTeacherGeneralAvailability = function (frm) {
		$(dv).html(fcom.getLoader());
		fcom.updateWithAjax(fcom.makeUrl('Teacher', 'setupTeacherGeneralAvailability'), 'data=' + frm, function (t) {
			teacherGeneralAvailability();
			//$("#ga_calendar").fullCalendar("refetchEvents");
		});
	};

	saveGeneralAvailability = function () {
		var allevents = calendar.getEvents();
		let data = allevents.map(function (e) {
			return {
				start: moment(e.start).format('HH:mm:ss'),
				end: moment(e.end).format('HH:mm:ss'),
				startTime: moment(e.start).format('HH:mm'),
				endTime: moment(e.end).format('HH:mm'),
				day: moment(e.start).format('d'),
				startDateTime: moment(e.start).format('YYYY-MM-DD HH:mm:ss'),
				endDateTime: moment(e.end).format('YYYY-MM-DD HH:mm:ss'),
				dayStart: moment(e.start).format('d'),
				dayEnd: moment(e.end).format('d'),
				classtype: e.classType,
			};
		});
		var json = JSON.stringify(data);

		setupTeacherGeneralAvailability(json);
	};



	deleteTeacherGeneralAvailability = function (event) {
		if (confirm(langLbl['confirmRemove'])) {
			event.remove();
		}
	};

	teacherWeeklySchedule = function () {
		$(dv).html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('Teacher', 'teacherWeeklySchedule'), '', function (t) {
			$(dv).html(t);

		});
	};

	getTeacherProfileProgress = function () {
		fcom.ajax(fcom.makeUrl('Teacher', 'getTeacherProfileProgress'), '', function (data) {
			tpp = data.teacherProfileProgress;
			$.each(tpp, function (key, value) {
				switch (key) {
					case 'isProfileCompleted':
						if (value) {
							$('.is-profile-complete-js').removeClass('infobar__media-icon--alert').addClass('infobar__media-icon--tick');
							$('.is-profile-complete-js').html('');
							$('.aside--progress--menu').addClass('is-completed');
						} else {
							$('.is-profile-complete-js').removeClass('infobar__media-icon--tick').addClass('infobar__media-icon--alert');
							$('.is-profile-complete-js').html('!');
						}
						break;
					case 'generalAvailabilityCount':
						value = parseInt(value);
						if (0 >= value) {
							$('.availability-setting-js').removeClass('is-completed');
						} else {
							$('.availability-setting-js').addClass('is-completed');

						}
						break;
					case 'totalFilledFields':
						$('.progress__step').removeClass('is-active');
						for (let totalFilledFields = 0; totalFilledFields < value; totalFilledFields++) {
							$('.progress__step').eq(totalFilledFields).addClass('is-active');
						}
						value = tpp.totalFilledFields + "/" + tpp.totalFields;
						$('.progress-count-js').text(value);

						if ((parseInt(tpp.isProfileCompleted) == 1) || (parseInt(tpp.totalFilledFields) == (parseInt(tpp.totalFields) - 1) && parseInt(tpp.generalAvailabilityCount) == 0)) {
							$('.profile-setting-js').addClass('is-completed');
						} else {
							$('.profile-setting-js').removeClass('is-completed');
						}
						break;
				}
			});
		}, { fOutMode: 'json' });
	}
	teacherGeneralAvailability();
})();