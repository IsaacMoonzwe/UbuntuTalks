$(document).ready(function(){
	searchTimezone(document.frmTimezoneSearch);
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
		var frm = document.frmTimezoneSearchPaging;		
		$(frm.page).val(page);
		searchTimezone(frm);
	}

	reloadList = function() {
		var frm = document.frmTimezoneSearchPaging;
		searchTimezone(frm);
	};	
	
	searchTimezone = function(form){		
		/*[ this block should be before dv.html('... anything here.....') otherwise it will through exception in ie due to form being removed from div 'dv' while putting html*/
		var data = '';
		if (form) {
			data = fcom.frmData(form);
		}
		/*]*/
		$(dv).html(fcom.getLoader());
		
		fcom.ajax(fcom.makeUrl('Timezones','search'),data,function(res){
			$(dv).html(res);			
		});
	};
		
	timezoneForm = function(timezoneId){
		fcom.displayProcessing();
		fcom.ajax(fcom.makeUrl('Timezones', 'form', [timezoneId]), '', function(t) {
			fcom.updateFaceboxContent(t);
		});
	};
	
	setupTimezone = function (frm){
		if (!$(frm).validate()) return;		
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('Timezones', 'setup'), data, function(t) {
			reloadList();
			if (t.langId>0) {
				timezoneLangForm(t.timezoneId, t.langId);
				return ;
			}
			$(document).trigger('close.facebox');
		});
	};
	
	timezoneLangForm = function(timezoneId,langId){
		fcom.displayProcessing();
		fcom.ajax(fcom.makeUrl('Timezones', 'langForm', [timezoneId, langId]), '', function(t) {
			fcom.updateFaceboxContent(t);
		});
	};
	
	setupLangTimezone = function (frm){
		if (!$(frm).validate()) return;		
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('Timezones', 'langSetup'), data, function(t) {
			reloadList();			
			if (t.langId>0) {
				timezoneLangForm(t.timezoneId, t.langId);
				return ;
			}			
			$(document).trigger('close.facebox');
		});
	};
	
	activeStatus = function(obj){
		
		if(!confirm(langLbl.confirmUpdateStatus)){
			e.preventDefault();
			return;
		}
		var timezoneId = parseInt(obj.value);
		if(timezoneId < 1){
			fcom.displayErrorMessage(langLbl.invalidRequest);
			return false;
		}
		data='timezoneId='+timezoneId+"&status="+active;
		fcom.ajax(fcom.makeUrl('Timezones','changeStatus'),data,function(res){
			var ans =$.parseJSON(res);
			
			if( ans.status == 1 ){
				
				
				$(obj).removeClass("inactive");
				$(obj).addClass("active");
				$(".status_"+timezoneId).attr('onclick','inactiveStatus(this)');
				fcom.displaySuccessMessage(ans.msg);
			}
		});
	};
	
	inactiveStatus = function(obj){
		
		if(!confirm(langLbl.confirmUpdateStatus)){
			e.preventDefault();
			return;
		}
		var timezoneId = parseInt(obj.value);
		if(timezoneId < 1){
			fcom.displayErrorMessage(langLbl.invalidRequest);
			return false;
		}
		data='timezoneId='+timezoneId+"&status="+inActive;
		fcom.ajax(fcom.makeUrl('Timezones','changeStatus'),data,function(res){
			var ans =$.parseJSON(res);
			
			if( ans.status == 1 ){
				
				
				$(obj).removeClass("active");
				$(obj).addClass("inactive");
				$(".status_"+timezoneId).attr('onclick','activeStatus(this)');
				fcom.displaySuccessMessage(ans.msg);
			}
		});
	};
	
	clearSearch = function(){
		document.frmSearch.reset();
		searchTimezone(document.frmSearch);
	};
})();