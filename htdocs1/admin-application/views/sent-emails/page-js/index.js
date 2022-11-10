$(document).ready(function(){
	searchSentEmails(document.sentEmailSrchForm);

	$(document).on('click',function(){
		$('.autoSuggest').empty();
	});

	$('input[name=\'keyword\']').autocomplete({
		'source': function(request, response) {
			$.ajax({
				url: fcom.makeUrl('SentEmails', 'autoCompleteJson'),
				data: {keyword: request, fIsAjax:1},
				dataType: 'json',
				type: 'post',
				success: function(json) {
					
					response($.map(json, function(item) {
						return { label: item['name'], value: item['id'], name: item['name']	};
					}));
				},
			});
		},
		'select': function(item) {
			console.log("hry",item);
			$("input[name='user_id']").val( item['value'] );
			$("input[name='keyword']").val( item['name'] );
		}
	});

	// $('input[name=\'keyword\']').keyup(function(){
	// 	$('input[name=\'user_id\']').val('');
	// });
});

(function() {
	var currentPage = 1;
	var runningAjaxReq = false;

	searchSentEmails = function(frm) {
		if(runningAjaxReq == true){
			return;
		}
		runningAjaxReq = true;
		/*[ this block should be before dv.html('... anything here.....') otherwise it will through exception in ie due to form being removed from div 'dv' while putting html*/
		var data = '';
		if (frm) {
			data = fcom.frmData(frm);
		}
		/*]*/
		
		var dv = $('#emails-list');
		$(dv).html(langLbl.processing);
		
		$("#emails-list").html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('SentEmails', 'search'), data, function(res) {
			dv.html(res);
			runningAjaxReq = false;
		});
	};
	
	goToSearchPage = function( page ) {
		if( typeof page == undefined || page == null ){
			page = 1;
		}
		var frm = document.frmSentEmailSearchPaging;		
		$(frm.page).val(page);
		searchSentEmails(frm);
	}

	clearUserSearch = function(){
		document.sentEmailSrchForm.reset();
		document.sentEmailSrchForm.emailarchive_id.value = '';
		searchUsers( document.sentEmailSrchForm );
	};
	
	/* searchProductCategories = function(form){
		//$.mbsmessage('Please wait...');
		$("#listing").html('Loading....');
		var data = '';
		if (form) {
			data = fcom.frmData(form);
		}
		fcom.ajax(fcom.makeUrl('productCategories','search'),data,function(res){
			$("#listing").html(res);
		});
	}; */
	
	listPage = function(page) {
		searchSentEmails(document.sentEmailSrchForm, page);
	};
	
	reloadProgramsList = function() {
		document.sentEmailSrchForm.reset();
		setTimeout(function(){
			searchSentEmails(document.sentEmailSrchForm, 1);
		}, 500);
		
	}
	
})();