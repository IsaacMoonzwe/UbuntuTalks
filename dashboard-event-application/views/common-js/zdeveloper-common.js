var isRuningTeacherFavoriteAjax = false;

function isUserLogged() {
	var isUserLogged = 0;
	$.ajax({
		url: fcom.makeUrl('GuestUser', 'checkAjaxUserLoggedIn', [], confFrontEndUrl),
		async: false,
		dataType: 'json',
	}).done(function (ans) {
		isUserLogged = parseInt(ans.isUserLogged);
	});
	return isUserLogged;
}
getStatisticalData = function (form) {

	if (!$(form).validate()) return;
	var data = fcom.frmData(form);
	fcom.ajax(fcom.makeUrl('TeacherReports', 'getStatisticalData'), data, function (res) {
		console.log(res.earningData);
		$('.earing-amount-js').html(res.earningData.earning);
		$('.lessons-sold-count-js').html(res.soldLessons.lessonCount);
	}, { fOutMode: 'json' });
}

generateThread = function (id) {
	$.loader.show();
	fcom.updateWithAjax(fcom.makeUrl('Messages', 'initiate', [id], confWebRootUrl), '', function (ans) {
		$.mbsmessage.close();
		$.loader.hide();
		if (ans.redirectUrl) {
			if (ans.threadId) {
				sessionStorage.setItem('threadId', ans.threadId);
			}
			window.location.href = ans.redirectUrl;
			return;
		}
		$.facebox(ans.html, '');
	});
};

sendMessage = function (frm) {
	if (!$(frm).validate()) return;
	var data = fcom.frmData(frm);
	// var dv = "#frm_fat_id_frmSendMessage";
	// $(dv).html(fcom.getLoader());
	$.loader.show();
	fcom.updateWithAjax(fcom.makeUrl('Messages', 'sendMessage', [], confWebRootUrl), data, function (t) {
		$.loader.hide();
		window.location.href = fcom.makeUrl('Messages', '', [], confWebRootUrl);
	});
};

getCookieConsentForm = function (inFacebox) {
	inFacebox = (inFacebox) ? inFacebox : false;
	fcom.ajax(fcom.makeUrl('Custom', 'cookieForm', [], confFrontEndUrl), '', function (t) {
		if (inFacebox) {
			$.facebox(t, 'facebox-medium cookies-popup');
			return;
		}
		$('#formBlock-js').html(t);
	});
}
saveCookieSetting = function (form) {
	if (!$(form).validate()) return;
	var data = fcom.frmData(form);
	fcom.updateWithAjax(fcom.makeUrl('Custom', 'saveCookieSetting', [], confFrontEndUrl), data, function (t) {
		$('.cookie-alert').remove();
		$.facebox.close();
	});
}
$(document).ready(function () {

	setUpJsTabs();

	setUpGoToTop();

	// /setUpStickyHeader();

	toggleNavDropDownForDevices();

	toggleHeaderNavigationForDevices();

	/* toggleFooterLinksForDevices(); */

	toggleHeaderCurrencyLanguageForDevices();

	toggleFooterCurrencyLanguage();



	if ($.datepicker) {

		var old_goToToday = $.datepicker._gotoToday
		$.datepicker._gotoToday = function (id) {
			old_goToToday.call(this, id);
			this._selectDate(id);
			$(id).blur();
			return;
		}
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
			$('html').addClass('show-facebox');
			facebocxHeight = screenHeight;
			$('#facebox .content').css('max-height', facebocxHeight - 50 + 'px');
			if ($('#facebox .content').height() + 100 >= screenHeight) {
				//$('#facebox .content').css('overflow-y', 'scroll');
				$('#facebox .content').css('display', 'block');
			} else {
				$('#facebox .content').css('max-height', '');
				$('#facebox .content').css('overflow', '');
			}
		},

		updateFaceboxContent: function (t, cls) {
			if (typeof cls == 'undefined' || cls == 'undefined') {
				cls = '';
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


	$(document).bind('reveal.facebox', function () {
		fcom.resetFaceboxHeight();
	});
	$(window).on("orientationchange", function () {
		facebocxHeight = screenHeight;
		$('#facebox .content').css('max-height', facebocxHeight - 50 + 'px');
		if ($('#facebox .content').height() + 100 >= screenHeight) {
			//$('#facebox .content').css('overflow-y', 'scroll');
			$('#facebox .content').css('display', 'block');
		} else {
			$('#facebox .content').css('max-height', '');
			$('#facebox .content').css('overflow', '');
		}
	});
	$(document).bind('loading.facebox', function () {
		//$('#facebox .content').addClass('fbminwidth');
	});
	$(document).bind('beforeReveal.facebox', function () {
		//$('#facebox .content').addClass('scrollbar scrollbar-js fbminwidth');
	});
	$(document).bind('afterClose.facebox', function () {
		$('html').removeClass('show-facebox');
	});

	setUpJsTabs = function () {

		/* upon loading[ */
		$(".tabs-content-js").hide();
		$(".tabs-js li:first").addClass("is-active").show();
		$(".tabs-content-js:first").show();
		/* ] */

	},

		setUpGoToTop = function () {
			$(window).scroll(function () {
				if ($(this).scrollTop() > 100) {
					$('.scroll-top-js').addClass("isvisible");
				} else {
					$('.scroll-top-js').removeClass("isvisible");
				}
			});

			$(".scroll-top-js").click(function () {
				$('body,html').animate({
					scrollTop: 0
				}, 800);
				return false;
			});
		},

		setUpStickyHeader = function () {
			if ($(window).width() > 767) {
				$(window).scroll(function () {
					if ($(".body").length > 0) {
						scroll_position = $(window).scrollTop();
						if (body_height.top < scroll_position) {
							$(".header").addClass("is-fixed");
						} else {
							$(".header").removeClass("is-fixed");
						}
					}

				});


			}

		},

		toggleNavDropDownForDevices = function () {
			if ($(window).width() < 1200) {
				$('.nav__dropdown-trigger-js').click(function () {
					if ($(this).hasClass('is-active')) {
						$('html').removeClass('show-dashboard-js');
						$(this).removeClass('is-active');
						$(this).siblings('.nav__dropdown-target-js').slideUp(); return false;
					}
					$('.nav__dropdown-trigger-js').removeClass('is-active');
					$('html').addClass('show-dashboard-js');
					$(this).addClass("is-active");
					$('.nav__dropdown-target-js').slideUp();
					$(this).siblings('.nav__dropdown-target-js').slideDown();
				});
			}
		},

		toggleHeaderNavigationForDevices = function () {
			$('.toggle--nav-js').click(function () {
				$(this).toggleClass("is-active");
				$('html').toggleClass("show-nav-js");
				$('html').removeClass("show-dashboard-js");
			});
		},

		jQuery(document).ready(function (e) {
			function t(t) {
				e(t).bind("click", function (t) {
					t.preventDefault();
					e(this).parent().fadeOut()
				})
			}

			$(".cc-cookie-accept-js").click(function () {
				fcom.ajax(fcom.makeUrl('Custom', 'updateUserCookies', [], confFrontEndUrl), '', function (t) {
					$(".cookie-alert").hide('slow');
					$(".cookie-alert").remove();
					$.facebox.close();
				});
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
				var t = e(this).parents(".toggle-group").children(".toggle__target-js").is(":hidden");
				e(".toggle-group .toggle__target-js").hide();
				e(".toggle-group .toggle__trigger-js").removeClass("is-active");
				if (t) {
					e(this).parents(".toggle-group").children(".toggle__target-js").toggle().parents(".toggle-group").children(".toggle__trigger-js").addClass("is-active")
				}

			});

			$(document.body).on('click', ".toggle__trigger-js", function () {
				var t = e(this).parents(".toggle-group").children(".toggle__target-js").is(":hidden");
				e(".toggle-group .toggle__target-js").hide();
				e(".toggle-group .toggle__trigger-js").removeClass("is-active");
				if (t) {
					e(this).parents(".toggle-group").children(".toggle__target-js").toggle().parents(".toggle-group").children(".toggle__trigger-js").addClass("is-active")
				}
			});
			e(document).bind("click", function (t) {
				var n = e(t.target);
				if (!n.parents().hasClass("toggle-group")) e(".toggle-group .toggle__target-js").hide();
			});
			e(document).bind("click", function (t) {
				var n = e(t.target);
				if (!n.parents().hasClass("toggle-group")) e(".toggle-group .toggle__trigger-js").removeClass("is-active");
			})

			$(".tab-swticher-small a").click(function () {
				$(".tab-swticher-small a").removeClass("is-active");
				$(this).addClass("is-active");

			});
		});

	toggleHeaderCurrencyLanguageForDevices = function () {
		$('.nav__item-settings-js').click(function () {
			$(this).toggleClass("is-active");
			$('html').toggleClass("show-setting-js");
		});
	},

		toggleFooterCurrencyLanguage = function () {
			$(".toggle-footer-lang-currency-js").click(function () {

				var clickedSectionClass = $(this).siblings(".listing-div-js").attr("div-for");

				$(".toggle-footer-lang-currency-js").each(function () {
					if ($(this).siblings(".listing-div-js").attr("div-for") != clickedSectionClass) {
						$(this).siblings(".listing-div-js").hide();
					}
				});

				$(this).siblings(".listing-div-js").slideToggle();
			});
		},

		setSiteDefaultLang = function (langId) {
			$.loader.show();
			var url = window.location.pathname;
			var srchString = window.location.search;
			var data = 'pathname=' + url;
			fcom.updateWithAjax(fcom.makeUrl('Home', 'setSiteDefaultLang', [langId], confFrontEndUrl), data, function (res) {
				window.location.href = res.redirectUrl + srchString;
			});
		},

		setSiteDefaultCurrency = function (currencyId) {
			$.loader.show();
			fcom.updateWithAjax(fcom.makeUrl('Home', 'setSiteDefaultCurrency', [currencyId], confFrontEndUrl), '', function (res) {
				document.location.reload();
			});
		},

		displayMessage = function (msg) {
			$.mbsmessage(msg, true, 'alert alert--success');
		};

	closeMessage = function () {
		$(document).trigger('close.mbsmessage');
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
			logInFormPopUp();
			return false;
		}
		var data = 'teacher_id=' + teacher_id;
		$.mbsmessage.close();
		fcom.updateWithAjax(fcom.makeUrl('Learner', 'toggleTeacherFavorite'), data, function (ans) {
			isRuningTeacherFavoriteAjax = false;
			if (ans.status) {
				if (ans.action == 'A') {
					$(el).addClass("is--active");
				} else if (ans.action == 'R') {
					$(el).removeClass("is--active");
				}
				if (typeof searchfavorites != 'undefined') {
					searchfavorites(document.frmFavSrch);
				}
			}
		});
		$(el).blur();
	}

	closeNavigation = function () {
		$('.subheader .nav__dropdown a').removeClass('is-active');
		$('.subheader .nav__dropdown-target').fadeOut();
	}


})(jQuery);


/**
* Check JSON String
* @returns {Boolean}
*/
function isJson(str) {
	try {
		JSON.parse(str);
	} catch (e) {
		return false;
	}
	return true;
}
