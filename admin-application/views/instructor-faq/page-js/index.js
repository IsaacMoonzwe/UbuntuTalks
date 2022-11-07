$(document).ready(function(){
	searchFaq(document.frmFaqearch);
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
		var frm = document.frmFaqearchPaging;		
		$(frm.page).val(page);
		searchFaq(frm);
	}

	reloadList = function() {
		searchFaq();
	};	
	
	searchFaq = function(form){		
		var data = '';
		if (form) {
			data = fcom.frmData(form);
		}
		$(dv).html(fcom.getLoader());
		
		fcom.ajax(fcom.makeUrl('InstructorFaq','search'),data,function(res){
			 
			$(dv).html(res);			
		});
	};
	addFaqForm = function(id) {
		$.facebox(function() { FaqForm(id);
		});
	};
	
	FaqForm = function(id) {
		fcom.displayProcessing();
		//$.facebox(function() {
			fcom.ajax(fcom.makeUrl('InstructorFaq', 'form', [id]), '', function(t) {
				//$.facebox(t,'faceboxWidth');
				fcom.updateFaceboxContent(t);
			});
		//});
	};
	editFaqFormNew = function(faqId){
		$.facebox(function() {	editFaqForm(faqId);
		});
	};
	
	editFaqForm = function(faqId){
		fcom.displayProcessing();
		//$.facebox(function() {
			fcom.ajax(fcom.makeUrl('InstructorFaq', 'form', [faqId]), '', function(t) {
				//$.facebox(t,'faceboxWidth');
				fcom.updateFaceboxContent(t);
			});
		//});
	};
	
	setupFaq = function (frm){
		if (!$(frm).validate()) return;		
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('InstructorFaq', 'setup'), data, function(t) {	
			//$.mbsmessage.close();
			reloadList();
			if (t.langId>0) {
				console.log(t.faqId + ',' +   t.langId);
				editFaqLangForm(t.faqId, t.langId);
				return ;
			}
				
			$(document).trigger('close.facebox');
		});
	}
	
	editFaqLangForm = function(faqId,langId){
		fcom.displayProcessing();
	//	$.facebox(function() {
		console.log(faqId + ', ' + langId);
			fcom.ajax(fcom.makeUrl('InstructorFaq', 'langForm', [faqId,langId]), '', function(t) {
				console.log("Hello Lang Form");
				//$.facebox(t,'faceboxWidth');
				fcom.updateFaceboxContent(t);
			});
		//});
	};
	
	setupLangFaq = function (frm){
		if (!$(frm).validate()) return;		
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('InstructorFaq', 'langSetup'), data, function(t) {
			reloadList();			
			if (t.langId>0) {
				editFaqLangForm(t.faqId, t.langId);
				return ;
			}
						
			$(document).trigger('close.facebox');
		});
	};
	
	deleteRecord = function(id){
		if(!confirm(langLbl.confirmDelete)){return;}
		data='faqId='+id;
		console.log(data);
		fcom.updateWithAjax(fcom.makeUrl('InstructorFaq', 'deleteRecord'),data,function(res){	
			console.log("Hello Delete");
			reloadList();
		});
	};
	
	activeStatus = function(obj){
		
		if(!confirm(langLbl.confirmUpdateStatus)){
			e.preventDefault();
			return;
		}
		var faqId = parseInt(obj.value);
		if(faqId < 1){

			//$.mbsmessage(langLbl.invalidRequest,true,'alert--danger');
			fcom.displayErrorMessage(langLbl.invalidRequest);
			return false;
		}
		data='faqId='+faqId+"&status="+active;
		fcom.ajax(fcom.makeUrl('InstructorFaq','changeStatus'),data,function(res){
		var ans =$.parseJSON(res);
			if(ans.status == 1){
				$(obj).removeClass("inactive");
				$(obj).addClass("active");
				$(".status_"+faqId).attr('onclick','inactiveStatus(this)');
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
	var faqId = parseInt(obj.value);
	if(faqId < 1){

		//$.mbsmessage(langLbl.invalidRequest,true,'alert--danger');
		fcom.displayErrorMessage(langLbl.invalidRequest);
		return false;
	}
	data='faqId='+faqId+"&status="+inActive;
	fcom.ajax(fcom.makeUrl('InstructorFaq','changeStatus'),data,function(res){
	var ans =$.parseJSON(res);
		if(ans.status == 1){
			$(obj).removeClass("active");
				$(obj).addClass("inactive");
				$(".status_"+faqId).attr('onclick','activeStatus(this)');
			fcom.displaySuccessMessage(ans.msg);				
		}else{
			fcom.displayErrorMessage(ans.msg);				
		}
	});
	};
	
	clearSearch = function(){
		document.frmSearch.reset();
		searchFaq(document.frmSearch);
	};
	
	

})();	


