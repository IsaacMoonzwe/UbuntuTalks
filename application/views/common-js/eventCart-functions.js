var eventCart = {
  props: {
    oneOnOne: 0,
    fromKids: 0,
    teacherId: 0,
    languageId: 0,
    lessonDuration: 0,
    lessonQty: 0,
    fromGroup: 0,
    fName: "",
    lName: "",
    kidsCount: 1,
    isCompany: 0,
    referralName: "",
    sponsorship: 1,
    sponsershipPlan: "",
    becomesponserPlan: {},
    donationAmount: 1,
    countOfTickets: 1,
    tickets_plan: 1,
    becomeSponserPlanQty: {},
    checkEventUserLogged: 0,
    selectSponserEventPlan: null,
    selectCorporateEventPlan: null,
    selectCorporateTicket:null,
    becomeSponserSelectedPlan: {},
    eventUserSelectedStaus: 'Login',
    concertPlan: '',
    concertTicket: 1,
    symposiumPlan: '',
    symposiumTicket: 1,
    currency: 'USD',
    currencyCode: '$',
  },
  couponCode: "",
  isWalletSelect: 0,
  paymentMethodId: 0,

  getLessonQtyPrice: function (lessonQty, planId) {
    lessonQty = parseInt(lessonQty);
    props = eventCart.props;
    props.lessonQty = lessonQty;
    eventCart.props.planId = planId;
    fcom.ajax(
      fcom.makeUrl("EventUser", "getSponserQtyPrice"),
      eventCart.props,
      function (res) {
        res.status = parseInt(res.status);
        if (res.status == 1) {
          $(".slab-price-js").html(res.priceLabel);
          eventCart.props.lessonQty = lessonQty;
          planQty = eventCart.props.becomeSponserPlanQty;
          if (planId in planQty) {
            delete planQty[planId];
          }
          planQty[planId] = lessonQty;
          eventCart.props.becomeSponserPlanQty = planQty;
          return;
        }
        $.mbsmessage(res.msg, true, "alert alert--danger");
      },
      { fOutMode: "json" }
    );
  },
  walletSelection: function (el) {
    eventCart.isWalletSelect = $(el).is(":checked") ? 1 : 0;
    var fromKids = localStorage.getItem("fromKids");
    var data =
      "payFromWallet=" + eventCart.isWalletSelect + "&fromKids=" + fromKids;
    $.loader.show();
    fcom.ajax(
      fcom.makeUrl("EventUser", "walletSelection"),
      data,
      function (ans) {
        $.loader.hide();
        eventCart.checkoutStep("getPaymentSummary", "");
      }
    );
  },
  applyPromoCode: function (code) {
    eventCart.couponCode = code.toString();
    if (eventCart.couponCode == "") {
      return;
    }
    data = "coupon_code=" + eventCart.couponCode;
    fcom.updateWithAjax(
      fcom.makeUrl("EventCart", "applyPromoCode"),
      data,
      function (res) {
        eventCart.checkoutStep("getPaymentSummary", "");
      }
    );
  },
  removePromoCode: function () {
    fcom.updateWithAjax(
      fcom.makeUrl("EventCart", "removePromoCode"),
      "",
      function (res) {
        eventCart.checkoutStep("getPaymentSummary", "");
      }
    );
  },
  proceedToStep: function (cartDetails, step) {
    this.props = $.extend(true, eventCart.props, cartDetails);
    if (step == "getPaymentSummary") {
      if (eventCart.props.oneOnOne == 0) {
        if (eventCart.props.fromKids > 0 || eventCart.props.fromGroup > 0) {
          if (eventCart.props.fName == "" || eventCart.props.lName == "") {
            return $.mbsmessage(
              "Please fill datails",
              true,
              "alert alert--danger"
            );
          }
        }
      }

      return eventCart.add(this.props);
    }

    eventCart.checkoutStep(step, this.props);
  },
  joinnow: function (data) {
    fcom.ajax(fcom.makeUrl("Kids", "joinnow"), data, function (t) {
      window.location.href = window.location.href;
    });
  },
  addFreeTrial: function (teacherId, startDateTime, endDateTime, languageId) {
    teacherId = parseInt(teacherId);
    languageId = parseInt(languageId);
    isStartDateTimeValid = moment(startDateTime).isValid();
    isEndDateTimeValid = moment(endDateTime).isValid();
    if (
      1 > teacherId ||
      1 > languageId ||
      !isStartDateTimeValid ||
      !isEndDateTimeValid ||
      moment(startDateTime) >= moment(endDateTime)
    ) {
      return false;
    }
    var data =
      "isFreeTrial=1" +
      "&teacherId=" +
      teacherId +
      "&languageId=" +
      languageId +
      "&startDateTime=" +
      startDateTime +
      "&endDateTime=" +
      endDateTime;
    eventCart.add(data);
  },
  add: function (data) {
    $.loader.show();
    if (isEventUserLogged() == 0) {
      $.loader.hide();
      logInFormPopUp();
      return false;
    }
    localStorage.setItem("fromKids", data.fromKids);
    localStorage.setItem("isSkipped", data.isSkipped);


    // if(data.isSkipped){
    // data=data+"&isSkipped="+data.isSkipped
    // }
    fcom.updateWithAjax(
      fcom.makeUrl("EventUser", "EventSignUpFormPopUp"),
      data,
      function (res) {
        $.loader.hide();

        if (res.msg) {
          $.facebox(ans.html, "");
          if ($("#frmRegisterPopUp #termLabelWrapper").length > 0) {
            $("#frmRegisterPopUp #termLabelWrapper")
              .find(".field-set")
              .first()
              .addClass("form__list--check");
          }
          // if (res.status == 1) {
          //     if (res.isFreeLesson) {
          //         eventCart.confirmOrder();
          //         return;
          //     }

          //     if (res.redirectUrl) {
          //         window.location.href = res.redirectUrl;
          //         return;
          //     }
          eventCart.checkoutStep("getPaymentSummary", "");
        } else {
          $.mbsmessage(res.msg, true, "alert alert--danger");
        }

        $.loader.hide();
      },
      { fOutMode: "json" }
    );
  },
  checkoutStep: function (step, data) {
    $.loader.show();
    if (isEventUserLogged() == 0) {
      $.loader.hide();
      logInFormPopUp();
      return false;
    }

    data = eventCart.props;
    var fromKids = localStorage.getItem("fromKids");
    //data="fromKids="+fromKids;
    fcom.ajax(fcom.makeUrl("EventUser", step), data, function (data) {
      $.loader.hide();
      try {
        data = JSON.parse(data);
        if (data.status == 0) {
          $.mbsmessage(data.msg, true, "alert alert--danger");
          $.loader.hide();
          return;
        }
      } catch (e) {
        $.facebox(data, "checkout-step " + step);
      }
    });
  },
  confirmOrder: function (orderType) {
    $.loader.show();
    // if (isEventUserLogged() == 0) {
    //   $.loader.hide();
    //   EventLogInFormPopUp('purchasePlan');
    //   return false;
    // }
    // if(eventCart.isWalletSelect>0){
    //   eventCart.paymentMethodId=0;
    // }
    // else{
    eventCart.paymentMethodId = parseInt(
      $('[name="payment_method"]:checked').val()
    );
    // }
    orderType = parseInt(orderType);
    var fromKids = localStorage.getItem("fromKids");
    localStorage.setItem("teacherId", eventCart.props.teacherId);
    data =
      "fromKids=" +
      fromKids +
      "&order_type=" +
      orderType +
      "&referralName=" +
      eventCart.referralName +
      "&pmethod_id=" +
      eventCart.paymentMethodId +
      "&teacherId=" +
      eventCart.props.teacherId +
      "&fName=" +
      eventCart.props.fName +
      "&lName=" +
      eventCart.props.lName +
      "&kidsCount=" +
      eventCart.props.kidsCount +
      "&currency=" +
      eventCart.props.currency +
      "&currencyCode=" +
      eventCart.props.currencyCode;
    console.log("data", data);
    datas = "teacherId=" + eventCart.props.teacherId;
    fcom.updateWithAjax(
      fcom.makeUrl("EventUser", "confirmOrder"),
      data,
      function (ans) {
        if (ans.redirectUrl != "") {
          // res=ans.redirectUrl;
          window.location.href = ans.redirectUrl;
        } else {
          $.loader.hide();
        }
      }
    );
  },
};

$(document).bind("afterClose.facebox", function () {
  eventCart.props = {
    fromKids: 0,
    teacherId: 0,
    languageId: 0,
    lessonDuration: 0,
    lessonQty: 0,
    isCompany: 0,
    becomeSponserSelectedPlan: {},
    selectSponserEventPlan: null,
    selectCorporateEventPlan: null,
  };
  eventCart.couponCode = "";
  eventCart.isWalletSelect = 0;
  eventCart.paymentMethodId = 0
});
$(document).ready(function () {

  $(".btn--back").click(function () {
    $("#fname").val(eventCart.props.fName);
  });
});
