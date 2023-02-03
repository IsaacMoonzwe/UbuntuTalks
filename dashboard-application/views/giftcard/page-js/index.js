$(document).ready(function() {
	var frm = document.frmGiftcardSrch;
    cartListing(frm);
});

var div = '#giftcardListing';
function cartListing(frm) {
    $(div).html(fcom.getLoader());
	var data = fcom.frmData(frm);
    fcom.ajax(fcom.makeUrl('Giftcard', 'listing'), data, function(res) {
        $(div).html(res);
    });
}

function cardUpdate(obj) {
		$(document.giftcardForm).validate();
    var amount = $(obj).val();
    if (!isNaN(amount)) {
       
				if($("#giftcard_price").next('ul.errorlist').length > 0) {
          if(amount == ''){
            $(".giftcardPrice").html('');
          }
					return;
				}
        $(".giftcardPrice").html('');
        $(".giftcardPrice").html(amount);
    }else{
			$(obj).val('');
		}

}
function cleardata() {
	document.giftcardForm.reset();
	$(".giftcardPrice").html('');

}



function validateCustomFields() {
    var error = 0;
			if (!$(document.giftcardForm).validate()) return;
    // $(".recipient_name").each(function() {
		//
    //     if ($.trim($(this).val()) == '') {
		//
    //         $(this).val($.trim($(this).val()));
    //         $(this).css("border", "1px solid red");
		//
    //         if (parseInt($(this).next(".errorlist").length) == 0) {
    //             $("<ul class='errorlist'><li><a href='javascript:void(0)'>Recipient Name is mandatory.</a></li></ul>").insertAfter($(this));
    //         }
    //         error++;
    //     } else {
    //         if (error > 0) {
    //             error--;
    //         }
		//
    //         $(this).css("border", "1px solid var(--border-color)");
    //         $(this).next(".errorlist").remove();
		//
    //     }
		//
    // });
		//
    // $(".recipient_email").each(function() {
		//
    //     if ($.trim($(this).val()) == '') {
		//
    //         $(this).val($.trim($(this).val()));
    //         $(this).css("border", "1px solid red");
		//
    //         if (parseInt($(this).next(".errorlist").length) == 0) {
    //             $("<ul class='errorlist'><li><a href='javascript:void(0)'>Recipient Email is mandatory.</a></li></ul>").insertAfter($(this));
    //         }
		//
    //         error++;
    //     } else if (!testPattern($.trim($(this).val()), "^((?:(?:(?:[a-zA-Z0-9][\\.\\-\\+_]?)*)[a-zA-Z0-9])+)\\@((?:(?:(?:[a-zA-Z0-9][\\.\\-_]?){0,62})[a-zA-Z0-9])+)\\.([a-zA-Z0-9]{2,6})$")) {
		//
    //         $(this).css("border", "1px solid red");
    //         if (parseInt($(this).next(".errorlist").length) == 0) {
    //             $("<ul class='errorlist'><li><a href='javascript:void(0)'>Please enter valid email ID for Recipient Email.</a></li></ul>").insertAfter($(this));
    //         } else {
    //             $(this).next(".errorlist").remove();
    //             $("<ul class='errorlist'><li><a href='javascript:void(0)'>Please enter valid email ID for Recipient Email.</a></li></ul>").insertAfter($(this));
    //         }
		//
    //         error++;
    //     } else {
    //         if (error > 0) {
    //             error--;
    //         }
		//
    //         $(this).css("border", "1px solid var(--border-color)");
    //         $(this).next(".errorlist").remove();
		//
    //     }
		//
    // });
		//
    // if (error > 0) {
    //     $('#frm_fat_id_giftcardForm').attr('onsubmit', 'return false;');
    // } else {
    //     $('#frm_fat_id_giftcardForm').attr('onsubmit', 'return true;');
    // }

}

removeCartItem = function(price) {
    var data = 'key=' + price;
    fcom.updateWithAjax(fcom.makeUrl('Giftcard', 'remove'), data, function(res) {
        cartListing();
    });
}

goToSearchPage = function(page) {
	if(typeof page == undefined || page == null){
		page = 1;
	}
	var frm = document.frmSearchPaging;
	$(frm.page).val(page);
	cartListing(frm);
};

clearSearch = function(){
	document.frmGiftcardSrch.reset();
	cartListing(document.frmGiftcardSrch);
};

var testPattern = function(value, pattern) {
    var regExp = new RegExp(pattern, "");
    return regExp.test(value);
}
