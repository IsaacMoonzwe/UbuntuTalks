$(document).ready(function () {
    searchPages(document.frmPagesSearch);
});

(function () {
    var currentPage = 1;
    var runningAjaxReq = false;

    goToSearchPage = function (page) {
        if (typeof page == undefined || page == null) {
            page = 1;
        }
        var frm = document.frmPagesSearchPaging;
        $(frm.page).val(page);
        searchPages(frm);
    }

    reloadList = function () {
        var frm = document.frmPagesSearchPaging;
        searchPages(frm);
    }

    searchPages = function (form) {
        var dv = '#pageListing';
        var data = '';
        if (form) {
            data = fcom.frmData(form);
        }
        $(dv).html('Loading....');
        fcom.ajax(fcom.makeUrl('BibleContent', 'search'), data, function (res) {
            $(dv).html(res);
        });
    };

    addForm = function (id) {
        
        fcom.resetEditorInstance();
        $.facebox(function () {
            fcom.ajax(fcom.makeUrl('BibleContent', 'form', [id]), '', function (t) {
                $.facebox(t, 'faceboxWidth');
                var frm = $('#facebox form')[0];
                var validator = $(frm).validation({errordisplay: 3});
                $(frm).submit(function (e) {
                    e.preventDefault();
                    setup(frm, validator);
                });
            });
        });
    };

    setup = function (frm, validator) {
        
        validator.validate();
        if (!validator.isValid())
            return;
		fcom.displayProcessing();
        var data = new FormData(  );
        var frmData = $(frm).serializeArray();
        $.each(frmData, function (index, value) {
            data.append(value.name, value.value);
        });

        $.ajax({
            url: fcom.makeUrl('BibleContent', 'setup'),
            type: "POST",
            data: data,
            processData: false,
            contentType: false,
            success: function (t) {

                try {
                    var ans = $.parseJSON(t);
                    fcom.displaySuccessMessage(ans.msg);                    
                    //$.systemMessage(ans.msg);
                    if (ans.status == '1') {
                        reloadList();
                        $(document).trigger('close.facebox');
                    }
                } catch (exc) {
                    $.systemMessage(t);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert("Error Occured.");
                return false;
            }
        });

        return false;
    };
    addLangForm = function (pageId, langId) {
        fcom.resetEditorInstance();
        $.facebox(function () {
            fcom.ajax(fcom.makeUrl('BibleContent', 'langForm', [pageId, langId]), '', function (t) {
                $.facebox(t);
                var frm = $('#facebox form')[0];
                var validator = $(frm).validation({errordisplay: 3});

                $(frm).submit(function (e) {
                    e.preventDefault();
                    validator.validate();
                    if (!validator.isValid())
                        return;
                    /* if (validator.validate() == false) {
                     return ;
                     } */
                    var data = fcom.frmData(frm);
                    fcom.updateWithAjax(fcom.makeUrl('BibleContent', 'langSetup'), data, function (t) {
                        $.mbsmessage.close();
                        reloadList();
                        if (t.langId > 0) {
                            addLangForm(t.biblecontent_id, t.langId);
                            return;
                        }
                        $(document).trigger('close.facebox');
                    });
                });
            });
        });
    };

    setupLang = function (frm) {
        if (!$(frm).validate())
            return;
        var data = fcom.frmData(frm);
        fcom.updateWithAjax(fcom.makeUrl('BibleContent', 'langSetup'), data, function (t) {
            $.mbsmessage.close();
            reloadList();
            if (t.langId > 0) {
                addLangForm(t.biblecontent_id, t.langId);
                return;
            }
            $(document).trigger('close.facebox');
        });
    };

    deleteRecord = function (id) {
        if (!confirm("Do you really want to delete this record?")) {
            return;
        }
        data = 'id=' + id;
        fcom.ajax(fcom.makeUrl('BibleContent', 'deleteRecord'), data, function (res) {
            reloadList();
        });
    };

    clearSearch = function () {
        document.frmPagesSearch.reset();
        searchPages(document.frmPagesSearch);
    };
    
    toggleStatus = function(obj){
		
		if(!confirm("Do you really want to update status?")){return;}
		var biblecontentId = parseInt(obj.id);
		if(biblecontentId < 1){
			$.mbsmessage('Invalid Request!');
			return false;
		}
        
        var statusStr = '';
        if($(obj).hasClass('active')){
            statusStr = 'biblecontent_active=0';
        }
        else{
            statusStr = 'biblecontent_active=1';
        }
		data='biblecontent_id='+biblecontentId+'&'+statusStr;
		fcom.ajax(fcom.makeUrl('BibleContent', 'changeStatus'),data,function(res){
            var ans =$.parseJSON(res);
			if(ans.status == 1){
				$(obj).toggleClass("active");
				setTimeout(function(){ reloadList(); }, 1000);
			}else{
				$.mbsmessage(ans.msg, true);
			}
		});
	};
	

})();
function showMarketingMediaType(val) {
    var selectedMediaType = parseInt(val);
    if (isNaN(selectedMediaType)) {
        selectedMediaType = 0;
    }

    $('.media-types').parents('.col-3').hide();
    //  $('#displayImage').hide();
    switch (selectedMediaType) {
        case 1:
            $('#ImageId').parents('.col-3').show();
            //   $('#ImageId').replaceWith($('#ImageId').val('').clone(true));
            break;
        case 2:
            $('#videoId').parents('.col-3').show();
            //  $('#ImageId').replaceWith($('#ImageId').val('').clone(true));
            break;


    }
    return true;
}


