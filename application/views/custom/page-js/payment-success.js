(function() {

	viewBookingCalendar = function(id, action = '') {

		var data = { 'action': action };
	
		$.mbsmessage(langLbl.processing, true, 'alert alert--process');
		fcom.ajax(fcom.makeUrl('LearnerScheduledLessons', 'viewPaymentCalendar', [id],confWebDashUrl), data, function (t) {
			$.mbsmessage.close();
			console.log(t);
			$.facebox(t, 'facebox-large booking-calendar-pop-js');
		});
	};

	setUpLessonSchedule = function (teacherId, lDetailId, startTime, endTime, date) {

		$.mbsmessage.close();
		$.loader.show();
		$.mbsmessage(langLbl.requestProcessing, false, 'alert alert--process');
		fcom.ajax(fcom.makeUrl('LearnerScheduledLessons', 'isSlotTaken',[],confWebDashUrl), 'teacherId=' + teacherId + '&startTime=' + startTime + '&endTime=' + endTime + '&date=' + date, function (t) {
			t = JSON.parse(t);
			slot = t.count;
	
			var ajaxData = 'teacherId=' + teacherId + '&lDetailId=' + lDetailId + '&startTime=' + startTime + '&endTime=' + endTime + '&date=' + date;
	
	
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
								fcom.updateWithAjax(fcom.makeUrl('LearnerScheduledLessons', 'setUpLessonSchedule'), ajaxData, function (doc) {
									window.location.href = fcom.makeUrl('LearnerScheduledLessons') + '#' + statusScheduled;
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
				fcom.updateWithAjax(fcom.makeUrl('LearnerScheduledLessons', 'setUpLessonSchedule',[],confWebDashUrl), ajaxData, function (doc) {
					window.location.href = fcom.makeUrl('LearnerScheduledLessons','index',[],confWebDashUrl) + '#' + statusScheduled;
				});
			}
		});
	};

})();