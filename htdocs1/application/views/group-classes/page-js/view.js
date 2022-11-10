$("document").ready(function(){
    var key = $('.card-listing').attr('id');
    if(getCookie(key)=="true"){
        changeTz($('.card-listing').find('.statustab'));
    }
});

