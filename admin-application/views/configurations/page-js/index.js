$(document).ready(function(){
	getForm(1);
	
	$(document).on("click", "#testMail-js", function () {
		fcom.ajax(fcom.makeUrl('Configurations', 'testEmail'), '', function(t) {
			var ans = $.parseJSON(t);
			if( ans.status == 1 ){
				$.systemMessage( ans.msg, 'alert--success' );
			} else {
				$.systemMessage( ans.msg, 'alert--danger' );
			}
		});
	});
	
	$('.info__icon').click(function(){
		$(this).toggleClass('is--active');
	});

	$(document).click(function(el){
		if (!$(el.target).parents().hasClass("info__icon")) $(".info__icon").removeClass("is--active");		
	});
});

(function() {
	var currentPage = 1;
	var runningAjaxReq = false;
	var dv = '#frmBlock';
	getForm = function(frmType){
		fcom.resetEditorInstance();
		$(dv).html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('Configurations', 'form', [frmType]), '', function(t) {
			$(dv).html(t);
		});
	};
	
	getLangForm = function(frmType,langId){
		
		fcom.resetEditorInstance();	
		$(dv).html(fcom.getLoader());
		fcom.displayProcessing();
		fcom.ajax(fcom.makeUrl('Configurations', 'langForm', [frmType,langId]), '', function(t) {
			
			$(dv).html(t);
			fcom.setEditorLayout(langId);
			if(frmType == 11){
				$('input[name=btn_submit]').hide();
			}
			var frm = $(dv+' form')[0];
			var validator = $(frm).validation({errordisplay: 3});			
			$(frm).submit(function(e) {
				e.preventDefault();
				if (validator.validate() == false) {	
					return ;
				}
				var data = fcom.frmData(frm);
				fcom.updateWithAjax(fcom.makeUrl('Configurations', 'setupLang'), data, function(t) {			
					runningAjaxReq = false;	
					fcom.resetEditorInstance();					
					if (t.langId > 0 && t.shopId > 0) {
						shopLangForm(t.shopId , t.langId);
						return;
					}	
				});
			});
			
		});
		$.systemMessage.close();
	}
	
	setup = function(frm) {
		if (!$(frm).validate()){ return; }		
		var data = fcom.frmData(frm);		
		fcom.updateWithAjax(fcom.makeUrl('Configurations', 'setup'), data, function(t) {
			if (t.langId > 0 && t.frmType > 0) {
				getLangForm(t.frmType, t.langId);
				return ;
			}
			if(t.frmType > 0){
				getForm(t.frmType);
				return ;
			}
			$(document).trigger('close.facebox');
		});
	}
	
	setupLang = function(frm) {
		if (!$(frm).validate()){ return; }	
		var data = fcom.frmData(frm);		
		fcom.updateWithAjax(fcom.makeUrl('Configurations', 'setupLang'), data, function(t) {
			if (t.langId > 0 && t.frmType > 0) {
				getLangForm(t.frmType, t.langId);
				return ;
			}
			if(t.frmType > 0){
				getForm(t.frmType);
				return ;
			}
			$(document).trigger('close.facebox');
		});
	}
	
	removeSiteAdminLogo = function( lang_id ){
		if(!confirm(langLbl.confirmDeleteImage)){return;}
		fcom.updateWithAjax(fcom.makeUrl('Configurations', 'removeSiteAdminLogo',[lang_id]), '', function(t) {
			getLangForm( document.frmConfiguration.form_type.value, lang_id );
		});
	};
	
	removeDesktopLogo = function(lang_id){
		if(!confirm(langLbl.confirmDeleteImage)){return;}
		fcom.updateWithAjax(fcom.makeUrl('Configurations', 'removeDesktopLogo', [lang_id]), '', function(t) {
			getLangForm( document.frmConfiguration.form_type.value, lang_id );
		});
	};

	removeDesktopWhiteLogo = function(lang_id){
		if(!confirm(langLbl.confirmDeleteImage)){return;}
		fcom.updateWithAjax(fcom.makeUrl('Configurations', 'removeDesktopWhiteLogo', [lang_id]), '', function(t) {
			getLangForm( document.frmConfiguration.form_type.value, lang_id );
		});
	};
	
	removeEmailLogo = function(lang_id){
		if(!confirm(langLbl.confirmDeleteImage)){return;}
		fcom.updateWithAjax(fcom.makeUrl('Configurations', 'removeEmailLogo', [lang_id]), '', function(t) {
			getLangForm( document.frmConfiguration.form_type.value, lang_id );
		});
	};
	
	removeFavicon = function(lang_id){
		if( !confirm(langLbl.confirmDeleteImage) ){ return; }
		fcom.updateWithAjax(fcom.makeUrl('Configurations', 'removeFavicon', [lang_id]), '', function(t) {
			getLangForm( document.frmConfiguration.form_type.value, lang_id );
		});
	};
	
	removeSocialFeedImage = function(lang_id){
		if(!confirm(langLbl.confirmDeleteImage)){return;}
		fcom.updateWithAjax(fcom.makeUrl('Configurations', 'removeSocialFeedImage', [lang_id]), '', function(t) {
			getLangForm( document.frmConfiguration.form_type.value, lang_id );
		});
	};
	
	removePaymentPageLogo = function(lang_id){
		if(!confirm(langLbl.confirmDeleteImage)){return;}
		fcom.updateWithAjax(fcom.makeUrl('Configurations', 'removePaymentPageLogo', [lang_id]), '', function(t) {
			getLangForm( document.frmConfiguration.form_type.value, lang_id );
		});
	};
	
	removeWatermarkImage = function(lang_id){
		if(!confirm(langLbl.confirmDeleteImage)){return;}
		fcom.updateWithAjax(fcom.makeUrl('Configurations', 'removeWatermarkImage', [lang_id]), '', function(t) {
			getLangForm( document.frmConfiguration.form_type.value, lang_id );
		});
	};
	
	removeAppleTouchIcon = function(lang_id){
		if(!confirm(langLbl.confirmDeleteImage)){return;}
		fcom.updateWithAjax(fcom.makeUrl('Configurations', 'removeAppleTouchIcon', [lang_id]), '', function(t) {
			getLangForm( document.frmConfiguration.form_type.value, lang_id );
		});
	};
	
	removeMobileLogo = function(lang_id){
		if(!confirm(langLbl.confirmDeleteImage)){return;}
		fcom.updateWithAjax(fcom.makeUrl('Configurations', 'removeMobileLogo', [lang_id]), '', function(t) {
			getLangForm( document.frmConfiguration.form_type.value, lang_id );
		});
	};
	
	removeCollectionBgImage = function(lang_id){
		if(!confirm(langLbl.confirmDeleteImage)){return;}
		fcom.updateWithAjax(fcom.makeUrl('Configurations', 'removeCollectionBgImage', [lang_id]), '', function(t) {
			getLangForm( document.frmConfiguration.form_type.value, lang_id );
		});
	};
	
	changedMessageAutoCloseSetting = function(val){
		if( val == YES ){
			
		}
		if( val == NO ){
			$("input[name='CONF_TIME_AUTO_CLOSE_SYSTEM_MESSAGES']").val(0);
		}
	};

	removeBlogImage = function(lang_id){
		if(!confirm(langLbl.confirmDeleteImage)){return;}
		fcom.updateWithAjax(fcom.makeUrl('Configurations', 'removeBlogImage', [lang_id]), '', function(t) {
			getLangForm( document.frmConfiguration.form_type.value, lang_id );
		});
	};

	removeMedicalCampaignImage = function(lang_id){
		if(!confirm(langLbl.confirmDeleteImage)){return;}
		fcom.updateWithAjax(fcom.makeUrl('Configurations', 'removeMedicalCampaignImage', [lang_id]), '', function(t) {
			getLangForm( document.frmConfiguration.form_type.value, lang_id );
		});
	};

	removeFaithGroupsCampaignImage = function(lang_id){
		if(!confirm(langLbl.confirmDeleteImage)){return;}
		fcom.updateWithAjax(fcom.makeUrl('Configurations', 'removeFaithGroupsCampaignImage', [lang_id]), '', function(t) {
			getLangForm( document.frmConfiguration.form_type.value, lang_id );
		});
	};

	removeEducationCampaignImage = function(lang_id){
		if(!confirm(langLbl.confirmDeleteImage)){return;}
		fcom.updateWithAjax(fcom.makeUrl('Configurations', 'removeEducationCampaignImage', [lang_id]), '', function(t) {
			getLangForm( document.frmConfiguration.form_type.value, lang_id );
		});
	};

	removeBusinessCampaignImage = function(lang_id){
		if(!confirm(langLbl.confirmDeleteImage)){return;}
		fcom.updateWithAjax(fcom.makeUrl('Configurations', 'removeBusinessCampaignImage', [lang_id]), '', function(t) {
			getLangForm( document.frmConfiguration.form_type.value, lang_id );
		});
	};

	removeReferralCampaignImage = function(lang_id){
		if(!confirm(langLbl.confirmDeleteImage)){return;}
		fcom.updateWithAjax(fcom.makeUrl('Configurations', 'removeReferralCampaignImage', [lang_id]), '', function(t) {
			getLangForm( document.frmConfiguration.form_type.value, lang_id );
		});
	};

	removeEventCampaignImage = function(lang_id){
		if(!confirm(langLbl.confirmDeleteImage)){return;}
		fcom.updateWithAjax(fcom.makeUrl('Configurations', 'removeEventCampaignImage', [lang_id]), '', function(t) {
			getLangForm( document.frmConfiguration.form_type.value, lang_id );
		});
	};

	removeEventSecondSliderCampaignImage = function(lang_id){
		if(!confirm(langLbl.confirmDeleteImage)){return;}
		fcom.updateWithAjax(fcom.makeUrl('Configurations', 'removeEventSecondSliderCampaignImage', [lang_id]), '', function(t) {
			getLangForm( document.frmConfiguration.form_type.value, lang_id );
		});
	};

	removeEventThirdSliderCampaignImage = function(lang_id){
		if(!confirm(langLbl.confirmDeleteImage)){return;}
		fcom.updateWithAjax(fcom.makeUrl('Configurations', 'removeEventThirdSliderCampaignImage', [lang_id]), '', function(t) {
			getLangForm( document.frmConfiguration.form_type.value, lang_id );
		});
	};

	removeEventFourthSliderCampaignImage = function(lang_id){
		if(!confirm(langLbl.confirmDeleteImage)){return;}
		fcom.updateWithAjax(fcom.makeUrl('Configurations', 'removeEventFourthSliderCampaignImage', [lang_id]), '', function(t) {
			getLangForm( document.frmConfiguration.form_type.value, lang_id );
		});
	};

	removeEventFifthSliderCampaignImage = function(lang_id){
		if(!confirm(langLbl.confirmDeleteImage)){return;}
		fcom.updateWithAjax(fcom.makeUrl('Configurations', 'removeEventFifthSliderCampaignImage', [lang_id]), '', function(t) {
			getLangForm( document.frmConfiguration.form_type.value, lang_id );
		});
	};

	removeVirtualSessionCampaignImage = function(lang_id){
		if(!confirm(langLbl.confirmDeleteImage)){return;}
		fcom.updateWithAjax(fcom.makeUrl('Configurations', 'removeVirtualSessionCampaignImage', [lang_id]), '', function(t) {
			getLangForm( document.frmConfiguration.form_type.value, lang_id );
		});
	};

	removeKidsImage = function(lang_id){
		if(!confirm(langLbl.confirmDeleteImage)){return;}
		fcom.updateWithAjax(fcom.makeUrl('Configurations', 'removeKidsImage', [lang_id]), '', function(t) {
			getLangForm( document.frmConfiguration.form_type.value, lang_id );
		});
	};

	// removeServiceImage = function(lang_id){
	// 	if(!confirm(langLbl.confirmDeleteImage)){return;}
	// 	fcom.updateWithAjax(fcom.makeUrl('Configurations', 'removeServiceImage', [lang_id]), '', function(t) {
	// 		getLangForm( document.frmConfiguration.form_type.value, lang_id );
	// 	});
	// };

	removeContactImage = function(lang_id){
		if(!confirm(langLbl.confirmDeleteImage)){return;}
		fcom.updateWithAjax(fcom.makeUrl('Configurations', 'removeContactImage', [lang_id]), '', function(t) {
			getLangForm( document.frmConfiguration.form_type.value, lang_id );
		});
	};

	removeQuoteImage = function(lang_id){
		if(!confirm(langLbl.confirmDeleteImage)){return;}
		fcom.updateWithAjax(fcom.makeUrl('Configurations', 'removeQuoteImage', [lang_id]), '', function(t) {
			getLangForm( document.frmConfiguration.form_type.value, lang_id );
		});
	};
	
	removeLessonImage = function(lang_id){
		if(!confirm(langLbl.confirmDeleteImage)){return;}
		fcom.updateWithAjax(fcom.makeUrl('Configurations', 'removeLessonImage', [lang_id]), '', function(t) {
			getLangForm( document.frmConfiguration.form_type.value, lang_id );
		});
	};

	removeApplyToTeachBannerImage = function(lang_id){
		if(!confirm(langLbl.confirmDeleteImage)){return;}
		fcom.updateWithAjax(fcom.makeUrl('Configurations', 'removeApplyToTeachBannerPage', [lang_id]), '', function(t) {
			getLangForm( document.frmConfiguration.form_type.value, lang_id );
		});
	};

	removeAllowedPaymentGatewayImage = function(lang_id){
		if(!confirm(langLbl.confirmDeleteImage)){return;}
		fcom.updateWithAjax(fcom.makeUrl('Configurations', 'removeAllowedPaymentGatewayImage', [lang_id]), '', function(t) {
			getLangForm( document.frmConfiguration.form_type.value, lang_id );
		});
	};
	
})();	


form = function(form_type){
	if(typeof form_type==undefined || form_type == null){
		form_type =1;
	}
	jQuery.ajax({
		type:"POST",
		data : {form:form_type,fIsAjax:1},
		url:fcom.makeUrl("configurations","form"),
		success:function(json){
			json = $.parseJSON(json);
			if("1" == json.status){
				$("#tabs_0"+form_type).html(json.msg);
			}else{
				jsonErrorMessage(json.msg)
			}
		}
	});
}

submitForm = function(form,v){
	 $(form).ajaxSubmit({ 
		delegation: true,
		beforeSubmit:function(){
						v.validate();
						if (!v.isValid()){
							return false;
						}
					},
		success:function(json){
			json = $.parseJSON(json);
			
			if(json.status == "1"){
				jsonSuccessMessage(json.msg)
				
			}else{
				jsonErrorMessage(json.msg);
			}
		}		
	}); 	
	return false;
}

$(document).on('click','.logoFiles-Js',function(){
	var node = this;
	$('#form-upload').remove();
	var fileType = $(node).attr('data-file_type');
	var lang_id = document.frmConfiguration.lang_id.value;
	var form_type = document.frmConfiguration.form_type.value;
	var frm = '<form enctype="multipart/form-data" id="form-upload" style="position:absolute; top:-100px;" >';
	frm = frm.concat('<input type="file" name="file" />'); 
	frm = frm.concat('<input type="hidden" name="file_type" value="'+fileType+'">'); 
	frm = frm.concat('<input type="hidden" name="lang_id" value="' + lang_id + '">'); 
	frm = frm.concat('</form>'); 
	$('body').prepend(frm);
	$('#form-upload input[name=\'file\']').trigger('click');
	if (typeof timer != 'undefined') {
		clearInterval(timer);
	}	
	timer = setInterval(function() {
		if ($('#form-upload input[name=\'file\']').val() != '') {
			clearInterval(timer);
			$val = $(node).val();			
			$.ajax({
				url: fcom.makeUrl('Configurations', 'uploadMedia'),
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
					if( !ans.status ){
						$.systemMessage( ans.msg, 'alert--danger' );
						return;
					}
					$.systemMessage( ans.msg, 'alert--success' );
					getLangForm( form_type, lang_id );
				},
				error: function(xhr, ajaxOptions, thrownError) {
					if(xhr.responseText){
						$.systemMessage(xhr.responseText,'alert--danger');
						return;
					}
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
				});			
		}
	}, 500);
});
