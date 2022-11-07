var cart = {
    props: {
        oneOnOne:0,
        fromKids:0,
        teacherId: 0,
        languageId: 0,
        lessonDuration: 0,
        lessonQty: 0,
        fromGroup:0,
        fName:'',
        lName:'',
        kidsCount:1,
        isCompany:0,
        referralName:'',
        sponsorship:0,
        isPrivateClass:0,
    },
    cartControllerName:'Cart',
    checkoutControllerName:'Checkout',
    couponCode: '',
    isWalletSelect: 0,
    paymentMethodId: 0,
    addFirstName:function(fName){
        props = cart.props
		props.fName = fName;
        fcom.ajax(fcom.makeUrl('Checkout', 'addFirstName'), cart.props, function (res) {
            res.status = parseInt(res.status);
            if (res.status == 1) {
                // $('#lName').html(res.fName);
                cart.props.fName = fName;
                return;
            }
            $.mbsmessage(res.msg, true, 'alert alert--danger');

        }, {fOutMode: 'json'});
    },
    addLastName:function(fName,lName){
        if(cart.fName==''){
            cart.addFirstName(fName)
        }
        props = cart.props
		props.lName = lName;
        fcom.ajax(fcom.makeUrl('Checkout', 'addLastName'), cart.props, function (res) {
            res.status = parseInt(res.status);
            if (res.status == 1) {
                // $('#lName').html(res.fName);
                cart.props.lName = lName;
                return;
            }
            $.mbsmessage(res.msg, true, 'alert alert--danger');

        }, {fOutMode: 'json'});
    },
     addClassFirstName:function(fName){
        props = cart.props
        props.fName = fName;
        fcom.ajax(fcom.makeUrl('Classes', 'addFirstName'), cart.props, function (res) {
            res.status = parseInt(res.status);
            if (res.status == 1) {
                // $('#lName').html(res.fName);
                cart.props.fName = fName;
                return;
            }
            $.mbsmessage(res.msg, true, 'alert alert--danger');

        }, {fOutMode: 'json'});
    },
    addClassLastName:function(fName,lName){
        if(cart.fName==''){
            cart.addFirstName(fName)
        }
        props = cart.props
        props.lName = lName;
        fcom.ajax(fcom.makeUrl('Classes', 'addLastName'), cart.props, function (res) {
            res.status = parseInt(res.status);
            if (res.status == 1) {
                // $('#lName').html(res.fName);
                cart.props.lName = lName;
                return;
            }
            $.mbsmessage(res.msg, true, 'alert alert--danger');

        }, {fOutMode: 'json'});
    },
    addKidsCount:function(fName,lName,count){
        if(cart.fName==''){
            cart.addFirstName(fName)
        }
        if(cart.lName==''){
            cart.addLastName(lName);
        }
        props = cart.props
		props.kidsCount = count;
        fcom.ajax(fcom.makeUrl('Checkout', 'addKidsCount'), cart.props, function (res) {
            res.status = parseInt(res.status);
            if (res.status == 1) {
                // $('#lName').html(res.fName);
                cart.props.kidsCount = count;
                return;
            }
            $.mbsmessage(res.msg, true, 'alert alert--danger');

        }, {fOutMode: 'json'});
    },
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
        fcom.ajax(fcom.makeUrl(cart.checkoutControllerName, 'getLessonQtyPrice'), cart.props, function (res) {
            res.status = parseInt(res.status);
            if (res.status == 1) {
                $('.slab-price-js').html(res.priceLabel);
                cart.props.lessonQty = lessonQty;
                return;
            }
            $.mbsmessage(res.msg, true, 'alert alert--danger');

        }, {fOutMode: 'json'});
    },
    walletSelection: function (el) {
        cart.isWalletSelect = ($(el).is(":checked")) ? 1 : 0;
        var fromKids= localStorage.getItem('fromKids');
        var data ='payFromWallet=' + cart.isWalletSelect + '&fromKids='+fromKids;
        $.loader.show();
        fcom.ajax(fcom.makeUrl(cart.checkoutControllerName, 'walletSelection'), data, function (ans) {
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
        fcom.updateWithAjax(fcom.makeUrl(cart.cartControllerName, 'applyPromoCode'), data, function (res) {
            cart.checkoutStep("getPaymentSummary", "");
        });
    },
    removePromoCode: function () {
		fcom.updateWithAjax(fcom.makeUrl(cart.checkoutControllerName, 'removePromoCode'), '', function (res) {
            cart.checkoutStep("getPaymentSummary", "");
		});
	},
    proceedToStep: function (cartDetails, step) {
        this.props = $.extend(true, cart.props, cartDetails);
        if (step == 'getPaymentSummary') {
            if(cart.props.oneOnOne==0){
            if(cart.props.fromKids>0 || cart.props.fromGroup>0){
                if(cart.props.fName=='' || cart.props.lName==''){
                    return  $.mbsmessage('Please fill datails', true, 'alert alert--danger');       
                }
            }
            else if(cart.props.oneOnOne>0){
                cart.getLessonQtyPrice();
            }
            }
            if(cartDetails.sponsorship==1){
                cart.checkoutControllerName='EventCheckout';
                cart.cartControllerName="EventCart";
            }
            
            return cart.add(this.props);
        }
        
        cart.checkoutStep(step, this.props);
    },
    joinnow: function (data) {
        console.log(data);
        fcom.ajax(fcom.makeUrl('Kids', 'joinnow'), data, function (t) {
          window.location.href = window.location.href;
        });
     
    },
    addFreeTrial: function (teacherId, startDateTime, endDateTime, languageId) {
        teacherId = parseInt(teacherId);
        languageId = parseInt(languageId);
        isStartDateTimeValid = moment(startDateTime).isValid();
        isEndDateTimeValid = moment(endDateTime).isValid();
        if (1 > teacherId || 1 > languageId || !isStartDateTimeValid || !isEndDateTimeValid || moment(startDateTime) >= moment(endDateTime)) {
            return false;
        }
        var data = 'isFreeTrial=1' + '&teacherId=' + teacherId + '&languageId=' + languageId + '&startDateTime=' + startDateTime + '&endDateTime=' + endDateTime;
        cart.add(data);
    },
    add: function (data) {
        $.loader.show();
        console.log(data);
        if(cart.props.sponsorship>0){
 if (isEventUserLogged() == 0) {
            $.loader.hide();
            logInFormPopUp();
            return false;
        }
        }
        else{
        if (isUserLogged() == 0) {
            $.loader.hide();
            logInFormPopUp();
            return false;
        }
    }
        localStorage.setItem('fromKids',data.fromKids);
        localStorage.setItem('isSkipped',data.isSkipped);
        
        // console.log('add',data);
        // if(data.isSkipped){
        // data=data+"&isSkipped="+data.isSkipped
        // }
        console.log(data);
        fcom.ajax(fcom.makeUrl(cart.cartControllerName, 'add'), data, function (res) {
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
        }, {fOutMode: 'json'});
    },
    checkoutStep: function (step, data) {
        $.loader.show();
        console.log("data==",data);
        cart.props.isPrivateClass=data.isPrivateClass
        if(cart.props.sponsorship>0){
            if(isEventUserLogged()==0){
                  $.loader.hide();
            logInFormPopUp();
            return false;
            }
        }
       
        else{
        if (isUserLogged() == 0) {
            $.loader.hide();
            logInFormPopUp();
            return false;
        }
        }
        	
        data=cart.props;
        var fromKids= localStorage.getItem('fromKids');
        //data="fromKids="+fromKids;
        fcom.ajax(fcom.makeUrl(cart.checkoutControllerName, step), data, function (data) {
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
        $.loader.show();
        cart.paymentMethodId = parseInt($('[name="payment_method"]:checked').val());
        orderType = parseInt(orderType);
        var fromKids= localStorage.getItem('fromKids');
        localStorage.setItem('teacherId',cart.props.teacherId);
        data = "fromKids="+fromKids+"&order_type=" + orderType + "&referralName=" +cart.referralName+"&pmethod_id=" + cart.paymentMethodId+"&teacherId="+cart.props.teacherId+"&fName="+cart.props.fName+"&lName="+cart.props.lName+"&kidsCount="+cart.props.kidsCount;
        datas = "teacherId="+cart.props.teacherId;
        fcom.updateWithAjax(fcom.makeUrl(cart.checkoutControllerName, 'confirmOrder'), data, function (ans) {
            if (ans.redirectUrl != '') {
                // res=ans.redirectUrl;
                window.location.href = ans.redirectUrl;
           
            }else{
                $.loader.hide();
            }
        });
    },
   
};

$(document).bind('afterClose.facebox', function () {
        cart.props = {
            fromKids:0,
            teacherId: 0,
            languageId: 0,
            lessonDuration: 0,
            lessonQty: 0,
            isCompany:0,
        };
        cart.couponCode = '';
        cart.isWalletSelect = 0;
        cart.paymentMethodId = 0;
        console.log("Hello");
});
$(document).ready(function() {
    // console.log(cart.props);
    $(".btn--back").click(function(){
        console.log(cart.props);
    $('#fname').val(cart.props.fName);
    });
  
  });