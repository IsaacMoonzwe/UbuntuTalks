<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php

$arr_flds = array(
    'listserial' => Label::getLabel('LBL_Sr_no.', $adminLangId),
    'corporate_ticket_no_of_tickets' => Label::getLabel('LBL_Number_of_Tickets', $adminLangId),
    'corporate_ticket_discount' => Label::getLabel('LBL_Discount', $adminLangId),
    'corporate_ticket_category_type' => Label::getLabel('LBL_Corporate_Category_Type', $adminLangId),
    'corporate_ticket_active' => Label::getLabel('LBL_Status', $adminLangId),
    'action' => Label::getLabel('LBL_Action', $adminLangId),
        );
$tbl = new HtmlElement('table', array('width' => '100%', 'class' => 'table table-responsive table--hovered'));
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) {
$e = $th->appendElement('th', array(), $val);
}
$sr_no = 0;
foreach ($arr_listing as $sn => $row) {
$sr_no++;
$tr = $tbl->appendElement('tr');
$tr->setAttribute("id", $row['corporate_ticket_id']);
foreach ($arr_flds as $key => $val) {
$td = $tr->appendElement('td');
switch ($key) {
case 'listserial':
$td->appendElement('plaintext', array(), $sr_no);
break;
case 'corporate_ticket_active':
$active = "";
$statusAct = '';
if ($row['corporate_ticket_active'] == applicationConstants::YES && $canEdit === true) {
$active = 'checked';
$statusAct = 'inactiveStatus(this)';
}
if ($row['corporate_ticket_active'] == applicationConstants::NO && $canEdit === true) {
$active = '';
$statusAct = 'activeStatus(this)';
}
$statusClass = ( $canEdit === false ) ? 'disabled' : '';
$str = '<label class="statustab -txt-uppercase">                 
                     <input ' . $active . ' type="checkbox" id="switch' . $row['corporate_ticket_id'] . '" value="' . $row['corporate_ticket_id'] . '" onclick="' . $statusAct . '" class="switch-labels status_' . $row['corporate_ticket_id'] . '"/>
                    <i class="switch-handles ' . $statusClass . '"></i></label>';
$td->appendElement('plaintext', array(), $str, true);
break;
case 'action':
$ul = $td->appendElement("ul", array("class" => "actions actions centered"));
if ($canEdit) {
$li = $ul->appendElement("li", array('class' => 'droplink'));
$li->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit', $adminLangId)), '<i class="ion-android-more-horizontal icon"></i>', true);
$innerDiv = $li->appendElement('div', array('class' => 'dropwrap'));
$innerUl = $innerDiv->appendElement('ul', array('class' => 'linksvertical'));
$innerLiEdit = $innerUl->appendElement('li');
$innerLiEdit->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green',
 'title' => Label::getLabel('LBL_Edit', $adminLangId), "onclick" => "editTestimonialFormNew(" . $row['corporate_ticket_id'] . ")"), Label::getLabel('LBL_Edit', $adminLangId),
 true);
$innerLiDelete = $innerUl->appendElement("li");
$innerLiDelete->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green',
 'title' => Label::getLabel('LBL_Delete', $adminLangId), "onclick" => "deleteRecord(" . $row['corporate_ticket_id'] . ")"), Label::getLabel('LBL_Delete', $adminLangId),
 true);
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