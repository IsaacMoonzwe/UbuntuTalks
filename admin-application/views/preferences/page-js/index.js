$(document).ready(function(){
	searchPreferences(document.frmPreferenceSearch);
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
		var frm = document.frmPreferenceSearchPaging;		
		$(frm.page).val(page);
		searchPreferences(frm);
	}

	reloadList = function() {
		searchPreferences(document.frmPreferenceSearch);
	};	
	
	searchPreferences = function(form){		
		var data = '';
		if (form) {
			data = fcom.frmData(form);
		}
		$(dv).html(fcom.getLoader());
		
		fcom.ajax(fcom.makeUrl('Preferences','search'),data,function(res){
			$(dv).html(res);			
		});
	};
	addPreferenceForm = function(id,type) {
		
		$.facebox(function() { preferenceForm(id,type);
		});
	};
	
	preferenceForm = function(id,type) {
		fcom.displayProcessing();
		//$.facebox(function() {
			fcom.ajax(fcom.makeUrl('Preferences', 'form', [id,type]), '', function(t) {
				//$.facebox(t,'faceboxWidth');
				fcom.updateFaceboxContent(t);
			});
		//});
	};
	editPreferenceFormNew = function(preferenceId){
		$.facebox(function() {	editPreferenceForm(preferenceId);
		});
	};
	
	editPreferenceForm = function(preferenceId){
		fcom.displayProcessing();
		//$.facebox(function() {
			fcom.ajax(fcom.makeUrl('Preferences', 'form', [preferenceId]), '', function(t) {
				//$.facebox(t,'faceboxWidth');
				fcom.updateFaceboxContent(t);
			});
		//});
	};
	
	setupPreference = function (frm){
		if (!$(frm).validate()) return;		
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('Preferences', 'setup'), data, function(t) {			
			//$.mbsmessage.close();
			reloadList();
			if (t.langId>0) {
				editPreferenceLangForm(t.preferenceId, t.langId);
				return ;
			}
			
			$(document).trigger('close.facebox');
		});
	}
	
	editPreferenceLangForm = function(preferenceId,langId){
		fcom.displayProcessing();
	//	$.facebox(function() {
			fcom.ajax(fcom.makeUrl('Preferences', 'langForm', [preferenceId,langId]), '', function(t) {
				//$.facebox(t,'faceboxWidth');
				fcom.updateFaceboxContent(t);
			});
		//});
	};
	
	setupLangPreference = function (frm){
		if (!$(frm).validate()) return;		
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('Preferences', 'langSetup'), data, function(t) {
			reloadList();			
			if (t.langId>0) {
				editPreferenceLangForm(t.preferenceId, t.langId);
				return ;
			}
					
			$(document).trigger('close.facebox');
		});
	};
	
	deleteRecord = function(id){
		if(!confirm(langLbl.confirmDelete)){return;}
		data='preferenceId='+id;
		fcom.updateWithAjax(fcom.makeUrl('Preferences', 'deleteRecord'),data,function(res){		
			reloadList();
		});
	};
	

	
	clearSearch = function(){
		document.frmPreferenceSearch.reset();
		searchPreferences(document.frmPreferenceSearch);
	};
	
	
	
})();	


