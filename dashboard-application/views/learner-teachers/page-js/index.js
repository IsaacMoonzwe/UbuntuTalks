$(function() {
	var dv = '#listItems';
	searchTeachers = function(frm){
		$(dv).html(fcom.getLoader());
		var data = fcom.frmData(frm);
		fcom.ajax(fcom.makeUrl('LearnerTeachers','search'),data,function(t){
			$(dv).html(t);
		});
	};
	
	clearSearch = function(){
		document.frmSrch.reset();
		searchTeachers( document.frmSrch );
	}
	
	goToSearchPage = function(page) {
		if(typeof page == undefined || page == null){
			page = 1;
		}		
		var frm = document.frmLearnerTeachersSearchPaging;		
		$(frm.page).val(page);
		searchTeachers(frm);
	};
	
	searchTeachers(document.frmSrch);
});


