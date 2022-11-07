$(document).ready(function(){
	searchCountry(document.frmCountrySearch);
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
		var frm = document.frmCountrySearchPaging;		
		$(frm.page).val(page);
		searchCountry(frm);
	}

	reloadList = function() {
		var frm = document.frmCountrySearchPaging;
		searchCountry(frm);
	};	
	
	searchCountry = function(form){		
		/*[ this block should be before dv.html('... anything here.....') otherwise it will through exception in ie due to form being removed from div 'dv' while putting html*/
		var data = '';
		if (form) {
			data = fcom.frmData(form);
		}
		/*]*/
		$(dv).html(fcom.getLoader());
		
		fcom.ajax(fcom.makeUrl('Countries','search'),data,function(res){
			$(dv).html(res);			
		});
	};
	addCountryForm = function(id) {
		$.facebox(function() {
			countryForm(id);		
		});

	};
	
	countryForm = function(id) {
		fcom.displayProcessing();
		///$.facebox(function() {
			fcom.ajax(fcom.makeUrl('Countries', 'form', [id]), '', function(t) {
				$.facebox(t,'faceboxWidth');
				fcom.updateFaceboxContent(t);
			});
		//});
	};
	
	editCountryFormNew = function(countryId){
		$.facebox(function() { editCountryForm(countryId);
		});
	};
	
	editCountryForm = function(countryId){
		fcom.displayProcessing();
		fcom.ajax(fcom.makeUrl('Countries', 'form', [countryId]), '', function(t) {
				//$.facebox(t,'faceboxWidth');
				fcom.updateFaceboxContent(t);
			});
	};
	
	setupCountry = function (frm){
		if (!$(frm).validate()) return;		
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('Countries', 'setup'), data, function(t) {
			reloadList();
			if (t.langId>0) {
				editCountryLangForm(t.countryId, t.langId);
				return ;
			}
			$(document).trigger('close.facebox');
		});
	};
	
	editCountryLangForm = function(countryId,langId){
		fcom.displayProcessing();
		//$.facebox(function() {
			fcom.ajax(fcom.makeUrl('Countries', 'langForm', [countryId,langId]), '', function(t) {
				//$.facebox(t,'faceboxWidth');
				fcom.updateFaceboxContent(t);
			});
		//});
	};
	
	setupLangCountry = function (frm){
		if (!$(frm).validate()) return;		
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('Countries', 'langSetup'), data, function(t) {
			reloadList();			
			if (t.langId>0) {
				editCountryLangForm(t.countryId, t.langId);
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
		var countryId = parseInt(obj.value);
		if(countryId < 1){
			fcom.displayErrorMessage(langLbl.invalidRequest);
			return false;
		}
		data='countryId='+countryId+"&status="+active;
		fcom.ajax(fcom.makeUrl('Countries','changeStatus'),data,function(res){
			var ans =$.parseJSON(res);
			
			if( ans.status == 1 ){
				
				
				$(obj).removeClass("inactive");
				$(obj).addClass("active");
				$(".status_"+countryId).attr('onclick','inactiveStatus(this)');
				fcom.displaySuccessMessage(ans.msg);
			}
		});
	};
	
	inactiveStatus = function(obj){
		
		if(!confirm(langLbl.confirmUpdateStatus)){
			e.preventDefault();
			return;
		}
		var countryId = parseInt(obj.value);
		if(countryId < 1){
			fcom.displayErrorMessage(langLbl.invalidRequest);
			return false;
		}
		data='countryId='+countryId+"&status="+inActive;
		fcom.ajax(fcom.makeUrl('Countries','changeStatus'),data,function(res){
			var ans =$.parseJSON(res);
			
			if( ans.status == 1 ){
				
				
				$(obj).removeClass("active");
				$(obj).addClass("inactive");
				$(".status_"+countryId).attr('onclick','activeStatus(this)');
				fcom.displaySuccessMessage(ans.msg);
			}
		});
	};
	
	clearSearch = function(){
		document.frmSearch.reset();
		searchCountry(document.frmSearch);
	};
	
	countryMediaForm = function(countryId){
		fcom.displayProcessing();
		fcom.ajax(fcom.makeUrl('Countries', 'media', [countryId]), '', function(t) {
			countryImages(countryId);
			fcom.updateFaceboxContent(t);
		});
	};
	
	countryImages = function(countryId){
		fcom.ajax(fcom.makeUrl('Countries', 'images', [countryId]), '', function(t) {
			$('#image-listing').html(t);
			fcom.resetFaceboxHeight();
		});
	};
	
	deleteFlagImage = function( countryId ){
		if(!confirm(langLbl.confirmDeleteLogo)){return;}
		fcom.updateWithAjax(fcom.makeUrl('Countries', 'deleteFlagImage',[countryId]), '', function(t) {
			countryImages(countryId);
		});
	};
	
})();


$(document).on('click','.uploadFile-Js',function(){
	var node = this;
	$('#form-upload').remove();	
	
	var countryId = $(node).attr( 'data-country_id' );	
	
	var frm = '<form enctype="multipart/form-data" id="form-upload" style="position:absolute; top:-100px;" >';
	frm = frm.concat('<input type="file" name="file" />'); 
	frm = frm.concat('<input type="hidden" name="country_id" value="' + countryId + '"/>'); 	
	frm = frm.concat('</form>'); 	
	$( 'body' ).prepend( frm );
	$('#form-upload input[name=\'file\']').trigger('click');
	if ( typeof timer != 'undefined' ) {
		clearInterval(timer);
	}	
	timer = setInterval(function() {
		if ($('#form-upload input[name=\'file\']').val() != '') {
			clearInterval(timer);
			$val = $(node).val();			
			$.ajax({
				url: fcom.makeUrl('Countries', 'setUpFlagImage'),
				type: 'post',
				dataType: 'json',
				data: new FormData($('#form-upload')[0]),
				cache: false,
				contentType: false,
				processData: false,
				beforeSend: function() {
					$(node).val('Loading');
				},
				complete: function() {
					$(node).val($val);
				},
				success: function(ans) {
						$('.text-danger').remove();
						$('#input-field').html(ans.msg);						
						if(ans.status==1){	
							fcom.displaySuccessMessage(ans.msg);
							$('#form-upload').remove();	
							countryImages(ans.countryId);
						}else{
							fcom.displayErrorMessage(ans.msg);
						}
					},
					error: function(xhr, ajaxOptions, thrownError) {
                        fcom.displayErrorMessage(xhr.responseText);
						//alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});			
		}
	}, 500);
});