<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$arr_flds = array(
    'listserial' => Label::getLabel('LBL_Sr._No', $adminLangId),
    'coupon_identifier' => Label::getLabel('LBL_Coupon_Title', $adminLangId),
    'coupon_code' => Label::getLabel('LBL_Coupon_Code', $adminLangId),
    'coupon_discount_value' => Label::getLabel('LBL_Coupon_Discount', $adminLangId),
    'coupon_start_date' => Label::getLabel('LBL_Available', $adminLangId),
    'coupon_active' => Label::getLabel('LBL_Status', $adminLangId),
    'action' => Label::getLabel('LBL_Action', $adminLangId),
);
$tbl = new HtmlElement('table', array('width' => '100%', 'class' => 'table table-responsive table--hovered'));
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) {
    $e = $th->appendElement('th', array(), $val);
}
$sr_no = $page == 1 ? 0 : $pageSize * ($page - 1);
foreach ($arr_listing as $sn => $row) {
    $sr_no++;
    $tr = $tbl->appendElement('tr');
    if ($row['coupon_active'] != applicationConstants::ACTIVE) {
        $tr->setAttribute("class", "fat-inactive");
    }
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'listserial':
                $td->appendElement('plaintext', array(), $sr_no);
                break;
            case 'coupon_identifier':
                if ($row['coupon_title'] != '') {
                    $td->appendElement('plaintext', array(), $row['coupon_title'], true);
                    $td->appendElement('br', array());
                    $td->appendElement('plaintext', array(), '(' . $row[$key] . ')', true);
                } else {
                    $td->appendElement('plaintext', array(), $row[$key], true);
                }
                break;
            case 'coupon_discount_value':
                $discountValue = ($row['coupon_discount_in_percent'] == ApplicationConstants::PERCENTAGE) ? $row[$key] . ' %' : CommonHelper::displayMoneyFormat($row[$key]);
                $td->appendElement('plaintext', array(), $discountValue);
                break;
            case 'coupon_start_date':
                $dispDate = FatDate::format($row[$key]) . '<br>' . FatDate::format($row['coupon_end_date']);
                $td->appendElement('plaintext', array(), $dispDate, true);
                break;
            case 'coupon_active':
                $isExpired = false;
                $isExpired = ($row['coupon_end_date'] != "0000-00-00" && strtotime($row['coupon_end_date']) < strtotime(date('Y-m-d'))) ? true : false;
                if ($isExpired) {
                    $td->appendElement('plaintext', array(), Label::getLabel("LBL_Expired", $adminLangId), true);
                } else {
                    $active = "";
                    if ($row['coupon_active']) {
                        $active = 'checked';
                    }
                    $statusAct = ( $canEdit === true ) ? 'toggleStatus(event,this,' . applicationConstants::YES . ')' : 'toggleStatus(event,this,' . applicationConstants::NO . ')';
                    $statusClass = ( $canEdit === false ) ? 'disabled' : '';
                    $str = '<label class="statustab -txt-uppercase">                 
                     <input ' . $active . ' type="checkbox" id="switch' . $row['coupon_id'] . '" value="' . $row['coupon_id'] . '" onclick="' . $statusAct . '" class="switch-labels"/>
                    <span class="switch-handles ' . $statusClass . '"></span></label>';
                    $td->appendElement('plaintext', array(), $str, true);
                }
                break;
            case 'action':
                $ul = $td->appendElement("ul", array("class" => "actions actions--centered"));
                if ($canEdit) {
                    $li = $ul->appendElement("li", array('class' => 'droplink'));
                    $li->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit', $adminLangId)), '<i class="ion-android-more-horizontal icon"></i>', true);
                    $innerDiv = $li->appendElement('div', array('class' => 'dropwrap'));
                    $innerUl = $innerDiv->appendElement('ul', array('class' => 'linksvertical'));
                    $innerLiEdit = $innerUl->appendElement('li');
                    $innerLiEdit->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit', $adminLangId), "onclick" => "addCouponFormNew(" . $row['coupon_id'] . ")"), Label::getLabel('LBL_Edit', $adminLangId), true);
                } else {
                    $li = $ul->appendElement("li", array('class' => 'droplink'));
                    $innerDiv = $li->appendElement('div', array('class' => 'dropwrap'));
                    $innerUl = $innerDiv->appendElement('ul', array('class' => 'linksvertical'));
                }
                $innerLiHistory = $innerUl->appendElement('li');
                $innerLiHistory->appendElement('a', array('href' => "javascript:void(0)", 'class' => 'button small green', 'title' => Label::getLabel('LBL_History', $adminLangId), "onclick" => "couponHistory(" . $row['coupon_id'] . ")"), Label::getLabel('LBL_History', $adminLangId), true);
                break;
            default:
                $td->appendElement('plaintext', array(), $row[$key], true);
                break;
        }
    }
}
if (count($arr_listing) == 0) {
    $tbl->appendElement('tr')->appendElement('td', array('colspan' => count($arr_flds)), Label::getLabel('LBL_No_Records_Found', $adminLangId));
}
echo $tbl->getHtml();
$postedData['page'] = $page;
echo FatUtility::createHiddenFormFromData($postedData, array(
    'name' => 'frmCouponSearchPaging'
));
$pagingArr = array('pageCount' => $pageCount, 'page' => $page, 'recordCount' => $recordCount, 'adminLangId' => $adminLangId);
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
?>
<script >
    var DISCOUNT_IN_PERCENTAGE = '<?php echo applicationConstants::PERCENTAGE; ?>';
    var DISCOUNT_IN_FLAT = '<?php echo applicationConstants::FLAT; ?>';
    function callCouponDiscountIn(val) {
        if (val == DISCOUNT_IN_PERCENTAGE) {
            $("#coupon_max_discount_value_div").show();
        }
        if (val == DISCOUNT_IN_FLAT) {
            $("#coupon_max_discount_value_div").hide();
        }
    }
</script>
