<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php

$arr_flds = array(
    'listserial' => Label::getLabel('LBL_Sr._No', $adminLangId),
    'commsetting_user_id' => Label::getLabel('LBL_Teacher', $adminLangId),
    'commsetting_fees' => Label::getLabel('LBL_Fees_[%]', $adminLangId),
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
            case 'commsetting_user_id':
                if ($row['commsetting_user_id'] == 0) {
                    $txt = Label::getLabel('LBL_One_to_One_COMMISSION');
                    if ($row['commsetting_is_grpcls'] == 1) {
                        $txt = Label::getLabel('LBL_GROUP_CLASS_COMMISSION');
                    }
                    $str = "<span class='label label-success'>$txt</span>";
                    $td->appendElement('plaintext', array(), $str, true);
                } else {
                    $td->appendElement('plaintext', array(), CommonHelper::displayText($row['vendor']), true);
                }
                break;
            case 'action':
                $ul = $td->appendElement("ul", array("class" => "actions"));
                if ($canEdit) {
                    $li = $ul->appendElement("li");
                    $li->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green',
                        'title' => Label::getLabel('LBL_Edit', $adminLangId), "onclick" => "editCommissionForm(" . $row['commsetting_id'] . ")"), '<i class="ion-edit icon"></i>',
                            true);
                    $li = $ul->appendElement("li");
                    $li->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green',
                        'title' => Label::getLabel('LBL_History', $adminLangId), "onclick" => "viewHistory(" . $row['commsetting_id'] . ")"), '<i class="ion-grid icon"></i>',
                            true);
                    if ($row['commsetting_is_mandatory'] != 1 AND $row['commsetting_user_id'] != 0) {
                        $li = $ul->appendElement("li");
                        $li->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green',
                            'title' => Label::getLabel('LBL_Delete', $adminLangId), "onclick" => "deleteCommission('" . $row['commsetting_id'] . "')"), '<i class="ion-android-delete icon"></i>',
                                true);
                    }
                }
                break;
            default:
                $td->appendElement('plaintext', array(), CommonHelper::displayText($row[$key]));
                break;
        }
    }
}
if (count($arr_listing) == 0) {
    $tbl->appendElement('tr')->appendElement('td', array('colspan' => count($arr_flds)), Label::getLabel('LBL_No_Record_Found', $adminLangId));
}
echo $tbl->getHtml();
?>