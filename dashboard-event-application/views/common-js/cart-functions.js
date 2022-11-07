var cart = {
	props: {
		teacherId: 0,
		languageId: 0,
		lessonDuration: 0,
		lessonQty: 0
	},
	couponCode: '',
	isWalletSelect: 0,
	paymentMethodId: 0,
	getLessonQtyPrice: function (lessonQty) {
		teacherId = parseInt(cart.props.teacherId);
		languageId = parseInt(cart.props.languageId);
		lessonDuration = parseInt(cart.props.lessonDuration);
		lessonQty = parseInt(lessonQty);
		if (1 > lessonQty && 1 > teacherId || 1 > languageId || 1 > lessonDuration) {
			return false;
		}
		props = cart.props
		props.lessonQty = lessonQty;
		fcom.ajax(fcom.makeUrl('Checkout', 'getLessonQtyPrice', [], confFrontEndUrl), props, function (res) {
			res.status = parseInt(res.status);
			if (res.status == 1) {
				$('.slab-price-js').html(res.priceLabel);
				cart.props.lessonQty = lessonQty;
				return;
			}
			$.mbsmessage(res.msg, true, 'alert alert--danger');

		}, { fOutMode: 'json' });
	},
	walletSelection: function (el) {
		cart.isWalletSelect = ($(el).is(":checked")) ? 1 : 0;
		var data = 'payFromWallet=' + cart.isWalletSelect;
		$.loader.show();
		fcom.ajax(fcom.makeUrl('Checkout', 'walletSelection', [], confFrontEndUrl), data, function (ans) {
			$.loader.hide();
			cart.checkoutStep("getPaymentSummary", "");
		});
	},
	applyPromoCode: function (code) {
		cart.couponCode = code.toString();
		if (cart.couponCode == '') {
			return;
		}
		data = 'coupon_code=' + cart.couponCode;
		fcom.updateWithAjax(fcom.makeUrl('Cart', 'applyPromoCode', [], confFrontEndUrl), data, function (res) {
			cart.checkoutStep("getPaymentSummary", "");
		});
	},
	removePromoCode: function () {
		fcom.updateWithAjax(fcom.makeUrl('Cart', 'removePromoCode', [], confFrontEndUrl), '', function (res) {
			cart.checkoutStep("getPaymentSummary", "");
		});
	},
	proceedToStep: function (cartDetails, step) {
		this.props = $.extend(true, cart.props, cartDetails);
		if (step == 'getPaymentSummary') {
			return cart.add(this.props);
		}
		cart.checkoutStep(step, this.props);
	},
	add: function (data) {
		$.loader.show();
		fcom.ajax(fcom.makeUrl('Cart', 'add', [], confFrontEndUrl), data, function (res) {
			$.loader.hide();
			if (res.status == 1) {
				if (res.isFreeLesson) {
					cart.confirmOrder();
					return;
				}

				if (res.redirectUrl) {
					window.location.href = res.redirectUrl;
					return;
				}
				cart.checkoutStep("getPaymentSummary", "");
			} else {
				$.mbsmessage(res.msg, true, 'alert alert--danger');
			}

			$.loader.hide();
		}, { fOutMode: 'json' });
	},
	checkoutStep: function (step, data) {
		$.loader.show();
		fcom.ajax(fcom.makeUrl('Checkout', step, [], confFrontEndUrl), data, function (data) {
			$.loader.hide();
			try {
				data = JSON.parse(data);
				if (data.status == 0) {
					$.mbsmessage(data.msg, true, 'alert alert--danger');
					$.loader.hide();
					return;
				}
			} catch (e) {
				$.facebox(data, 'checkout-step ' + step);
			}
		});
	},
	confirmOrder: function (orderType) {
		cart.paymentMethodId = parseInt($('[name="payment_method"]:checked').val());
		orderType = parseInt(orderType);
		data = "order_type=" + orderType + "&pmethod_id=" + cart.paymentMethodId;
		$.loader.show();
		fcom.updateWithAjax(fcom.makeUrl('Checkout', 'confirmOrder', [], confFrontEndUrl), data, function (ans) {
			if (ans.redirectUrl != '') {
				window.location.href = ans.redirectUrl;
			} else {
				$.loader.hide();
			}
		});
	}
};

$(document).bind('afterClose.facebox', function () {
	$(document).bind('afterClose.facebox', function () {
		cart.props = {
			teacherId: 0,
			languageId: 0,
			lessonDuration: 0,
			lessonQty: 0,
		};
		cart.couponCode = '';
		cart.isWalletSelect = 0;
		cart.paymentMethodId = 0;
	});

});