$(document).ready(function(){
	searchBlocks(document.frmBlockSearch);
});

(function() {
	var currentPage = 1;
	var active = 1;
	var inActive = 0;
	
	reloadList = function() {
		var frm = document.frmBlockSearch;
		searchBlocks(frm);
	}
	
	searchBlocks = function(form){
		var dv = '#blockListing';
		var data = '';
		if (form){
			data = fcom.frmData(form);
		}
		$(dv).html( fcom.getLoader() );
		fcom.ajax(fcom.makeUrl('CovidInformation','search'),data,function(res){
			$(dv).html(res);
		});
	};

	addBlockFormNew = function(id){

		$.facebox(function() { addBlockForm(id);
		});
		
	};
	addBlockForm = function(id) {
		fcom.displayProcessing();
		var frm = document.frmBlockSearch;			
			fcom.ajax(fcom.makeUrl('CovidInformation', 'form', [id]), '', function(t) {
				fcom.updateFaceboxContent(t);
		});
	};
	
	setupBlock = function(frm) {
		if (!$(frm).validate()) return;		
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('CovidInformation', 'setup'), data, function(t) {
			if ( t.langId > 0 ) {
				addBlockLangForm(t.epageId, t.langId);
				return ;
			}
			reloadList();
			$(document).trigger('close.facebox');
		});
	};

	addBlockLangForm = function(epageId, langId){
		fcom.displayProcessing();
		fcom.resetEditorInstance();
			fcom.ajax(fcom.makeUrl('CovidInformation', 'langForm', [epageId, langId]), '', function(t) {

				fcom.updateFaceboxContent(t);
				fcom.setEditorLayout(langId);
				var frm = $('#facebox form')[0];
			
				var validator = $(frm).validation({errordisplay: 3});
				
				$(frm).submit(function(e) {
					e.preventDefault();
					validator.validate();
					if (!validator.isValid()) return;
					var data = fcom.frmData(frm);
					fcom.updateWithAjax(fcom.makeUrl('CovidInformation', 'langSetup'), data, function(t) {
						fcom.resetEditorInstance();	
						reloadList();				
						if (t.langId>0) {
							addBlockLangForm(t.epageId, t.langId);
							return ;
						}
						$(document).trigger('close.facebox');
					});
				});
			});
	};
	

	
	setupBlockLang=function(frm){
		if (!$(frm).validate()) return;
		var data = fcom.frmData(frm);		
		fcom.updateWithAjax(fcom.makeUrl('CovidInformation', 'langSetup'), data, function(t) {
			reloadList();				
			if (t.langId>0) {
				addBlockLangForm(t.epageId, t.langId);
				return ;
			}
			$(document).trigger('close.facebox');
		});
	};
	
	resetToDefaultContent =  function(){
		var agree  = confirm(langLbl.confirmReplaceCurrentToDefault);
		if( !agree ){ return false; }
		oUtil.obj.putHTML( $("#editor_default_content").html() );
	};
	
	activeStatus = function(obj){
		if(!confirm(langLbl.confirmUpdateStatus)){
			e.preventDefault();
			return;
		}
		var epageId = parseInt(obj.value);
		if( epageId < 1 ){

			fcom.displayErrorMessage(langLbl.invalidRequest);
			//$.mbsmessage(langLbl.invalidRequest,true,'alert--danger');
			return false;
		}
		data='epageId='+epageId+'&status='+active;
		fcom.ajax(fcom.makeUrl('CovidInformation','changeStatus'),data,function(res){
		var ans = $.parseJSON(res);
			if( ans.status == 1 ){
				fcom.displaySuccessMessage(ans.msg);
				//$.mbsmessage(ans.msg,true,'alert--success');
				$(obj).removeClass("inactive");
				$(obj).addClass("active");
				$(".status_"+epageId).attr('onclick','inactiveStatus(this)');
			}
			else{
				fcom.displayErrorMessage(ans.msg);

				//$.mbsmessage(ans.msg,true,'alert--danger');
			}
		});
	};
	
	inactiveStatus = function(obj){
		if(!confirm(langLbl.confirmUpdateStatus)){
			e.preventDefault();
			return;
		}
		var epageId = parseInt(obj.value);
		if( epageId < 1 ){

			fcom.displayErrorMessage(langLbl.invalidRequest);
			//$.mbsmessage(langLbl.invalidRequest,true,'alert--danger');
			return false;
		}
		data='epageId='+epageId+'&status='+inActive;
		fcom.ajax(fcom.makeUrl('CovidInformation','changeStatus'),data,function(res){
		var ans = $.parseJSON(res);
			if( ans.status == 1 ){
				fcom.displaySuccessMessage(ans.msg);
				//$.mbsmessage(ans.msg,true,'alert--success');
				$(obj).removeClass("active");
				$(obj).addClass("inactive");
				$(".status_"+epageId).attr('onclick','activeStatus(this)');
			}
			else{
				fcom.displayErrorMessage(ans.msg);

				//$.mbsmessage(ans.msg,true,'alert--danger');
			}
		});
	};
	
	removeBgImage = function( epage_id, langId, file_type ){
		if( !confirm(langLbl.confirmDeleteImage) ){ return; }
		fcom.updateWithAjax( fcom.makeUrl('CovidInformation', 'removeBgImage',[epage_id, langId, file_type]), '', function(t) {
			addBlockLangForm(epage_id, langId);
		});
	};
	
})();

