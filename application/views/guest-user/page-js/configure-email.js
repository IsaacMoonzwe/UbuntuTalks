$(document).ready(function(){
	//changeEmailForm();
});

(function() {
	var runningAjaxReq = false;

	checkRunningAjax = function(){
		if( runningAjaxReq == true ){
			return;
		}
		runningAjaxReq = true;
	};

	// changeEmailForm = function(){
	// 	$(dv).html(fcom.getLoader());
	// 	fcom.ajax(fcom.makeUrl('GuestUser', 'changeEmailForm'), '', function(t) {
	// 		$(dv).html(t);
	// 	});
  //   };

	updateEmail = function (frm){
		if (!$(frm).validate()) return;
		var data = fcom.frmData(frm);
		fcom.ajax(fcom.makeUrl('GuestUser', 'updateEmail'), data, function(ans) {
			$.mbsmessage.close();
			if (ans.status != 1) {
				$(document).trigger('close.mbsmessage');
				$.mbsmessage(ans.msg , true, 'alert alert--danger');
			}else{
				$.mbsmessage(ans.msg , true, 'alert alert--success');
			}
			if( ans.redirectUrl ){
				setTimeout(function(){window.location.href = ans.redirectUrl }, 1000);
			}

			frm.reset();
		},{fOutMode:'json'});
    };


})();
