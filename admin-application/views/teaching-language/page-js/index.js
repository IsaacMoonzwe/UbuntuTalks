$(document).ready(function () {
	searchTeachingLanguage(document.frmTeachingLanguageSearch);
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
		var frm = document.frmTeachingLanguageearchPaging;
		$(frm.page).val(page);
		searchTeachingLanguage(frm);
	}

	reloadList = function () {
		searchTeachingLanguage();
	};

	searchTeachingLanguage = function (form) {
		var data = '';
		if (form) {
			data = fcom.frmData(form);
		}
		$(dv).html(fcom.getLoader());

		fcom.ajax(fcom.makeUrl('TeachingLanguage', 'search'), data, function (res) {
			$(dv).html(res);
		});
	};
	clearSearch = function () {
		document.frmTeachingLanguageSearch.reset();
		searchTeachingLanguage(document.frmTeachingLanguageSearch);
	};


	addTeachingLanguageForm = function (id) {

		$.facebox(function () {
			TeachingLanguageForm(id);
		});
	};

	TeachingLanguageForm = function (id) {
		fcom.displayProcessing();
		//$.facebox(function() {
		fcom.ajax(fcom.makeUrl('TeachingLanguage', 'form', [id]), '', function (t) {
			//$.facebox(t,'faceboxWidth');
			fcom.updateFaceboxContent(t);
		});
		//});
	};
	editTeachingLanguageFormNew = function (tLangId) {
		$.facebox(function () {
			editTeachingLanguageForm(tLangId);
		});
	};

	editTeachingLanguageForm = function (tLangId) {
		fcom.displayProcessing();
		//$.facebox(function() {
		fcom.ajax(fcom.makeUrl('TeachingLanguage', 'form', [tLangId]), '', function (t) {
			//$.facebox(t,'faceboxWidth');
			fcom.updateFaceboxContent(t);
		});
		//});
	};

	setupTeachingLanguage = function (frm) {
		if (!$(frm).validate()) return;
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('TeachingLanguage', 'setup'), data, function (t) {
			//$.mbsmessage.close();
			reloadList();
			if (t.langId > 0) {
				editTeachingLanguageLangForm(t.tLangId, t.langId);
				return;
			}

			$(document).trigger('close.facebox');
		});
	}

	editTeachingLanguageLangForm = function (tLangId, langId) {
		fcom.displayProcessing();
		//	$.facebox(function() {
		fcom.ajax(fcom.makeUrl('TeachingLanguage', 'langForm', [tLangId, langId]), '', function (t) {
			//$.facebox(t,'faceboxWidth');
			fcom.updateFaceboxContent(t);
		});
		//});
	};

	setupLangTeachingLanguage = function (frm) {
		if (!$(frm).validate()) return;
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('TeachingLanguage', 'langSetup'), data, function (t) {
			reloadList();
			if (t.langId > 0) {
				editTeachingLanguageLangForm(t.tLangId, t.langId);
				return;
			}

			$(document).trigger('close.facebox');
		});
	};

	deleteRecord = function (id) {
		if (!confirm(langLbl.teachingLangUpdNotice)) { return; }
		data = 'tLangId=' + id;
		fcom.updateWithAjax(fcom.makeUrl('TeachingLanguage', 'deleteRecord'), data, function (res) {
			reloadList();
		});
	};

	activeStatus = function (obj) {

		let isChecked = $(obj).is(":checked");
		if (!confirm(langLbl.confirmUpdateStatus)) {
			(isChecked) ? $(obj).prop('checked', false) : $(obj).prop('checked', true);
			return;
		}
		var tLangId = parseInt(obj.value);
		if (tLangId < 1) {
			fcom.displayErrorMessage(langLbl.invalidRequest);
			return false;
		}
		data = 'tLangId=' + tLangId + "&status=" + active;
		fcom.ajax(fcom.makeUrl('TeachingLanguage', 'changeStatus'), data, function (res) {
			var ans = $.parseJSON(res);
			if (ans.status == 1) {
				$(obj).removeClass("inactive");
				$(obj).addClass("active");
				$(".status_" + tLangId).attr('onclick', 'inactiveStatus(this)');
				fcom.displaySuccessMessage(ans.msg);
			} else {
				fcom.displayErrorMessage(ans.msg);
			}
		});
	};

	inactiveStatus = function (obj) {
		let isChecked = $(obj).is(":checked");
		if (!confirm(langLbl.teachingLangUpdNotice)) {
			(isChecked) ? $(obj).prop('checked', false) : $(obj).prop('checked', true);
			return;
		}

		var tLangId = parseInt(obj.value);
		if (tLangId < 1) {

			//$.mbsmessage(langLbl.invalidRequest,true,'alert--danger');
			fcom.displayErrorMessage(langLbl.invalidRequest);
			return false;
		}
		data = 'tLangId=' + tLangId + "&status=" + inActive;
		fcom.ajax(fcom.makeUrl('TeachingLanguage', 'changeStatus'), data, function (res) {
			var ans = $.parseJSON(res);
			if (ans.status == 1) {
				$(obj).removeClass("active");
				$(obj).addClass("inactive");
				$(".status_" + tLangId).attr('onclick', 'activeStatus(this)');
				fcom.displaySuccessMessage(ans.msg);
			} else {
				fcom.displayErrorMessage(ans.msg);
			}
		});
	};

	mediaForm = function (tLangId) {
		fcom.displayProcessing();
		fcom.ajax(fcom.makeUrl('TeachingLanguage', 'mediaForm', [tLangId]), '', function (t) {
			images(tLangId, 0, 0);
			flagImages(tLangId, 0, 0);
			fcom.updateFaceboxContent(t);
		});
	};

	images = function (tLangId) {
		fcom.ajax(fcom.makeUrl('TeachingLanguage', 'images', [tLangId]), '', function (t) {
			$('#image-listing').html(t);
			fcom.resetFaceboxHeight();
		});
	};

	flagImages = function (tLangId) {
		fcom.ajax(fcom.makeUrl('TeachingLanguage', 'flagImages', [tLangId]), '', function (t) {
			$('#flag-image-listing').html(t);
			fcom.resetFaceboxHeight();
		});
	};

	removeImage = function (tLangId) {
		if (!confirm(langLbl.confirmDeleteImage)) { return; }
		fcom.updateWithAjax(fcom.makeUrl('TeachingLanguage', 'removeImage', [tLangId]), '', function (t) {
			images(tLangId, 0, 0);
		});
	};

	removeFlagImage = function (tLangId) {
		if (!confirm(langLbl.confirmDeleteImage)) { return; }
		fcom.updateWithAjax(fcom.makeUrl('TeachingLanguage', 'removeFlagImage', [tLangId]), '', function (t) {
			flagImages(tLangId, 0, 0);
		});
	};

})();
$(document).on('click', '.tlanguageFile-Js', function () {
	var node = this;
	$('#form-upload').remove();
	var bannerId = document.frmTeachingLanguageMedia.tlanguage_id.value;
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
				url: fcom.makeUrl('TeachingLanguage', 'upload', [bannerId]),
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


$(document).on('click', '.tlanguageFlagFile-Js', function () {
	var node = this;
	$('#form-upload').remove();
	var bannerId = document.frmTeachingLanguageMedia.tlanguage_id.value;
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
				url: fcom.makeUrl('TeachingLanguage', 'flagUpload', [bannerId]),
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