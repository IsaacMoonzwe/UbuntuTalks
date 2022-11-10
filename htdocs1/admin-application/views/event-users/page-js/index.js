$(document).ready(function(){

	searchUsers(document.frmUserSearch);

	$(document).on('click',function(){
		$('.autoSuggest').empty();
	});

	$('input[name=\'keyword\']').autocomplete({
		'source': function(request, response) {
			$.ajax({
				url: fcom.makeUrl('EventUsers', 'autoCompleteJson'),
				data: {keyword: request, fIsAjax:1},
				dataType: 'json',
				type: 'post',
				success: function(json) {
					response($.map(json, function(item) {
						return { label: item['name'] +'(' + item['username'] + ')', value: item['id'], name: item['username']	};
					}));
				},
			});
		},
		'select': function(item) {
			$("input[name='user_id']").val( item['value'] );
			$("input[name='keyword']").val( item['name'] );
		}
	});

	$('input[name=\'keyword\']').keyup(function(){
		$('input[name=\'user_id\']').val('');
	});

	//redirect user to login page
	$(document).on('click','ul.linksvertical li a.redirect--js',function(event){
		event.stopPropagation();
	});

});

(function() {
	var currentPage = 1;
	var transactionUserId = 0;
	var active = 1;
	var inActive = 0;

	goToSearchPage = function(page) {
		if(typeof page == undefined || page == null){
			page = 1;
		}
		var frm = document.frmUserSearchPaging;
		$(frm.page).val(page);
		searchUsers(frm);
	};
	userLogin = function(userId) {
		fcom.updateWithAjax(fcom.makeUrl('EventUsers', 'login', [userId]), '', function(t) {
			if(t.status == 1) {
				$.systemMessage.close();
                window.open(fcom.makeUrl('DashboardEventVisitor', '', [],'https://ubuntutalks.com/'),"_blank");
			}
		});
	};

	searchUsers = function(form,page){
		if (!page) {
			page = currentPage;
		}
		currentPage = page;
		/*[ this block should be before dv.html('... anything here.....') otherwise it will through exception in ie due to form being removed from div 'dv' while putting html*/
		var data = '';
		if (form) {
			data = fcom.frmData(form);
		}
		/*]*/

		$("#userListing").html(fcom.getLoader());

		fcom.ajax(fcom.makeUrl('EventUsers','search'),data,function(res){
			$("#userListing").html(res);
		});
	};

	reloadUserList = function() {
		searchUsers(document.frmUserSearchPaging, currentPage);
	};

	fillSuggetion = function(v) {
		$('#keyword').val(v);
		$('.autoSuggest').hide();
	};

	viewUserForm = function(id) {
		var frm = document.frmUserSearchPaging;
		$.facebox(function() {
			fcom.ajax(fcom.makeUrl('EventUsers', 'view', [id]), '', function(t) {
				fcom.updateFaceboxContent(t);
			});
		});
	};

	userForm = function (id){
		fcom.displayProcessing();
		fcom.ajax(fcom.makeUrl('EventUsers', 'form', [id]), '', function(t) {
			fcom.updateFaceboxContent(t);
		});
	};

	transactions = function(userId){
		transactionUserId = userId;
		$.facebox(function() {
			addTransaction(userId);
		});
	};

	changePassword = function(userId){
		fcom.ajax(fcom.makeUrl('EventUsers', 'changePasswordForm'),{userId:userId}, function(t) {
			fcom.updateFaceboxContent(t);
		});
	}

	updatePassword = function(frm){
		if (!$(frm).validate()) return;
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('EventUsers', 'updatePassword'), data, function(t) {
			$.facebox.close()
		});
	}

	addTransaction = function(userId){
		fcom.ajax(fcom.makeUrl('EventUsers', 'transaction',[userId]), '', function(t) {
			fcom.updateFaceboxContent(t);
		});
	};

	goToTransactionPage = function(page) {
		if(typeof page == undefined || page == null){
			page = 1;
		}
		var frm = document.frmTransactionSearchPaging;
		$(frm.page).val(page);
		data = fcom.frmData(frm);
	};

	updateTransaction = function(data) {
		fcom.displayProcessing();
		fcom.ajax(fcom.makeUrl('EventUsers', 'transaction', [transactionUserId]), data, function(t) {
			fcom.updateFaceboxContent(t);
		});
	};

	addUserTransaction = function(userId){
		fcom.displayProcessing();
		fcom.ajax(fcom.makeUrl('EventUsers', 'addUserTransaction', [userId]), '', function(t) {
			fcom.updateFaceboxContent(t);
		});
	};

	setupUserTransaction = function(frm){
		if (!$(frm).validate()) return;
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('EventUsers', 'setupUserTransaction'), data, function(t) {
 			if(t.userId > 0) {
				addTransaction(t.userId);
			}
		});
	};

	setupUsers = function(frm){
		if (!$(frm).validate()) return;
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('EventUsers', 'setup'), data, function(t) {
			$(document).trigger('close.facebox');
		});
	};

	activeStatus = function(obj){
		//if(!confirm(langLbl.confirmUpdateStatus)){return;}
		var userId = parseInt(obj.id);
		if(userId < 1){
			fcom.displayErrorMessage(langLbl.invalidRequest);
			return false;
		}
		data='userId='+userId+'&status='+active;
		fcom.displayProcessing();
		fcom.ajax(fcom.makeUrl('Eventusers','changeStatus'),data,function(res){
		var ans =$.parseJSON(res);
			if(ans.status == 1){
				$(obj).removeClass("inactive");
				$(obj).addClass("active");
				$(".status_"+userId).attr('onclick','inactiveStatus(this)');
				fcom.displaySuccessMessage(ans.msg);
			}else{
				fcom.displayErrorMessage(ans.msg);
			}
		});
		$.systemMessage.close();
	};

	inactiveStatus = function(obj){
		//if(!confirm(langLbl.confirmUpdateStatus)){return;}
		var userId = parseInt(obj.id);
		if(userId < 1){
			fcom.displayErrorMessage(langLbl.invalidRequest);
			return false;
		}
		data='userId='+userId+'&status='+inActive;
		fcom.displayProcessing();
		fcom.ajax(fcom.makeUrl('Eventusers','changeStatus'),data,function(res){
		var ans =$.parseJSON(res);
			if(ans.status == 1){
				$(obj).removeClass("active");
				$(obj).addClass("inactive");
				$(".status_"+userId).attr('onclick','activeStatus(this)');
				fcom.displaySuccessMessage(ans.msg);
			}else{
				fcom.displayErrorMessage(ans.msg);
			}
		});
		$.systemMessage.close();
	};

	clearUserSearch = function(){
		document.frmUserSearch.reset();
		document.frmUserSearch.user_id.value = '';
		searchUsers( document.frmUserSearch );
	};

	getCountryStates = function(countryId,stateId,dv){
		fcom.displayProcessing();
		fcom.ajax(fcom.makeUrl('EventUsers','getStates',[countryId,stateId]),'',function(res){
			$(dv).empty();
			$(dv).append(res);
		});
		$.systemMessage.close();
	};
})();
