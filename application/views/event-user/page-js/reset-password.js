(function() {
	resetpwd = function(frm, v) {
		v.validate();
		if (!v.isValid()) return;
		fcom.updateWithAjax(fcom.makeUrl('EventUser', 'resetPasswordSetup'), fcom.frmData(frm), function(t) {
			setTimeout(function(){
				location.href = fcom.makeUrl('EventUser', 'loginForm');
			}, 3000);
		});
	};
})();