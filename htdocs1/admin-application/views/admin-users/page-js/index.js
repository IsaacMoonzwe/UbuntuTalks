$(document).ready(function(){
	searchAdminUsers();
	
	$(document).on('click','ul.linksvertical li a.redirect--js',function(event){
		event.stopPropagation();
	});		
});

(function() {
	var runningAjaxReq = false;
	var active = 1;
	var inActive = 0;
	var dv = '#listing';
	
	reloadList = function() {
		searchAdminUsers();
	};	
	
	searchAdminUsers = function(){
		$(dv).html(fcom.getLoader());
		
		fcom.ajax(fcom.makeUrl('AdminUsers','search'),'',function(res){
			$(dv).html(res);			
		});
	};
	
	adminUserForm = function(id) {
		$.facebox(function() {
			addForm(id);
		});
	};
	
	addForm = function(id) {
		fcom.ajax(fcom.makeUrl('AdminUsers', 'form', [id]), '', function(t) {
			fcom.updateFaceboxContent(t);
		});		
	};
	
	editAdminUserForm = function(adminId){
		$.facebox(function() {
			editForm(adminId);
		});
	};
	
	editForm = function(adminId){
		fcom.ajax(fcom.makeUrl('AdminUsers', 'form', [adminId]), '', function(t) {
			fcom.updateFaceboxContent(t);
		});		
	};
	
	setupAdminUser = function (frm){
		if (!$(frm).validate()) return;
		var pwd = $("input[name=password]").val();
		if(checkPassword(pwd)==false){
			$.systemMessage('Your password must contain at least one special character and one digit and minimum 8 characters', 'alert--danger');
			return false;
		}
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('AdminUsers', 'setup'), data, function(t) {
			reloadList();
			$(document).trigger('close.facebox');
		});
	}
	
	changePasswordForm = function(id) {	
		$.facebox(function() {
			fcom.ajax(fcom.makeUrl('AdminUsers', 'changePassword', [id]), '', function(t) {
				fcom.updateFaceboxContent(t);
			});
		});
	};
	
	setupChangePassword = function (frm){
		if (!$(frm).validate()) return;
		var pwd = $("input[name=password]").val();
		if(checkPassword(pwd)==false){
			$.systemMessage('Your password must contain at least one special character and one digit and minimum 8 characters', 'alert--danger');
			return false;
		}
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('AdminUsers', 'setupChangePassword'), data, function(t) {
			reloadList();
			$(document).trigger('close.facebox');
		});
	}
	
	activeStatus = function(obj){
		
		if(!confirm(langLbl.confirmUpdateStatus)){return;}
		var adminId = parseInt(obj.id);
		if(adminId < 1){
			fcom.displayErrorMessage(langLbl.invalidRequest);
			return false;
		}
		data='adminId='+adminId+'&status=1';
		fcom.ajax(fcom.makeUrl('AdminUsers','changeStatus'),data,function(res){
		var ans =$.parseJSON(res);
			if(ans.status == 1){
				fcom.displaySuccessMessage(ans.msg);
				$(obj).removeClass("inactive");
				$(obj).addClass("active");
				$(".status_"+adminId).attr('onclick','inactiveStatus(this)');
				//setTimeout(function(){ reloadList(); }, 1000);
			}else{
				fcom.displayErrorMessage(ans.msg);
			}
		});
	};
	
	inactiveStatus = function(obj){
		
		if(!confirm(langLbl.confirmUpdateStatus)){return;}
		var adminId = parseInt(obj.id);
		if(adminId < 1){
			fcom.displayErrorMessage(langLbl.invalidRequest);
			return false;
		}
		data='adminId='+adminId+'&status=0';
		fcom.ajax(fcom.makeUrl('AdminUsers','changeStatus'),data,function(res){
		var ans =$.parseJSON(res);
			if(ans.status == 1){
				fcom.displaySuccessMessage(ans.msg);
				$(obj).removeClass("active");
				$(obj).addClass("inactive");
				$(".status_"+adminId).attr('onclick','activeStatus(this)');
				//setTimeout(function(){ reloadList(); }, 1000);
			}else{
				fcom.displayErrorMessage(ans.msg);
			}
		});
	};
	
	/* deleteRecord = function(id){
		if(!confirm(langLbl.confirmDelete)){return;}
		data='adminId='+id;
		fcom.ajax(fcom.makeUrl('AdminUsers','deleteRecord'),data,function(res){		
			reloadList();
		});
	}; */
	
	clearSearch = function(){
		document.frmSearch.reset();
		searchAdminUsers(document.frmSearch);
	};
	checkPassword=function(str)
	{
		var re = /^(?=.*\d)(?=.*[!@#$%^&*])(?=.*[a-z])(?=.*).{8,}$/;
		return re.test(str);
	}
})();
