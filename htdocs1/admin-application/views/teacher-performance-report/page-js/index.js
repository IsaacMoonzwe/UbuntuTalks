$(document).ready(function(){
	searchTopLangReport(document.frmSalesReportSearch);
});
(function() {
	var currentPage = 1;
	var runningAjaxReq = false;
	var dv = '#listing';

	goToSearchPage = function(page) {	
		if(typeof page == undefined || page == null){
			page =1;
		}
		var frm = document.frmSalesReportSearchPaging;		
		$(frm.page).val(page);
		searchTopLangReport(frm);
	};
	redirectBack=function(redirecrt){

	var url=	SITE_ROOT_URL +''+redirecrt;
	window.location=url;
	}
	reloadList = function() {
		var frm = document.frmSalesReportSearchPaging;
		searchTopLangReport(frm);
	};
	
	searchTopLangReport = function(form){
		var data = '';
		if (form) {
			data = fcom.frmData(form);
		}
		
		$(dv).html(fcom.getLoader());
		
		fcom.ajax(fcom.makeUrl('TeacherPerformanceReport','search'),data,function(res){
			$(dv).html(res);
		});
	};
	
	exportReport = function(dateFormat){
		document.frmSalesReportSearch.action = fcom.makeUrl('TeacherPerformanceReport','export');
		document.frmSalesReportSearch.submit();		
	}
	
	clearSearch = function(){
		document.frmSalesReportSearch.reset();
		searchTopLangReport(document.frmSalesReportSearch);
	};
})();	