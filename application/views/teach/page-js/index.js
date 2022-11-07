$("document").ready(function(){
	/* for faq toggles[ */
	$(".accordian__body-js").hide();
	$(".accordian__body-js:first").show();

	$(".accordian__title-js").click(function(){
		if($(this).parents('.accordian-js').hasClass('is-active')){
			$(this).siblings('.accordian__body-js').slideUp();
			$('.accordian-js').removeClass('is-active');            
		}else{
			$('.accordian-js').removeClass('is-active');            
			$(this).parents('.accordian-js').addClass('is-active');
			$('.accordian__body-js').slideUp();
			$(this).siblings('.accordian__body-js').slideDown();
		  }
	});
	/* ] */
});
