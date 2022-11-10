<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php

$arr_flds = array(
    'listserial' => Label::getLabel('LBL_Sr_no.', $adminLangId),
    'title' => Label::getLabel('LBL_Title', $adminLangId),
    'prislab_min' => Label::getLabel('LBL_Min_Slabs', $adminLangId),
    'prislab_max' => Label::getLabel('LBL_Max_Slabs', $adminLangId),
    'prislab_active' => Label::getLabel('LBL_Status', $adminLangId),
    'action' => Label::getLabel('LBL_Action', $adminLangId),
);
$tbl = new HtmlElement('table', array('width' => '100%', 'class' => 'table table-responsive table--hovered'));
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) {
    $e = $th->appendElement('th', array(), $val);
}
$sr_no = 0;
foreach ($records as $sn => $row) {
    $sr_no++;
    $tr = $tbl->appendElement('tr');
    $tr->setAttribute("id", $row['prislab_id']);
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'listserial':
                $td->appendElement('plaintext', array(), $sr_no);
                break;
            case 'title':
                $title = Label::getLabel('LBL_{min}_to_{max}_Lesson(s)'); //5 to 9 hrs
                $title = str_replace(['{min}', '{max}'], [$row['prislab_min'], $row['prislab_max']], $title);
                $td->appendElement('plaintext', array(), $title);
                break;
            case 'prislab_active':
                $active = "";
                $onChange = "";
                $statusClass = "disabled";
                if ($canEdit === true) {
                    $statusClass = "";
                    $onChangeStatus = applicationConstants::ACTIVE;
                    if ($row['prislab_active']) {
                        $onChangeStatus = applicationConstants::INACTIVE;
                        $active = 'checked';
                    }
                    $onChange = "onChange='changeStatus(this," . $row['prislab_id'] . " ," . $onChangeStatus . ")'";
                }
                $str = '<label class="statustab -txt-uppercase">                 
                     <input ' . $active . ' type="checkbox" id="switch' . $row['prislab_id'] . '" value="' . $row['prislab_id'] . '" ' . $onChange . ' class="switch-labels status_' . $row['prislab_id'] . '"/>
                    <i class="switch-handles ' . $statusClass . '"></i></label>';
                $td->appendElement('plaintext', array(), $str, true);
                break;
            case 'action':
                $ul = $td->appendElement("ul", array("class" => "actions actions centered"));
                if ($canEdit) {
                    //$li = $ul->appendElement("li");
                    $li = $ul->appendElement("li", array('class' => 'droplink'));
                    $li->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit', $adminLangId)), '<i class="ion-android-more-horizontal icon"></i>', true);
                    $innerDiv = $li->appendElement('div', array('class' => 'dropwrap'));
                    $innerUl = $innerDiv->appendElement('ul', array('class' => 'linksvertical'));
                    $innerLiEdit = $innerUl->appendElement('li');
                    $innerLiEdit->appendElement(
                            'a',
                            array(
                                'href' => 'javascript:void(0)', 'class' => 'button small green',
                                'title' => Label::getLabel('LBL_Edit', $adminLangId), "onclick" => "priceSlabForm(" . $row['prislab_id'] . ");"
                            ),
                            Label::getLabel('LBL_Edit', $adminLangId),
                            true
                    );
                }
                break;
            default:
                $td->appendElement('plaintext', array(), $row[$key], true);
                break;
        }
    }
}
if (empty($records)) {
    $tbl->appendElement('tr')->appendElement('td', array('colspan' => count($arr_flds)), Label::getLabel('LBL_No_Records_Found', $adminLangId));
}
echo $tbl->getHtml();
$postedData['page'] = $page;
echo FatUtility::createHiddenFormFromData($postedData, array('name' => 'priceSlabPagingForm'));
$pagingArr = array('pageCount' => $pageCount, 'page' => $page, 'pageSize' => $pageSize, 'recordCount' => $recordCount, 'adminLangId' => $adminLangId);
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
