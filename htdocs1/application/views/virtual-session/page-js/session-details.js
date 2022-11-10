var runningAjaxReq = false;
var session = '#listing';
var navigation = '#navigation';
var sessionwiselisting = '#sessionwiselisting';

var addToCartAjaxRunning = false;
$(document).ready(function () {
	
	agendaSetupTestimonial = function (frm) {
		console.log("Hello123");
		var data = fcom.frmData(frm);
		// $.loader.show();
		if (isEventUserLogged() == 0) {
		  $.loader.hide();
		  logInFormPopUp();
		  return false;
		}
		console.log("data", data);
		//data = data + '&start_time=' + startTime + '&end_Time=' + endTime;
		fcom.updateWithAjax(fcom.makeUrl('VirtualSession', 'agendasetup'), data, function (t) {
			location.reload(true);
			reloadList();
			// if (t.langId > 0) {
			// 	editTestimonialLangForm(t.testimonialId, t.langId);
			// 	return;
			// }
			// $(document).trigger('close.facebox');
		});
	}

	getNavigationMenu();
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


	getNavigationMenu = function () {
		$(navigation).html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('VirtualSession', 'navigationMenu'), '', function (t) {
			$(navigation).html(t);
		});
	};


})();