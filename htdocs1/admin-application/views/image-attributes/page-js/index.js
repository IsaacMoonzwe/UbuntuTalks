$(document).ready(function () {
    searchImageAttributes(document.frmSearch);
});
(function () {
    var currentPage = 1;
    var dv = '#listing';
    listImageAttributes = function (imageAttributeType) {
        $("input[name='imageAttributeType']").val(imageAttributeType);
        searchImageAttributes(document.frmSearch);
    };
    searchImageAttributes = function (form) {
        var data = '';
        data = fcom.frmData(form);
        $(dv).html(fcom.getLoader());
        fcom.ajax(fcom.makeUrl('ImageAttributes', 'search'), data, function (res) {
            $(dv).html(res);
        });
    };

    editImageAttributeForm = function (afileId, recordId, type) {
        fcom.displayProcessing();
        fcom.ajax(fcom.makeUrl('ImageAttributes', 'form'), { afileId: afileId, recordId: recordId, type: type }, function (t) {
            fcom.updateFaceboxContent(t);
        });
    };

    goToSearchPage = function (page) {
        if (typeof page == undefined || page == null) {
            page = 1;
        }
        var frm = document.frmImgAttrPaging;
        $(frm.page).val(page);
        searchImageAttributes(frm);
    };

    setupImageAttr = function (frm) {
        if (!$(frm).validate()) return;
        var data = fcom.frmData(frm);
        fcom.updateWithAjax(fcom.makeUrl('ImageAttributes', 'setup'), data, function (t) {
            $.facebox.close();
        });
    };

    attributeForm = function (record_id) {
        fcom.ajax(fcom.makeUrl('ImageAttributes', 'attributeForm', [record_id, moduleType]), '', function (t) {
            $("#dvForm").html(t).show();
            $("#dvAlert").hide();
        });
    };

    clearSearch = function () {
        document.frmSearch.reset();
        searchImageAttributes(document.frmSearch);
    };

})();
