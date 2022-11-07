<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php
$arr_flds = array(
    'listserial' => Label::getLabel('LBL_Sr_no.', $adminLangId),
    'ExpertName' => Label::getLabel('LBL_Expert_Name', $adminLangId),
    'StudentName' => Label::getLabel('LBL_Student_Name', $adminLangId),
    'sldetail_order_id' => Label::getLabel('LBL_Order_ID', $adminLangId),
    'slesson_id' => Label::getLabel('LBL_Lesson_ID', $adminLangId),
    'StartTime' => Label::getLabel('LBL_Start_Time', $adminLangId),
    'EndTime' => Label::getLabel('LBL_End_Time', $adminLangId),
    'slesson_status' => Label::getLabel('LBL_Current_Status', $adminLangId),
    'slesson_added_on' => Label::getLabel('LBL_Added_On', $adminLangId),
    'slesson_comments' => Label::getLabel('LBL_Comments', $adminLangId)
);
$tbl = new HtmlElement('table', array('width' => '100%', 'class' => 'table table--hovered'));
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) {
    $e = $th->appendElement('th', array(), $val);
}
$sr_no = 0;
foreach ($lessons as $sn => $row) {
    $sr_no++;
    $tr = $tbl->appendElement('tr');

    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'listserial':
                $td->appendElement('plaintext', array(), $sr_no);
                break;
            case 'ExpertName':
                $td->appendElement('plaintext', array(), $row[$key], true);
                break;
            case 'StudentName':
                $td->appendElement('plaintext', array(), $row[$key], true);
                break;
            case 'sldetail_order_id':
                $td->appendElement('plaintext', array(), $row[$key], true);
                break;
                $innerLiBoth = $innerUl->appendElement('li');
            case 'StartTime':
                $td->appendElement('plaintext', array(), MyDate::format($row[$key], true), true);
                break;
            case 'EndTime':
                $td->appendElement('plaintext', array(), MyDate::format($row[$key], true), true);
                break;
            case 'slesson_status':
                $td->appendElement('plaintext', array(), $statusArr[$row[$key]], true);
                break;
            case 'slesson_added_on':
                $td->appendElement('plaintext', array(), MyDate::format($row[$key], true), true);
                break;
            case 'slesson_comments':
                $td->appendElement('p', array('title' => nl2br($row[$key])), CommonHelper::truncateCharacters($row[$key], 20, '', '', true), true);
                break;
            default:
                $td->appendElement('plaintext', array(), $row[$key]);
                break;
        }
    }
}
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_Report', $adminLangId); ?></h4>
        <div class="-float-right">
            <a onClick="exportUpcomingAndCompletedReport(<?php echo $report_user_id; ?>, <?php echo $report_type; ?>)" class='btn btn-primary export-btn btn-sm'>Export CSV</a>
        </div>
    </div>
    <div class="sectionbody space">
        <div class="tabs_nav_container responsive flat">
            <div class="tabs_panel_wrap">
                <div class="tabs_panel">
                    <div class="row table-responsive">
                        <?php
                        if (count($lessons) == 0) {
                            $tbl->appendElement('tr')->appendElement('td', array('colspan' => count($arr_flds)), Label::getLabel('LBL_No_Records_Found', $adminLangId));
                        }
                        echo $tbl->getHtml();
                        $postedData['page'] = $page;
                        echo FatUtility::createHiddenFormFromData($postedData, array(
                            'name' => 'frmReportSearchPaging'
                        ));
                        $pagingArr = array('callBackJsFunc' => 'goToNextPage', 'pageCount' => $pageCount, 'page' => $page, 'recordCount' => $recordCount, 'adminLangId' => $adminLangId);
                        $this->includeTemplate('_partial/pagination.php', $pagingArr, false);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>