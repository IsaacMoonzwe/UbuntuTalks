$(function() {
	
	checkPassword=function(str)
		{
			var re = /^(?=.*\d)(?=.*[!@#$%^&*])(?=.*[a-z])(?=.*).{8,}$/;
			return re.test(str);
		}
	
	changePassword = function(frm, v) {
	
		if (!$(frm).validate()) return;
		/* if (!v.isValid()){
			$('ul.errorlist').each(function(){
				$(this).parents('.field_control:first').addClass('error');
			});
			return; 
		} */
	
		var newPwd = $("#new_password").val();
		if(checkPassword(newPwd)==false){
			$.systemMessage('Your password must contain at least one special character and one digit and minimum 8 characters', 'alert--danger');
			return false;
				
		}
		//return false;
		
		fcom.ajax(fcom.makeUrl("profile", "updatePassword"), fcom.frmData(frm), function(t) {
			var t = $.parseJSON(t);
			if(t.status == 1){
				fcom.waitAndRedirect(t.msg, fcom.makeUrl('profile', 'changePassword'), 2000);
				$.systemMessage(t.msg, 'alert--success');
			}else{
					
				$.systemMessage(t.msg, 'alert--danger');
			}
		});    
		return false;
	}
});