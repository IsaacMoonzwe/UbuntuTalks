$(document).ready(function () {
	searchSpokenLanguage(document.frmSpokenLanguageSearch);
});

(function () {
	var active = 1;
	var inActive = 0;
	var runningAjaxReq = false;
	var dv = '#listing';

	goToSearchPage = function (page) {
		if (typeof page == undefined || page == null) {
			page = 1;
		}
		var frm = document.frmSpokenLanguageearchPaging;
		$(frm.page).val(page);
		searchSpokenLanguage(frm);
	}

	reloadList = function () {
		searchSpokenLanguage();
	};

	searchSpokenLanguage = function (form) {
		var data = '';
		if (form) {
			data = fcom.frmData(form);
		}
		$(dv).html(fcom.getLoader());

		fcom.ajax(fcom.makeUrl('SpokenLanguage', 'search'), data, function (res) {
			$(dv).html(res);
		});
	};
	addSpokenLanguageForm = function (id) {

		$.facebox(function () {
			SpokenLanguageForm(id);
		});
	};

	SpokenLanguageForm = function (id) {
		fcom.displayProcessing();
		//$.facebox(function() {
		fcom.ajax(fcom.makeUrl('SpokenLanguage', 'form', [id]), '', function (t) {
			//$.facebox(t,'faceboxWidth');
			fcom.updateFaceboxContent(t);
		});
		//});
	};
	editSpokenLanguageFormNew = function (sLangId) {
		$.facebox(function () {
			editSpokenLanguageForm(sLangId);
		});
	};

	editSpokenLanguageForm = function (sLangId) {
		fcom.displayProcessing();
		//$.facebox(function() {
		fcom.ajax(fcom.makeUrl('SpokenLanguage', 'form', [sLangId]), '', function (t) {
			//$.facebox(t,'faceboxWidth');
			fcom.updateFaceboxContent(t);
		});
		//});
	};

	setupSpokenLanguage = function (frm) {
		if (!$(frm).validate()) return;
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('SpokenLanguage', 'setup'), data, function (t) {
			//$.mbsmessage.close();
			reloadList();
			if (t.langId > 0) {
				editSpokenLanguageLangForm(t.sLangId, t.langId);
				return;
			}

			$(document).trigger('close.facebox');
		});
	}

	editSpokenLanguageLangForm = function (sLangId, langId) {
		fcom.displayProcessing();
		//	$.facebox(function() {
		fcom.ajax(fcom.makeUrl('SpokenLanguage', 'langForm', [sLangId, langId]), '', function (t) {
			//$.facebox(t,'faceboxWidth');
			fcom.updateFaceboxContent(t);
		});
		//});
	};

	setupLangSpokenLanguage = function (frm) {
		if (!$(frm).validate()) return;
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('SpokenLanguage', 'langSetup'), data, function (t) {
			reloadList();
			if (t.langId > 0) {
				editSpokenLanguageLangForm(t.sLangId, t.langId);
				return;
			}

			$(document).trigger('close.facebox');
		});
	};

	deleteRecord = function (id) {
		if (!confirm(langLbl.confirmDelete)) { return; }
		data = 'sLangId=' + id;
		fcom.updateWithAjax(fcom.makeUrl('SpokenLanguage', 'deleteRecord'), data, function (res) {
			reloadList();
		});
	};

	activeStatus = function (obj) {

		let isChecked = $(obj).is(":checked");
		if (!confirm(langLbl.confirmUpdateStatus)) {
			(isChecked) ? $(obj).prop('checked', false) : $(obj).prop('checked', true);
			return;
		}
		var sLangId = parseInt(obj.value);
		if (sLangId < 1) {

			//$.mbsmessage(langLbl.invalidRequest,true,'alert--danger');
			fcom.displayErrorMessage(langLbl.invalidRequest);
			return false;
		}
		data = 'sLangId=' + sLangId + "&status=" + active;
		fcom.ajax(fcom.makeUrl('SpokenLanguage', 'changeStatus'), data, function (res) {
			var ans = $.parseJSON(res);
			if (ans.status == 1) {
				$(obj).removeClass("inactive");
				$(obj).addClass("active");
				$(".status_" + sLangId).attr('onclick', 'inactiveStatus(this)');
				fcom.displaySuccessMessage(ans.msg);
			} else {
				fcom.displayErrorMessage(ans.msg);
			}
		});
	};

	inactiveStatus = function (obj) {

		let isChecked = $(obj).is(":checked");
		if (!confirm(langLbl.confirmUpdateStatus)) {
			(isChecked) ? $(obj).prop('checked', false) : $(obj).prop('checked', true);
			return;
		}
		var sLangId = parseInt(obj.value);
		if (sLangId < 1) {

			//$.mbsmessage(langLbl.invalidRequest,true,'alert--danger');
			fcom.displayErrorMessage(langLbl.invalidRequest);
			return false;
		}
		data = 'sLangId=' + sLangId + "&status=" + inActive;
		fcom.ajax(fcom.makeUrl('SpokenLanguage', 'changeStatus'), data, function (res) {
			var ans = $.parseJSON(res);
			if (ans.status == 1) {
				$(obj).removeClass("active");
				$(obj).addClass("inactive");
				$(".status_" + sLangId).attr('onclick', 'activeStatus(this)');
				fcom.displaySuccessMessage(ans.msg);
			} else {
				fcom.displayErrorMessage(ans.msg);
			}
		});
	};

	clearSearch = function () {
		document.frmSpokenLanguageSearch.reset();
		searchSpokenLanguage(document.frmSpokenLanguageSearch);
	};

	mediaForm = function (sLangId) {
		fcom.displayProcessing();
		fcom.ajax(fcom.makeUrl('SpokenLanguage', 'mediaForm', [sLangId]), '', function (t) {
			images(sLangId, 0, 0);
			flagImages(sLangId, 0, 0);
			fcom.updateFaceboxContent(t);
		});
	};

	images = function (sLangId) {
		fcom.ajax(fcom.makeUrl('SpokenLanguage', 'images', [sLangId]), '', function (t) {
			$('#image-listing').html(t);
			fcom.resetFaceboxHeight();
		});
	};

	flagImages = function (sLangId) {
		fcom.ajax(fcom.makeUrl('SpokenLanguage', 'flagImages', [sLangId]), '', function (t) {
			$('#flag-image-listing').html(t);
			fcom.resetFaceboxHeight();
		});
	};

	removeImage = function (sLangId) {
		if (!confirm(langLbl.confirmDeleteImage)) { return; }
		fcom.updateWithAjax(fcom.makeUrl('SpokenLanguage', 'removeImage', [sLangId]), '', function (t) {
			images(sLangId, 0, 0);
		});
	};

	removeFlagImage = function (sLangId) {
		if (!confirm(langLbl.confirmDeleteImage)) { return; }
		fcom.updateWithAjax(fcom.makeUrl('SpokenLanguage', 'removeFlagImage', [sLangId]), '', function (t) {
			flagImages(sLangId, 0, 0);
		});
	};

})();
$(document).on('click', '.slanguageFile-Js', function () {
	var node = this;
	$('#form-upload').remove();
	var bannerId = document.frmSpokenLanguageMedia.slanguage_id.value;
	var banner_image = $(this).attr('name');

	var langId = 0;
	var banner_screen = 0;

	var frm = '<form enctype="multipart/form-data" id="form-upload" style="position:absolute; top:-100px;" >';
	frm = frm.concat('<input type="file" name="file" />');
	frm = frm.concat('<input type="hidden" name="banner_id" value="' + bannerId + '"/>');
	frm = frm.concat('<input type="hidden" name="lang_id" value="' + langId + '"/>');
	$('body').prepend(frm);
	$('#form-upload input[name=\'file\']').trigger('click');
	if (typeof timer != 'undefined') {
		clearInterval(timer);
	}
	timer = setInterval(function () {
		if ($('#form-upload input[name=\'file\']').val() != '') {
			clearInterval(timer);
			$val = $(node).val();
			$.ajax({
				url: fcom.makeUrl('SpokenLanguage', 'upload', [bannerId]),
				type: 'post',
				dataType: 'json',
				data: new FormData($('#form-upload')[0]),
				cache: false,
				contentType: false,
				processData: false,
				beforeSend: function () {
					$(node).val('Loading');
				},
				complete: function () {
					$(node).val($val);
				},
				success: function (ans) {
					if (ans.status == 1) {
						fcom.displaySuccessMessage(ans.msg);
						reloadList();
						$('#form-upload').remove();
						images(bannerId, 0, 0);
					} else {
						fcom.displayErrorMessage(ans.msg);
					}
				},
				error: function (xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	}, 500);
});

$(document).on('click', '.slanguageFlagFile-Js', function () {
	var node = this;
	$('#form-upload').remove();
	var bannerId = document.frmSpokenLanguageMedia.slanguage_id.value;
	var banner_image = $(this).attr('name');

	var langId = 0;
	var banner_screen = 0;

	var frm = '<form enctype="multipart/form-data" id="form-upload" style="position:absolute; top:-100px;" >';
	frm = frm.concat('<input type="file" name="file" />');
	frm = frm.concat('<input type="hidden" name="banner_id" value="' + bannerId + '"/>');
	frm = frm.concat('<input type="hidden" name="lang_id" value="' + langId + '"/>');
	$('body').prepend(frm);
	$('#form-upload input[name=\'file\']').trigger('click');
	if (typeof timer != 'undefined') {
		clearInterval(timer);
	}
	timer = setInterval(function () {
		if ($('#form-upload input[name=\'file\']').val() != '') {
			clearInterval(timer);
			$val = $(node).val();
			$.ajax({
				url: fcom.makeUrl('SpokenLanguage', 'flagUpload', [bannerId]),
				type: 'post',
				dataType: 'json',
				data: new FormData($('#form-upload')[0]),
				cache: false,
				contentType: false,
				processData: false,
				beforeSend: function () {
					$(node).val('Loading');
				},
				complete: function () {
					$(node).val($val);
				},
				success: function (ans) {
					if (ans.status == 1) {
						fcom.displaySuccessMessage(ans.msg);
						reloadList();
						$('#form-upload').remove();
						flagImages(bannerId, 0, 0);
					} else {
						fcom.displayErrorMessage(ans.msg);
					}
				},
				error: function (xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	}, 500);
});