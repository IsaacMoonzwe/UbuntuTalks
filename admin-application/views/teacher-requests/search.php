<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php

$arr_flds = array(
    'listserial' => Label::getLabel('LBL_Sr._No', $adminLangId),
    'utrequest_reference' => Label::getLabel('LBL_Reference_Number', $adminLangId),
    'user_full_name' => Label::getLabel('LBL_Name', $adminLangId),
    'credential_email' => Label::getLabel('LBL_Email', $adminLangId),
    'utrequest_date' => Label::getLabel('LBL_Requested_On', $adminLangId),
    'status' => Label::getLabel('LBL_Status', $adminLangId),
    'action' => Label::getLabel('LBL_Action', $adminLangId),
);
$tbl = new HtmlElement('table', array('width' => '100%', 'class' => 'table table-responsive'));
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
            case 'user_full_name':
                $td->appendElement('plaintext', array(), CommonHelper::htmlEntitiesDecode($row['user_first_name'] . ' ' . $row['user_last_name']));
                break;
            case 'status':
                $td->appendElement('plaintext', array(), $reqStatusArr[$row['utrequest_status']], true);
                break;
            case 'action':
                $ul = $td->appendElement("ul", array("class" => "actions actions--centered"));
                $li = $ul->appendElement("li", array('class' => 'droplink'));
                $li->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit', $adminLangId)), '<i class="ion-android-more-horizontal icon"></i>', true);
                $innerDiv = $li->appendElement('div', array('class' => 'dropwrap'));
                $innerUl = $innerDiv->appendElement('ul', array('class' => 'linksvertical'));
                /* view detail link */
                $innerLi = $innerUl->appendElement('li');
                $innerLi->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_View', $adminLangId), "onclick" => "viewTeacherRequest(" . $row['utrequest_id'] . ")"), Label::getLabel('LBL_View', $adminLangId), true);
                /* ] */
                /* [ */
                $innerLi = $innerUl->appendElement('li');
                $innerLi->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Qualifications', $adminLangId), "onclick" => "searchQualifications(" . $row['utrequest_user_id'] . ")"), Label::getLabel('LBL_Qualifications', $adminLangId), true);
                /* ] */
                /* Status Update Form[ */
                if ($canEditTeacherApprovalRequests && $row['utrequest_status'] == TeacherRequest::STATUS_PENDING) {
                    $innerLi = $innerUl->appendElement('li');
                    $innerLi->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Change_Status', $adminLangId), "onclick" => "teacherRequestUpdateForm(" . $row['utrequest_id'] . ")"), Label::getLabel('LBL_Change_Status', $adminLangId), true);
                }
                /* ] */
                break;
            case 'utrequest_date':
                $td->appendElement('plaintext', array(), FatDate::format($row['utrequest_date'], true));
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
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
?>