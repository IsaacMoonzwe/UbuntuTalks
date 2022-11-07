<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php

$arrFlds1 = array(
    'listserial' => Label::getLabel('LBL_Sr_no.', $adminLangId),
    'order_date' => Label::getLabel('LBL_Date', $adminLangId),
    'totOrders' => Label::getLabel('LBL_No._of_Orders', $adminLangId),
    'orderNetAmount' => Label::getLabel('LBL_Order_Net_Amount', $adminLangId),
    'op_total_refund_amount' => Label::getLabel('LBL_Order_Refund_Amount', $adminLangId),
);
$arrFlds2 = array(
    'listserial' => Label::getLabel('LBL_Sr_no.', $adminLangId),
    'order_net_amount' => Label::getLabel('LBL_Order_Net_Amount', $adminLangId),
);
$arr = array(
    'Earnings' => Label::getLabel('LBL_Sales_Earnings', $adminLangId)
);
if (empty($orderDate)) {
    $arr_flds = array_merge($arrFlds1, $arr);
} else {
    $arr_flds = array_merge($arrFlds2, $arr);
}

$tbl = new HtmlElement('table',
        array('width' => '100%', 'class' => 'table table-responsive table--hovered'));
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) {
    $e = $th->appendElement('th', array(), $val);
}
$sr_no = $page == 1 ? 0 : $pageSize * ($page - 1);
foreach ($arr_listing as $sn => $row) {
    $sr_no++;
    $tr = $tbl->appendElement('tr');

    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'listserial':
                $td->appendElement('plaintext', array(), $sr_no);
                break;
            case 'order_date':
                $td->appendElement('plaintext', array(), FatDate::format($row[$key]), true);
                break;
            case 'order_net_amount':
                $amt = CommonHelper::orderProductAmount($row);
                $td->appendElement('plaintext', array(), CommonHelper::displayMoneyFormat($amt, true, true));
                break;
            case 'totalSalesEarnings':
            case 'totalRefundedAmount':
            case 'orderNetAmount':
            case 'taxTotal':
            case 'shippingTotal':
            case 'op_total_refund_amount':
                $td->appendElement('plaintext', array(), CommonHelper::displayMoneyFormat($row[$key], true, true));
                break;
            default:
                $td->appendElement('plaintext', array(), $row[$key], true);
                break;
        }
    }
}
if (count($arr_listing) == 0) {
    $tbl->appendElement('tr')->appendElement('td', array(
        'colspan' => count($arr_flds)),
            Label::getLabel('LBL_No_Records_Found', $adminLangId)
    );
}
echo $tbl->getHtml();
$postedData['page'] = $page;
echo FatUtility::createHiddenFormFromData($postedData, array('name' => 'frmSalesReportSearchPaging'));
$pagingArr = array('pageCount' => $pageCount, 'page' => $page, 'recordCount' => $recordCount, 'adminLangId' => $adminLangId);
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
?>