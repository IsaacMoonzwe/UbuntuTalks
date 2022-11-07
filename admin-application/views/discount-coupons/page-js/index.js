$(document).ready(function(){
	searchCoupons(document.frmCouponSearch);
});
$(document).delegate('.language-js','change',function(){
	var lang_id = $(this).val();
	var coupon_id = $("input[name='coupon_id']").val();
	couponImages(coupon_id,lang_id);
});
(function() {
	var currentPage = 1;
	var couponHistoryId = 0;
	var runningAjaxReq = false;
	var dv = '#couponListing';

	goToSearchPage = function(page) {
		if(typeof page == undefined || page == null){
			page = 1;
		}
		var frm = document.frmCouponSearchPaging;		
		$(frm.page).val(page);
		searchCoupons(frm);
	}

	reloadList = function() {
		var frm = document.frmCouponSearchPaging;
		searchCoupons(frm);
	}
	addCouponFormNew = function(id) {			
		$.facebox(function() {
			addCouponForm(id);
		});
	};


	addCouponForm = function(id) {	
		fcom.displayProcessing();		
		//$.facebox(function() {
			fcom.ajax(fcom.makeUrl('DiscountCoupons', 'form', [id]), '', function(t) {
				//$.facebox(t,'faceboxWidth');
				fcom.updateFaceboxContent(t);
			});
		//});
	};

	setupCoupon = function(frm) {
		if (!$(frm).validate()) return;		
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('DiscountCoupons', 'setup'), data, function(t) {
			reloadList();
			if (t.langId>0) {
				addCouponLangForm(t.couponId, t.langId);
				return ;
			}
			$(document).trigger('close.facebox');
		});
	};

	addCouponLangForm = function(couponId, langId) {	
		fcom.displayProcessing();	
		//$.facebox(function() {
			fcom.ajax(fcom.makeUrl('DiscountCoupons', 'langForm', [couponId, langId]), '', function(t) {
				//$.facebox(t,'faceboxWidth');
				fcom.updateFaceboxContent(t);
			});
		//});
	};
	
	setupCouponLang = function(frm){ 
		if (!$(frm).validate()) return;
		var data = fcom.frmData(frm);		
		fcom.updateWithAjax(fcom.makeUrl('DiscountCoupons', 'langSetup'), data, function(t) {
			reloadList();				
			if (t.langId>0) {
				addCouponLangForm(t.couponId, t.langId);
				return ;
			}
			$(document).trigger('close.facebox');
		});
	};

	searchCoupons = function(form){		
		/*[ this block should be written before overriding html of 'form's parent div/element, otherwise it will through exception in ie due to form being removed from div */
		var data = '';
		if (form) {
			data = fcom.frmData(form);
		}
		/*]*/
		$(dv).html(fcom.getLoader());
		
		fcom.ajax(fcom.makeUrl('DiscountCoupons','search'),data,function(res){
			$(dv).html(res);
		});
	};
	
	deleteImage = function(couponId, langId){
		var agree = confirm(langLbl.confirmDeleteImage);
		if(!agree){ return false; }
		fcom.updateWithAjax(fcom.makeUrl('DiscountCoupons', 'removeCouponImage'), 'coupon_id='+couponId+'&lang_id='+langId, function(t) {
			couponImages(couponId,langId);
		});
	};
	
	removeCouponProduct = function(couponId, productId){
		var agree = confirm(langLbl.confirmRemoveOption);
		if(!agree){ return false; }
		fcom.updateWithAjax(fcom.makeUrl('DiscountCoupons', 'removeCouponProduct'), 'coupon_id='+couponId+'&product_id='+productId, function(t) {
			reloadCouponProduct(couponId);
		});
	};
	
	reloadCouponUser = function(couponId){
		$("#coupon_users_list").html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('DiscountCoupons', 'couponUsers', [couponId]), '', function(t) {
			$("#coupon_users_list").html(t);
		});
	};
	
	updateCouponUser = function(couponId,userId){
		var data = 'coupon_id='+couponId+'&user_id='+userId;
		fcom.updateWithAjax(fcom.makeUrl('DiscountCoupons', 'updateCouponUser'), data, function(t) {		
			reloadCouponUser(couponId);
		});
	};
	
	removeCouponUser = function(couponId, userId){
		var agree = confirm(langLbl.confirmRemoveOption);
		if(!agree){ return false; }
		fcom.updateWithAjax(fcom.makeUrl('DiscountCoupons', 'removeCouponUser'), 'coupon_id='+couponId+'&user_id='+userId, function(t) {
			reloadCouponUser(couponId);
		});
	};
	
	deleteRecord = function(id){
		if(!confirm(langLbl.confirmDelete)){return;}
		data = 'id='+id;
		fcom.ajax(fcom.makeUrl('DiscountCoupons','deleteRecord'),data,function(res){		
			reloadList();
		});
	};
	
	clearSearch = function(){
		document.frmCouponSearch.reset();
		searchCoupons(document.frmCouponSearch);
	};
	
	couponHistory = function(couponId){
		couponHistoryId = couponId;
		$.facebox(function() {
			fcom.ajax(fcom.makeUrl('DiscountCoupons', 'usesHistory', [couponHistoryId]), '', function(t) {
				$.facebox(t,'faceboxWidth');
			});
		});
	};
	
	goToCouponHistoryPage = function(page) {	
		if(typeof page == undefined || page == null){
			page = 1;
		}		
		var frm = document.frmHistorySearchPaging;	
		$(frm.page).val(page);
		data = fcom.frmData(frm);
		fcom.ajax(fcom.makeUrl('DiscountCoupons', 'usesHistory', [couponHistoryId]), data, function(t) {
			$.facebox(t,'faceboxWidth');
		});		
	};
	callCouponTypePopulate = function(val){
		if( val == 1 ){
			//if cms Page
			$("#coupon_minorder_div").show();
			$("#coupon_validfor_div").hide();
			
		}if( val == 3 ){
			$("#coupon_minorder_div").hide();
			$("#coupon_validfor_div").show();
		}
	};
	
	toggleStatus = function( e,obj,canEdit ){
		if(canEdit == 0){
			e.preventDefault();
			return;
		}
		if(!confirm(langLbl.confirmUpdateStatus)){
			e.preventDefault();
			return;
		}
		var couponId = parseInt(obj.value);
		if(couponId < 1){
			fcom.displayErrorMessage(langLbl.invalidRequest);
			//$.mbsmessage(langLbl.invalidRequest,true,'alert--danger');
			return false;
		}
		data='couponId='+couponId;
		fcom.ajax(fcom.makeUrl('DiscountCoupons','changeStatus'),data,function(res){
		var ans =$.parseJSON(res);
			if(ans.status == 1){
				$(obj).toggleClass("active");
				fcom.displaySuccessMessage(ans.msg);
				//$.mbsmessage(ans.msg,true,'alert--success');
			}else
			{
				fcom.displayErrorMessage(ans.msg);
				//$.mbsmessage(ans.msg,true,'alert--danger');
			}
		});
	};
	
})();