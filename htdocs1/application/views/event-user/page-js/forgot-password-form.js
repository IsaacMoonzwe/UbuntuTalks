(function() {
	forgot = function(frm, v) {
		v.validate();
		if (!v.isValid()) return;
		fcom.updateWithAjax(fcom.makeUrl('EventUser', 'forgotPassword'), fcom.frmData(frm), function(t) {
			if( t.status == 1){
				location.href = fcom.makeUrl('EventUser', 'loginForm');
			}else{
				$.systemMessage(t.msg,'alert alert--danger');				
			}
			$.mbsmessage.close();
			return;
		});
	};
})();