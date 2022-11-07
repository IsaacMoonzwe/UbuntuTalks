var containerId = '#tabs-container';
var tabsId = '#payment_methods_tab';

function loadTab( tabObj ){
	if(!tabObj || !tabObj.length){ return; }
	$(containerId).html( fcom.getLoader() );
	fcom.ajax(tabObj.attr('href'),'',function(response){
		$(containerId).html(response);
	});
}

confirmOrder = function( frm ){
  var data = fcom.frmData(frm);
  //$("#checkout-left-side").addClass('form--processing');
  fcom.updateWithAjax(fcom.makeUrl('Checkout', 'confirmOrder'), data, function(ans) {
    if( ans.redirectUrl != '' ){
      $( location ).attr( "href", ans.redirectUrl );
    }
  });
  return false;
}

$(document).ready(function(){
     if( $(tabsId + ' li.is-active').length > 0 ){
         loadTab( $(tabsId + ' li.is-active a') );
     }
     $(tabsId + ' a').click(function(){
        if($(this).parent().hasClass('is-active')){ return false; }
        $('li').removeClass('is-active');
        $(this).parent().addClass('is-active');
        loadTab($(this));
        return false;
	 });
});
