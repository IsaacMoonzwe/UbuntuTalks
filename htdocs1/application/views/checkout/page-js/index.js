var financialSummaryDiv = "#financialSummaryListing";
var paymentDiv = '#paymentDiv';
var couponDiv = '.cpn-frm';
var lessonDetails = '#lessonDetails'

var addToCartAjaxRunning = false;
$("document").ready(function () {
	loadFinancialSummary();
});

(function () {
	loadFinancialSummary = function () {
		$(financialSummaryDiv).html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('Checkout', 'listFinancialSummary'), '', function (ans) {
			var ans = JSON.parse(ans);
			$(financialSummaryDiv).html(ans.html);
			if (ans.couponApplied > 0) {
				$('.coupon-input').click();
			}
		});
	};

	loadPaymentSummary = function () {
		$(paymentDiv).html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('Checkout', 'paymentSummary'), '', function (ans) {
			try {
				data = JSON.parse(ans);
				if (data.redirectUrl != '') {
					window.location.href = data.redirectUrl;
				}
				return false;
			} catch (e) {
				$(paymentDiv).html(ans);
			}


			//$("#payment_methods_tab  li:first a").trigger('click');
		});
	};

	walletSelection = function (el) {
		$.loader.show();
		var wallet = ($(el).is(":checked")) ? 1 : 0;
		var data = 'payFromWallet=' + wallet;
		fcom.ajax(fcom.makeUrl('Checkout', 'walletSelection'), data, function (ans) {
			loadPaymentSummary();
			loadFinancialSummary();
			$.loader.hide();
		});
	};

	confirmOrder = function (frm) {
		var data = fcom.frmData(frm);
		$.loader.show();
		fcom.ajax(fcom.makeUrl('Checkout', 'confirmOrder'), data, function (ans) {
			try {
					$(document).trigger('close.mbsmessage');	
					if (ans.redirectUrl == '') {
						$.loader.hide();
					}
					if (ans.status != 1) {
						$.mbsmessage(ans.msg,true, 'alert alert--danger');
					}else{
						$.mbsmessage( ans.msg,true, 'alert alert--success');
					}
					if (ans.redirectUrl != '') {
						setTimeout(function(){  window.location.href = ans.redirectUrl }, 3000);
					}

			} catch (error) {
				$.loader.hide();
				$(location).attr("href", fcom.makeUrl('Teachers'));
			}

			
		},{fOutMode:'json'});
		return false;
	}

	$(document).on('click', '.coupon-input', function () {
		$(couponDiv).html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('Checkout', 'getCouponForm'), '', function (ans) {
			$(couponDiv).html(ans);
			$("input[name='coupon_code']").focus();
		});
	});

	applyPromoCode = function (frm) {
		if (!$(frm).validate()) return;
		var data = fcom.frmData(frm);

		fcom.updateWithAjax(fcom.makeUrl('Cart', 'applyPromoCode'), data, function (res) {
			$("#facebox .close").trigger('click');
			$.systemMessage.close();
			loadFinancialSummary();
			loadPaymentSummary();
		});
	};

	triggerApplyCoupon = function (coupon_code) {
		document.frmPromoCoupons.coupon_code.value = coupon_code;
		applyPromoCode(document.frmPromoCoupons);
		return false;
	};

	removePromoCode = function () {
		fcom.updateWithAjax(fcom.makeUrl('Cart', 'removePromoCode'), '', function (res) {
			loadFinancialSummary();
			loadPaymentSummary();
		});
	};

	redeemGiftcardForm = function () {
		$.facebox(function () {
			fcom.ajax(fcom.makeUrl('wallet', 'giftcard-redeem-form'), '', function (t) {
				$.facebox(t, 'faceboxWidth');
			});
		});
	};
	giftcardRedeem = function (frm1) {
		if (!$(frm1).validate()) return;
		var data1 = fcom.frmData(frm1);

		fcom.updateWithAjax(fcom.makeUrl('Wallet', 'reedemGiftcard'), data1, function (res1) {
			searchCredits(document.frmCreditSrch);
		});
	};


	addToCart = function(teacherId, languageId, lessonDuration, lessonQty) {
		$.loader.show();
		teacherId = parseInt(teacherId);
		lessonQty = parseInt(lessonQty);
		lessonDuration = parseInt(lessonDuration);
		languageId = parseInt(languageId);
		
        if (addToCartAjaxRunning) {
			event.preventDefault();
            return false;
        }

		
        addToCartAjaxRunning = true;
		var data = '&teacherId=' + teacherId + '&lessonQty=' + lessonQty + '&languageId=' + languageId + '&lessonDuration=' + lessonDuration;
        fcom.ajax(fcom.makeUrl('Cart', 'add'), data, function (ans) {
			
			if (ans.status != 1) {
				$.loader.hide();
				addToCartAjaxRunning = false;
				$.mbsmessage(ans.msg,true, 'alert alert--danger');
				if( ans.redirectUrl ){
					setTimeout(function(){ window.location.href = ans.redirectUrl }, 3000);
				}
				return ;
			}
			if (ans.redirectUrl) {
                loadFinancialSummary();
                loadPaymentSummary();
				getTeacherPriceSlabs(languageId, lessonDuration);
            }
            $('.cart-lang-id-js').html(teachLanguages[languageId]);
            $('.cart-lesson-duration').html(langLbl.lessonMints.replace("%s", lessonDuration));
            addToCartAjaxRunning = false;
			$.loader.hide();
        }, {
			fOutMode:'json',
            errorFn: function (errorDet) {
				$.loader.hide();
                addToCartAjaxRunning = false;
                console.log(errorDet);
            }
        });
    };

	updateCart =  function (teacherId) {

		teacherId = parseInt(teacherId);

		if(1 > teacherId)
		{
			return false;
		}
		
		lessonQty = parseInt($("#lessonQty").val());
		lessonDuration = parseInt($('[name="lessonDuration"]:checked').val());
		languageId = parseInt($('[name="language"]:checked').val());
		addToCart(teacherId, languageId, lessonDuration, lessonQty);
	};

	getTeacherPriceSlabs = function (languageId, lessonDuration) {

		var data = 'languageId=' + languageId + '&lessonDuration=' + lessonDuration;
		$.loader.show()
		fcom.ajax(fcom.makeUrl('Checkout', 'getTeacherPriceSlabs'), data, function (ans) {
			$('#price-slabs').html(ans);
			$.loader.hide()
		});
	};

	getBookingDurations = function () {
		fcom.ajax(fcom.makeUrl('Checkout', 'getBookingDurations'), '', function (ans) {
			$('#booking-durations-js').html(ans);
		});
	};


})();