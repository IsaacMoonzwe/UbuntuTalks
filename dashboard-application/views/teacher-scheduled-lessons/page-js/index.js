$(function() {
	var dv = '#listItemsLessons';
    
    getLessonsByStatus = function(lStatus){
        $('[name=status]').val(lStatus);
        searchLessons(document.frmSrch);
    };
    
    searchAllStatusLessons = function(frm){
        $('.lessons-list-tabs--js li').removeClass('is-active').first().addClass('is-active');
        frm.status.value='';
        searchLessons(frm);
    };
    
	searchLessons = function(frm){
		$(dv).html(fcom.getLoader());
		$('.calender-js').removeClass('is-active');
		$('.list-js').addClass('is-active');
		var data = fcom.frmData(frm);
		fcom.ajax(fcom.makeUrl('TeacherScheduledLessons','search'),data,function(t){
			$(dv).html(t);
		});
	};

	remove = function(elem,id){
		if(confirm(langLbl.confirmRemove))
		{
			$(elem).closest('tr').remove();
			$(dv).html(fcom.getLoader());
			fcom.ajax(fcom.makeUrl('TeacherLessonsPlan','remove',[id]),'',function(t){
				searchLessons(document.frmSrch);
			});
		}
	};

	viewCalendar = function(frm){
		var data = fcom.frmData(frm);
		$('.tab-switch__item').removeClass('is-active');
		$('.calender-js').addClass('is-active');
		fcom.ajax(fcom.makeUrl('TeacherScheduledLessons','viewCalendar'),data,function(t){
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

	loadLessonsTab = function () {
        let urlHashVal = window.location.hash.replace('#', '');
        let activeTab = urlHashVal ? urlHashVal : '';
        $('#lesson-status option[value="'+activeTab+'"]').prop('selected', true);
		searchLessons(document.frmSrch);
    }
	// clearSearch();
	loadLessonsTab();
});
$(document).ready(function () {
    $('#lesson-status').change(function (event) {
        window.location.href = fcom.makeUrl('TeacherScheduledLessons', '', [], confWebRootUrl) + '#' + $('#lesson-status').val();
    });
});
