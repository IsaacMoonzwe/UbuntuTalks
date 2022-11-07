$(function() {
	var dv = '#listItemsLessons';
	searchLessons = function(frm){
		$('.calender-lessons-js a').removeClass('is-active');
		$('.list-js').addClass('is-active');
		$(dv).html(fcom.getLoader());
		var data = fcom.frmData(frm);
		fcom.ajax(fcom.makeUrl('LearnerGroupClasses','search'),data,function(t){
			$(dv).html(t);
            $('.countdowntimer').each(function (i) {
                $(this).countdowntimer({
                    startDate : $(this).data('starttime'),
                    dateAndTime : $(this).data('endtime'),
                    size : "sm",
                });
            });
		});
	};

	assignLessonPlanToLessons = function(lessonId,planId){
		fcom.updateWithAjax(fcom.makeUrl('LearnerScheduledLessons', 'assignLessonPlanToLessons'), 'ltp_slessonid='+lessonId+'&ltp_tlpn_id='+planId , function(t) {
				$.facebox.close();
				searchLessons(document.frmSrch);
		});
	};

	removeAssignedLessonPlan = function(lessonId){
		if(confirm(langLbl.confirmRemove))
		{
			fcom.updateWithAjax(fcom.makeUrl('LearnerScheduledLessons', 'removeAssignedLessonPlan'), 'ltp_slessonid='+lessonId , function(t) {
					$.facebox.close();
					searchLessons(document.frmSrch);
			});
		}
	};

	viewAssignedLessonPlan = function(lessonId){
		fcom.ajax(fcom.makeUrl('LearnerScheduledLessons', 'viewAssignedLessonPlan',[lessonId]), '', function(t) {
				searchLessons(document.frmSrch);
			$.facebox( t,'facebox-medium');
		});
	};

	viewCalendar = function(frm){
		var data = fcom.frmData(frm);
		fcom.ajax(fcom.makeUrl('LearnerScheduledLessons','viewCalendar'),data,function(t){
			$(dv).html(t);
                        $('body').addClass('calendar-facebox');
		});
	};

	clearSearch = function(){
		document.frmSrch.reset();
		searchLessons( document.frmSrch );
	};

	goToSearchPage = function(page) {
		if(typeof page == undefined || page == null){
			page = 1;
		}
		var frm = document.frmSLnsSearchPaging;
		$(frm.page).val(page);
		searchLessons(frm);
	};
	searchLessons(document.frmSrch);
});


$(document).on('click','.tab-swticher a',function(){
	$('.calender-lessons-js a').removeClass('is-active');
	$(this).addClass('is-active');
});
