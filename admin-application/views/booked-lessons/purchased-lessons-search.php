<?php

defined('SYSTEM_INIT') or die('Invalid Usage.');
const confFrontEndUrl = "' . CONF_WEBROOT_URL . '";
$arr_flds = array(
    'listserial' => Label::getLabel('LBL_Sr_no.', $adminLangId),
    'slesson_id' => Label::getLabel('LBL_Lesson_Id', $adminLangId),
    'learner_name' => Label::getLabel('LBL_Learner_Name', $adminLangId),
    'learner_email' => Label::getLabel('LBL_Learner_Email', $adminLangId),
    'instructor_email' => Label::getLabel('LBL_Instructor_Email', $adminLangId),
    'teacher_name' => Label::getLabel('LBL_Teacher_Name', $adminLangId),
    'slesson_kids_class' => Label::getLabel('LBL_Class_Type', $adminLangId),
    'slesson_status' => Label::getLabel('LBL_Status', $adminLangId),
    'action' => Label::getLabel('LBL_Details', $adminLangId),
    //'email_reply' => Label::getLabel('LBL_Report_Details', $adminLangId),
);
$adminTimezone = Admin::getAdminTimeZone();
$tbl = new HtmlElement('table', array('width' => '100%', 'class' => 'table table-responsive'));
$disabledStatusArr = [ScheduledLesson::STATUS_SCHEDULED, ScheduledLesson::STATUS_UPCOMING, ScheduledLesson::STATUS_ISSUE_REPORTED];
$th = $tbl->appendElement('thead')->appendElement('tr');
$orderStatus = Order::getPaymentStatusArr($adminLangId);
$userType = User::getUserTypesArr($adminLangId);
$yesAndNoArr = applicationConstants::getYesNoArr($adminLangId);
$statusArr = ScheduledLesson::getStatusArr();
$teachers = $rawData;
unset($statusArr[ScheduledLesson::STATUS_RESCHEDULED]);
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
            case 'slesson_kids_class':
                $date = ($row[$key] == "1") ? Label::getLabel('LBL_Kids_Class') : Label::getLabel('LBL_Group_Class');
                $td->appendElement('plaintext', array(), $date, true);
                break;
            case 'slesson_date':
                $date = ($row[$key] == "0000-00-00") ? Label::getLabel('LBL_N/A') : date('Y-m-d', strtotime($row[$key]));
                $td->appendElement('plaintext', array(), $date, true);
                break;
            case 'slesson_ended_on':
                $endTime = ($row[$key] == "0000-00-00 00:00:00" && $row['slesson_date'] == "0000-00-00") ? Label::getLabel("LBL_N/A") : MyDate::convertTimeFromSystemToUserTimezone('H:i:s A', $row['slesson_date'] . " " . $row['slesson_end_time'], true, $adminTimezone);
                $td->appendElement('plaintext', array(), $endTime, true);
                break;
            case 'slesson_date':
                $date = ($row[$key] == "0000-00-00") ? Label::getLabel('LBL_N/A') : MyDate::format($row['slesson_date'] . " " . $row['slesson_start_time'], false, true, $adminTimezone);
                $td->appendElement('plaintext', array(), $date, true);
                break;
            case 'slesson_start_time':
                $startTime = ($row[$key] == "00:00:00" && $row['slesson_date'] == "0000-00-00") ? Label::getLabel("LBL_N/A") : MyDate::convertTimeFromSystemToUserTimezone('H:i:s A', $row['slesson_date'] . " " . $row['slesson_start_time'], true, $adminTimezone);
                $td->appendElement('plaintext', array(), $startTime, true);
                break;
            case 'slesson_ended_by':
                $str = (!empty($row[$key])) ? $userType[$row[$key]] : Label::getLabel('LBL_N/A');
                $td->appendElement('plaintext', array(), $str, true);
                break;
                // case 'op_lpackage_is_free_trial':
                //     $td->appendElement('plaintext', array(), $yesAndNoArr[$row[$key]], true);
                //     break;
                // case 'teacherTeachLanguageName':
                //     $text = ($row['op_lpackage_is_free_trial']) ? Label::getLabel('LBL_N/A', $adminLangId) : $row[$key];
                //     $td->appendElement('plaintext', array(), $text, true);
                //     break;
            case 'slesson_status':
                $td->appendElement('plaintext', array(), $statusArr[$row[$key]], true);
                break;
            case 'slesson_change_status':
                $selectStatusArr = $statusArr;
                if ($row['slesson_status'] == ScheduledLesson::STATUS_CANCELLED) {
                    $selectStatusArr = array(ScheduledLesson::STATUS_CANCELLED => $selectStatusArr[ScheduledLesson::STATUS_CANCELLED]);
                }
                $select = new HtmlElement('select', array('id' => 'user_confirmed_select_' . $row['sldetail_id'], 'class' => 'lesson-status-dropdown status-field', 'name' => 'order_is_paid', 'onchange' => "updateScheduleStatus(this, '" . $row['sldetail_id'] . "',this.value,'" . $row['slesson_status'] . "')"));
                if ($row['slesson_grpcls_id'] > 0) {
                    $disabledStatusArr[] = ScheduledLesson::STATUS_NEED_SCHEDULING;
                }
                foreach ($selectStatusArr as $status_key => $status_value) {
                    $disableOption = [];
                    $selectedOption = [];
                    if (in_array($status_key, $disabledStatusArr)) {
                        $disableOption = ['disabled' => 'disabled'];
                    }
                    if ($status_key == $row['slesson_status']) {
                        $selectedOption = ['selected' => 'selected'];
                    }
                    $option = $select->appendElement('option', ['value' => $status_key] + $disableOption + $selectedOption, $status_value);
                }
                $td->appendHtmlElement($select);
                break;

            case 'assign_class':
                $teacherData = $teachers;
                foreach ($teachers as $teacher_keys => $teacher_value) {
                    if ($teacher_value['teacher_name'] == $row['teacher_name'] && $row['slesson_status'] == ScheduledLesson::STATUS_CANCELLED) {
                        $selected_teacher_id = $teacher_value['user_id'];
                        $teacherValue = $teacher_value;
                        break;
                    }
                }
                if ($row['slesson_status'] == ScheduledLesson::STATUS_CANCELLED) {
                    $teacherData = array($selected_teacher_id => $teacherValue);
                } else {
                    $teachers = $teacherData;
                }
                $select = new HtmlElement('select', array('id' => 'user_confirmed_select_' . $row['sldetail_id'], 'class' => 'lesson-status-dropdown status-field', 'name' => 'order_is_paid', 'onchange' => "updateAssignClassStatus(this, '" . $row['sldetail_id'] . "',this.value,'" . $row['teacher_name'] . "')"));
                foreach ($teacherData as $teacher_keys => $teacher_value) {
                    $disableOption = [];
                    $selectedOption = [];
                    if ($teacher_value['teacher_name'] == $row['teacher_name']) {
                        $selectedOption = ['selected' => 'selected'];
                    }
                    $selectStatusArr = $statusArr;
                    if ($selectStatusArr['4'] == 'Cancelled') {
                        $option = $select->appendElement('option', ['value' => $teacher_value['user_id']] + $selectedOption, $teacher_value['teacher_name'], disabled);
                    } else {
                        $option = $select->appendElement('option', ['value' => $teacher_value['user_id']] + $selectedOption, $teacher_value['teacher_name']);
                    }
                }
                $td->appendHtmlElement($select);
                break;

            case 'action':
                $ul = $td->appendElement("ul", array("class" => "actions"));
                $li = $ul->appendElement("li");
                $li->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => 'View Order Detail', 'onclick' => 'viewDetail(' . $row['slesson_id'] . ');'), '<i class="ion-eye icon"></i>', true);
                break;

            case 'email_reply':
                $ul = $td->appendElement("ul", array("class" => "actions"));
                $li = $ul->appendElement("li");
                $li->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => 'Report Detail', 'onclick' => 'reportDetail(' . $row['slesson_id'] . ');'), '<i class="ion-eye icon"></i>', true);
                break;

            case 'reschedule':
                if ($row['slesson_status'] == 1) {
                    $ul = $td->appendElement("ul", array("class" => "actions"));
                    $li = $ul->appendElement("li");
                    $li->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => 'Reschdule Booking', 'onclick' => 'requestReschedule(' . $row['sldetail_id'] . ');'), '<i class="icon ion-clock"></i>', true);
                } else {
                    $ul = $td->appendElement("ul", array("class" => "actions"));
                    $li = $ul->appendElement("li");
                    $li->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => 'Not Schedule'), '<i class="icon ion-close-circled"></i>', true);
                    // $str = appendElement("<span class="iconify" data-icon='ion:close-circle-outline'></span>");
                    // $td->appendElement('plaintext', array(), $str, true);
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
echo FatUtility::createHiddenFormFromData($postedData, array('name' => 'frmPurchaseLessonSearchPaging'));
$pagingArr = array('pageCount' => $pageCount, 'page' => $page, 'pageSize' => $pageSize, 'recordCount' => $recordCount, 'adminLangId' => $adminLangId);
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
