<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class='page'>
    <div class='fixed_container'>
        <div class="row">
            <div class="space">
                <div class="page__title">
                    <div class="row">
                        <div class="col--first col-lg-6">
                            <span class="page__icon"><i class="ion-android-star"></i></span>
                            <h5><?php echo Label::getLabel('LBL_Manage_Lessons', $adminLangId); ?> </h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                    </div>
                </div>
                <section class="section">
                    <div class="sectionhead">
                        <h4><?php echo Label::getLabel('LBL_View_Schedules', $adminLangId); ?></h4>
                    </div>
                    <div class="sectionbody">
                        <div class="tablewrap" >
                            <div id="listing">
                                <?php
                                $arr_flds = array(
                                    'listserial' => Label::getLabel('LBL_Sr_no.', $adminLangId),
                                    'slesson_id' => Label::getLabel('LBL_Lesson_Id', $adminLangId),
                                    'learner_username' => Label::getLabel('LBL_Lerner', $adminLangId),
                                    'teacher_username' => Label::getLabel('LBL_Teacher', $adminLangId),
                                    'slesson_date' => Label::getLabel('LBL_Lesson_Date', $adminLangId),
                                    'teacherTeachLanguageName' => Label::getLabel('LBL_Language', $adminLangId),
                                    'slesson_status' => Label::getLabel('LBL_Status', $adminLangId),
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
                                    foreach ($arr_flds as $key => $val) {
                                        $td = $tr->appendElement('td');
                                        switch ($key) {
                                            case 'listserial':
                                                $td->appendElement('plaintext', array(), $sr_no);
                                                break;
                                            case 'lpackage_active':
                                                $active = "";
                                                $statusAct = '';
                                                if ($row['lpackage_active'] == applicationConstants::YES && $canEdit === true) {
                                                    $active = 'checked';
                                                    $statusAct = 'inactiveStatus(this)';
                                                }
                                                if ($row['lpackage_active'] == applicationConstants::NO && $canEdit === true) {
                                                    $active = '';
                                                    $statusAct = 'activeStatus(this)';
                                                }
                                                $statusClass = ( $canEdit === false ) ? 'disabled' : '';
                                                $str = '<label class="statustab -txt-uppercase">                 
                     <input ' . $active . ' type="checkbox" id="switch' . $row['lpackage_id'] . '" value="' . $row['lpackage_id'] . '" onclick="' . $statusAct . '" class="switch-labels status_' . $row['lpackage_id'] . '"/>
                    <i class="switch-handles ' . $statusClass . '"></i></label>';
                                                $td->appendElement('plaintext', array(), $str, true);
                                                break;
                                            case 'slesson_status':
                                                $td->appendElement('plaintext', array(), $status_arr[$row[$key]], true);
                                                break;
                                            case 'learner_username':
                                                $td->appendElement('plaintext', array(), $row[$key], true);
                                                break;
                                            case 'teacher_username':
                                                $td->appendElement('plaintext', array(), $row[$key], true);
                                                break;
                                            case 'lpackage_is_free_trial':
                                                $td->appendElement('plaintext', array(), applicationConstants::getYesNoArr($adminLangId)[$row[$key]], true);
                                                break;
                                            case 'slesson_date':
                                                if ($row[$key] == '0000-00-00') {
                                                    $_str = '-';
                                                } else {
                                                    $_str = date('l, F d, Y', strtotime($row[$key]));
                                                }
                                                $td->appendElement('plaintext', array(), $_str, true);
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
                            </div>
                        </div> 
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>