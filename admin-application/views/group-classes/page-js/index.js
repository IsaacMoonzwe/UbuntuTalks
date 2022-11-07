$(function () {
	var currentPage = 1;
	var dv = '#listItems';
	searchGroupClasses = function (frm, page) {
		if (!page) {
			page = currentPage;
		}
		currentPage = page;
		var data = '';
		if (frm) {
			data = fcom.frmData(frm);
		}

		fcom.ajax(fcom.makeUrl('GroupClasses', 'search'), data, function (t) {
			$(dv).html(t);
		});
	};

	viewJoinedLearners = function (id) {
		fcom.displayProcessing();
		fcom.ajax(fcom.makeUrl('GroupClasses', 'viewJoinedLearners', [id]), '', function (t) {
			fcom.updateFaceboxContent(t);
		});
	};

	form = function (id) {
		fcom.displayProcessing();
		fcom.ajax(fcom.makeUrl('GroupClasses', 'form', [id]), '', function (t) {
			fcom.updateFaceboxContent(t);
			jQuery('#grpcls_start_datetime,#grpcls_end_datetime').each(function () {
				$(this).datetimepicker({
					format: 'Y-m-d H:i'
				});
			});
		});
	};

	removeClass = function (id) {
		if (!confirm(langLbl.confirmRemove)) {
			return;
		}
		$.loader.show();
		fcom.ajax(fcom.makeUrl('GroupClasses', 'removeClass', [id]), '', function (t) {
			$.loader.hide();
			try {
				let res = JSON.parse(t);
				if (res.status == 1) {
					$.systemMessage(res.msg, 'alert alert--success');
					searchGroupClasses(document.frmSrch);
					return;
				}
				$.systemMessage(res.msg, 'alert alert--danger');
			} catch (exc) {
				console.error(exc);
			}
			searchGroupClasses(document.frmSrch);
		});

	};

	cancelClass = function (id) {
		if (confirm(langLbl.confirmCancel)) {
			$.loader.show();
			fcom.ajax(fcom.makeUrl('GroupClasses', 'cancelClass', [id]), '', function (t) {
				$.loader.hide();
				let res = JSON.parse(t);
				if (res.status == 1) {
					$.systemMessage(res.msg, 'alert alert--success');
					searchGroupClasses(document.frmSrch);
					return;
				}
				$.systemMessage(res.msg, 'alert alert--danger');


			});
		}
	};

	setup = function (frm) {
		if (!$(frm).validate()) return false;
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('GroupClasses', 'setup'), data, function (t) {
			searchGroupClasses(document.frmSrch);
			$(document).trigger('close.facebox');
		});
	};

	clearSearch = function () {
		document.frmSrch.reset();
		document.frmSrch.teacher_id.value = '';
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

	jQuery('#grpcls_start_datetime,#grpcls_end_datetime').each(function () {
		$(this).datetimepicker({
			format: 'Y-m-d H:i'
		});
	});

	$(document).on('click', function () {
		$('.autoSuggest').empty();
	});

	$('input[name=\'teacher\']').autocomplete({
		'source': function (request, response) {
			$.ajax({
				url: fcom.makeUrl('Users', 'autoCompleteJson'),
				data: { keyword: request, fIsAjax: 1 },
				dataType: 'json',
				type: 'post',
				success: function (json) {
					response($.map(json, function (item) {
						return { label: item['name'] + '(' + item['username'] + ')', value: item['id'], name: item['username'] };
					}));
				},
			});
		},
		'select': function (item) {
			$("input[name='teacher_id']").val(item['value']);
			$("input[name='teacher']").val(item['name']);
		}
	});

	$('input[name=\'teacher\']').keyup(function () {
		$('input[name=\'teacher_id\']').val('');
	});

	searchGroupClasses(document.frmSrch);
});