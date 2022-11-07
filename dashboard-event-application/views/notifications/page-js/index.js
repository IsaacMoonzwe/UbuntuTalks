$(document).ready(function(){
	searchNotification(document.frmNotificationSrch);
		
});
(function() {
	var dv = '#ordersListing';
	searchNotification = function(frm){
		/*[ this block should be written before overriding html of 'form's parent div/element, otherwise it will through exception in ie due to form being removed from div */
		var data = fcom.frmData(frm);
		/*]*/
		
		$(dv).html( fcom.getLoader() );
		fcom.ajax(fcom.makeUrl('notifications','search'), data, function(res){
			$(dv).html(res);
		}); 
	};
	goToNotificationSearchPage = function(page) {
		if(typeof page==undefined || page == null){
			page =1;
		}
		var frm = document.frmNotificationSrch;		
		$(frm.page).val(page);
		searchNotification(frm);
	}
	deleteRecords = function(){
		var recordIdArr = [];
	
		$('.check-record').each(function(i, obj) {
			if($(this).prop('checked') == true){
				recordIdArr.push($(this).attr('rel'));
			}
		});
		
		if(recordIdArr.length < 1){
			return false;
		}
		
		if(!confirm('Are you sure?')){return;}
		
		var data = 'record_ids='+recordIdArr;
			
		fcom.updateWithAjax(fcom.makeUrl('Notifications', 'deleteRecords'), data, function(t) {						
			reloadList();	
		});	
	};

	changeStatus = function(status){
		var recordIdArr = [];
		$('.check-record').each(function(i, obj) {
			if($(this).prop('checked') == true){
				recordIdArr.push($(this).attr('rel'));
			}
		});	
		if(recordIdArr.length < 1){
			return false;
		}	
		var data = 'record_ids='+recordIdArr+'&status='+status;
	
		fcom.updateWithAjax(fcom.makeUrl('Notifications', 'changeStatus'), data, function(t) {						
			reloadList();	
		});			
	};

	goToSearchPage = function(page) {
		if(typeof page == undefined || page == null){
			page = 1;
		}		
		var frm = document.frmLearnerTeachersSearchPaging;		
		earchNotification(document.frmNotificationSrch);
		$(frm.page).val(page);
		searchTeachers(frm);
	};
	
	
	reloadList = function(){
		searchNotification(document.frmNotificationSrch);
	};	
})();