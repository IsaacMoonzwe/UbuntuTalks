<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php

$arr_flds = array(
    'listserial' => '',
    'grpcls_title' => Label::getLabel('LBL_Class_Title', $adminLangId),
    'teacher_name' => Label::getLabel('LBL_Teacher', $adminLangId),
    'grpcls_max_learner' => Label::getLabel('LBL_Max_Learners', $adminLangId),
    'grpcls_entry_fee' => Label::getLabel('LBL_Entry_Fee', $adminLangId),
    'grpcls_start_datetime' => Label::getLabel('LBL_Start_at', $adminLangId),
    'grpcls_end_datetime' => Label::getLabel('LBL_End_At', $adminLangId),
    'grpcls_status' => Label::getLabel('LBL_Status', $adminLangId),
    'grpcls_added_on' => Label::getLabel('LBL_Added_On', $adminLangId),
    'action' => Label::getLabel('LBL_Action', $adminLangId),
);
$tbl = new HtmlElement('table', array('width' => '100%', 'class' => 'table table--hovered table-responsive'));
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) {
    $e = $th->appendElement('th', array(), $val);
}
$sr_no = $page == 1 ? 0 : $pageSize * ($page - 1);
foreach ($classes as $sn => $row) {
    $sr_no++;
    $tr = $tbl->appendElement('tr');
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'listserial':
                $td->appendElement('span', ['class'=>'sr-no'], $sr_no);
                break;
            case 'teacher_name':
                $td->appendElement('plaintext', array(), $row['user_first_name'] . ' ' . $row['user_last_name']);
                break;
            case 'grpcls_entry_fee':
                $td->appendElement('plaintext', array(), CommonHelper::displayMoneyFormat($row[$key], true, true));
                break;
            case 'grpcls_added_on':
            case 'grpcls_start_datetime':
            case 'grpcls_end_datetime':
                $td->appendElement('plaintext', array(), MyDate::format($row[$key], true, true, Admin::getAdminTimeZone()));
                break;
            case 'grpcls_status':
                $td->appendElement('plaintext', array(), $classStatusArr[$row[$key]], true);
                break;
            case 'action':
                $ul = $td->appendElement("ul", array("class" => "actions actions--centered"));
                $li = $ul->appendElement("li", array('class' => 'droplink'));
                $li->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green'), '<i class="ion-android-more-horizontal icon"></i>', true);
                $innerDiv = $li->appendElement('div', array('class' => 'dropwrap'));
                $innerUl = $innerDiv->appendElement('ul', array('class' => 'linksvertical'));
                $innerLi = $innerUl->appendElement('li');
                if ($row['grpcls_status'] != TeacherKidsClasses::STATUS_COMPLETED && $row['grpcls_status'] != TeacherKidsClasses::STATUS_CANCELLED) {
                    if (strtotime($row['grpcls_start_datetime']) > time()) {
                        $innerLi->appendElement('a', array('href' => 'javascript:;', 'onclick' => 'form(' . $row['grpcls_id'] . ');', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit', $adminLangId)), Label::getLabel('LBL_Edit', $adminLangId), true);
                        $innerLi->appendElement('a', array('href' => 'javascript:;', 'onclick' => 'cancelClass(' . $row['grpcls_id'] . ');', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Cancel', $adminLangId)), Label::getLabel('LBL_Cancel', $adminLangId), true);
                        $innerLi->appendElement('a', array('href' => 'javascript:;', 'onclick' => 'removeClass(' . $row['grpcls_id'] . ');', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Delete', $adminLangId)), Label::getLabel('LBL_Delete', $adminLangId), true);
                    }
                }
                $innerLi->appendElement('a', array('href' => 'javascript:;', 'onclick' => 'viewJoinedLearners(' . $row['grpcls_id'] . ');', 'class' => 'button small green', 'title' => Label::getLabel('LBL_View_Joined_Learners', $adminLangId)), Label::getLabel('LBL_Joined_Learners', $adminLangId), true);
                break;
            default:
                $td->appendElement('plaintext', array(), $row[$key]);
                break;
        }
    }
}
if (count($classes) == 0) {
    $tbl->appendElement('tr')->appendElement('td', array('colspan' => count($arr_flds)), Label::getLabel('LBL_No_Records_Found', $adminLangId));
}
echo $tbl->getHtml();
$postedData['page'] = $page;
echo FatUtility::createHiddenFormFromData($postedData, array(
    'name' => 'frmSearchPaging'
));
$pagingArr = array('pageCount' => $pageCount, 'page' => $page, 'pageSize' => $pageSize, 'recordCount' => $recordCount, 'adminLangId' => $adminLangId);
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
?>
