(function () {
    gdprApprovalReuest = function (frm) {
        if (!$(frm).validate()) return;
        var data = fcom.frmData(frm);
        fcom.updateWithAjax(fcom.makeUrl('Gdpr', 'gdprApprovalReuest'), data, function (t) {
            location.reload();
        });
    }

})();