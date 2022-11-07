<?php

defined('SYSTEM_INIT') or die('Invalid Usage.');
$user_timezone = MyDate::getUserTimeZone();
$arr_flds = array(
    'utxn_id' => Label::getLabel('LBL_Txn_ID', $siteLangId),
    'utxn_date' => Label::getLabel('LBL_Date', $siteLangId),
    'utxn_credit' => Label::getLabel('LBL_Credit', $siteLangId),
    'utxn_debit' => Label::getLabel('LBL_Debit', $siteLangId),
    'balance' => Label::getLabel('LBL_Balance', $siteLangId),
    'utxn_comments' => Label::getLabel('LBL_Comments', $siteLangId),
    'utxn_status' => Label::getLabel('LBL_Status', $siteLangId),
);
$tbl = new HtmlElement('table', array('class' => 'table table--styled table--responsive table--aligned-middle'));
$th = $tbl->appendElement('tr', array('class' => 'title-row'));
foreach ($arr_flds as $val) {
    $th->appendElement('th', array(), $val);
}
foreach ($arrListing as $sn => $row) {
    $tr = $tbl->appendElement('tr', array('class' => ''));
    foreach ($arr_flds as $key => $val) {
        $div = $tr->appendElement('td')->appendElement('div', array('class' => 'flex-cell'));
        $div->appendElement('div', array('class' => 'flex-cell__label'), $val, true);
        switch ($key) {
            case 'utxn_id':
                $div->appendElement('div', array('class' => 'flex-cell__content'), Transaction::formatTransactionNumber($row[$key]), true);
                break;
            case 'utxn_date':
                $utxn_date = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d', $row[$key], true, $user_timezone);
                $div->appendElement('div', array('class' => 'flex-cell__content'), $utxn_date, true);
                break;
            case 'utxn_status':
                if ($row[$key] == Transaction::STATUS_COMPLETED) {
                    $cls = 'green';
                } else {
                    $cls = 'yellow';
                }
                $statusSpn = '<span class="badge color-' . $cls . ' badge--curve">' . $statusArr[$row[$key]] . '</span>';
                $div->appendElement('div', array('class' => 'flex-cell__content'), $statusSpn, true);
                break;
            case 'utxn_credit':
                $txt = CommonHelper::displayMoneyFormat($row[$key]);
                $div->appendElement('div', array('class' => 'flex-cell__content'), $txt, true);
                break;
            case 'utxn_debit':
                $txt = CommonHelper::displayMoneyFormat($row[$key]);
                $div->appendElement('div', array('class' => 'flex-cell__content'), $txt, true);
                break;
            case 'balance':
                $txt = CommonHelper::displayMoneyFormat($row[$key]);
                $div->appendElement('div', array('class' => 'flex-cell__content'), $txt, true);
                break;
            case 'utxn_comments':
                $div->appendElement('div', array('class' => 'flex-cell__content direction-column'), Transaction::formatTransactionComments($row[$key]), true);
                break;
            default:
                $div->appendElement('div', array('class' => 'flex-cell__content'), $row[$key], true);
                break;
        }
    }
}
if (count($arrListing) == 0) {
    $this->includeTemplate('_partial/no-record-found.php', array('siteLangId' => $siteLangId), false);
} else {
    echo $tbl->getHtml();
}
$postedData['page'] = $page;
echo FatUtility::createHiddenFormFromData($postedData, array('name' => 'frmCreditSrchPaging'));
$pagingArr = array('pageCount' => $pageCount, 'page' => $page, 'recordCount' => $recordCount, 'callBackJsFunc' => 'goToCreditSearchPage');
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
