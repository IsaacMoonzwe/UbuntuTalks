<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php
$arr_flds = array(
    'listserial' => '',
    'learnerFullName' => Label::getLabel('LBL_Learner', $adminLangId),
    'sldetail_learner_status' => Label::getLabel('LBL_Status', $adminLangId),
    'sldetail_order_id' => Label::getLabel('LBL_Order_ID', $adminLangId),
    'sldetail_added_on' => Label::getLabel('LBL_Added_On', $adminLangId),
);
$tbl = new HtmlElement('table', array('width' => '100%', 'class' => 'table table--hovered table-responsive'));
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
            case 'sldetail_learner_status':
                $td->appendElement('plaintext', array(), $statusArr[$row[$key]], true);
                break;
            case 'sldetail_added_on':
                $td->appendElement('plaintext', array(), MyDate::format($row[$key], true));
                break;
            case 'action':
                $ul = $td->appendElement("ul", array("class" => "actions actions--centered"));
                $li = $ul->appendElement("li", array('class' => 'droplink'));
                $li->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green'), '<i class="ion-android-more-horizontal icon"></i>', true);
                $innerDiv = $li->appendElement('div', array('class' => 'dropwrap'));
                $innerUl = $innerDiv->appendElement('ul', array('class' => 'linksvertical'));
                $innerLi = $innerUl->appendElement('li');
                $innerLi->appendElement('a', array('href' => 'javascript:;', 'onclick' => 'viewJoinedLearners(' . $row['grpcls_id'] . ');', 'class' => 'button small green', 'title' => Label::getLabel('LBL_View_Joined_Learners', $adminLangId)), Label::getLabel('LBL_View_Joined_Learners', $adminLangId), true);
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
        <h4><?php echo Label::getLabel('LBL_Joined_Learners', $adminLangId); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="tabs_nav_container responsive flat">
            <div class="tabs_panel_wrap">
                <div class="tabs_panel">
                    <div class="row">
                        <?php
                        if (count($lessons) == 0) {
                            $tbl->appendElement('tr')->appendElement('td', array('colspan' => count($arr_flds)), Label::getLabel('LBL_No_Records_Found', $adminLangId));
                        }
                        echo $tbl->getHtml();
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>