$(document).ready(function(){
	if( $('.system_message').find('.div_error').length > 0 || $('.system_message').find('.div_msg').length > 0 || 	$('.system_message').find('.div_info').length > 0 || $('.system_message').find('.div_msg_dialog').length > 0 ){
		$('.system_message').show();
	}
	$('.closeMsg').click(function(){
		$('.system_message').find('.div_error').remove();
		$('.system_message').find('.div_msg').remove();
		$('.system_message').find('.div_info').remove();
		$('.system_message').find('.div_msg_dialog').remove();
		$('.system_message').hide();
	});
});

(function($){
	$.systemMessage = function(data, cls){
		initialize();
		$.systemMessage.loading();
		$.systemMessage.fillSysMessage(data, cls);
	};

	$.extend($.systemMessage, {
		settings:{
			closeimage:siteConstants.webroot + 'images/facebox/close.gif',
		},
		loading: function(){
			$('.system_message').show();
		},
		fillSysMessage:function(data, cls){
			if(cls) $('.system_message').addClass(cls);
			$('.system_message .content').html(data);
			$('.system_message').fadeIn();
			
			if( CONF_AUTO_CLOSE_SYSTEM_MESSAGES == 1 ){
				var time = CONF_TIME_AUTO_CLOSE_SYSTEM_MESSAGES * 1000;
				setTimeout(function(){
					$.systemMessage.close();
				}, time);
			}
			
			/* $('.system_message').css({top:10}); */
		},
		close:function(){
			$(document).trigger('closeMsg.systemMessage');
		},
	});

	$(document).bind('closeMsg.systemMessage', function() {		
		$('.system_message').fadeOut();
	});

	function initialize(){
		$('.system_message .closeMsg').click($.systemMessage.close);
	}
})(jQuery);