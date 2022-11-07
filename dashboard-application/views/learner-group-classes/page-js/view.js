$(function() {
	var dv = '#listItems';
	var frmFlashCardSrch = document.frmFlashCardSrch;

	viewLessonDetail = function(){
		$(dv).html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('LearnerScheduledLessons','viewLessonDetail',[lessonDetailId]),'',function(t){
			$(dv).html(t);
			frmFlashCardSrch = document.frmFlashCardSrch;
			searchFlashCards( frmFlashCardSrch );
		});
	};

	markLearnerJoinTime = function(){
        fcom.ajax(fcom.makeUrl('LearnerScheduledLessons', 'markLearnerJoinTime'), 'lessonId='+lessonId , function(t) {
        });
    };

	goToFlashCardSearchPage = function(page) {
		if(typeof page == undefined || page == null){
			page = 1;
		}
		var frm = document.frmFlashCardSearchPaging;
		$(frm.page).val(page);
		searchFlashCards(frm);
	};

	scheduleLessonSetup = function(lessonId,startTime,endTime,date){
		fcom.ajax(fcom.makeUrl('LearnerScheduledLessons', 'scheduleLessonSetup'), 'lessonId='+lessonId+'&startTime='+startTime+'&endTime='+endTime+'&date='+date, function(doc) {
			$.facebox.close();
			viewLessonDetail();
		});
	};

	/*lessonFeedback = function(lessonId){
		fcom.ajax(fcom.makeUrl('LearnerScheduledLessons','lessonFeedback',[lessonId]),'',function(t){
			$.facebox( t,'facebox-medium');
		});
	};

	setupLessonFeedback = function(frm){
		if (!$(frm).validate()) return false;
		var data = fcom.frmData(frm);
	 fcom.updateWithAjax(fcom.makeUrl('LearnerScheduledLessons', 'setupLessonFeedback'), data , function(t) {
				$.facebox.close();
				viewLessonDetail();
		});
	}*/

	searchFlashCards = function(frm){
		$('#flashCardListing').html(fcom.getLoader());
		var data = fcom.frmData(frm);
		fcom.ajax(fcom.makeUrl('LearnerScheduledLessons','searchFlashCards'),data,function(t){
			$('#flashCardListing').html(t);
		});
	};

	viewFlashCard = function(flashcardId){
		fcom.ajax(fcom.makeUrl('LearnerScheduledLessons','viewFlashCard',[flashcardId]),'',function(t){
			$.facebox( t,'facebox-medium');
		});
	};

	flashCardForm = function( lessonId, flashcardId ){
		fcom.ajax(fcom.makeUrl('LearnerScheduledLessons','flashCardForm'),'flashcardId='+flashcardId + '&lessonId='+lessonId,function(t){
			$.facebox( t,'facebox-medium');
		});
	};

	removeFlashcard = function(id){
        $.confirm({
            title: langLbl.Confirm,
            content: langLbl.confirmRemove,
            buttons: {
                Proceed: {
                    text: langLbl.Proceed,
                    btnClass: 'btn btn--primary',
                    keys: ['enter', 'shift'],
                    action: function(){
                        fcom.updateWithAjax(fcom.makeUrl('LearnerScheduledLessons','removeFlashcard',[id]),'',function(t){
                            searchFlashCards( frmFlashCardSrch );
                        });
                    }
                },
                Quit: {
                    text: langLbl.Quit,
                    btnClass: 'btn btn--secondary',
                    keys: ['enter', 'shift'],
                    action: function(){
                    }
                }
            }
        });
	};

	setupFlashCard = function(frm){
		if (!$(frm).validate()) return false;
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('LearnerScheduledLessons', 'setupFlashCard'), data, function(t) {
			searchFlashCards( frmFlashCardSrch );
			$.facebox.close();
		});
	};

	createChatBox = function(){
		var chat_height = '100%';
		var chat_width = '100%';
		$("#cometChatBox").html('<div id="cometchat_embed_synergy_container" style="width:'+chat_width+';height:'+chat_height+';max-width:100%;border:1px solid #CCCCCC;border-radius:5px;overflow:hidden;"></div>');
		var chat_js = document.createElement('script'); chat_js.type = 'text/javascript'; chat_js.src = '//fast.cometondemand.net/'+chat_appid+'x_xchatx_xcorex_xembedcode.js';
		chat_js.onload = function() {
		var chat_iframe = {};chat_iframe.module="synergy";chat_iframe.style="min-height:"+chat_height+";min-width:"+chat_width+";";chat_iframe.width=chat_width.replace('px','');chat_iframe.height=chat_height.replace('px','');chat_iframe.src='//'+chat_appid+'.cometondemand.net/cometchat_embedded.php?guid='+chat_group_id; if(typeof(addEmbedIframe)=="function"){addEmbedIframe(chat_iframe);}
		}
		var chat_script = document.getElementsByTagName('script')[0]; chat_script.parentNode.insertBefore(chat_js, chat_script);
	};

	addFriendsCometUsers = function(learnerId,teacherId){
		$.ajax({
		  method: "POST",
		  url: "https://api.cometondemand.net/api/v2/addFriends",
		  data: { UID:learnerId,friendsUID:teacherId},
		  beforeSend: function (xhr) {
			xhr.setRequestHeader('api-key', chat_api_key);
			},
		})
		.done(function( msg ) {
			  if(typeof(msg.success) != "undefined" && msg.success !== null)
			  {
				  sessionStorage.setItem('cometChatUserExists',chat_group_id);
				  $.mbsmessage( msg.success.message,true, 'alert alert--success');
				  createChatBox();
                  markLearnerJoinTime();
			  }
			  else{
                                      markLearnerJoinTime();
				  //$.mbsmessage( msg.failed.message,true, 'alert alert--danger');
			  }
			});
	};

	/* createUserCometChatApi = function(CometJsonData,CometJsonFriendData){
		$(CometJsonData).each(function(i,val){
			$.ajax({
			  method: "POST",
			  url: "https://api.cometondemand.net/api/v2/createUser",
			  data: { UID:val.userId,name:val.fname,"role":val.role },
			  beforeSend: function (xhr) {
				xhr.setRequestHeader('api-key', '50978xedfcf997f213f06970117bcaf0ef3301');
				},
			})
			.done(function( msg ) {
				  if(typeof(msg.success) != "undefined" && msg.success !== null)
				  {

					  $.mbsmessage( msg.success.message,true, 'alert alert--success');
					  addFriendsCometUsers(CometJsonFriendData.userId,CometJsonFriendData.friendId);
				  }
				  else{
					  //$.mbsmessage( msg.failed.message,true, 'alert alert--danger');
					  addFriendsCometUsers(CometJsonFriendData.userId,CometJsonFriendData.friendId);
				  }
				});
			});
	} */

	checkUSerExistInCometChatApi = function(learnerId,teacherId){
		$.ajax({
			  method: "POST",
			  url: "https://api.cometondemand.net/api/v2/getGroupMessages",
			  //data: { UID:learnerId },
			  data: { GUIDs:chat_group_id },
			  beforeSend: function (xhr) {
				xhr.setRequestHeader('api-key', chat_api_key);
				},
			})
			.done(function( msg ) {
				  if(typeof(msg.success) != "undefined" && msg.success !== null)
				  {
					   //addFriendsCometUsers(learnerId,teacherId);
					   joinLessonButtonAction();
					   sessionStorage.setItem('cometChatUserExists',chat_group_id);
                       markLearnerJoinTime();
					   createChatBox();
					   location.reload();
					  $.mbsmessage( msg.success.message,true, 'alert alert--success');
				}
				  else{
					  checkEveryMinuteStatus();
					  $.mbsmessage( "Wait Let the Teacher Initiate the lesson from His/Her End!",true, 'alert alert--danger');
				  }
				});

	};
	endLesson = function (lessonId) {
                $.confirm({
                    title: langLbl.Confirm,
                    content: langLbl.endLessonAlert,
                    buttons: {
                        Proceed: {
                            text: langLbl.Procee,
                            btnClass: 'btn btn--primary',
                            keys: ['enter', 'shift'],
                            action: function(){
                                fcom.updateWithAjax(fcom.makeUrl('LearnerScheduledLessons', 'endLessonSetup'), 'lessonId='+lessonId , function(t) {
									if (typeof checkEveryMinuteStatusVar != "undefined") {
										clearInterval(checkEveryMinuteStatusVar);
									}
                                        $('.screen-chat-js').hide();
                                        sessionStorage.removeItem('cometChatUserExists');
                                        endLessonButtonAction();
                                        $.facebox.close();
                                        viewLessonDetail();
                                });
                            }
                        },
                        Quit: {
                            text: langLbl.Quit,
                            btnClass: 'btn btn--secondary',
                            keys: ['enter', 'shift'],
                            action: function(){
                            }
                        }
                    }
                });
	};
	endLessonSetup = function(lessonId){
		//if(confirm(langLbl.confirmRemove))
		//{
			fcom.updateWithAjax(fcom.makeUrl('LearnerScheduledLessons', 'endLessonSetup'), 'lessonId='+lessonId , function(t) {
				if (typeof checkEveryMinuteStatusVar != "undefined") {
					clearInterval(checkEveryMinuteStatusVar);
				}
					$('.screen-chat-js').hide();
					sessionStorage.removeItem('cometChatUserExists');
					endLessonButtonAction();
					$.facebox.close();
					viewLessonDetail();
			});
		//}
	};

	/*viewBookingCalendar = function(id){
		fcom.ajax(fcom.makeUrl('LearnerScheduledLessons','viewBookingCalendar',[id]),'',function(t){
			viewLessonDetail();
			$.facebox( t,'facebox-medium');
		});
	};*/

	/*cancelLesson = function(id){
		$(dv).html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('LearnerScheduledLessons','cancelLesson',[id]),'',function(t){
			viewLessonDetail();
			$.facebox( t,'facebox-medium');
		});
	};

	cancelLessonSetup = function(frm){
		if (!$(frm).validate()) return;
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('LearnerScheduledLessons', 'cancelLessonSetup'), data , function(t) {
				$.facebox.close();
				viewLessonDetail();
		});
	};*/

	/*issueReported = function(id){
		fcom.ajax(fcom.makeUrl('LearnerScheduledLessons','issueReported',[id]),'',function(t){
			$.facebox( t,'facebox-medium');
		});
	};

	issueReportedSetup = function(frm){
		if (!$(frm).validate()) return;
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('LearnerScheduledLessons', 'issueReportedSetup'), data , function(t) {
				$.facebox.close();
				viewLessonDetail();
		});
	};*/

	/*requestReschedule = function(id){
		fcom.ajax(fcom.makeUrl('LearnerScheduledLessons','requestReschedule',[id]),'',function(t){
			$.facebox( t,'facebox-medium');
		});
	};

	requestRescheduleSetup = function(frm){
	if (!$(frm).validate()) return;
		var data = fcom.frmData(frm);
		fcom.updateWithAjax(fcom.makeUrl('LearnerScheduledLessons', 'requestRescheduleSetup'), data , function(t) {
				$.facebox.close();
				viewLessonDetail();
		});
	};*/

	getListingLessonPlans = function(id){
		$(dv).html(fcom.getLoader());
		fcom.ajax(fcom.makeUrl('LearnerScheduledLessons','getListingLessonPlans',[id]),'',function(t){
			viewLessonDetail();
			$.facebox( t,'facebox-medium');
		});
	};

	changeLessonPlan = function(id){
		fcom.ajax(fcom.makeUrl('LearnerScheduledLessons','changeLessonPlan',[id]),'',function(t){
			$.facebox( t,'facebox-medium');
		});
	};

	assignLessonPlanToLessons = function(lessonId,planId){
		fcom.updateWithAjax(fcom.makeUrl('LearnerScheduledLessons', 'assignLessonPlanToLessons'), 'ltp_slessonid='+lessonId+'&ltp_tlpn_id='+planId , function(t) {
				$.facebox.close();
				viewLessonDetail();
		});
	};

	removeAssignedLessonPlan = function(lessonId){
		if(confirm(langLbl.confirmRemove))
		{
			fcom.updateWithAjax(fcom.makeUrl('LearnerScheduledLessons', 'removeAssignedLessonPlan'), 'ltp_slessonid='+lessonId , function(t) {
					$.facebox.close();
					viewLessonDetail();
			});
		}
	};

	viewAssignedLessonPlan = function(lessonId){
		fcom.ajax(fcom.makeUrl('LearnerScheduledLessons', 'viewAssignedLessonPlan',[lessonId]), '', function(t) {
			//viewLessonDetail();
			$.facebox( t,'facebox-medium');
		});
	};

	viewLessonDetail();

});
