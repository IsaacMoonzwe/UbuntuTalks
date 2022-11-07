$("document").ready(function(){
	var frm = document.frmBibleSrch;	
	searchBible( frm );	
});

(function() {	
	searchBible = function(frm){ 
		var data = fcom.frmData(frm);
		var dv = $("#bibleListingContainer");
		$(dv).html(fcom.getLoader());
		
		fcom.updateWithAjax( fcom.makeUrl('Bible','search'), data,function(ans){
			$.mbsmessage.close();
			if( $('#total_records').length > 0  ){
				$('#total_records').html(ans.totalRecords);
			}
			if( $('#start_record').length > 0  ){
				$('#start_record').html(ans.startRecord);
			}
			if( $('#end_record').length > 0  ){
				$('#end_record').html(ans.endRecord);
			}
			if(ans.totalRecords == 0){
				$('.pagCount').html('');
			}
			$(dv).html( ans.html );
		});
	};
	
	goToSearchPage = function(page) {
		if(typeof page == undefined || page == null){
			page = 1;
		}		
		var frm = document.frmBibleSearchPaging;		
		$(frm.page).val(page);
		searchBible(frm);
	};
	
})();