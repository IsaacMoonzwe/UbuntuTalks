$(document).ready(function(){
	searchTestimonial(document.frmTestimonialSearch);
});

(function() {
	var active = 1;
	var inActive = 0;
	var runningAjaxReq = false;
	var dv = '#listing';
	

	onChangeDateTime=function(date,type){
		console.log('sjdgf',type);
		if(type=='start'){
			onChangeStartTime(date);
		}
		else{
			onChangeEndTime(date);
		}
	}
	onChangeStartTime=function(start){
		console.log("start",startTime);
		startTime+=start+','
	}

	onChangeEndTime=function(end){
		console.log("start",endTime);
		endTime+=end+','
	}

	goToSearchPage = function(page) {	
		if(typeof page==undefined || page == null){
			page =1;
		}
		var frm = document.frmTestimonialSearchPaging;		
		$(frm.page).val(page);
		searchTestimonial(frm);
	}
	

	form = function (id) {
		fcom.displayProcessing();
		fcom.ajax(fcom.makeUrl('Agenda', 'form', [id]), '', function (t) {
			fcom.updateFaceboxContent(t);
			jQuery('#grpcls_start_datetime').each(function () {
				$(this).datetimepicker({
					format: 'Y-m-d H:i'
				});
			});
		});
	};

	reloadList = function() {
		searchTestimonial();
	};	
	
	searchTestimonial = function(frm){
		//if (!$(frm).validate()) return;
		var data = '';
		if (frm) {
			data = fcom.frmData(frm);
		}
		$(dv).html(fcom.getLoader());
		
		fcom.ajax(fcom.makeUrl('Agenda','search'),data,function(res){
			$(dv).html(res);			
		});
	};
	addTestimonialForm = function(id) {
		
		$.facebox(function() { testimonialForm(id);
		});
	};
	editTestimonialAgendaFormNew = function(testimonialId){
		$.facebox(function() {	editAgendaFormNew(testimonialId);
		});
	};
	editAgendaFormNew = function(testimonialId){
		fcom.displayProcessing();

		//$.facebox(function() {
			fcom.ajax(fcom.makeUrl('Agenda', 'agendaForm', [testimonialId]), '', function(t) {
				//$.facebox(t,'faceboxWidth');
				fcom.updateFaceboxContent(t);
				jQuery('#agenda_start_time').each(function () {
					$(this).datetimepicker({
						format: 'Y-m-d H:i',
						onClose: function(date) {
										onChangeStartTime(date);
									},
					});
				});
					jQuery('#agenda_end_time').each(function () {
					$(this).datetimepicker({
						format: 'Y-m-d H:i',
						onClose: function(date) {
										onChangeEndTime(date);
									},
					});
				
			});
		});
	};
	testimonialForm = function(id) {
		fcom.displayProcessing();
		//$.facebox(function() {
			fcom.ajax(fcom.makeUrl('Agenda', 'form', [id]), '', function(t) {
				//$.facebox(t,'faceboxWidth');
				fcom.updateFaceboxContent(t);
				jQuery('#agenda_start_time,#agenda_end_time').each(function () {
					$(this).datetimepicker({
						format: 'Y-m-d H:i',
						
					});
				});
			});
		//});
	};
	

	editTestimonialFormNew = function(testimonialId){
		$.facebox(function() {	editTestimonialForm(testimonialId);
		});
	};
	
	editTestimonialForm = function(testimonialId){
		fcom.displayProcessing();
		//$.facebox(function() {
			fcom.ajax(fcom.makeUrl('Agenda', 'agendaform', [testimonialId]), '', function(t) {
				//$.facebox(t,'faceboxWidth');
				
				fcom.updateFaceboxContent(t);
				jQuery('#agenda_start_time,#agenda_end_time').each(function () {
					$(this).datetimepicker({
						format: 'Y-m-d H:i',
						onClose: function(date) {
							alert(date)
						 },
					});
				});
			});
		//});
	};
	testfunction=function(frm){
		console.log("hi",fcom.frmData(frm));
	}
	agendaSetupTestimonial = function (frm){
		// if (!$(frm).validate()) return;		
		var data = fcom.frmData(frm);
		console.log("data",data);
		data=data+'&start_time='+startTime+'&end_Time='+endTime;
		fcom.updateWithAjax(fcom.makeUrl('Agenda', 'agendasetup'), data, function(t) {			
			//$.mbsmessage.close();
			reloadList();
			if (t.langId>0) {
				editTestimonialLangForm(t.testimonialId, t.langId);
				return ;
			}
			// if (t.openMediaForm)
			// {
			// 	testimonialMediaForm(t.testimonialId);
			// 	return;
			// }	
			$(document).trigger('close.facebox');
		});
	}
	setupTestimonial = function (frm){
		if (!$(frm).validate()) return;		
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('Agenda', 'setup'), data, function(t) {			
			//$.mbsmessage.close();
			reloadList();
			if (t.langId>0) {
				editTestimonialLangForm(t.testimonialId, t.langId);
				return ;
			}
			// if (t.openMediaForm)
			// {
			// 	testimonialMediaForm(t.testimonialId);
			// 	return;
			// }	
			$(document).trigger('close.facebox');
		});
	}
	
	editTestimonialLangForm = function(testimonialId,langId){
		fcom.displayProcessing();
	//	$.facebox(function() {
			fcom.ajax(fcom.makeUrl('Agenda', 'langForm', [testimonialId,langId]), '', function(t) {
				//$.facebox(t,'faceboxWidth');
				fcom.updateFaceboxContent(t);
			});
		//});
	};
	
	setupLangTestimonial = function (frm){
		if (!$(frm).validate()) return;		
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('Agenda', 'langSetup'), data, function(t) {
			reloadList();			
			if (t.langId>0) {
				editTestimonialLangForm(t.testimonialId, t.langId);
				return ;
			}
			if (t.openMediaForm)
			{
				testimonialMediaForm(t.testimonialId);
				return;
			}			
			$(document).trigger('close.facebox');
		});
	};
	
	deleteRecord = function(id){
		if(!confirm(langLbl.confirmDelete)){return;}
		data='testimonialId='+id;
		fcom.updateWithAjax(fcom.makeUrl('Agenda', 'deleteRecord'),data,function(res){		
			reloadList();
		});
	};
	
	activeStatus = function(obj){
		
		if(!confirm(langLbl.confirmUpdateStatus)){
			e.preventDefault();
			return;
		}
		var testimonialId = parseInt(obj.value);
		if(testimonialId < 1){

			//$.mbsmessage(langLbl.invalidRequest,true,'alert--danger');
			fcom.displayErrorMessage(langLbl.invalidRequest);
			return false;
		}
		data='testimonialId='+testimonialId+"&status="+active;
		fcom.ajax(fcom.makeUrl('Agenda','changeStatus'),data,function(res){
		var ans =$.parseJSON(res);
			if(ans.status == 1){
				$(obj).removeClass("inactive");
				$(obj).addClass("active");
				$(".status_"+testimonialId).attr('onclick','inactiveStatus(this)');
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
	var testimonialId = parseInt(obj.value);
	if(testimonialId < 1){

		//$.mbsmessage(langLbl.invalidRequest,true,'alert--danger');
		fcom.displayErrorMessage(langLbl.invalidRequest);
		return false;
	}
	data='testimonialId='+testimonialId+"&status="+inActive;
	fcom.ajax(fcom.makeUrl('Agenda','changeStatus'),data,function(res){
	var ans =$.parseJSON(res);
		if(ans.status == 1){
			$(obj).removeClass("active");
				$(obj).addClass("inactive");
				$(".status_"+testimonialId).attr('onclick','activeStatus(this)');
			fcom.displaySuccessMessage(ans.msg);				
		}else{
			fcom.displayErrorMessage(ans.msg);				
		}
	});
	};
	
	clearSearch = function(){
		document.frmSearch.reset();
		searchTestimonial(document.frmSearch);
	};
	
	
	testimonialMediaForm = function(testimonialId){
		//$.facebox(function() {
			fcom.displayProcessing();
			fcom.ajax(fcom.makeUrl('Agenda', 'media', [testimonialId]), '', function(t) {
				//$.facebox(t);
				fcom.updateFaceboxContent(t);
			});
		//});
	};
	
	removeTestimonialImage = function( testimonialId, langId ){
		if(!confirm(langLbl.confirmDeleteImage)){return;}
		fcom.updateWithAjax(fcom.makeUrl('Agenda', 'removeTestimonialImage',[testimonialId, langId]), '', function(t) {
			testimonialMediaForm( testimonialId );
		});
	}
})();	


$(document).on('click','.uploadFile-Js',function(){
	var node = this;
	$('#form-upload').remove();	
	/* var brandId = document.frmProdBrandLang.brand_id.value;
	var langId = document.frmProdBrandLang.lang_id.value; */
	
	var testimonialId = $(node).attr( 'data-testimonial_id' );	
	var langId = 0;
	
	var frm = '<form enctype="multipart/form-data" id="form-upload" style="position:absolute; top:-100px;" >';
	frm = frm.concat('<input type="file" name="file" />'); 
	frm = frm.concat('<input type="hidden" name="testimonialId" value="' + testimonialId + '"/>'); 	
	frm = frm.concat('<input type="hidden" name="lang_id" value="' + langId + '"/>'); 	
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
				url: fcom.makeUrl('Agenda', 'uploadTestimonialMedia'),
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
						if( !ans.status ){
							fcom.displayErrorMessage(ans.msg);							
							return;
						}
						fcom.displaySuccessMessage(ans.msg);						
						testimonialMediaForm(ans.testimonialId);
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});			
		}
	}, 500);
});