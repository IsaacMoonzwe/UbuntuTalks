<?php

defined('SYSTEM_INIT') or die('Invalid Usage.');
$arr_flds = [
    'listserial' => Label::getLabel('LBL_S.No.', $adminLangId),
    'repiss_sldetail_id' => Label::getLabel('LBL_Lesson_Id', $adminLangId),
    'sldetail_order_id' => Label::getLabel('LBL_Order_Id', $adminLangId),
    'repiss_title' => Label::getLabel('LBL_Issue', $adminLangId),
    'repiss_reported_by' => Label::getLabel('LBL_Reported_By', $adminLangId),
    'repiss_reported_on' => Label::getLabel('LBL_Reported_On', $adminLangId),
    'repiss_status' => Label::getLabel('LBL_Status', $adminLangId),
    'action' => Label::getLabel('LBL_Action', $adminLangId),
];
$tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table-responsive']);
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) {
    $e = $th->appendElement('th', [], $val);
}
$srNo = $post['page'] == 1 ? 0 : $post['pageSize'] * ($post['page'] - 1);
foreach ($records as $sn => $row) {
    $srNo++;
    $tr = $tbl->appendElement('tr', []);
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'listserial':
                $td->appendElement('plaintext', [], $srNo);
                break;
            case 'repiss_reported_by':
                $td->appendElement('plaintext', [], $row['reporter_username']);
                break;
            case 'repiss_status':
                $td->appendElement('plaintext', [], ReportedIssue::getStatusArr($row[$key]), true);
                break;
            case 'action':
                $ul = $td->appendElement("ul", ["class" => "actions actions--centered"]);
                $li = $ul->appendElement("li", ['class' => 'droplink']);
                $li->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit', $adminLangId)], '<i class="ion-android-more-horizontal icon"></i>', true);
                $innerDiv = $li->appendElement('div', ['class' => 'dropwrap']);
                $innerUl = $innerDiv->appendElement('ul', ['class' => 'linksvertical']);
                $viewLi = $innerUl->appendElement('li');
                $viewLi->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_View', $adminLangId), "onclick" => "view(" . $row['repiss_id'] . ")"], Label::getLabel('LBL_View', $adminLangId), true);
                if ($row['repiss_status'] == ReportedIssue::STATUS_ESCLATED) {
                    $actionLi = $innerUl->appendElement("li");
                    $actionLi->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Action', $adminLangId), "onclick" => "actionForm(" . $row['repiss_id'] . ")"], Label::getLabel('LBL_Action', $adminLangId), true);
                }
                break;
            default:
                $td->appendElement('plaintext', [], $row[$key] ?? 'NA', true);
                break;
        }
    }
}
if (count($records) == 0) {
    $tbl->appendElement('tr')->appendElement('td', ['colspan' => count($arr_flds)], Label::getLabel('LBL_No_Records_Found', $adminLangId));
}
echo $tbl->getHtml();
echo FatUtility::createHiddenFormFromData($post, ['name' => 'frmUserSearchPaging']);
$this->includeTemplate('_partial/pagination.php', [
    'pageCount' => $pageCount,
    'page' => $post['page'],
    'pageSize' => $post['pageSize'],
    'recordCount' => $recordCount,
    'adminLangId' => $adminLangId], false);
