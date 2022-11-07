$(function () {
    var dv = '#listItems';
    searchGroupClasses = function (frm) {
        $('.search-filter').hide();
        var data = fcom.frmData(frm);
        $(dv).html(fcom.getLoader());
        fcom.ajax(fcom.makeUrl('TeacherKidsClasses', 'search'), data, function (t) {
            $(dv).html(t);
        });
    };

    form = function (id) {
        $.loader.show();
        fcom.ajax(fcom.makeUrl('TeacherKidsClasses', 'form', [id]), '', function (t) {
            $.facebox(t, 'facebox-large');
            var lastValue = weekDayNames.shortName[6];
            weekDayNames.shortName.pop();
            weekDayNames.shortName.unshift(lastValue);
            $.fn.datetimepicker.defaults.i18n = {
                '': {
                    months: monthNames.longName,
                    dayOfWeek: weekDayNames.shortName
                },
                en: { // English
                    months: [
                      "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"
                    ],
                    dayOfWeek: [
                      "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"
                    ],
                  
                  },
            };
            jQuery('#grpcls_start_datetime,#grpcls_end_datetime').each(function () {
                $(this).datetimepicker({
                    format: 'Y-m-d H:i',
                    step: 15,
                    formatDate: 'Y-m-d',
                    defaultDate: currentDate,
                    defaultTime: currentTime,
                    lang: 'en',
                });
            });
            $.loader.hide();
        });
    };

    removeClass = function (elem, id) {
        if (confirm(langLbl.confirmRemove)) {
            $(elem).closest('tr').remove();
            $.loader.show();
            fcom.ajax(fcom.makeUrl('TeacherKidsClasses', 'removeClass', [id]), '', function (t) {
                $.loader.hide();
                let res = JSON.parse(t);
                if (res.status == 1) {
                    $.mbsmessage(res.msg, true, 'alert alert--success');
                    searchGroupClasses(document.frmSrch);
                    return;
                }
                $.mbsmessage(res.msg, true, 'alert alert--danger');
            });
        }
    };

    cancelClass = function (id) {
        if (confirm(langLbl.confirmCancel)) {
            $.loader.show();
            fcom.ajax(fcom.makeUrl('TeacherKidsClasses', 'cancelClass', [id]), '', function (t) {
                $.loader.hide();
                let res = JSON.parse(t);
                if (res.status == 1) {
                    $.mbsmessage(res.msg, true, 'alert alert--success');
                    searchGroupClasses(document.frmSrch);
                    return;
                }
                $.mbsmessage(res.msg, true, 'alert alert--danger');
            });
        }
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
        if (!checkFileSize(frm.tlpn_image, 1)) {
			return false;
		}
        $.loader.show();
        var formData = new FormData(frm);
        $.ajax({
            url: fcom.makeUrl('TeacherKidsClasses', 'setup'),
            type: 'POST',
            data: formData,
            mimeType: "multipart/form-data",
            contentType: false,
            processData: false,
            success: function (data, textStatus, jqXHR) {
                var data = JSON.parse(data);
                $.loader.hide();
                if (data.status == 0) {
                    $.mbsmessage(data.msg, true, 'alert alert--danger');
                    return false;
                } else {
                    searchGroupClasses(document.frmSrch);
                    $.mbsmessage(data.msg, true, 'alert alert--success');
                    if (data.lang_id > 0) {
                        editGroupClassLangForm(data.grpcls_id, data.lang_id);
                    }
                }
                setTimeout(function () {
                    $.systemMessage.close();
                }, 2000);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $.loader.hide();
                $.systemMessage(jqXHR.msg, true);
            }
        });
    };
    clearSearch = function () {
        document.frmSrch.reset();
        searchGroupClasses(document.frmSrch);
    };
    goToSearchPage = function (page) {
        if (typeof page == undefined || page == null) {
            page = 1;
        }
        var frm = document.frmSearchPaging;
        $(frm.page).val(page);
        searchGroupClasses(frm);
    };
    editGroupClassLangForm = function (groupClassId, langId) {
        $.loader.show();
        fcom.ajax(fcom.makeUrl('TeacherKidsClasses', 'langForm', [groupClassId, langId]), '', function (t) {
            $.facebox(t, 'facebox-medium');
            $.loader.hide();
        });
    };
    setupGroupClassLang = function (frm) {
        if (!$(frm).validate()) return;
        var data = fcom.frmData(frm);
        $.loader.show();
        fcom.updateWithAjax(fcom.makeUrl('TeacherKidsClasses', 'langSetup'), data, function (t) {
            $.loader.hide();
            if (t.langId > 0) {
                editGroupClassLangForm(t.grpclsId, t.langId);
                return;
            }
            $(document).trigger('close.facebox');
            searchGroupClasses(document.frmSrch);
        });
    }
    searchGroupClasses(document.frmSrch);
});