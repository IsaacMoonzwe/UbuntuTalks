$("document").ready(function(){
    var key = $('.card-listing').attr('id');
    if(getCookie(key)=="true"){
        changeTz($('.card-listing').find('.statustab'));
    }
});

function viewCalendar(teacherId, languageId, action = '') {
    var dv = $('#availbility');
    if (action == 'free_trial') {
        if (isUserLogged() == 0) {
            $.loader.hide();
            logInFormPopUp();
            return false;
        }
    }
    fcom.ajax(fcom.makeUrl('Teachers', 'viewCalendar', [teacherId, languageId]), 'action=' + action, function (t) {
        if (action == 'free_trial') {
            $.facebox(t, 'facebox-large');
            $('body').addClass('calendar-facebox');
        } else {
            $(dv).html(t);
        }
    });
}


