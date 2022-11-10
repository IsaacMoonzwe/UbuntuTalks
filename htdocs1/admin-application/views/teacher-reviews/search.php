<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php

$arr_flds = array(
    'listserial' => 'S.N.',
    'tlreview_order_id' => 'Order ID',
    'teacher' => 'Reviewed to',
    'reviewed_by' => 'Reviewed By',
    'average_rating' => 'Rating',
    'tlreview_posted_on' => 'Date',
    'tlreview_status' => 'Status',
    'action' => 'Action',
);
if (!$canEdit) {
    unset($arr_flds['action']);
}
$tbl = new HtmlElement('table', array('width' => '100%', 'class' => 'table table-responsive'));
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
            case 'product_name':
                $td->setAttribute('width', '20%');
                $td->appendElement('plaintext', array(), $row[$key]);
                break;
            case 'teacher':
                $teacherUserUrl = FatUtility::generateUrl('Users', 'index', array($row['teacher_user_id']));
                $teacherDetail = '<strong>' . Label::getLabel('LBL_N') . ':</strong> ' . $row['teacher_name'] . '<br/>';
                $teacherDetail .= '<strong>' . Label::getLabel('LBL_Email') . ':</strong> ' . $row['teacher_email_id'] . '<br/>';
                $td->appendElement('plaintext', array(), $teacherDetail, true);
                break;
            case 'reviewed_by':
                $reviewedByUserUrl = FatUtility::generateUrl('Users', 'index', array($row['learner_user_id']));
                $reviewedByUserDetail = '<strong>' . Label::getLabel('LBL_N') . ':</strong> ' . $row['learner_name'] . '<br/>';
                $reviewedByUserDetail .= '<strong>' . Label::getLabel('LBL_Email') . ':</strong> ' . $row['learner_email_id'] . '<br/>';
                $td->appendElement('plaintext', array(), $reviewedByUserDetail, true);
                break;
            case 'tlreview_status':
                $td->appendElement('plaintext', array(), $reviewStatus[$row[$key]], true);
                break;
            case 'average_rating':
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
            case 'tlreview_posted_on':
                $td->appendElement('plaintext', array(), FatDate::format($row[$key], true));
                break;
            case 'action':
                $ul = $td->appendElement("ul", array("class" => "actions"));
                if ($canEdit) {
                    $li = $ul->appendElement("li");
                    $li->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => 'Edit', "onclick" => "viewReview(" . $row['tlreview_id'] . ")"), '<i class="ion-eye icon"></i>', true);
                    /* $li = $ul->appendElement("li");
                      $li->appendElement('a', array('href'=>'javascript:void(0)', 'class'=>'button small green', 'title'=>'Edit',"onclick"=>"brandForm(".$row['brand_id'].")"),'<i class="ion-eye icon"></i>', true);
                      $li = $ul->appendElement("li");
                      $li->appendElement('a', array('href'=>"javascript:void(0)", 'class'=>'button small green', 'title'=>'Delete',"onclick"=>"deleteRecord(".$row['brand_id'].")"),'<i class="ion-android-delete icon"></i>', true); */
                }
                break;
            default:
                $td->appendElement('plaintext', array(), $row[$key]);
                break;
        }
    }
}
if (count($arr_listing) == 0) {
    $tbl->appendElement('tr')->appendElement('td', array('colspan' => count($arr_flds)), 'No records found');
}
echo $tbl->getHtml();
$postedData['page'] = $page;
echo FatUtility::createHiddenFormFromData($postedData, array(
    'name' => 'frmReviewSearchPaging'
));
$pagingArr = array('pageCount' => $pageCount, 'page' => $page, 'recordCount' => $recordCount, 'adminLangId' => $adminLangId);
$this->includeTemplate('_partial/pagination.php', $pagingArr);
?>