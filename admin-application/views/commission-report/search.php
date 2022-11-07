<?php

$columnArr = [
    'Sr' => Label::getLabel('LBL_Sr_no.', $adminLangId),
    'fullName' => label::getLabel('LBL_Expert_Detail', $adminLangId),
    'saleOrderValue' => label::getLabel('LBL_Sales_Order_Value', $adminLangId),
    'totalCommision' => label::getLabel('LBL_Commission_Earned_By_Admin', $adminLangId),
];
$tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table-responsive table--hovered']);
$tr = $tbl->appendElement('thead')->appendElement('tr');
foreach ($columnArr as $arr) {
    $tr->appendElement('th', [], $arr);
}
$serial_no = $page == 1 ? 0 : $pageSize * ($page - 1);
foreach ($data as $d) {
    $serial_no++;
    $tr = $tbl->appendElement('tr');
    foreach ($columnArr as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'Sr':
                $td->appendElement('plaintext', [], $serial_no);
                break;
            case 'fullName':
                $detail = Label::getLabel('LBL_N') . " : " . $d['fullName'] . "</br> " . Label::getLabel('LBL_E') . " : (" . $d['credential_email'] . ")";
                $td->appendElement('plaintext', [], $detail, true);
                break;
            case 'saleOrderValue':
            case 'totalCommision':
                $td->appendElement('plaintext', [], CommonHelper::displayMoneyFormat($d[$key], true, true));
                break;
            default:
                $td->appendElement('plaintext', [], $d[$key]);
                break;
        }
    }
}
if (empty($data)) {
    $tbl->appendElement('tr')->appendElement(
            'td',
            array(
                'colspan' => count($columnArr)
            ),
            Label::getLabel('LBL_No_Records_Found', $adminLangId)
    );
}
echo $tbl->getHtml();
$postedData['page'] = $page;
echo FatUtility::createHiddenFormFromData($postedData, array(
    'name' => 'commissionReportSearchPaging'
));
$pagingArr = array('pageCount' => $pageCount, 'page' => $page, 'recordCount' => $recordCount, 'adminLangId' => $adminLangId);
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
