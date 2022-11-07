var ispaymentSendAjaxRuning = false;
var sendPayment = function (frm) {
	if (!$(frm).validate()) return;
	if (ispaymentSendAjaxRuning) {
		return;
	}
	$.loader.show();
	ispaymentSendAjaxRuning = true;
	var data = fcom.frmData(frm);
	var action = $(frm).attr('action');
	fcom.ajax(action, data, function (t) {
		try {
			ispaymentSendAjaxRuning = false;
			var json = $.parseJSON(t);
			$.loader.hide();
			var el = $('#ajax_message');
			if (json['error']) {
				el.html('<div class="alert alert--danger">' + json['error'] + '<div>');
			}
			if (json['redirect']) {
				$(location).attr("href", json['redirect']);
			}
		} catch (exc) {
			$.loader.hide();
			ispaymentSendAjaxRuning = false;
		}
	});
};

$(function () {
	$("#cc_number").keydown(function () {
		var obj = $(this);
		var cc = obj.val();
		obj.attr('class', 'p-cards');
		if (cc != '') {
			var card_type = getCardType(cc).toLowerCase();
			obj.addClass('p-cards ' + card_type);
			/* var data="cc="+cc;
			fcom.ajax(fcom.makeUrl('AuthorizeAimPay', 'checkCardType'), data, function(t){
				var ans = $.parseJSON(t);
				var card_type = ans.card_type.toLowerCase();
				obj.addClass('p-cards ' + card_type );
			}); */
		}
	});
});

function getCardType(number) {
	// visa
	var re = new RegExp("^4");
	if (number.match(re) != null)
		return "Visa";

	// Mastercard
	re = new RegExp("^5[1-5]");
	if (number.match(re) != null)
		return "Mastercard";

	// AMEX
	re = new RegExp("^3[47]");
	if (number.match(re) != null)
		return "AMEX";

	// Discover
	re = new RegExp("^(6011|622(12[6-9]|1[3-9][0-9]|[2-8][0-9]{2}|9[0-1][0-9]|92[0-5]|64[4-9])|65)");
	if (number.match(re) != null)
		return "Discover";

	// Diners
	re = new RegExp("^36");
	if (number.match(re) != null)
		return "Diners";

	// Diners - Carte Blanche
	re = new RegExp("^30[0-5]");
	if (number.match(re) != null)
		return "Diners - Carte Blanche";

	// JCB
	re = new RegExp("^35(2[89]|[3-8][0-9])");
	if (number.match(re) != null)
		return "JCB";

	// Visa Electron
	re = new RegExp("^(4026|417500|4508|4844|491(3|7))");
	if (number.match(re) != null)
		return "Visa Electron";

	return "";
}
