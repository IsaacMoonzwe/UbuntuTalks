$(document).ready(function(){
	searchIssueReportOptions(document.frmIssueReoprtOptions);
});

(function() {
	var active = 1;
	var inActive = 0;
	var runningAjaxReq = false;
	var dv = '#listing';
	
	goToSearchPage = function(page) {	
		if(typeof page==undefined || page == null){
			page =1;
		}
		var frm = document.frmIssueReportOptionsPaging;		
		$(frm.page).val(page);
		searchIssueReportOptions(frm);
	}

	reloadList = function() {
		searchIssueReportOptions();
	};	
	
	searchIssueReportOptions = function(form){		
		var data = '';
		if (form) {
			data = fcom.frmData(form);
		}
		$(dv).html(fcom.getLoader());
		
		fcom.ajax(fcom.makeUrl('IssueReportOptions','search'),data,function(res){
			$(dv).html(res);			
		});
	};
	clearSearch = function(){
		document.frmIssueReoprtOptions.reset();
		searchIssueReportOptions(document.frmIssueReoprtOptions);
	};
	
	
	addIssueReportOptionForm = function(id) {
		$.facebox(function() {
			IssueReoprtOptionForm(id);
		});
	};
	
	IssueReoprtOptionForm = function(id) {
		fcom.displayProcessing();
		fcom.ajax(fcom.makeUrl('IssueReportOptions', 'form', [id]), '', function(t) {
			fcom.updateFaceboxContent(t);
		});
	};
	editOptionFormNew = function(optId){
		$.facebox(function() {	
			editOptionForm(optId);
		});
	};
	
	editOptionForm = function(optId){
		fcom.displayProcessing();
		fcom.ajax(fcom.makeUrl('IssueReportOptions', 'form', [optId]), '', function(t) {
			fcom.updateFaceboxContent(t);
		});
	};
	
	setupIssueOption = function (frm){
		if (!$(frm).validate()) return;		
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('IssueReportOptions', 'setup'), data, function(t) {			
			reloadList();
			if (t.langId > 0) {
				editOptionLangForm(t.optId, t.langId);
				return ;
			}
			$(document).trigger('close.facebox');
		});
	}
	
	editOptionLangForm = function(optId,langId) {
		fcom.displayProcessing();
		fcom.ajax(fcom.makeUrl('IssueReportOptions', 'langForm', [optId, langId]), '', function(t) {
			fcom.updateFaceboxContent(t);
		});
	};
	
	setupLangIssueReport = function (frm) {
		if (!$(frm).validate()) return;		
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('IssueReportOptions', 'langSetup'), data, function(t) {
			reloadList();			
			if (t.langId>0) {
				editOptionLangForm(t.optId, t.langId);
				return ;
			}
						
			$(document).trigger('close.facebox');
		});
	};
	
	deleteRecord = function(id) {
		if(!confirm(langLbl.confirmDelete)){return;}
		data='optId='+id;
		fcom.updateWithAjax(fcom.makeUrl('IssueReportOptions', 'deleteRecord'),data,function(res){		
			reloadList();
		});
	};
	
	activeStatus = function(obj){
		if(!confirm(langLbl.confirmUpdateStatus)){
			e.preventDefault();
			return;
		}
		var optId = parseInt(obj.value);
		if(optId < 1){
			fcom.displayErrorMessage(langLbl.invalidRequest);
			return false;
		}
		data='optId='+optId+"&status="+active;
		fcom.ajax(fcom.makeUrl('IssueReportOptions','changeStatus'),data,function(res) {
		var ans = $.parseJSON(res);
			if(ans.status == 1){
				$(obj).removeClass("inactive");
				$(obj).addClass("active");
				$(".status_"+optId).attr('onclick','inactiveStatus(this)');
				fcom.displaySuccessMessage(ans.msg);				
			}else{
				fcom.displayErrorMessage(ans.msg);				
			}
		});
	};
	
	inactiveStatus = function(obj){
	
	if(!confirm(langLbl.confirmUpdateStatus)){
		e.preventDefault();
		return;
	}
	var optId = parseInt(obj.value);
	if(optId < 1){
		fcom.displayErrorMessage(langLbl.invalidRequest);
		return false;
	}
	data='optId='+optId+"&status="+inActive;
	fcom.ajax(fcom.makeUrl('IssueReportOptions','changeStatus'),data,function(res){
	var ans =$.parseJSON(res);
		if(ans.status == 1){
			$(obj).removeClass("active");
				$(obj).addClass("inactive");
				$(".status_"+optId).attr('onclick','activeStatus(this)');
			fcom.displaySuccessMessage(ans.msg);				
		}else{
			fcom.displayErrorMessage(ans.msg);				
		}
	});
	};
	   
})();	