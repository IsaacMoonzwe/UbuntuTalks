<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php

$arr_flds = array(
    'listserial' => Label::getLabel('LBL_S.No.', $adminLangId),
    'user' => Label::getLabel('LBL_User', $adminLangId),
    'type' => Label::getLabel('LBL_User_Type', $adminLangId),
    'user_added_on' => Label::getLabel('LBL_Reg._Date', $adminLangId),
    'credential_active' => Label::getLabel('LBL_Status', $adminLangId),
    'credential_verified' => Label::getLabel('LBL_verified', $adminLangId),
    'action' => Label::getLabel('LBL_Action', $adminLangId),
);
$tbl = new HtmlElement('table', array('width' => '100%', 'class' => 'table table-responsive'));
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) {
    $e = $th->appendElement('th', array(), $val);
}
$userTypeArray = EventUser::getUserTypesArr($adminLangId);
$sr_no = $page == 1 ? 0 : $pageSize * ($page - 1);
foreach ($arr_listing as $sn => $row) {
    $sr_no++;
    $tr = $tbl->appendElement('tr', array());
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'listserial':
                $td->appendElement('plaintext', array(), $sr_no);
                break;
            case 'user':
                $userDetail = '<strong>' . Label::getLabel('LBL_N:', $adminLangId) . ' </strong>' . $row['user_first_name'] . ' ' . $row['user_last_name'] . '<br/>';
                $userDetail .= '<strong>' . Label::getLabel('LBL_Email:', $adminLangId) . ' </strong>' . $row['credential_email'] . '<br/>';
                $userDetail .= '<strong>' . Label::getLabel('LBL_User_ID:', $adminLangId) . ' </strong>' . $row['user_id'] . '<br/>';
                $td->appendElement('plaintext', array(), $userDetail, true);
                break;
            case 'credential_active':
                $active = "active";
                $strTxt = Label::getLabel('LBL_Active', $adminLangId);
                $statusAct = '';
                if ($row['credential_active'] == applicationConstants::ACTIVE) {
                    $active = 'active';
                    $statusAct = 'inactiveStatus(this)';
                }
                if ($row['credential_active'] == applicationConstants::INACTIVE) {
                    $active = '';
                    $strTxt = Label::getLabel('LBL_Inactive', $adminLangId);
                    $statusAct = 'activeStatus(this)';
                }
                if ($canEdit === true) {
                    $str = '<label id="' . $row['user_id'] . '" class="statustab status_' . $row['user_id'] . ' ' . $active . '" onclick="' . $statusAct . '">
				  <span data-off="' . Label::getLabel('LBL_Active', $adminLangId) . '" data-on="' . Label::getLabel('LBL_Inactive', $adminLangId) . '" class="switch-labels"></span>
				  <span class="switch-handles"></span>
				</label>';
                } else {
                    $str = $strTxt;
                }
                $td->appendElement('plaintext', array(), $str, true);
                break;
            case 'user_regdate':
            case 'user_added_on':
                $td->appendElement('plaintext', array(), MyDate::format($row[$key], true, true, Admin::getAdminTimeZone()));
                break;
            case 'type':
                $str = '';
                $arr = EventUser::getUserTypesArr($adminLangId);
                if ($row['user_is_learner']) {
                    $str .= "Event Visitor" . '<br/>';
                }
                $td->appendElement('plaintext', array(), $str, true);
                break;
            case 'credential_verified':
                $yesNoArr = applicationConstants::getYesNoArr($adminLangId);
                $str = isset($row[$key]) ? $yesNoArr[$row[$key]] : '';
                $td->appendElement('plaintext', array(), $str, true);
                break;
            case 'action':
                $ul = $td->appendElement("ul", array("class" => "actions actions--centered"));
                if ($canEdit) {
                    $li = $ul->appendElement("li", array('class' => 'droplink'));
                    $li->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit', $adminLangId)), '<i class="ion-android-more-horizontal icon"></i>', true);
                    $innerDiv = $li->appendElement('div', array('class' => 'dropwrap'));
                    $innerUl = $innerDiv->appendElement('ul', array('class' => 'linksvertical'));
                    $innerLi = $innerUl->appendElement('li');
                    $innerLi->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit', $adminLangId), "onclick" => "viewUserForm(" . $row['user_id'] . ")"), Label::getLabel('LBL_View', $adminLangId), true);
                    $innerLi = $innerUl->appendElement('li');
                    $innerLi->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit', $adminLangId), "onclick" => "userForm(" . $row['user_id'] . ")"), Label::getLabel('LBL_Edit', $adminLangId), true);
                    $innerLi = $innerUl->appendElement("li");
                    $innerLi->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green',
                        'title' => Label::getLabel('LBL_Transactions', $adminLangId), "onclick" => "transactions(" . $row['user_id'] . ")"), Label::getLabel('LBL_Transactions', $adminLangId), true);
                    $innerLi = $innerUl->appendElement('li');
                    $innerLi->appendElement('a', array('href' => "javascript:void(0)", 'onClick' => "changePassword(" . $row['user_id'] . ")", 'class' => 'button small green redirect--js', 'title' => Label::getLabel('LBL_Change_Password', $adminLangId)), Label::getLabel('LBL_Change_Password', $adminLangId), true);
                    $innerLi = $innerUl->appendElement('li');
                    $innerLi->appendElement('a', array('href' => "javascript:void(0)", 'onClick' => "userLogin(" . $row['user_id'] . ")", 'class' => 'button small green redirect--js', 'title' => Label::getLabel('LBL_Log_into_store', $adminLangId)), Label::getLabel('LBL_Log_into_Profile', $adminLangId), true);
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
echo FatUtility::createHiddenFormFromData($postedData, array(
    'name' => 'frmUserSearchPaging'
));
$pagingArr = array('pageCount' => $pageCount, 'page' => $page, 'pageSize' => $pageSize, 'recordCount' => $recordCount, 'adminLangId' => $adminLangId);
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
?>
