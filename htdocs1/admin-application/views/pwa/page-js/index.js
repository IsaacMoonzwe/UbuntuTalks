function validateFrm() {
    return $(this).validate();
}

$(document).ready(function () {
    $(document.pwaFrm[0]).focus();
    $(document.pwaFrm).submit(validateFrm);
});
