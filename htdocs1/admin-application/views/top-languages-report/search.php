<?php

defined('SYSTEM_INIT') or die('Invalid Usage.');
$arr_flds = array(
    'listserial' => Label::getLabel('LBL_Sr_no.', $adminLangId),
    'languageName' => Label::getLabel('LBL_Language_Name', $adminLangId),
    'lessonsSold' => Label::getLabel('LBL_No._of_Sold_Lessons', $adminLangId),
    'completedLessons' => Label::getLabel('LBL_Completed_Lessons', $adminLangId),
    'cancelledLessons' => Label::getLabel('LBL_Cancelled_Lessons', $adminLangId),
    'action' => Label::getLabel('LBL_Action', $adminLangId)
);
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
            case 'languageName':
                $td->appendElement('plaintext', array(), $row[$key], true);
                break;
            case 'lessonsSold':
                $td->appendElement('plaintext', array(), $row[$key], true);
                break;
            case 'action':
                if (isset($country_id)) {
                    $queryArray = array($row['slesson_slanguage_id'], $country_id);
                } else {
                    $queryArray = array($row['slesson_slanguage_id']);
                }
                $td->appendElement("a", array('href' => CommonHelper::generateUrl('TopLanguagesReport', 'viewSchedules', $queryArray), 'class' => 'button small green', 'title' => Label::getLabel('LBL_View_Schedules', $adminLangId)), Label::getLabel('LBL_View_Schedules', $adminLangId), true);
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
echo FatUtility::createHiddenFormFromData($postedData, array('name' => 'frmSalesReportSchedulePaging'));
$pagingArr = array('pageCount' => $pageCount, 'page' => $page, 'recordCount' => $recordCount, 'adminLangId' => $adminLangId);
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
