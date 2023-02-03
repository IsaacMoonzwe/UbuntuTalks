$(document).ready(function(){
	searchLessons(document.frmSrch);
})

var dv = '#listItemsLessons';
function searchLessons (frm){
	$(dv).html(fcom.getLoader());
	var data = fcom.frmData(frm);
	data = data+'&dashboard=1';

	fcom.ajax(fcom.makeUrl('LearnerScheduledLessons','search'),data,function(t){
		$(dv).html(t);
	});
};

function viewCalendar (frm){
	var data = fcom.frmData(frm);
	fcom.ajax(fcom.makeUrl('LearnerScheduledLessons','viewCalendar'),data,function(t){
		$(dv).html(t);
                $('body').addClass('calendar-facebox');
	});
};