<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php

$arr_flds = array(
    'listserial' => Label::getLabel('LBL_Sr._No', $adminLangId),
    'author_name' => Label::getLabel('LBL_Author_Name', $adminLangId),
    'bcontributions_author_email' => Label::getLabel('LBL_Author_Email', $adminLangId),
    'bcontributions_author_phone' => Label::getLabel('LBL_Author_Phone', $adminLangId),
    'bcontributions_status' => Label::getLabel('LBL_Status', $adminLangId),
    'bcontributions_added_on' => Label::getLabel('LBL_Posted_On', $adminLangId),
    'action' => Label::getLabel('LBL_Action', $adminLangId),
);
$tbl = new HtmlElement('table', array('width' => '100%', 'class' => 'table table-responsive table--hovered', 'id' => 'post'));
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
            case 'bcontributions_added_on':
                $td->appendElement('plaintext', array(), FatDate::format($row['bcontributions_added_on'], true));
                break;
            case 'author_name':
                $td->appendElement('plaintext', array(), $row[$key], true);
                break;
            case 'bcontributions_status':
                $statusArr = applicationConstants::getBlogContributionStatusArr($adminLangId);
                $td->appendElement('plaintext', array(), $statusArr[$row[$key]], true);
                break;
            case 'action':
                $ul = $td->appendElement("ul", array("class" => "actions actions--centered"));
                if ($canEdit) {
                    $li = $ul->appendElement("li", array('class' => 'droplink'));
                    $li->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit', $adminLangId)), '<i class="ion-android-more-horizontal icon"></i>', true);
                    $innerDiv = $li->appendElement('div', array('class' => 'dropwrap'));
                    $innerUl = $innerDiv->appendElement('ul', array('class' => 'linksvertical'));
                    $innerLiEdit = $innerUl->appendElement('li');
                    $innerLiEdit->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit', $adminLangId), "onclick" => "view(" . $row['bcontributions_id'] . ")"), Label::getLabel('LBL_Edit', $adminLangId), true);
                    $innerLiDelete = $innerUl->appendElement('li');
                    $innerLiDelete->appendElement('a', array('href' => "javascript:void(0)", 'class' => 'button small green', 'title' => Label::getLabel('LBL_Delete', $adminLangId), "onclick" => "deleteRecord(" . $row['bcontributions_id'] . ")"), Label::getLabel('LBL_Delete', $adminLangId), true);
                }
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
echo FatUtility::createHiddenFormFromData($postedData, array('name' => 'frmSearchPaging'));
$pagingArr = array('pageCount' => $pageCount, 'page' => $page, 'recordCount' => $recordCount, 'adminLangId' => $adminLangId);
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
