(function () {
    var currentPage = 1;
    var dv = '#listItems';

    searchGdprRequests = function (frm, page) {
        page = page ? page : currentPage;
        let data = frm ? fcom.frmData(frm) : '';
        fcom.ajax(fcom.makeUrl('GdprRequests', 'search'), data, function (t) {
            $(dv).html(t);
        });
    };

    reloadList = function () {
        searchGdprRequests(document.frmSrch);
    }

    view = function (requestId) {
        fcom.ajax(fcom.makeUrl('GdprRequests', 'view'), { id: requestId }, function (t) {
            $.facebox(t, 'faceboxWidth')
        });
    };

    updateStatus = function (frm) {
        if (!$(frm).validate()) return;
        var data = fcom.frmData(frm);
        fcom.updateWithAjax(fcom.makeUrl('GdprRequests', 'updateStatus'), data, function (t) {
            $.facebox.close();
            searchGdprRequests(document.frmSrch);
        });
    };

    clearSearch = function () {
        document.frmSrch.reset();
        searchGdprRequests(document.frmSrch);
    };

    goToSearchPage = function(page) {	
		if(typeof page == undefined || page == null){
			page = currentPage;
		}
		var frm = document.frmSrch;		
		$(frm.page).val(page);
		searchGdprRequests(frm);
	};

    searchGdprRequests(document.frmSrch);
})();