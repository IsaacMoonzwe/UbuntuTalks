<?php

defined('SYSTEM_INIT') or die('Invalid Usage.');
$arr_flds = array(
    'listserial' => Label::getLabel('LBL_Sr_no.', $adminLangId),
    'user_name' => Label::getLabel('LBL_Name', $adminLangId),
    'teacherTotLessons' => Label::getLabel('LBL_No._of_Sold_Lessons', $adminLangId),
    'teacherSchLessons' => Label::getLabel('LBL_Completed_Lessons', $adminLangId),
    'cancelledLessons' => Label::getLabel('LBL_Cancelled_Lessons', $adminLangId),
    'studentIds' => Label::getLabel('LBL_No._Students', $adminLangId),
    'teacher_rating' => Label::getLabel('LBL_Rating', $adminLangId)
);
$tbl = new HtmlElement('table',
        array('width' => '100%', 'class' => 'table table-responsive table--hovered'));
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
            case 'user_name':
                $td->appendElement('plaintext', array(), $row[$key], true);
                break;
            case 'teacherTotLessons':
                $td->appendElement('plaintext', array(), $row[$key], true);
                break;
            case 'teacher_rating':
                $rating = '<ul class="rating list-inline">';
                for ($j = 1; $j <= 5; $j++) {
                    $class = ($j <= round($row[$key])) ? "active" : "in-active";
                    $fillColor = ($j <= round($row[$key])) ? "#ff3a59" : "#474747";
                    $rating .= '<li class="' . $class . '">
					<svg xml:space="preserve" enable-background="new 0 0 70 70" viewBox="0 0 70 70" height="18px" width="18px" y="0px" x="0px" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg" id="Layer_1" version="1.1">
					<g><path d="M51,42l5.6,24.6L35,53.6l-21.6,13L19,42L0,25.4l25.1-2.2L35,0l9.9,23.2L70,25.4L51,42z M51,42" fill="' . $fillColor . '" /></g></svg>
					
				  </li>';
                }
                $rating .= '</ul>';
                $td->appendElement('plaintext', array(), $rating, true);
                break;
            case 'studentIds':
                if ($row[$key] != '') {
                    $idsArray = explode(',', $row[$key]);
                } else {
                    $idsArray = array();
                }
                $td->appendElement('plaintext', array(), count($idsArray), true);
                break;
            default:
                $td->appendElement('plaintext', array(), $row[$key], true);
                break;
        }
    }
}
if (count($arr_listing) == 0) {
    $tbl->appendElement('tr')->appendElement('td', array(
        'colspan' => count($arr_flds)),
            Label::getLabel('LBL_No_Records_Found', $adminLangId)
    );
}
echo $tbl->getHtml();
$postedData['page'] = $page;
echo FatUtility::createHiddenFormFromData($postedData, array('name' => 'frmSalesReportSearchPaging'));
$pagingArr = array('pageCount' => $pageCount, 'page' => $page, 'recordCount' => $recordCount, 'adminLangId' => $adminLangId);
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
?>