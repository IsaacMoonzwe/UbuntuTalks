$(document).ready(function () {
    var frm = document.frmMessageSrch;
    threadListing(frm);
    $(".window__search-field-js").click(function () {
        $(".window__search-form-js").toggle();
    });
    if ($(window).width() > 1199) {
        $('.scrollbar-js').enscroll({
            verticalTrackClass: 'scrollbar-track',
            verticalHandleClass: 'scrollbar-handle'
        });
    }
    if (sessionStorage.getItem('threadId') != null) {
        getThread(sessionStorage.getItem('threadId'));
        sessionStorage.removeItem('threadId');
    }

    $(".msg-list__action-js").click(function(){       
        $(this).parent().toggleClass("is-active"); 
        $(".message-details-js").show();
        $("html").addClass("show-message-details"); return false;
    });  
           
    $(".msg-close-js").click(function(){
        $(".message-details-js").hide();
        $("html").removeClass("show-message-details"); return false;
    });
    
    $(document).on('keyup','textarea[name="message_text"]',function(e){
        if(e.keyCode == 13){
            sendMessage(document.frmSendMessage);
        }            
    })
           
  
});
var messageThreadPage = 1;
var messageThreadAjax = false;
var div = '#threadListing';

$.chatLoader = {
    show: function() {
        $('.load-more-js').html(fcom.getLoader())
    },
    hide: function() {
        $('.load-more-js').html('')
    }
};

function threadListing(frm, id) {
    var data = fcom.frmData(frm);
    data = data + "&isactive=" + id;
    fcom.ajax(fcom.makeUrl('Messages', 'search'), data, function (res) {
        $(div).html(res);
    });
    $(".window__search-form-js").hide();
    updateHeaderNotifCount();
}
var msgCntDiv = ".unrdMsgCnt";
function updateHeaderNotifCount() {
    fcom.ajax(fcom.makeUrl('Messages', 'getUnreadCount'), '', function (data) {
        var data = JSON.parse(data);
        $(msgCntDiv).html(data.html);
    });
}

clearSearch = function () {
    document.frmMessageSrch.reset();
    threadListing(document.frmMessageSrch);
};
$(".select-box__value-js").click(function () {
    $(".select-box__target-js").slideToggle();
});

/* FUNCTION FOR SCROLLBAR */
if ($(window).width() > 1199) {
    $('.scrollbar-js').enscroll({
        verticalTrackClass: 'scrollbar-track',
        verticalHandleClass: 'scrollbar-handle'
    });
}

function closethread() {
    $("body .message-details-js").hide();
    $("html").removeClass("show-message-details"); 
}

function getThread(id, page) {
    page  = (page) ? page : messageThreadPage;

    if(page == 1){
        messageThreadAjax = false;
    }
    
    if(messageThreadAjax){
        return false;
    }

    $.chatLoader.show();
    messageThreadPage += 1; 
    dv = ".message-details-js";
    data = "thread_id=" + id + "&page="+page;
    fcom.ajax(fcom.makeUrl('Messages', 'messageSearch'), data, function (ans) {
        $.chatLoader.hide();
        var data = JSON.parse(ans);
        if(page == 1){
            $(dv).html(data.html).show();
            $("html").addClass("show-message-details"); 
            $( ".chat-room__body" ).scrollTop($( ".chat-room__body" )[0].scrollHeight);
        }else{
            $('.load-more-js').remove();
            $('.chat-list').prepend(data.html);
        }
       // $(dv).prepend(data.loadMore);
    });
    $('html').addClass('show-message-details');
    threadListing(document.frmMessageSrch, id);
}
function goToLoadPrevious() {

}

function sendMessage(frm) {
    if (!$(frm).validate()) return;
    var data = fcom.frmData(frm);

    $.loader.show();
    fcom.ajax(fcom.makeUrl('Messages', 'sendMessage'), data, function (t) {
        $.loader.hide();
        var data = JSON.parse(t);
        if(data.status == 0){
            $.mbsmessage(data.msg,true, 'alert alert--danger');
            return;
        }
      
        messageThreadPage = 1;
        getThread(data.threadId);
        threadListing(document.frmMessageSrch);
    });
}