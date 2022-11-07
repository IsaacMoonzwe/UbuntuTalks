<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php

$arr_flds = array(
    'listserial' => '',
    'order_id' => Label::getLabel('LBL_Order_ID', $adminLangId),
    'order_date_added' => Label::getLabel('LBL_Order_Date', $adminLangId),
    'order_net_amount' => Label::getLabel('LBL_Total', $adminLangId),
    'order_is_paid' => Label::getLabel('LBL_Payment_Status', $adminLangId),
    'action' => Label::getLabel('LBL_Action', $adminLangId),
);
$tbl = new HtmlElement('table', array('width' => '100%', 'class' => 'table table--hovered table-responsive'));
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) {
    $e = $th->appendElement('th', array(), $val);
}
$sr_no = $page == 1 ? 0 : $pageSize * ($page - 1);
foreach ($ordersList as $sn => $row) {
    $sr_no++;
    $tr = $tbl->appendElement('tr');
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'listserial':
                $td->appendElement('plaintext', array(), $sr_no);
                break;
            case 'buyer_user_name':
                $td->appendElement('plaintext', array(), $row[$key] . '<br/>' . $row['buyer_email'], true);
                break;
            case 'order_net_amount':
                $td->appendElement('plaintext', array(), CommonHelper::displayMoneyFormat($row['order_net_amount'], true, true));
                break;
            case 'order_date_added':
                $td->appendElement('plaintext', array(), MyDate::format($row[$key], true));
                break;
            case 'order_is_paid':
                $cls = 'label-info';
                switch ($row[$key]) {
                    case Order::ORDER_IS_PENDING :
                        $cls = 'label-info';
                        break;
                    case Order::ORDER_IS_PAID :
                        $cls = 'label-success';
                        break;
                    case Order::ORDER_IS_CANCELLED :
                        $cls = 'label-danger';
                        break;
                }
                $td->appendElement('span', array('class' => 'label ' . $cls), Order::getPaymentStatusArr($adminLangId)[$row[$key]]);
                break;
            case 'action':
                $ul = $td->appendElement("ul", array("class" => "actions actions--centered"));
                $li = $ul->appendElement("li", array('class' => 'droplink'));
                $li->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit', $adminLangId)), '<i class="ion-android-more-horizontal icon"></i>', true);
                $innerDiv = $li->appendElement('div', array('class' => 'dropwrap'));
                $innerUl = $innerDiv->appendElement('ul', array('class' => 'linksvertical'));
                $innerLi = $innerUl->appendElement('li');
                $innerLi->appendElement('a', array('href' => FatUtility::generateUrl('Giftcards', 'view', array($row['order_id'])), 'class' => 'button small green redirect--js', 'title' => Label::getLabel('LBL_View_Order_Detail', $adminLangId)), Label::getLabel('LBL_View_Order_Detail', $adminLangId), true);
                break;
            default:
                $td->appendElement('plaintext', array(), $row[$key]);
                break;
        }
    }
}
if (count($ordersList) == 0) {
    $tbl->appendElement('tr')->appendElement('td', array('colspan' => count($arr_flds)), Label::getLabel('LBL_No_Records_Found', $adminLangId));
}
echo $tbl->getHtml();
$postedData['page'] = $page;
echo FatUtility::createHiddenFormFromData($postedData, array('name' => 'frmOrderSearchPaging'));
$pagingArr = array('pageCount' => $pageCount, 'page' => $page, 'pageSize' => $pageSize, 'recordCount' => $recordCount, 'adminLangId' => $adminLangId);
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
?>
