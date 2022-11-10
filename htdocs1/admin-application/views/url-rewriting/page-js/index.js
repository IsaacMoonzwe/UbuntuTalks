$(document).ready(function () {
	searchUrls(document.frmSearch);
});
(function () {
	var currentPage = 1;
	var dv = '#listing';

	goToSearchPage = function (page) {
		if (typeof page == undefined || page == null) {
			page = 1;
		}
		var frm = document.frmUrlSearchPaging;
		$(frm.page).val(page);
		searchUrls(frm);
	};

	reloadList = function () {
		var frm = document.frmUrlSearchPaging;
		searchUrls(frm);
	};

	searchUrls = function (form) {
		var data = '';
		if (form) {
			data = fcom.frmData(form);
		}
		$(dv).html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('UrlRewriting', 'search'), data, function (res) {
			$(dv).html(res);
		});
	};

	urlForm = function (UrlRewriteId, originalUrl) {
		$.facebox(function () {
			fcom.ajax(fcom.makeUrl('UrlRewriting', 'form'), { UrlRewriteId: UrlRewriteId, originalUrl: originalUrl }, function (t) {
				$.facebox(t, 'faceboxWidth');
			});
		});
	};

	setup = function (frm) {
		if (!$(frm).validate()) return;
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('UrlRewriting', 'setup'), data, function (t) {
			reloadList();
			$(document).trigger('close.facebox');
		});
	};

	deleteRecord = function (id) {
		if (!confirm(langLbl.confirmDelete)) { return; }
		data = 'id=' + id;
		fcom.updateWithAjax(fcom.makeUrl('UrlRewriting', 'deleteRecord'), data, function (res) {
			reloadList();
		});
	};

	clearSearch = function () {
		document.frmSearch.reset();
		searchUrls(document.frmSearch);
	};

})();	