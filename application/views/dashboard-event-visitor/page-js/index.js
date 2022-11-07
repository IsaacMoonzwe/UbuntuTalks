var runningAjaxReq = false;
var dv = '#formBlock-js';
var paymentInfoDiv = '#paymentInfoDiv';
var profileInfoFormDiv = '#profileInfoFrmBlock';
var faq = '#eventfaq';
var myAccount = '#accountinformation';
var myRequirement = '#requirementinformation';
var myDailySchedule = '#dailyschedule';
var myReportAnIssue = '#reportanissue';

var addToCartAjaxRunning = false;
$(document).ready(function () {
	eventProfileInfoForm();
	getEventFaq();
	getMyAccount();
	getMyRequirement();
	getMyDailySchedule();
	getMyReportAnIssue();
	$('body').on('click', '.tab-ul-js li a', function () {
		$('.tab-ul-js li').removeClass('is-active');
		$(this).parent('li').addClass('is-active');
	});
	agendaSetupTestimonial = function (frm) {
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('DashboardEventVisitor', 'agendasetup'), data, function (t) {
			location.reload(true);
			reloadList();
			if (t.langId > 0) {
				editTestimonialLangForm(t.testimonialId, t.langId);
				return;
			}
			$(document).trigger('close.facebox');
		});
	}
});
(function () {
	var runningAjaxReq = false;
	var dv = '#formBlock-js';
	var paymentInfoDiv = '#paymentInfoDiv';
	var profileInfoFormDiv = '#profileInfoFrmBlock';


	checkRunningAjax = function () {
		if (runningAjaxReq == true) {
			return;
		}
		runningAjaxReq = true;
	};
	eventProfileInfoForm = function () {
		$(dv).html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('DashboardEventVisitor', 'eventProfileInfoForm'), '', function (t) {
			$(dv).html(t);
			// if (userIsTeacher) {
			// 	getTeacherProfileProgress();
			// }
		});
	};

	$(document).on('click', '[data-method]', function () {
		var data = $(this).data(),
			$target,
			result;

		if (data.method) {
			data = $.extend({}, data);
			if (typeof data.target !== 'undefined') {
				$target = $(data.target);
				if (typeof data.option === 'undefined') {
					try {
						data.option = JSON.parse($target.val());
					} catch (e) {
						console.log(e.message);
					}
				}
			}
			result = $image.cropper(data.method, data.option);
			if (data.method === 'getCroppedCanvas') {
				$('#getCroppedCanvasModal').modal().find('.modal-body').html(result);
			}

			if ($.isPlainObject(result) && $target) {
				try {
					$target.val(JSON.stringify(result));
				} catch (e) {
					console.log(e.message);
				}
			}

		}
	});

	var $image;
	cropImage = function (obj) {
		$image = obj;
		$image.cropper({
			aspectRatio: 1,
			guides: false,
			highlight: false,
			dragCrop: false,
			cropBoxMovable: false,
			cropBoxResizable: false,
			rotatable: true,
			responsive: true,
			crop: function (e) {
				var json = [
					'{"x":' + e.detail.x,
					'"y":' + e.detail.y,
					'"height":' + e.detail.height,
					'"width":' + e.detail.width,
					'"rotate":' + e.detail.rotate + '}'
				].join();
				$("#img_data").val(json);
			},
			built: function () {
				$(this).cropper("zoom", 0.5);
			},
		})
	};

	popupImage = function (input) {
		$.facebox(fcom.getLoader());

		wid = $(window).width();
		if (wid > 767) {
			wid = 500;
		} else {
			wid = 280;
		}


		var defaultform = "#frmProfile";
		$("#avatar-action").val("demo_avatar");
		$(defaultform).ajaxSubmit({
			delegation: true,
			success: function (json) {
				json = $.parseJSON(json);
				if (json.status == 1) {
					$("#avatar-action").val("avatar");
					var fn = "sumbmitProfileImage();";
					$.facebox('<div class="popup__body"><div class="img-container "><img alt="Picture" src="" class="img_responsive" id="new-img" /></div><div class="img-description"><div class="rotator-info">Use Mouse Scroll to Adjust Image</div><div class="-align-center rotator-actions"><a href="javascript:void(0)" class="btn btn--primary btn--sm" title="' + $("#rotate_left").val() + '" data-option="-90" data-method="rotate">' + $("#rotate_left").val() + '</a>&nbsp;<a onclick=' + fn + ' href="javascript:void(0)" class="btn btn--secondary btn--sm">' + $("#update_profile_img").val() + '</a>&nbsp;<a href="javascript:void(0)" class="btn btn--primary btn--sm rotate-right" title="' + $("#rotate_right").val() + '" data-option="90" data-method="rotate">' + $("#rotate_right").val() + '</a></div></div></div>', '');
					$('#new-img').attr('src', json.file);
					$('#new-img').width(wid);
					cropImage($('#new-img'));
				} else {
					$.mbsmessage(json.msg, true, 'alert alert--danger');
					$(document).trigger('close.facebox');
					return false;
				}
			}
		});
	};

	removeProfileImage = function () {
		if (!confirm(langLbl.deleteImageCnfMsg)) {
			return false;
		}
		$.loader.show();
		fcom.ajax(fcom.makeUrl('DashboardEventVisitor', 'removeProfileImage'), '', function (t) {
			$.loader.hide();
			$.mbsmessage("Profile Image Removed", true, 'alert alert--success');
			// profileImageForm();
			getMyAccount('ProfileImage');
			if (isCometChatMeetingToolActive) {
				name = userData.user_first_name + " " + userData.user_last_name;
				userSeoUrl = userSeoBaseUrl + userData.user_url_name;
				updateCometChatUser(userData.user_id, name, '', userSeoUrl);
			}

		});
	};

	updateCometChatUser = function (userId, name, avatarURL, profileURL) {
		if (!isCometChatMeetingToolActive) {
			return true;
		}
		console.log("user--", userId);
		console.log('chat_api_key--', chat_api_key);
		var settings = {
			"async": true,
			"crossDomain": true,
			"url": "https://api.cometondemand.net/api/v2/updateUser",
			"method": "POST",
			"headers": {
				"api-key": chat_api_key,
				"content-type": "application/x-www-form-urlencoded",
			},
			"data": {
				"UID": userId,
				"name": name,
				"avatarURL": avatarURL,
				"profileURL": profileURL,
			}
		}

		$.ajax(settings).done(function (response) {
			console.log(response);
		});
	};

	profileImageForm = function () {
		$(profileInfoFormDiv).html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('DashboardEventVisitor', 'profileImageForm'), '', function (t) {
			$(profileInfoFormDiv).html(t);
		});
	};


	sumbmitProfileImage = function (goToLangForm) {
		if (!$("#frmProfile").validate()) {
			return;
		}
		$.loader.show();
		$("#frmProfile").ajaxSubmit({
			delegation: true,
			success: function (json) {
				json = $.parseJSON(json);
				$.loader.hide();
				$(document).trigger('close.facebox');
				if (json.status == 1) {
					if (isCometChatMeetingToolActive) {
						name = userData.user_first_name + " " + userData.user_last_name;
						userSeoUrl = userSeoBaseUrl + userData.user_url_name;
						updateCometChatUser(userData.user_id, name, userImage, userSeoUrl);
					}

					$.mbsmessage(json.msg, true, 'alert alert--success');
					if (goToLangForm && $('.profile-lang-li').length > 0) {
						$('.profile-lang-li').first().click();
					} else {
						// profileImageForm();
						getMyAccount('ProfileImage');

					}
				} else {
					$.mbsmessage(json.msg, true, 'alert alert--danger');
					return false;
				}

			}
		});
	};


	// setUpProfileInfo = function (frm, gotoProfileImageForm) {
	// 	if (!$(frm).validate()) {
	// 		$("html, body").animate({ scrollTop: $(".error").eq(0).offset().top - 100 }, "slow");
	// 		return false;
	// 	}
	// 	$.loader.show();
	// 	var data = fcom.frmData(frm);
	// 	fcom.updateWithAjax(fcom.makeUrl('DashboardEventVisitor', 'setUpProfileInfo'), data, function (t) {
	// 		setTimeout(function () {
	// 			$.systemMessage.close();
	// 		}, 3000);
	// 		$.loader.hide();

	// 		return true;
	// 	});
	// };

	setUpProfileInfo = function (frm, gotoProfileImageForm) {
		if (!$(frm).validate()) {
			$("html, body").animate({ scrollTop: $(".error").eq(0).offset().top - 100 }, "slow");
			return false;
		}
		$.loader.show();
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('DashboardEventVisitor', 'setUpProfileInfo'), data, function (t) {
			setTimeout(function () {
				$.systemMessage.close();
			}, 3000);
			$.loader.hide();
			if (isCometChatMeetingToolActive) {
				name = frm.user_first_name.value + " " + frm.user_last_name.value;
				userSeoUrl = '';
				if (frm.user_url_name) {
					userSeoUrl = userSeoBaseUrl + frm.user_url_name.value;
				}
				updateCometChatUser(userData.user_id, name, userImage, userSeoUrl);
			}

			if (userIsTeacher) {
				getTeacherProfileProgress();
			}

			if (gotoProfileImageForm) {
				$('.profile-imag-li').click();
			}
			return true;
		});
	};

	setUpProfileRequirementInfo = function (frm, gotoProfileImageForm) {
		if (!$(frm).validate()) {
			$("html, body").animate({ scrollTop: $(".error").eq(0).offset().top - 100 }, "slow");
			return false;
		}
		$.loader.show();
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('DashboardEventVisitor', 'setUpProfileRequirementInfo'), data, function (t) {
			setTimeout(function () {
				$.systemMessage.close();
			}, 3000);
			$.loader.hide();

			return true;
		});
	};
	changeEmailForm = function () {
		$(dv).html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('DashboardEventVisitor', 'changeEmailForm'), '', function (t) {
			$(dv).html(t);
		});
	};

	setUpEmail = function (frm) {
		if (!$(frm).validate()) return;
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('DashboardEventVisitor', 'setUpEmail'), data, function (t) {
			// changeEmailForm();
			window.location.href = window.location.href;
		});
	};
	getEventFaq = function () {
		$(faq).html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('DashboardEventVisitor', 'eventFaq'), '', function (t) {
			$(faq).html(t);
		});
	};


	getMyAccount = function (location = '') {
		$(myAccount).html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('DashboardEventVisitor', 'myAccount'), '', function (t) {
			$(myAccount).html(t);
			if (location != '') {
				openCity(event, location);
			}
		});
	};

	getMyRequirement = function () {
		$(myRequirement).html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('DashboardEventVisitor', 'requirement'), '', function (t) {
			$('#requirementinformation').html(t);
		});
	};


	getMyDailySchedule = function () {
		$(myDailySchedule).html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('DashboardEventVisitor', 'dailySchedule'), '', function (t) {
			$('#dailyschedule').html(t);
		});
	};

	getMyReportAnIssue = function () {
		$(myReportAnIssue).html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('DashboardEventVisitor', 'report'), '', function (t) {
			$('#reportanissue').html(t);
		});
	};




	changePasswordForm = function () {
		$(dv).html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('DashboardEventVisitor', 'changePasswordForm'), '', function (t) {
			$(dv).html(t);
		});
	};

	updatePassword = function (frm) {
		if (!$(frm).validate()) return;
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('DashboardEventVisitor', 'updatePassword'), data, function (t) {
			changePasswordForm();
		});
	};

	setUpPassword = function (frm) {
		if (!$(frm).validate()) return;
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('DashboardEventVisitor', 'setUpPassword'), data, function (t) {
			// changePasswordForm();
			window.location.href = window.location.href;
		});
	};
	successHelp = function (frm) {
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('DashboardEventVisitor', 'Sucesshelp'), data, function (t) {
			setTimeout(function () {
				$.systemMessage.close();
			}, 3000);
			$.loader.hide();

			frm.reset();
			return true;
		});
	};

})();