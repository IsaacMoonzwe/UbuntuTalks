(function () {
    var currentPage = 1;
    var div = "#lessonListing";
    
    viewDetail = function (lessonId) {
        $.facebox(function () {
            fcom.ajax(fcom.makeUrl('EventsReportAnIssue', 'viewDetail', [lessonId]), '', function (t) {
                $.facebox(t, 'faceboxWidth');
            });
        });
    };
    reportDetail = function(lessonId){
		$.facebox(function() {
			fcom.ajax(fcom.makeUrl('EventsReportAnIssue', 'reportDetail', [lessonId]), '', function(t) {
			$.facebox(t,'faceboxWidth');
            console.log("Hellobfb1111");
			});
		});
	};
        agendaSetupTestimonial = function (frm){
            var data = fcom.frmData(frm);
            fcom.updateWithAjax(fcom.makeUrl('EventsReportAnIssue', 'agendasetup'), data, function(t) {		
                console.log("comments");
                location.reload(true);
                reloadList();
                if (t.langId>0) {
                    editTestimonialLangForm(t.testimonialId, t.langId);
                    return ;
                }	
                $(document).trigger('close.facebox');
            });
        }
    updateAssignClassStatus = function(obj, id, value, oldValue){
		if (!confirm("Do you really want to assign the class ?")) {
            $(obj).val(oldValue);
            return;
        }
        if (id === null) {
            $.mbsmessage('Invalid Request!');
            return false;
        }
		fcom.ajax(fcom.makeUrl('EventsReportAnIssue','updateAssignClass'),{"sldetail_id":id, "teachers_id" : value},function(json){
			res = $.parseJSON(json);
            $(div).html(fcom.getLoader());
			if(res.status == "1"){
				  $.mbsmessage( res.msg,true, 'alert alert--success');
                  window.location.href=window.location.href;

			}else{
					$(obj).val(oldValue);
				  $.mbsmessage( res.msg,true, 'alert alert--danger');
			}
		});
	};



    ChangeStatus = function(value){
		
		fcom.ajax(fcom.makeUrl('EventsReportAnIssue','changeStatus',[value]),"",function(json){
			res = $.parseJSON(json);
            $(div).html(fcom.getLoader());
			if(res.status == "1"){
				  $.mbsmessage( res.msg,true, 'alert alert--success');
                  window.location.href=window.location.href;

			}else{
					$(obj).val(oldValue);
				  $.mbsmessage( res.msg,true, 'alert alert--danger');
			}
		});
	};






    
    updateScheduleStatus = function (obj, id, value, oldValue) {
        // var currentValue = $(obj).val();
        if (!confirm("Do you really want to update status?")) {
            $(obj).val(oldValue);
            return;
        }
        if (id === null) {
            $.mbsmessage('Invalid Request!');
            return false;
        }
        fcom.ajax(fcom.makeUrl('EventsReportAnIssue', 'updateStatusSetup'), {"sldetail_id": id, "slesson_status": value}, function (json) {
            res = $.parseJSON(json);
            if (res.status == "1") {
                $.mbsmessage(res.msg, true, 'alert alert--success');
            } else {
                $(obj).val(oldValue);
                $.mbsmessage(res.msg, true, 'alert alert--danger');
            }
        });
    };
    goToSearchPage = function (page) {
        if (typeof page == undefined || page == null) {
            page = 1;
        }
        var frm = document.frmPurchaseLessonSearchPaging;
        $(frm.page).val(page);
        searchPuchasedLessons(frm);
    };
    searchPuchasedLessons = function (form) {
        // currentPage = (page && !page) ? currentPage : page;
        data = (form) ? fcom.frmData(form) : '';
        $(div).html(fcom.getLoader());
        fcom.ajax(fcom.makeUrl('EventsReportAnIssue', 'purchasedLessonsSearch'), data, function (res) {
            $(div).html(res);
        });
    };
    clearPuchasedLessonSearch = function () {
        document.purchasedLessonsSearchForm.reset();
        document.purchasedLessonsSearchForm.slesson_teacher_id.value = '';
        searchPuchasedLessons(document.purchasedLessonsSearchForm);
    };
})();
$(document).ready(function () {
    searchPuchasedLessons(document.purchasedLessonsSearchForm);
    $('input[name=\'teacher\']').autocomplete({
        'source': function (request, response) {
            $.ajax({
                url: fcom.makeUrl('Users', 'autoCompleteJson'),
                data: {keyword: request, fIsAjax: 1},
                dataType: 'json',
                type: 'post',
                success: function (json) {
                    response($.map(json, function (item) {
                        return {label: item['name'] + '(' + item['username'] + ')', value: item['id'], name: item['username']};
                    }));
                },
            });
        },
        'select': function (item) {
            $("input[name='slesson_teacher_id']").val(item['value']);
            $("input[name='teacher']").val(item['name']);
        }
    });
    $('input[name=\'learner\']').autocomplete({
        'source': function (request, response) {
            $.ajax({
                url: fcom.makeUrl('Users', 'autoCompleteJson'),
                data: {keyword: request, fIsAjax: 1},
                dataType: 'json',
                type: 'post',
                success: function (json) {
                    response($.map(json, function (item) {
                        return {label: item['name'] + '(' + item['username'] + ')', value: item['id'], name: item['username']};
                    }));
                },
            });
        },
        'select': function (item) {
            $("input[name='sldetail_learner_id']").val(item['value']);
            $("input[name='learner']").val(item['name']);
        }
    });
    $('input[name=\'learner\']').keyup(function () {
        $('input[name=\'order_user_id\']').val('');
    });
    $('input[name=\'teacher\']').keyup(function () {
        $('input[name=\'op_teacher_id\']').val('');
    });
});
