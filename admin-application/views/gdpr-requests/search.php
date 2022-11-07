<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php

$arr_flds = array(
    'listserial' => Label::getLabel('LBL_Sr._No', $adminLangId),
    'gdprdatareq_reason' => Label::getLabel('LBL_Reason_For_Erasure', $adminLangId),
    'gdprdatareq_user_name' => Label::getLabel('LBL_User_Name', $adminLangId),
    'gdprdatareq_added_on' => Label::getLabel('LBL_Added_On', $adminLangId),
    'gdprdatareq_updated_on' => Label::getLabel('LBL_Updated_On', $adminLangId),
    'gdprdatareq_status' => Label::getLabel('LBL_Status', $adminLangId),
    'action' => Label::getLabel('LBL_Action', $adminLangId),
);
$tbl = new HtmlElement('table', array('width' => '100%', 'class' => 'table table--hovered table-responsive'));
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) {
    $th->appendElement('th', [], $val);
}
$sr_no = $page == 1 ? 0 : $pageSize * ($page - 1);
foreach ($gdprRequests as $sn => $row) {
    $userFullName = User::getAttributesById($row['gdprdatareq_user_id'], 'concat(user_first_name, " " ,user_last_name)');
    $sr_no++;
    $tr = $tbl->appendElement('tr');
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'listserial':
                $td->appendElement('plaintext', [], $sr_no);
                break;
            case 'gdprdatareq_reason':
                $td->appendElement('plaintext', [], CommonHelper::truncateCharacters($row['gdprdatareq_reason'], 50));
                break;
            case 'gdprdatareq_user_name':
                $td->appendElement('plaintext', [], $userFullName);
                break;
            case 'gdprdatareq_added_on':
            case 'gdprdatareq_updated_on':
                $td->appendElement('plaintext', [], FatDate::format($row[$key], true));
                break;
            case 'gdprdatareq_status':
                $td->appendElement('plaintext', [], $gdprStatus[$row[$key]] ?? '', true);
                break;
            case 'action':
                $ul = $td->appendElement("ul", array("class" => "actions"));
                if ($canEdit) {
                    $li = $ul->appendElement("li");
                    $li->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit', $adminLangId), "onclick" => "view(" . $row['gdprdatareq_id'] . ")"), '<i class="ion-eye icon"></i>', true);
                }
                break;
            default:
                $td->appendElement('plaintext', [], $row[$key]);
                break;
        }
    }
}
if (count($gdprRequests) == 0) {
    $tbl->appendElement('tr')->appendElement('td', array('colspan' => count($arr_flds)), Label::getLabel('LBL_No_Records_Found', $adminLangId));
}
echo $tbl->getHtml();
$postedData['page'] = $page;
echo FatUtility::createHiddenFormFromData($postedData, array('name' => 'frmSearchPaging'));
$pagingArr = array('pageCount' => $pageCount, 'page' => $page, 'pageSize' => $pageSize, 'recordCount' => $recordCount, 'adminLangId' => $adminLangId);
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
?>
