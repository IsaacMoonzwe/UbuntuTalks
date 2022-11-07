	var runningAjaxReq = false;
	var dv = '#formBlock-js';
	var paymentInfoDiv = '#paymentInfoDiv';
	var profileInfoFormDiv = '#profileInfoFrmBlock';
	var faq='#eventfaq';
	var myAccount='#accountinformation';
	var myRequirement='#requirementinformation';
	var myDailySchedule='#dailyschedule';

var addToCartAjaxRunning = false;
$(document).ready(function () {
	eventProfileInfoForm();
	getEventFaq();
	getMyAccount();
	getMyRequirement();
	getMyDailySchedule();
	$('body').on('click', '.tab-ul-js li a', function () {
		$('.tab-ul-js li').removeClass('is-active');
		$(this).parent('li').addClass('is-active');
	});
});
(function () {
	var runningAjaxReq = false;
	var dv = '#formBlock-js';
	var paymentInfoDiv = '#paymentInfoDiv';
	var profileInfoFormDiv = '#profileInfoFrmBlock';

	checkRunningAjax = function () {
		if (runningAjaxReq == true) {
			return;
		}
		runningAjaxReq = true;
	};
eventProfileInfoForm = function () {
		$(dv).html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('DashboardEventVisitor', 'eventProfileInfoForm'), '', function (t) {
			$(dv).html(t);
			// if (userIsTeacher) {
			// 	getTeacherProfileProgress();
			// }
		});
	};
setUpProfileInfo = function (frm, gotoProfileImageForm) {
		if (!$(frm).validate()) {
			$("html, body").animate({ scrollTop: $(".error").eq(0).offset().top - 100 }, "slow");
			return false;
		}
		$.loader.show();
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('DashboardEventVisitor', 'setUpProfileInfo'), data, function (t) {
			setTimeout(function () {
				$.systemMessage.close();
			}, 3000);
			$.loader.hide();
			
			return true;
		});
	};	
	
	setUpProfileRequirementInfo = function (frm, gotoProfileImageForm) {
		if (!$(frm).validate()) {
			$("html, body").animate({ scrollTop: $(".error").eq(0).offset().top - 100 }, "slow");
			return false;
		}
		$.loader.show();
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('DashboardEventVisitor', 'setUpProfileRequirementInfo'), data, function (t) {
			setTimeout(function () {
				$.systemMessage.close();
			}, 3000);
			$.loader.hide();
			
			return true;
		});
	};		
	changeEmailForm = function () {
		$(dv).html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('DashboardEventVisitor', 'changeEmailForm'), '', function (t) {
			$(dv).html(t);
		});
	};

	setUpEmail = function (frm) {
		if (!$(frm).validate()) return;
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('DashboardEventVisitor', 'setUpEmail'), data, function (t) {
			// changeEmailForm();
			window.location.href=window.location.href;
		});
	};
	getEventFaq = function () {
		$(faq).html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('DashboardEventVisitor', 'eventFaq'), '', function (t) {
			$(faq).html(t);
		});
	};


	getMyAccount = function () {
		$(myAccount).html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('DashboardEventVisitor', 'myAccount'), '', function (t) {
			$(myAccount).html(t);
		});
	};

	getMyRequirement = function () {
		$(myRequirement).html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('DashboardEventVisitor', 'requirement'), '', function (t) {
			$('#requirementinformation').html(t);
		});
	};


	getMyDailySchedule = function () {
		$(myDailySchedule).html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('DashboardEventVisitor', 'dailySchedule'), '', function (t) {
			$('#dailyschedule').html(t);
		});
	};




	changePasswordForm = function(){				
		$(dv).html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('DashboardEventVisitor', 'changePasswordForm'), '', function(t) {			
			$(dv).html(t);
		});
	};
	
	updatePassword = function (frm){
		if (!$(frm).validate()) return;	
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('DashboardEventVisitor', 'updatePassword'), data, function(t) {						
			changePasswordForm();			
		});	
	};

	setUpPassword = function (frm) {
		if (!$(frm).validate()) return;
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('DashboardEventVisitor', 'setUpPassword'), data, function (t) {
			// changePasswordForm();
			window.location.href=window.location.href;
		});
	};
	successHelp = function (frm) {
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('DashboardEventVisitor', 'Sucesshelp'), data, function (t) {
			setTimeout(function () {
				$.systemMessage.close();
			}, 3000);
			$.loader.hide();
	
			frm.reset();
			return true;
		});
	};

})();