<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php

$arr_flds = array(
    'listserial' => Label::getLabel('LBL_S.No.', $adminLangId),
    'order_id' => Label::getLabel('LBL_Order_Id', $adminLangId),
    'op_qty' => Label::getLabel('LBL_NO._OF_LESSONS', $adminLangId),
    'class_type' => Label::getLabel('LBL_Class_Type', $adminLangId),
    'learner_username' => Label::getLabel('LBL_Learner', $adminLangId),
    'teacher_username' => Label::getLabel('LBL_Teacher', $adminLangId),
    'language' => Label::getLabel('LBL_Language', $adminLangId),
    'op_lpackage_is_free_trial' => Label::getLabel('LBL_Free_trial', $adminLangId),
    'order_is_paid' => Label::getLabel('LBL_Status', $adminLangId),
    'action' => Label::getLabel('LBL_View_Lessons', $adminLangId),
);
$tbl = new HtmlElement('table', array('width' => '100%', 'class' => 'table table-responsive'));
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) {
    $e = $th->appendElement('th', array(), $val);
}
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
            case 'class_type':
                $class_types = ApplicationConstants::getClassTypes($adminLangId);
                $td->appendElement('plaintext', array(), (isset($row['grpcls_id']) && $row['grpcls_id'] > 0) ? $class_types[ApplicationConstants::CLASS_TYPE_GROUP] : $class_types[ApplicationConstants::CLASS_TYPE_1_TO_1]);
                break;
            case 'order_is_paid':
                $status = Order::getPaymentStatusArr($adminLangId);
                unset($status[Order::ORDER_IS_PENDING]);
                if ($row[$key] == Order::ORDER_IS_CANCELLED) {
                    $status = array(Order::ORDER_IS_CANCELLED => $status[Order::ORDER_IS_CANCELLED]);
                }
                if ($canEdit) {
                    $select = new HtmlElement('select', array('class' => 'status-field', 'id' => 'user_confirmed_select_' . $row['order_id'], 'name' => 'order_is_paid', 'onchange' => "updateOrderStatus(this,'" . $row['order_id'] . "',this.value,'" . $row[$key] . "')"));
                    foreach ($status as $status_key => $status_value) {
                        if ($status_key == $row[$key]) {
                            $select->appendElement('option', array('value' => $status_key, 'selected' => 'selected'), $status_value);
                        } else {
                            $select->appendElement('option', array('value' => $status_key), $status_value);
                        }
                    }
                    $td->appendHtmlElement($select);
                } else {
                    $td->appendElement('plaintext', array(), $status[$row[$key]]);
                }
                break;
            case 'op_lpackage_is_free_trial':
                $str = $row[$key] ? 'Yes' : 'No';
                $td->appendElement('plaintext', array(), $str, true);
                break;
            case 'learner_username':
                $td->appendElement('strong', array(), Label::getlabel('LBL_N_:'), true);
                $td->appendElement('plaintext', array(), ' ' . $row['learner_username']);
                $td->appendElement('strong', array(), ' <br>' . Label::getlabel('LBL_E_:'), true);
                $td->appendElement('plaintext', array(), $row['userEmail'], true);
                break;
            case 'teacher_username':
                $td->appendElement('strong', array(), Label::getlabel('LBL_N_:'), true);
                $td->appendElement('plaintext', array(), ' ' . $row['teacher_username']);
                $td->appendElement('strong', array(), ' <br>' . Label::getlabel('LBL_E_:'), true);
                $td->appendElement('plaintext', array(), $row['teacherEmail'], true);
                break;
            case 'language':
                $str = ($row['op_lpackage_is_free_trial']) ? Label::getLabel('LBL_N/A', $adminLangId) : $row[$key];
                $td->appendElement('plaintext', array(), $str, true);
                break;
            case 'action':
                $ul = $td->appendElement("ul", array("class" => "actions actions--centered"));
                $li = $ul->appendElement("li", array('class' => 'droplink'));
                $li->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit', $adminLangId)), '<i class="ion-android-more-horizontal icon"></i>', true);
                $innerDiv = $li->appendElement('div', array('class' => 'dropwrap'));
                $innerUl = $innerDiv->appendElement('ul', array('class' => 'linksvertical'));
                $innerLi = $innerUl->appendElement('li');
                $innerLi->appendElement("a", array('href' => CommonHelper::generateUrl('TransactionHistory', 'view', array($row['order_id'])), 'class' => 'button small green', 'title' => Label::getLabel('LBL_View_Details', $adminLangId)), Label::getLabel('LBL_View_Details', $adminLangId), true);
                if ($row['order_is_paid'] == Order::ORDER_IS_PAID) {
                    $innerLi = $innerUl->appendElement('li');
                    $innerLi->appendElement("a", array('href' => CommonHelper::generateUrl('TransactionHistory', 'viewSchedules', array("all", $row['order_id'])), 'class' => 'button small green', 'title' => Label::getLabel('LBL_View_Schedules', $adminLangId)), Label::getLabel('LBL_View_Schedules', $adminLangId), true);
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
echo FatUtility::createHiddenFormFromData($postedData, array('name' => 'frmUserSearchPaging'));
$pagingArr = array('pageCount' => $pageCount, 'page' => $page, 'pageSize' => $pageSize, 'recordCount' => $recordCount, 'adminLangId' => $adminLangId);
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
?>
