$(document).ready(function(){
	searchfavorites(document.frmFavSrch);
})

var dv = '#listItems';
searchfavorites = function(frm){	
	var data = fcom.frmData(frm);
	fcom.ajax(fcom.makeUrl('Learner','getFavourites'),data,function(t){
		$(dv).html(t);
	});
};

clearSearch = function(){
	document.frmFavSrch.reset();
	searchfavorites(document.frmFavSrch);
};		

goToSearchPage = function(page) {
	if(typeof page == undefined || page == null){
		page = 1;
	}		
	var frm = document.frmFavSearchPaging;		
	$(frm.page).val(page);
	searchfavorites(frm);
};