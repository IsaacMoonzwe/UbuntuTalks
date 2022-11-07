$(function () {
	var dv = '#listItemsLessons';
	var isSetupAjaxrun = false;
	searchLessons = function (frm) {
		var data = fcom.frmData(frm);
		fcom.ajax(fcom.makeUrl('TeacherLessonsPlan', 'getListing'), data, function (t) {
			$(dv).html(t);
		});
	};


	add = function (id) {
		$.loader.show();
		fcom.ajax(fcom.makeUrl('TeacherLessonsPlan', 'add', [id]), '', function (t) {
			$.loader.hide();
			$.facebox(t, 'facebox-medium');
		});
	};

	removeLesson = function (id) {

		$.confirm({
			title: langLbl.Confirm,
			content: langLbl.confirmDeleteLessonPlanText,
			buttons: {
				Proceed: {
					text: langLbl.Proceed,
					btnClass: 'btn btn--primary',
					keys: ['enter', 'shift'],
					action: function () {
						fcom.updateWithAjax(fcom.makeUrl('TeacherLessonsPlan', 'removeLessonSetup'), 'lessonPlanId=' + id, function (t) {
							$.facebox.close();
							searchLessons(document.lessonPlanSerach);
						});
					}
				},
				Quit: {
					text: langLbl.Quit,
					btnClass: 'btn btn--secondary',
					keys: ['enter', 'shift'],
					action: function () {
					}
				}
			}
		});
	};

	removeFile = function (celement, id) {
		$.confirm({
			title: langLbl.Confirm,
			content: langLbl.confirmRemove,
			buttons: {
				Proceed: {
					text: langLbl.Proceed,
					btnClass: 'btn btn--primary',
					keys: ['enter', 'shift'],
					action: function () {
						fcom.ajax(fcom.makeUrl('TeacherLessonsPlan', 'removeFile', [id]), '', function (t) {
							$(celement).parent().remove();
						});
					}
				},
				Quit: {
					text: langLbl.Quit,
					btnClass: 'btn btn--secondary',
					keys: ['enter', 'shift'],
					action: function () {
					}
				}
			}
		});
	};
	checkFileSize = function (file, size) {
		size = parseInt(size);
		size = (size > 0) ? size : 3;
		let files = file.files;
		var fileSize = 0;
		$.mbsmessage.close();
		if (files.length > 0) {
			for (let index = 0; index < files.length; index++) {
				var fileSize = fileSize + files[index].size;
			}
			fileSize = (fileSize / 1048576);
			if (fileSize > size) {
				let name = $(file).attr('data-field-caption');
				fileSizeLabel = langLbl.fileSize.replace("{size}", size);
				fileSizeLabel = fileSizeLabel.replace("{name}", name);
				$.mbsmessage(fileSizeLabel, true, 'alert alert--danger');
				file.value = null;
				return false;
			}
		}
		return true;
	};
	setup = function (frm) {
		if (!$(frm).validate()) return false;
		if (isSetupAjaxrun) { return true; }
		if (!checkFileSize(frm.tlpn_file, 3)) {
			return false;
		}
		if (!checkFileSize(frm.tlpn_image, 1)) {
			return false;
		}
		isSetupAjaxrun = true;
		$.loader.show();
		var formData = new FormData(frm);
		$.ajax({
			url: fcom.makeUrl('TeacherLessonsPlan', 'setup'),
			type: 'POST',
			data: formData,
			mimeType: "multipart/form-data",
			contentType: false,
			processData: false,
			async: true,
			success: function (data, textStatus, jqXHR) {
				$.loader.hide();
				isSetupAjaxrun = false;
				var data = JSON.parse(data);
				if (data.status == 0) {
					$.mbsmessage(data.msg, true, 'alert alert--danger');
					return false;
				} else {
					$.facebox.close();
					$.mbsmessage(data.msg, true, 'alert alert--success');
					searchLessons('');
					setTimeout(function () {
						$.mbsmessage.close();
					}, 2000);
				}
			},
			error: function (jqXHR, textStatus, errorThrown) {
				isSetupAjaxrun = false;
				$('body').find('.-padding-20').remove();
				$.loader.hide();
				$.mbsmessage(jqXHR.msg, true, 'alert alert--danger');
			}
		});
	};

	clearSearch = function () {
		document.lessonPlanSerach.reset();
		searchLessons(document.lessonPlanSerach);
	};
	goToSearchPage = function (page) {
		if (typeof page == undefined || page == null) {
			page = 1;
		}
		var form = document.lessonPlanPaginationForm;
		$(form.page).val(page);
		searchLessons(form);
	};
	searchLessons(document.lessonPlanSerach);

});
