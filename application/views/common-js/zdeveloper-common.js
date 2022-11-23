var isRuningTeacherFavoriteAjax = false;
var newsletterAjaxRuning = false;
function getCountryStates(countryId, stateId, dv) {
  fcom.ajax(
    fcom.makeUrl("GuestUser", "getStates", [countryId, stateId]),
    "",
    function (res) {
      $(dv).empty();
      $(dv).append(res);
    }
  );
}
function isUserLogged() {
  var isUserLogged = 0;
  $.ajax({
    url: fcom.makeUrl("GuestUser", "checkAjaxUserLoggedIn"),
    async: false,
    dataType: "json",
  }).done(function (ans) {
    isUserLogged = parseInt(ans.isUserLogged);
  });
  return isUserLogged;
}
function isAdminUserLogged() {
  var isUserLogged = 0;
  $.ajax({
    url: fcom.makeUrl("GuestUser", "checkAjaxAdminLoggedIn"),
    async: false,
    dataType: "json",

  }).done(function (ans) {
    isUserLogged = parseInt(ans.isUserLogged);
  });
  return isUserLogged;
}

function isEventUserLogged() {
  var isUserLogged = 0;
  $.ajax({
    url: fcom.makeUrl("EventUser", "checkAjaxUserLoggedIn"),
    async: false,
    dataType: "json",
  }).done(function (ans) {
    isUserLogged = parseInt(ans.isUserLogged);
  });
  return isUserLogged;
}
getStatisticalData = function (type) {
  if (type == 1) {
    duration = $("#earningMonth").val();
  }
  if (type == 2) {
    duration = $("#lessonsMonth").val();
  }
  fcom.ajax(
    fcom.makeUrl("TeacherReports", "getStatisticalData"),
    "duration=" + duration + "&type=" + type,
    function (res) {
      if (type == 1) {
        $("#earningContent").html(res);
      }
      if (type == 2) {
        $("#lessonsSold").html(res);
      }
    }
  );
};
getCookieConsentForm = function () {
  fcom.ajax(fcom.makeUrl("Custom", "cookieForm"), "", function (t) {
    $.facebox(t, "facebox-medium cookies-popup");
  });
};
saveCookieSetting = function (form) {
  if (!$(form).validate()) return;
  var data = fcom.frmData(form);
  fcom.updateWithAjax(
    fcom.makeUrl("Custom", "saveCookieSetting"),
    data,
    function (t) {
      $(".cookie-alert").remove();
      $.facebox.close();
    }
  );
};

submitNewsletterForm = function (form) {
  if (newsletterAjaxRuning) {
    return false;
  }
  if (!$(form).validate()) {
    return;
  }
  newsletterAjaxRuning = true;
  $.loader.show();
  var data = fcom.frmData(form);
  fcom.ajax(
    fcom.makeUrl("MyApp", "setUpNewsLetter"),
    data,
    function (response) {
      msgClass = response.status == 1 ? "success" : "danger";
      $.mbsmessage(response.msg, true, "alert alert--" + msgClass);
      if (response.status == 1) {
        form.reset();
      }
      $.loader.hide();
      newsletterAjaxRuning = false;
    },
    { fOutMode: "json" }
  );
};

$(document).ready(function () {
  setUpJsTabs();

  setUpGoToTop();

  //setUpStickyHeader();

  toggleNavDropDownForDevices();

  toggleHeaderCurrencyLanguageForDevices();

  toggleFooterCurrencyLanguage();

  if ($.datepicker) {
    var old_goToToday = $.datepicker._gotoToday;
    $.datepicker._gotoToday = function (id) {
      old_goToToday.call(this, id);
      this._selectDate(id);
      $(id).blur();
      return;
    };
  }
});

(function ($) {
  var screenHeight = $(window).height() - 100;
  window.onresize = function (event) {
    var screenHeight = $(window).height() - 100;
  };

  $.extend(fcom, {
    getLoader: function () {
      return '<div class="-padding-20"><div class="loader -no-margin-bottom"></div></div>';
    },

    resetFaceboxHeight: function () {
      //$('html').css('overflow', 'hidden');
      $("html").addClass("show-facebox");
      facebocxHeight = screenHeight;
      $("#facebox .content").css("max-height", facebocxHeight - 50 + "px");
      if ($("#facebox .content").height() + 100 >= screenHeight) {
        //$('#facebox .content').css('overflow-y', 'scroll');
        $("#facebox .content").css("display", "block");
      } else {
        $("#facebox .content").css("max-height", "");
        $("#facebox .content").css("overflow", "");
      }
    },

    updateFaceboxContent: function (t, cls) {
      if (typeof cls == "undefined" || cls == "undefined") {
        cls = "";
      }
      $.facebox(t, cls);
      $.systemMessage.close();
      fcom.resetFaceboxHeight();
    },

    waitAndRedirect: function (redirectUrl) {
      setTimeout(function () {
        window.location.href = redirectUrl;
      }, 3000);
    },
  });

  $(document).bind("reveal.facebox", function () {
    fcom.resetFaceboxHeight();
  });
  $(window).on("orientationchange", function () {
    facebocxHeight = screenHeight;
    $("#facebox .content").css("max-height", facebocxHeight - 50 + "px");
    if ($("#facebox .content").height() + 100 >= screenHeight) {
      //$('#facebox .content').css('overflow-y', 'scroll');
      $("#facebox .content").css("display", "block");
    } else {
      $("#facebox .content").css("max-height", "");
      $("#facebox .content").css("overflow", "");
    }
  });
  $(document).bind("loading.facebox", function () {
    //$('#facebox .content').addClass('fbminwidth');
  });
  $(document).bind("beforeReveal.facebox", function () {
    //$('#facebox .content').addClass('scrollbar scrollbar-js fbminwidth');
  });
  $(document).bind("afterClose.facebox", function () {
    $("html").removeClass("show-facebox");
  });

  (setUpJsTabs = function () {
    /* upon loading[ */
    $(".tabs-content-js").hide();
    $(".tabs-js li:first").addClass("is-active").show();
    $(".tabs-content-js:first").show();
    /* ] */
  }),
    (setUpGoToTop = function () {
      $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
          $(".scroll-top-js").addClass("isvisible");
        } else {
          $(".scroll-top-js").removeClass("isvisible");
        }
      });

      $(".scroll-top-js").click(function () {
        $("body,html").animate(
          {
            scrollTop: 0,
          },
          800
        );
        return false;
      });
    }),
    (setUpStickyHeader = function () {
      if ($(window).width() > 767) {
        $(window).scroll(function () {
          body_height = $(".body").position();
          scroll_position = $(window).scrollTop();
          if (body_height.top < scroll_position) {
            $(".header").addClass("is-fixed");
          } else {
            $(".header").removeClass("is-fixed");
          }
        });
      }
    }),
    (toggleNavDropDownForDevices = function () {
      if ($(window).width() < 1200) {
        $(".nav__dropdown-trigger-js").click(function () {
          if ($(this).hasClass("is-active")) {
            $("html").removeClass("show-dashboard-js");
            $(this).removeClass("is-active");
            $(this).siblings(".nav__dropdown-target-js").slideUp();
            return false;
          }
          $(".nav__dropdown-trigger-js").removeClass("is-active");
          $("html").addClass("show-dashboard-js");
          $(this).addClass("is-active");
          $(".nav__dropdown-target-js").slideUp();
          $(this).siblings(".nav__dropdown-target-js").slideDown();
        });
      }
    }),
    jQuery(document).ready(function (e) {
      function t(t) {
        e(t).bind("click", function (t) {
          t.preventDefault();
          e(this).parent().fadeOut();
        });
      }

      $(".cc-cookie-accept-js").click(function () {
        fcom.ajax(
          fcom.makeUrl("Custom", "updateUserCookies"),
          "",
          function (t) {
            $(".cookie-alert").hide("slow");
            $(".cookie-alert").remove();
            $.facebox.close();
          }
        );
      });

      //When page loads...
      $(".tabs-content-js").hide(); //Hide all content
      $(".tabs-js li:first").addClass("is-active").show(); //Activate first tab
      $(".tabs-content-js:first").show(); //Show first tab content

      //On Click Event
      $(".tabs-js li").click(function () {
        $(".tabs-js li").removeClass("is-active"); //Remove any "active" class
        $(this).addClass("is-active"); //Add "active" class to selected tab
        $(".tabs-content-js").hide(); //Hide all tab content

        var activeTab = $(this).data("href"); //Find the href attribute value to identify the active tab + content

        $(activeTab).fadeIn(); //Fade in the active ID content
        return true;
      });

      e(".toggle__trigger-js").click(function () {
        var t = e(this)
          .parents(".toggle-group")
          .children(".toggle__target-js")
          .is(":hidden");
        e(".toggle-group .toggle__target-js").hide();
        e(".toggle-group .toggle__trigger-js").removeClass("is-active");
        if (t) {
          e(this)
            .parents(".toggle-group")
            .children(".toggle__target-js")
            .toggle()
            .parents(".toggle-group")
            .children(".toggle__trigger-js")
            .addClass("is-active");
        }
      });

      $(document.body).on("click", ".toggle__trigger-js", function () {
        var t = e(this)
          .parents(".toggle-group")
          .children(".toggle__target-js")
          .is(":hidden");
        e(".toggle-group .toggle__target-js").hide();
        e(".toggle-group .toggle__trigger-js").removeClass("is-active");
        if (t) {
          e(this)
            .parents(".toggle-group")
            .children(".toggle__target-js")
            .toggle()
            .parents(".toggle-group")
            .children(".toggle__trigger-js")
            .addClass("is-active");
        }
      });
      e(document).bind("click", function (t) {
        var n = e(t.target);
        if (!n.parents().hasClass("toggle-group"))
          e(".toggle-group .toggle__target-js").hide();
      });
      e(document).bind("click", function (t) {
        var n = e(t.target);
        if (!n.parents().hasClass("toggle-group"))
          e(".toggle-group .toggle__trigger-js").removeClass("is-active");
      });

      $(".tab-swticher-small a").click(function () {
        $(".tab-swticher-small a").removeClass("is-active");
        $(this).addClass("is-active");
      });
    });

  (toggleHeaderCurrencyLanguageForDevices = function () {
    $(".nav__item-settings-js").click(function () {
      $(this).toggleClass("is-active");
      $("html").toggleClass("show-setting-js");
    });
  }),
    (toggleFooterCurrencyLanguage = function () {
      $(".toggle-footer-lang-currency-js").click(function () {
        var clickedSectionClass = $(this)
          .siblings(".listing-div-js")
          .attr("div-for");

        $(".toggle-footer-lang-currency-js").each(function () {
          if (
            $(this).siblings(".listing-div-js").attr("div-for") !=
            clickedSectionClass
          ) {
            $(this).siblings(".listing-div-js").hide();
          }
        });

        $(this).siblings(".listing-div-js").slideToggle();
      });
    }),
    (setSiteDefaultLang = function (langId) {
      $.loader.show();
      var url = window.location.pathname;
      var srchString = window.location.search;
      var data = "pathname=" + url;
      fcom.updateWithAjax(
        fcom.makeUrl("Home", "setSiteDefaultLang", [langId]),
        data,
        function (res) {
          window.location.href = res.redirectUrl + srchString;
        }
      );
    }),
    (setSiteDefaultCurrency = function (currencyId) {
      $.loader.show();
      fcom.updateWithAjax(
        fcom.makeUrl("Home", "setSiteDefaultCurrency", [currencyId]),
        "",
        function (res) {
          document.location.reload();
        }
      );
    }),
    (signUpFormPopUp = function (signUpType) {
      var data = "signUpType=" + signUpType;
      fcom.updateWithAjax(
        fcom.makeUrl("GuestUser", "signUpFormPopUp"),
        data,
        function (ans) {
          $.mbsmessage.close();
          if (ans.redirectUrl) {
            window.location.href = ans.redirectUrl;
            return;
          }
          $.facebox(ans.html, "");
          if ($("#frmRegisterPopUp #termLabelWrapper").length > 0) {
            $("#frmRegisterPopUp #termLabelWrapper")
              .find(".field-set")
              .first()
              .addClass("form__list--check");
          }
        }
      );
    });


  RegisterPlanEventUser = function (fromEvent = '', fromBack = 0) {
    $.loader.show();

    var checkLogged = 1;
    if (isEventUserLogged() == 0) {
      checkLogged = 0;
    }
    // var data="method="+eventCart.props.sponsershipPlan;
    fcom.ajax(
      fcom.makeUrl("EventUser", "RegisterPlanEventUserData", [fromEvent, fromBack, checkLogged]),
      "",
      function (res) {
        try {
          let data = JSON.parse(res);
          !data.status
            ? $.mbsmessage(data.msg, true, "alert alert--danger")
            : void 0;
        } catch (exc) {
          $.facebox(res, "");
        }
      }
    );
    $.loader.hide();
  };

  RegisterEventUser = function (fromEvent = '', fromBack = 0) {
    $.loader.show();
    var method = eventCart.props.becomesponserPlan;
    var checkLogged = 1;
    if (isEventUserLogged() == 0) {
      checkLogged = 0;
    }

    if (Object.keys(method).length <= 0) {
      $.loader.hide();
      $.mbsmessage("Please Select Plan", true, "alert alert--danger");
      return false;
    }

    // var data="method="+eventCart.props.sponsershipPlan;
    fcom.ajax(
      fcom.makeUrl("EventUser", "RegisterEventUserData", [fromEvent, fromBack, checkLogged]),
      "",
      function (res) {
        try {
          let data = JSON.parse(res);
          !data.status
            ? $.mbsmessage(data.msg, true, "alert alert--danger")
            : void 0;
        } catch (exc) {
          $.facebox(res, "");
        }
      }
    );
    $.loader.hide();
  };
  RegisterDonationEventUser = function (fromEvent = '', fromBack = 0) {
    $.loader.show();

    var checkLogged = 1;
    if (isEventUserLogged() == 0) {
      checkLogged = 0;
    }
    console.log("check--", checkLogged);
    // var data="method="+eventCart.props.sponsershipPlan;
    fcom.ajax(
      fcom.makeUrl("EventUser", "RegisterDonationEventUserData", [fromEvent, fromBack, checkLogged]),
      "",
      function (res) {
        try {
          let data = JSON.parse(res);
          !data.status
            ? $.mbsmessage(data.msg, true, "alert alert--danger")
            : void 0;
        } catch (exc) {
          $.facebox(res, "");
        }
      }
    );
    $.loader.hide();
  };

  EventSignUpFormPopUp = function (signUpType) {
    console.log("si", signUpType);
    var data = "signUpType=" + signUpType;
    fcom.updateWithAjax(
      fcom.makeUrl("EventUser", "EventSignUpFormPopUp"),
      data,
      function (ans) {
        $.mbsmessage.close();
        // if(ans.msg){
        // GetEventPaymentSummary();

        // }
        if (ans.redirectUrl) {
          window.location.href = ans.redirectUrl;

          return;
        }
        $.facebox(ans.html, "");
        if ($("#frmRegisterPopUp #termLabelWrapper").length > 0) {
          $("#frmRegisterPopUp #termLabelWrapper")
            .find(".field-set")
            .first()
            .addClass("form__list--check");
        }
      }
    );
  };
  GetEventBecomeSponserFillData = function (frm) {
    $.loader.show();
    data = fcom.frmData(frm);
    fcom.ajax(
      fcom.makeUrl("EventUser", "GetEventBecomeSponserFillData"),
      fcom.frmData(frm),
      function (res) {
        // if (res.status == 1) {
        window.location.href = res.redirectUrl;
        // 	return;
        // } else {
        $.loader.hide();
        $.mbsmessage(res.msg, true, "alert alert--danger");
        // }
      },
      { fOutMode: "json" }
    );
  };
  setUpSignUp = function (frm) {
    if (!$(frm).validate()) {
      return;
    }
    $.loader.show();
    data = fcom.frmData(frm);
    fcom.ajax(
      fcom.makeUrl("GuestUser", "setUpSignUp"),
      fcom.frmData(frm),
      function (res) {
        if (res.status == 1) {
          window.location.href = res.redirectUrl;
          return;
        } else {
          $.loader.hide();
          $.mbsmessage(res.msg, true, "alert alert--danger");
        }
      },
      { fOutMode: "json" }
    );
  };

  EventSetUpSignUp = function (frm) {
    if (!$(frm).validate()) {
      return;
    }
    $.loader.show();
    data = fcom.frmData(frm);
    data = data + "&sponsership=" + eventCart.props.sponsershipPlan;

    fcom.ajax(
      fcom.makeUrl("EventUser", "EventSetUpSignUp"),
      data,
      function (res) {
        if (res.status == 1) {
          window.location.href = res.redirectUrl;
          // GetEventPaymentSummary();
          // GetEventPlan();
          return;
        } else {
          $.loader.hide();
          $.mbsmessage(res.msg, true, "alert alert--danger");
        }
      },
      { fOutMode: "json" }
    );
  };
  addEventSponserShip = function (data) {
    console.log("kfhkf");
    $.loader.show();
    if (isEventUserLogged() == 0) {
      $.loader.hide();
      logInFormPopUp();
      return false;
    }
    localStorage.setItem("fromKids", data.fromKids);
    localStorage.setItem("isSkipped", data.isSkipped);

    // console.log('add',data);
    // if(data.isSkipped){
    // data=data+"&isSkipped="+data.isSkipped
    // }
    console.log(data);
    fcom.ajax(
      fcom.makeUrl("EventUser", "EventSignUpFormPopUp"),
      data,
      function (res) {
        $.loader.hide();
        $.mbsmessage.close();
        if (ans.redirectUrl) {
          GetEventPlan();
          return;
        }
        $.facebox(ans.html, "");
        if ($("#frmRegisterPopUp #termLabelWrapper").length > 0) {
          $("#frmRegisterPopUp #termLabelWrapper")
            .find(".field-set")
            .first()
            .addClass("form__list--check");
        }
        $.loader.hide();
      },
      { fOutMode: "json" }
    );
  };
  logInFormPopUp = function () {
    $.loader.show();
    fcom.ajax(
      fcom.makeUrl("GuestUser", "logInFormPopUp", []),
      "",
      function (res) {
        try {
          let data = JSON.parse(res);
          !data.status
            ? $.mbsmessage(data.msg, true, "alert alert--danger")
            : void 0;
        } catch (exc) {
          $.facebox(res, "");
        }
      }
    );
    $.loader.hide();
  };

  EventLogInFormPopUp = function (callBack = '') {
    $.loader.show();

    fcom.ajax(
      fcom.makeUrl("EventUser", "EventLogInFormPopUp", [callBack]),
      "",
      function (res) {
        try {
          let data = JSON.parse(res);
          !data.status
            ? $.mbsmessage(data.msg, true, "alert alert--danger")
            : void 0;
        } catch (exc) {
          $.facebox(res, "");
        }
      }
    );
    $.loader.hide();
  };

  //symposium Payment Flow

  GetSymposiumPlan = function (fromBack = 0) {
    $.loader.show();
    // if (isEventUserLogged() == 0) {
    //   $.loader.hide();
    //   EventLogInFormPopUp('purchasePlan');
    //   return false;
    // }
    var data = fcom.frmData(document.plan);
    console.log("data", data);
    fcom.ajax(
      fcom.makeUrl("EventUser", "GetSymposiumPlan", [fromBack]),
      data,
      function (res) {
        try {
          let data = JSON.parse(res);
          !data.status
            ? $.mbsmessage(data.msg, true, "alert alert--danger")
            : GetEventPaymentSummary();
        } catch (exc) {
          $.facebox(res, "");
        }
      }
    );
    $.loader.hide();
  };

  GetSymposiumTickets = function (method, fromPlan = 0, ticketCount = 1) {
    $.loader.show();

    if (method == "") {
      $.loader.hide();
      $.mbsmessage("Please Select Plan", true, "alert alert--danger");
      return false;
    }
    var checkLogged = 1;
    if (isEventUserLogged() == 0) {
      checkLogged = 0;
    }
    var data = fcom.frmData(document.plan);
    ticketCount = eventCart.props.symposiumTicket;
    console.log("plan", method);
    fcom.ajax(
      fcom.makeUrl("EventUser", "GetSymposiumTickets", [method, checkLogged, fromPlan, ticketCount]),
      data,
      function (res) {
        try {
          let data = JSON.parse(res);
          !data.status
            ? $.mbsmessage(data.msg, true, "alert alert--danger")
            : GetEventPaymentSummary();
        } catch (exc) {
          $.facebox(res, "");
        }
      }
    );
    $.loader.hide();
  };
  GetSymposiumTicketsPaymentSummary = function (plan, ticketCount, userStatus = '') {
    $.loader.show();
    if (plan == "" || ticketCount <= 0) {
      $.loader.hide();
      $.mbsmessage("Please Select Plan", true, "alert alert--danger");
      return false;
    }
    var checkLogged = 1;
    if (isEventUserLogged() == 0) {
      checkLogged = 0;
    }
    var data = fcom.frmData(document.registerForm);
    if (userStatus == '') {
      userStatus = eventCart.props.eventUserSelectedStaus;
    }
    if (userStatus == 'Registration') {
      data = fcom.frmData(document.registerForm);
    }
    else {
      data = fcom.frmData(document.loginForm);
    }
    fcom.updateWithAjax(fcom.makeUrl('EventUser', 'RegisterForEvents', [plan, checkLogged, userStatus]), data, function (t) {
      $.loader.hide();
      console.log(t);
      try {
        if (t.userId > 0) {
          eventCart.props.eventUserSelectedStaus = 'Registration';
          GetEventSymposiumTicketsPaymentSummary(plan, ticketCount);
          return;
        }
      } catch (exc) {
        console.log("error", exc);
        if (t.msg != '') {
          $.mbsmessage(t.msg, true, "alert alert--danger")
        }

      }
    }
    );
    $.loader.hide();
  }
  RegisterSymposiumUser = function (fromEvent = '', fromBack = 0) {
    $.loader.show();

    var checkLogged = 1;
    if (isEventUserLogged() == 0) {
      checkLogged = 0;
    }
    // var data="method="+eventCart.props.sponsershipPlan;
    fcom.ajax(
      fcom.makeUrl("EventUser", "RegisterSymposiumUserData", [fromEvent, fromBack, checkLogged]),
      "",
      function (res) {
        try {
          let data = JSON.parse(res);
          !data.status
            ? $.mbsmessage(data.msg, true, "alert alert--danger")
            : void 0;
        } catch (exc) {
          $.facebox(res, "");
        }
      }
    );
    $.loader.hide();
  };
  GetEventSymposiumTicketsPaymentSummary = function (plan, ticketCount) {
    $.loader.show();
    console.log(plan);
    console.log("ticketCount", ticketCount);
    if (plan == "" || ticketCount <= 0) {
      $.loader.hide();
      $.mbsmessage("Please Select Plan", true, "alert alert--danger");
      return false;
    }
    var checkLogged = 1;
    if (isEventUserLogged() == 0) {
      checkLogged = 0;
    }
    fcom.ajax(
      fcom.makeUrl("EventUser", "GetSymposiumTicketsPaymentSummary", [
        plan,
        ticketCount,
        checkLogged
      ]),
      '',
      function (res) {
        try {
          let data = JSON.parse(res);
          !data.status
            ? $.mbsmessage(data.msg, true, "alert alert--danger")
            : void 0;
        } catch (exc) {
          $.facebox(res, "");
        }
      }
    );
    $.loader.hide();
  };
  eventSymposiumApplyPromoCode = function (code) {
    console.log("code", code);
    eventCart.couponCode = code.toString();
    if (eventCart.couponCode == "") {
      return;
    }
    data = "coupon_code=" + eventCart.couponCode;
    fcom.updateWithAjax(
      fcom.makeUrl("EventUser", "eventApplyPromoCode"),
      data,
      function (res) {
        // eventCart.checkoutStep("getPaymentSummary", "");
        GetEventSymposiumTicketsPaymentSummary(eventCart.props.symposiumPlan, eventCart.props.symposiumTicket);

      }
    );
  };
  eventWalletSelection = function (el, walletBalance, fromSelector = '') {
    if (walletBalance <= 0) {
      $.loader.hide();
      $.mbsmessage("InSufficient wallet balance", true, "alert alert--danger");
      $(el).prop('checked', false);
      return false;
    }

    eventCart.isWalletSelect = $(el).is(":checked") ? 1 : 0;
    var data =
      "payFromWallet=" + eventCart.isWalletSelect;
    $.loader.show();
    fcom.ajax(
      fcom.makeUrl("EventUser", "walletSelection"),
      data,
      function (ans) {
        $.loader.hide();
        if (fromSelector == 'fromSponser') {
          GetBecomeSponserPaymentSummary(eventCart.props.becomesponserPlan, eventCart.props.becomeSponserPlanQty);
        }
        else if (fromSelector == 'donation') {
          GetDonationPaymentSummary(eventCart.props.donationAmount);
        }
        else if (fromSelector == 'registrationPlan') {
          GetPlanTicketsPaymentSummary(eventCart.props.sponsershipPlan, eventCart.props.countOfTickets);
        }
        else if (fromSelector == 'benefitConcertPlan') {
          GetConcertTicketsPaymentSummary(eventCart.props.concertPlan, eventCart.props.concertTicket);
        }
        else if (fromSelector == 'SymposiumDinnerPlan') {
          GetEventSymposiumTicketsPaymentSummary(eventCart.props.symposiumPlan, eventCart.props.symposiumTicket);
        }
      }
    );
  },


    //concert payment flow
    GetConcertPlan = function (fromBack = 0) {
      $.loader.show();
      // if (isEventUserLogged() == 0) {
      //   $.loader.hide();
      //   EventLogInFormPopUp('purchasePlan');
      //   return false;
      // }
      var data = fcom.frmData(document.plan);

      fcom.ajax(
        fcom.makeUrl("EventUser", "GetConcertPlan", [fromBack]),
        data,
        function (res) {
          try {
            let data = JSON.parse(res);
            !data.status
              ? $.mbsmessage(data.msg, true, "alert alert--danger")
              : GetEventPaymentSummary();
          } catch (exc) {
            $.facebox(res, "");
          }
        }
      );
      $.loader.hide();
    };

  GetConcertTickets = function (method, fromPlan = 0, ticketCount = 1) {
    $.loader.show();

    if (method == "") {
      $.loader.hide();
      $.mbsmessage("Please Select Plan", true, "alert alert--danger");
      return false;
    }
    var checkLogged = 1;
    if (isEventUserLogged() == 0) {
      checkLogged = 0;
    }
    var data = fcom.frmData(document.plan);
    ticketCount = eventCart.props.concertTicket;
    console.log("plan", method);
    fcom.ajax(
      fcom.makeUrl("EventUser", "GetConcertTickets", [method, checkLogged, fromPlan, ticketCount]),
      data,
      function (res) {
        try {
          let data = JSON.parse(res);
          !data.status
            ? $.mbsmessage(data.msg, true, "alert alert--danger")
            : GetEventPaymentSummary();
        } catch (exc) {
          $.facebox(res, "");
        }
      }
    );
    $.loader.hide();
  };
  GetConcertTicketsPaymentSummary = function (plan, ticketCount, userStatus = '') {
    $.loader.show();
    if (plan == "" || ticketCount <= 0) {
      $.loader.hide();
      $.mbsmessage("Please Select Plan", true, "alert alert--danger");
      return false;
    }
    var checkLogged = 1;
    if (isEventUserLogged() == 0) {
      checkLogged = 0;
    }
    var data = fcom.frmData(document.registerForm);
    if (userStatus == '') {
      userStatus = eventCart.props.eventUserSelectedStaus;
    }
    if (userStatus == 'Registration') {
      data = fcom.frmData(document.registerForm);
    }
    else {
      data = fcom.frmData(document.loginForm);
    }
    fcom.updateWithAjax(fcom.makeUrl('EventUser', 'RegisterForEvents', [plan, checkLogged, userStatus]), data, function (t) {
      $.loader.hide();
      console.log(t);
      try {
        if (t.userId > 0) {
          eventCart.props.eventUserSelectedStaus = 'Registration';
          GetEventConcertTicketsPaymentSummary(plan, ticketCount);
          return;
        }
      } catch (exc) {
        console.log("error", exc);
        if (t.msg != '') {
          $.mbsmessage(t.msg, true, "alert alert--danger")
        }

      }
    }
    );
    $.loader.hide();
  }
  RegisterConcertUser = function (fromEvent = '', fromBack = 0) {
    $.loader.show();

    var checkLogged = 1;
    if (isEventUserLogged() == 0) {
      checkLogged = 0;
    }
    // var data="method="+eventCart.props.sponsershipPlan;
    fcom.ajax(
      fcom.makeUrl("EventUser", "RegisterConcertUserData", [fromEvent, fromBack, checkLogged]),
      "",
      function (res) {
        try {
          let data = JSON.parse(res);
          !data.status
            ? $.mbsmessage(data.msg, true, "alert alert--danger")
            : void 0;
        } catch (exc) {
          $.facebox(res, "");
        }
      }
    );
    $.loader.hide();
  };
  GetEventConcertTicketsPaymentSummary = function (plan, ticketCount) {
    $.loader.show();
    console.log(plan);
    console.log("ticketCount", ticketCount);
    if (plan == "" || ticketCount <= 0) {
      $.loader.hide();
      $.mbsmessage("Please Select Plan", true, "alert alert--danger");
      return false;
    }
    var checkLogged = 1;
    if (isEventUserLogged() == 0) {
      checkLogged = 0;
    }
    fcom.ajax(
      fcom.makeUrl("EventUser", "GetConcertTicketsPaymentSummary", [
        plan,
        ticketCount,
        checkLogged
      ]),
      '',
      function (res) {
        try {
          let data = JSON.parse(res);
          !data.status
            ? $.mbsmessage(data.msg, true, "alert alert--danger")
            : void 0;
        } catch (exc) {
          $.facebox(res, "");
        }
      }
    );
    $.loader.hide();
  };



  GetEventPlan = function (fromBack = 0) {
    $.loader.show();
    // if (isEventUserLogged() == 0) {
    //   $.loader.hide();
    //   EventLogInFormPopUp('purchasePlan');
    //   return false;
    // }
    var data = fcom.frmData(document.plan);

    fcom.ajax(
      fcom.makeUrl("EventUser", "GetEventPlan", [fromBack]),
      data,
      function (res) {
        try {
          let data = JSON.parse(res);
          !data.status
            ? $.mbsmessage(data.msg, true, "alert alert--danger")
            : GetEventPaymentSummary();
        } catch (exc) {
          $.facebox(res, "");
        }
      }
    );
    $.loader.hide();
  };
  GetEventTickets = function (method, fromPlan = 0, ticketCount = 1) {
    $.loader.show();

    if (method == "") {
      $.loader.hide();
      $.mbsmessage("Please Select Plan", true, "alert alert--danger");
      return false;
    }
    var checkLogged = 1;
    if (isEventUserLogged() == 0) {
      checkLogged = 0;
    }
    var data = fcom.frmData(document.plan);
    ticketCount = eventCart.props.countOfTickets;
    console.log("plan", method);
    fcom.ajax(
      fcom.makeUrl("EventUser", "GetEventTickets", [method, checkLogged, fromPlan, ticketCount]),
      data,
      function (res) {
        try {
          let data = JSON.parse(res);
          !data.status
            ? $.mbsmessage(data.msg, true, "alert alert--danger")
            : GetEventPaymentSummary();
        } catch (exc) {
          $.facebox(res, "");
        }
      }
    );
    $.loader.hide();
  };


  GetEventTicketsPaymentSummary = function (plan, ticketCount, userStatus = '') {
    $.loader.show();
    if (plan == "" || ticketCount <= 0) {
      $.loader.hide();
      $.mbsmessage("Please Select Plan", true, "alert alert--danger");
      return false;
    }
    var checkLogged = 1;
    if (isEventUserLogged() == 0) {
      checkLogged = 0;
    }
    var data = fcom.frmData(document.registerForm);
    if (userStatus == '') {
      userStatus = eventCart.props.eventUserSelectedStaus;
    }
    if (userStatus == 'Registration') {
      data = fcom.frmData(document.registerForm);
    }
    else {
      data = fcom.frmData(document.loginForm);
    }
    fcom.updateWithAjax(fcom.makeUrl('EventUser', 'RegisterForEvents', [plan, checkLogged, userStatus]), data, function (t) {
      $.loader.hide();
      console.log(t);
      try {
        if (t.userId > 0) {
          eventCart.props.eventUserSelectedStaus = 'Registration';
          GetPlanTicketsPaymentSummary(plan, ticketCount);
          return;
        }
      } catch (exc) {
        console.log("error", exc);
        if (t.msg != '') {
          $.mbsmessage(t.msg, true, "alert alert--danger")
        }

      }
    }
    );
    $.loader.hide();
  }
  GetPlanTicketsPaymentSummary = function (plan, ticketCount) {
    $.loader.show();
    if (plan == "" || ticketCount <= 0) {
      $.loader.hide();
      $.mbsmessage("Please Select Plan", true, "alert alert--danger");
      return false;
    }
    var checkLogged = 1;
    if (isEventUserLogged() == 0) {
      checkLogged = 0;
    }
    fcom.ajax(
      fcom.makeUrl("EventUser", "GetEventTicketsPaymentSummary", [
        plan,
        ticketCount,
        checkLogged,
        eventCart.props.currency
      ]),
      '',
      function (res) {
        try {
          let data = JSON.parse(res);
          !data.status
            ? $.mbsmessage(data.msg, true, "alert alert--danger")
            : void 0;
        } catch (exc) {
          $.facebox(res, "");
        }
      }
    );
    $.loader.hide();
  };


  GetBenefitConcertPlanTicketsPaymentSummary = function (ticketCount) {
    $.loader.show();
    if (ticketCount <= 0) {
      $.loader.hide();
      $.mbsmessage("Please Select Plan", true, "alert alert--danger");
      return false;
    }
    var checkLogged = 1;
    if (isEventUserLogged() == 0) {
      checkLogged = 0;
    }
    fcom.ajax(
      fcom.makeUrl("EventUser", "GetConcertTicketsPaymentSummary", [
        ticketCount,
        checkLogged
      ]),
      '',
      function (res) {
        try {
          let data = JSON.parse(res);
          !data.status
            ? $.mbsmessage(data.msg, true, "alert alert--danger")
            : void 0;
        } catch (exc) {
          $.facebox(res, "");
        }
      }
    );
    $.loader.hide();
  };

  GetEventPaymentSummary = function (method) {
    $.loader.show();
    if (method == "") {
      $.loader.hide();
      $.mbsmessage("Please Select Plan", true, "alert alert--danger");
      return false;
    }
    // var data="sponsershipPlan="+eventCart.props.sponsershipPlan;
    var data = 'sponsershipPlan="' + eventCart.props.sponsershipPlan + '"';
    console.log("data", data);
    fcom.ajax(
      fcom.makeUrl("EventUser", "GetEventPaymentSummary", [method]),
      "",
      function (res) {
        try {
          let data = JSON.parse(res);
          !data.status
            ? $.mbsmessage(data.msg, true, "alert alert--danger")
            : void 0;
        } catch (exc) {
          $.facebox(res, "");
        }
      }
    );
    $.loader.hide();
  };

  //donation
  GetEventDonation = function (fromPayment = 0, donationAmount = 1) {
    $.loader.show();
    // if (isEventUserLogged() == 0) {
    //   $.loader.hide();
    //   EventLogInFormPopUp();
    //   return false;
    // }
    console.log("donationAmount", donationAmount);
    var checkLogged = 1;
    if (isEventUserLogged() == 0) {
      checkLogged = 0;
    }
    var data = fcom.frmData(document.plan);
    console.log("plan", data);
    fcom.ajax(
      fcom.makeUrl("EventUser", "GetEventDonation", [fromPayment, checkLogged, donationAmount]),
      data,
      function (res) {
        try {
          let data = JSON.parse(res);
          !data.status
            ? $.mbsmessage(data.msg, true, "alert alert--danger")
            : GetEventPaymentSummary();
        } catch (exc) {
          $.facebox(res, "");
        }
      }
    );
    $.loader.hide();
  };
  GetEventDonationPaymentSummary = function (donation = 1) {
    $.loader.show();
    if (donation < 1) {
      $.loader.hide();
      $.mbsmessage(
        "Please Fill amount greater than 1",
        true,
        "alert alert--danger"
      );
      return false;
    }
    eventCart.props.donationAmount = donation;
    var checkLogged = 1;
    if (isEventUserLogged() == 0) {
      checkLogged = 0;
    }
    var data = fcom.frmData(document.registerForm);
    var userStatus = '';
    if (eventCart.props.eventUserSelectedStaus == undefined) {
      eventCart.props.eventUserSelectedStaus = "Login";
    }
    if (userStatus == '') {
      userStatus = eventCart.props.eventUserSelectedStaus;
    }

    if (userStatus == 'Registration') {
      data = fcom.frmData(document.registerForm);
    }
    else {
      data = fcom.frmData(document.loginForm);
    }
    fcom.updateWithAjax(fcom.makeUrl('EventUser', 'RegisterForEvents', [donation, checkLogged, userStatus]), data, function (t) {
      $.loader.hide();
      console.log(t);
      try {
        if (t.userId > 0) {
          eventCart.props.eventUserSelectedStaus = "Login";
          GetDonationPaymentSummary(donation, checkLogged);
          return;
        }
      } catch (exc) {
        console.log("error", exc);
        if (t.msg != '') {
          $.mbsmessage(t.msg, true, "alert alert--danger")
        }
      }
    }
    );
    $.loader.hide();
  };
  GetDonationPaymentSummary = function (donation) {
    $.loader.show();
    if (donation < 1) {
      $.loader.hide();
      $.mbsmessage(
        "Please Fill amount greater than 1",
        true,
        "alert alert--danger"
      );
      return false;
    }
    var checkLogged = 1;
    if (isEventUserLogged() == 0) {
      checkLogged = 0;
    }
    fcom.ajax(fcom.makeUrl('EventUser', 'GetEventDonationPaymentSummary', [donation, checkLogged]), '', function (t) {
      $.loader.hide();
      console.log(t);
      try {
        console.log("success", t);
        let data = JSON.parse(t);
        !data.status
          ? $.mbsmessage(data.msg, true, "alert alert--danger")
          : void 0;
      } catch (exc) {
        console.log("error", exc);
        $.facebox(t, "");
      }
    }
    );
    $.loader.hide();
  };
  // become sponser plan

  GetEventBecomeSponserPlan = function (fromback = 0) {
    $.loader.show();
    var plan = eventCart.props.selectSponserEventPlan;
    console.log("plan", plan);
    if (plan == null) {
      $.loader.hide();
      $.mbsmessage("Please Select Event", true, "alert alert--danger");
      return false;
    }
    // if (isEventUserLogged() == 0) {
    //   $.loader.hide();
    //   EventLogInFormPopUp();
    //   return false;
    // }
    if (fromback == 0) {
      eventCart.props.becomeSponserPlanQty = {};
      eventCart.props.becomesponserPlan = {};
    }
    var data = fcom.frmData(document.plan);
    var checkLogged = 1;
    if (isEventUserLogged() == 0) {
      checkLogged = 0;
    }
    fcom.ajax(
      fcom.makeUrl("EventUser", "GetEventBecomeSponserPlan", [eventCart.props.selectSponserEventPlan, checkLogged]),
      data,
      function (res) {
        try {
          let data = JSON.parse(res);
          !data.status
            ? $.mbsmessage(data.msg, true, "alert alert--danger")
            : GetEventPaymentSummary();
        } catch (exc) {
          $.facebox(res, "");
        }
      }
    );
    $.loader.hide();
  };


  GetSelectEventBecomeSponserPlan = function (fromback = 0) {
    $.loader.show();
    // if (isEventUserLogged() == 0) {
    //   $.loader.hide();
    //   EventLogInFormPopUp();
    //   return false;
    // }
    if (fromback == 0) {
      eventCart.props.becomeSponserPlanQty = {};
      eventCart.props.becomesponserPlan = {};
    }
    var data = fcom.frmData(document.plan);
    var checkLogged = 1;
    if (isEventUserLogged() == 0) {
      checkLogged = 0;
    }
    fcom.ajax(
      fcom.makeUrl("EventUser", "GetSelectEventBecomeSponserPlan", [checkLogged]),
      data,
      function (res) {
        try {
          let data = JSON.parse(res);
          !data.status
            ? $.mbsmessage(data.msg, true, "alert alert--danger")
            : GetEventPaymentSummary();
        } catch (exc) {
          $.facebox(res, "");
        }
      }
    );
    $.loader.hide();
  };
  GetEventBecomeSponserPaymentSummary = function (method, qty) {

    $.loader.show();
    if (Object.keys(method).length <= 0) {
      $.loader.hide();
      $.mbsmessage("Please Select Plan", true, "alert alert--danger");
      return false;
    }
    var checkLogged = 1;
    if (isEventUserLogged() == 0) {
      checkLogged = 0;
    }


    var data = fcom.frmData(document.registerForm);
    var userStatus = '';
    if (userStatus == '') {
      userStatus = eventCart.props.eventUserSelectedStaus;
    }
    if (userStatus == 'Registration') {
      data = fcom.frmData(document.registerForm);
    }
    else {
      data = fcom.frmData(document.loginForm);
    }
    fcom.updateWithAjax(fcom.makeUrl('EventUser', 'RegisterForEvents', [method, checkLogged, userStatus]), data, function (t) {
      $.loader.hide();
      console.log(t);
      try {
        if (t.userId > 0) {
          var qty = eventCart.props.becomeSponserPlanQty;
          var method = eventCart.props.becomesponserPlan;
          GetBecomeSponserPaymentSummary(method, qty);
          return;
        }
      } catch (exc) {
        console.log("error", exc);
        if (t.msg != '') {
          $.mbsmessage(t.msg, true, "alert alert--danger")
        }

      }
    }
    );
    $.loader.hide();
  };

  eventDonationApplyPromoCode = function (code) {
    console.log("code", code);
    eventCart.couponCode = code.toString();
    if (eventCart.couponCode == "") {
      return;
    }
    data = "coupon_code=" + eventCart.couponCode;
    fcom.updateWithAjax(
      fcom.makeUrl("EventUser", "eventApplyPromoCode"),
      data,
      function (res) {
        // eventCart.checkoutStep("getPaymentSummary", "");
        GetBecomeSponserPaymentSummary(eventCart.props.becomesponserPlan, eventCart.props.becomeSponserPlanQty);
      }
    );
  };




  eventApplyPromoCode = function (code, fromSelector = '') {
    console.log("code", code);
    eventCart.couponCode = code.toString();
    if (eventCart.couponCode == "") {
      return;
    }
    console.log('fromSelector', fromSelector);
    data = "coupon_code=" + eventCart.couponCode + "&fromSelector=" + fromSelector;
    fcom.updateWithAjax(
      fcom.makeUrl("EventUser", "eventApplyPromoCode"),
      data,
      function (res) {
        // eventCart.checkoutStep("getPaymentSummary", "");
        if (fromSelector == 'fromSponser') {
          GetBecomeSponserPaymentSummary(eventCart.props.becomesponserPlan, eventCart.props.becomeSponserPlanQty);
        }
        else if (fromSelector == 'donation') {
          GetDonationPaymentSummary(eventCart.props.donationAmount);
        }
        else if (fromSelector == 'registrationPlan') {
          GetPlanTicketsPaymentSummary(eventCart.props.sponsershipPlan, eventCart.props.countOfTickets);
        }
        else if (fromSelector == 'benefitConcertPlan') {
          GetConcertTicketsPaymentSummary(eventCart.props.concertPlan, eventCart.props.concertTicket);
        }
        else if (fromSelector == 'SymposiumDinnerPlan') {
          GetEventSymposiumTicketsPaymentSummary(eventCart.props.symposiumPlan, eventCart.props.symposiumTicket);
        }
      }
    );
  };
  eventRemovePromoCode = function (fromSelector = '') {
    fcom.updateWithAjax(
      fcom.makeUrl("EventUser", "eventremovePromoCode"),
      "",
      function (res) {
        if (fromSelector == 'fromSponser') {
          GetBecomeSponserPaymentSummary(eventCart.props.becomesponserPlan, eventCart.props.becomeSponserPlanQty);
        }
        else if (fromSelector == 'donation') {
          GetDonationPaymentSummary(eventCart.props.donationAmount);
        }
        else if (fromSelector == 'registrationPlan') {
          GetPlanTicketsPaymentSummary(eventCart.props.sponsershipPlan, eventCart.props.countOfTickets);
        }
        else if (fromSelector == 'benefitConcertPlan') {
          GetConcertTicketsPaymentSummary(eventCart.props.concertPlan, eventCart.props.concertTicket);
        }
        else if (fromSelector == 'SymposiumDinnerPlan') {
          GetEventSymposiumTicketsPaymentSummary(eventCart.props.symposiumPlan, eventCart.props.symposiumTicket);
        }
      }
    );
  },

    GetBecomeSponserPaymentSummary = function (method, qty) {
      $.loader.show();
      if (Object.keys(method).length <= 0) {
        $.loader.hide();
        $.mbsmessage("Please Select Plan", true, "alert alert--danger");
        return false;
      }
      var checkLogged = 1;
      if (isEventUserLogged() == 0) {
        checkLogged = 0;
      }

      // var sponser_event_plan=eventCart.props.becomeSponserSelectedPlan;
      var sponser_event_plan = eventCart.props.selectSponserEventPlan;
      // var data="method="+eventCart.props.sponsershipPlan;
      fcom.ajax(
        fcom.makeUrl("EventUser", "GetEventBecomeSponserPaymentSummary", [
          JSON.stringify(method),
          JSON.stringify(qty),
          sponser_event_plan,
          checkLogged
        ]),
        '',
        function (res) {
          try {
            let data = JSON.parse(res);
            !data.status
              ? $.mbsmessage(data.msg, true, "alert alert--danger")
              : void 0;
          } catch (exc) {
            $.facebox(res, "");
          }
        }
      );
      $.loader.hide();
    };

  logInEventFormPopUp = function () {
    $.loader.show();
    fcom.ajax(
      fcom.makeUrl("EventUser", "logInEventFormPopUp", []),
      "",
      function (res) {
        try {
          let data = JSON.parse(res);
          !data.status
            ? $.mbsmessage(data.msg, true, "alert alert--danger")
            : void 0;
        } catch (exc) {
          $.facebox(res, "");
        }
      }
    );
    $.loader.hide();
  };

  setUpLogin = function (frm) {
    if (!$(frm).validate()) {
      return;
    }
    console.log("cart==", cart.props.isPrivateClass);
    var checkAdmin = cart.props.isPrivateClass;
    $.loader.show();
    fcom.ajax(
      fcom.makeUrl("GuestUser", "setUpLogin", [checkAdmin]),
      fcom.frmData(frm),
      function (res) {
        console.log("err", res);
        if (res.status == 1 && res.redirectUrl) {
          window.location.href = res.redirectUrl;
          return;
        } else {
          $.loader.hide();
          $.mbsmessage(res.msg, true, "alert alert--danger");
        }
      },
      { fOutMode: "json" }
    );
  };

  EventSetUpLogin = function (frm) {
    if (!$(frm).validate()) {
      return;
    }
    $.loader.show();
    fcom.ajax(
      fcom.makeUrl("EventUser", "EventSetUpLogin"),
      fcom.frmData(frm),
      function (res) {
        if (res.status == 1 && res.redirectUrl) {
          $.mbsmessage(res.msg, true, "alert alert--success");
          window.location.href = res.redirectUrl;
          return;
        } else {
          $.loader.hide();
          $.mbsmessage(res.msg, true, "alert alert--danger");
        }
      },
      { fOutMode: "json" }
    );
  };

  resendEmailVerificationLink = function (username) {
    if (username == "undefined" || typeof username === "undefined") {
      username = "";
    }
    closeMessage();
    fcom.updateWithAjax(
      fcom.makeUrl("GuestUser", "resendEmailVerificationLink", [username]),
      "",
      function (ans) {
        displayMessage(ans.msg);
      }
    );
  };

  displayMessage = function (msg) {
    $.mbsmessage(msg, true, "alert alert--success");
  };

  closeMessage = function () {
    $(document).trigger("close.mbsmessage");
  };

  togglePassword = function (e) {
    var passType = $("input[name='user_password']").attr("type");
    if (passType == "text") {
      $("input[name='user_password']").attr("type", "password");
      $(e).html($(e).attr("data-show-caption"));
    } else {
      $("input[name='user_password']").attr("type", "text");
      $(e).html($(e).attr("data-hide-caption"));
    }
  };

  /* Confirm Password */
  toggleConfirmPassword = function (e) {
    var passType = $("input[name='conf_new_password']").attr("type");
    if (passType == "text") {
      $("input[name='conf_new_password']").attr("type", "password");
      $(e).html($(e).attr("data-show-caption"));
    } else {
      $("input[name='conf_new_password']").attr("type", "text");
      $(e).html($(e).attr("data-hide-caption"));
    }
  };
  toggleLoginPassword = function (e) {
    var passType = $("input[name='password']").attr("type");
    if (passType == "text") {
      $("input[name='password']").attr("type", "password");
      $(e).html($(e).attr("data-show-caption"));
    } else {
      $("input[name='password']").attr("type", "text");
      $(e).html($(e).attr("data-hide-caption"));
    }
  };

  toggleTeacherFavorite = function (teacher_id, el) {
    if (isRuningTeacherFavoriteAjax) {
      return false;
    }
    isRuningTeacherFavoriteAjax = true;
    if (isUserLogged() == 0) {
      isRuningTeacherFavoriteAjax = false;
      logInFormPopUp();
      return false;
    }
    var data = "teacher_id=" + teacher_id;
    $.mbsmessage.close();
    fcom.ajax(
      fcom.makeUrl("Learner", "toggleTeacherFavorite", [], confWebDashUrl),
      data,
      function (ans) {
        isRuningTeacherFavoriteAjax = false;
        if (ans.status) {
          if (ans.action == "A") {
            $(el).addClass("is--active");
          } else if (ans.action == "R") {
            $(el).removeClass("is--active");
          }
          if (typeof searchfavorites != "undefined") {
            searchfavorites(document.frmFavSrch);
          }
          $.mbsmessage(ans.msg, true, "alert alert--success");
        } else {
          $.mbsmessage(ans.msg, true, "alert alert--danger");
        }
      },
      {
        fOutMode: "json",
        errorFn: function () {
          isRuningTeacherFavoriteAjax = false;
        },
      }
    );
    $(el).blur();
  };

  generateThread = function (id) {
    if (isUserLogged() == 0) {
      logInFormPopUp();
      return false;
    }

    fcom.updateWithAjax(
      fcom.makeUrl("Messages", "initiate", [id], confWebDashUrl),
      "",
      function (ans) {
        $.mbsmessage.close();
        if (ans.redirectUrl) {
          if (ans.threadId) {
            sessionStorage.setItem("threadId", ans.threadId);
          }
          window.location.href = ans.redirectUrl;
          return;
        }
        $.facebox(ans.html, "");
      }
    );
  };

  sendMessage = function (frm) {
    if (!$(frm).validate()) return;
    var data = fcom.frmData(frm);
    var dv = "#frm_fat_id_frmSendMessage";
    $.loader.show();
    fcom.updateWithAjax(
      fcom.makeUrl("Messages", "sendMessage", [], confWebDashUrl),
      data,
      function (t) {
        $.loader.hide();
        window.location.href = fcom.makeUrl("Messages", "", [], confWebDashUrl);
      }
    );
  };

  // function resendVerificationLink(username){
  // /* if(user==''){
  // return false;
  // } */
  // //$(document).trigger('closeMsg.systemMessage');
  // console.log(username + " is heare");
  // $.systemMessage.close();
  // /* $.mbsmessage(langLbl.processing,false,'alert--process alert');
  // fcom.updateWithAjax( fcom.makeUrl('GuestUser','resendVerification',[username]),'',function(ans){
  // $.mbsmessage(ans.msg, false, 'alert alert--success');
  // }); */
  // }

  closeNavigation = function () {
    $(".subheader .nav__dropdown a").removeClass("is-active");
    $(".subheader .nav__dropdown-target").fadeOut();
  };
})(jQuery);
