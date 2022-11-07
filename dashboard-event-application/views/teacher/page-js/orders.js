var lastValue = weekDayNames.longName[6];
weekDayNames.longName.pop();
weekDayNames.longName.unshift(lastValue);

var lastValue = weekDayNames.shortName[6];
weekDayNames.shortName.pop();
weekDayNames.shortName.unshift(lastValue);

defaultsValue = {
	monthNames: monthNames.longName,
	monthNamesShort: monthNames.shortName,
	dayNamesMin: weekDayNames.shortName,
	dayNamesShort: weekDayNames.shortName,
	currentText: langLbl.today,
	closeText: langLbl.done,
	prevText: langLbl.prev,
	nextText: langLbl.next,
	isRTL : (layoutDirection == 'rtl')
}

$.datepicker.regional[''] = $.extend(true, {}, defaultsValue);
$.datepicker.setDefaults($.datepicker.regional['']);

$(document).ready(function () {
	searchOrders(document.frmOrderSrch);
})

var dv = '#listItems';
searchOrders = function (frm) {
	var data = fcom.frmData(frm);
	fcom.ajax(fcom.makeUrl('Teacher', 'getOrders'), data, function (t) {
		$(dv).html(t);
	});
};

clearSearch = function () {
	document.frmOrderSrch.reset();
	searchOrders(document.frmOrderSrch);
};

goToSearchPage = function (page) {
	if (typeof page == undefined || page == null) {
		page = 1;
	}
	var frm = document.frmOrderSearchPaging;
	$(frm.page).val(page);
	searchOrders(frm);
};