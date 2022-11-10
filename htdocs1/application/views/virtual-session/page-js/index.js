	var runningAjaxReq = false;
	var session = '#listing';
	var navigation = '#navigation';

var addToCartAjaxRunning = false;
$(document).ready(function () {
	getVirtualSession();
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

	getVirtualSession = function () {
		$(session).html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('VirtualSession', 'virtualSessionListing'), '', function (t) {
			$(session).html(t);
		});
	};

	getNavigationMenu= function () {
		$(navigation).html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('VirtualSession', 'navigationMenu'), '', function (t) {
			$(navigation).html(t);
		});
	};

	
	
})();