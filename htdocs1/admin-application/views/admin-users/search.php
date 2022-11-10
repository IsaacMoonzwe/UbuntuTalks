<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php

$arr_flds = array(
    'listserial' => Label::getLabel('LBL_Sr._No', $adminLangId),
    'admin_name' => Label::getLabel('LBL_Full_Name', $adminLangId),
    'admin_username' => Label::getLabel('LBL_Username', $adminLangId),
    'admin_email' => Label::getLabel('LBL_Email', $adminLangId),
    'admin_active' => Label::getLabel('LBL_Status', $adminLangId),
    'action' => Label::getLabel('LBL_Action', $adminLangId),
);
$tbl = new HtmlElement('table', array('width' => '100%', 'class' => 'table table-responsive'));
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) {
    $e = $th->appendElement('th', array(), $val);
}
$sr_no = 0;
foreach ($arr_listing as $sn => $row) {
    $sr_no++;
    $tr = $tbl->appendElement('tr');
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'listserial':
                $td->appendElement('plaintext', array(), $sr_no);
                break;
            case 'action':
                $ul = $td->appendElement("ul", array("class" => "actions actions--centered"));
                if ($canEdit) {
                    $li = $ul->appendElement("li", array('class' => 'droplink'));
                    if ($row['admin_id'] > 1 || $adminLoggedInId == 1) {
                        $li->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit', $adminLangId)), '<i class="ion-android-more-horizontal icon"></i>', true);
                    }
                    $innerDiv = $li->appendElement('div', array('class' => 'dropwrap'));
                    $innerUl = $innerDiv->appendElement('ul', array('class' => 'linksvertical'));
                    $innerLi = $innerUl->appendElement('li');
                    $innerLi->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit', $adminLangId), "onclick" => "editAdminUserForm(" . $row['admin_id'] . ")"), Label::getLabel('LBL_Edit', $adminLangId), true);
                    $innerLi = $innerUl->appendElement('li');
                    $innerLi->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Change_Password', $adminLangId), "onclick" => "changePasswordForm(" . $row['admin_id'] . ")"), Label::getLabel('LBL_Change_Password', $adminLangId), true);
                    if ($row['admin_id'] > 1 && $row['admin_id'] != $adminLoggedInId && $canViewAdminPermissions === true) {
                        $innerLi = $innerUl->appendElement('li');
                        $innerLi->appendElement('a', array('href' => CommonHelper::generateUrl('AdminUsers', 'permissions', array($row['admin_id'])), 'class' => 'button small green redirect--js', 'title' => Label::getLabel('LBL_Permissions', $adminLangId)), Label::getLabel('LBL_Permissions', $adminLangId), true);
                    }
                }
                break;
            case 'admin_active':
                if ($row['admin_id'] > 1 && $row['admin_id'] != $adminLoggedInId) {
                    $active = "active";
                    $statucAct = '';
                    if ($row['admin_active'] == applicationConstants::YES && $canEdit === true) {
                        $active = 'active';
                        $statucAct = 'inactiveStatus(this)';
                    }
                    if ($row['admin_active'] == applicationConstants::NO && $canEdit === true) {
                        $active = '';
                        $statucAct = 'activeStatus(this)';
                    }
                    $str = '<label id="' . $row['admin_id'] . '" class="statustab ' . $active . ' status_' . $row['admin_id'] . '" onclick="' . $statucAct . '">
					  <span data-off="' . Label::getLabel('LBL_Active', $adminLangId) . '" data-on="' . Label::getLabel('LBL_Inactive', $adminLangId) . '" class="switch-labels "></span>
					  <span class="switch-handles"></span>
					</label>';
                    $td->appendElement('plaintext', array(), $str, true);
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
?>
