$(document).ready(function(){
	searchTeacherRequests(document.frmSrch);
});

(function() {
	var currentPage = 1;
	searchTeacherRequests = function(form,page){
		if (!page) {
			page = currentPage;
		}
		currentPage = page;	
		var data = '';
		if (form) {
			data = fcom.frmData(form);
		}
		
		$("#listing").html(fcom.getLoader());
		
		fcom.ajax(fcom.makeUrl('TeacherRequests','search'),data,function(res){
			$("#listing").html(res);
		});
	};
	
	clearTeacherRequestSearch = function(){
		document.frmSrch.reset();
		searchTeacherRequests( document.frmSrch );
	};
	
	viewTeacherRequest = function(utrequest_id){
		$.facebox(function() {
			fcom.ajax(fcom.makeUrl('TeacherRequests','view',[utrequest_id]),'',function(t){
				fcom.updateFaceboxContent(t);
			});	
		});		
	};
	
	teacherRequestUpdateForm = function(utrequest_id){
		$.facebox(function() {
			fcom.ajax(fcom.makeUrl('TeacherRequests','teacherRequestUpdateForm',[utrequest_id]),'',function(t){
				fcom.updateFaceboxContent(t);
			});	
		});
	};
	
	showHideCommentBox = function(val){
		if( val == STATUS_CANCELLED ){
			$('#div_comments_box').removeClass('hide');
		} else {
			$('#div_comments_box').addClass('hide');
		}		
	};
	
	reloadList = function() {
		searchTeacherRequests(document.frmSearchPaging, currentPage);
	};
	
	setUpTeacherRequestStatus = function( frm ){
		if ( !$(frm).validate() ){ return; }
		fcom.updateWithAjax(fcom.makeUrl('TeacherRequests', 'setUpTeacherRequestStatus'), fcom.frmData(frm), function(res) {
			reloadList();			
			$(document).trigger('close.facebox');
		});
	};
	
	searchQualifications = function(utrequest_user_id){
		$.facebox(function() {
			fcom.ajax(fcom.makeUrl('TeacherRequests','searchQualifications',[utrequest_user_id]),'',function(t){
				fcom.updateFaceboxContent(t);
			});	
		});
	}
    
    goToSearchPage = function(page) {	
        if(typeof page == undefined || page == null){
            page = 1;
        }		
        var frm = document.frmSearchPaging;		
        $(frm.page).val(page);
        searchTeacherRequests(frm);
    };
	
})();