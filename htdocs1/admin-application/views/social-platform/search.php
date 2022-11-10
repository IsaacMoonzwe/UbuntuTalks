<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="sectionhead" >
    <h4><?php echo Label::getLabel('LBL_Social_Platforms_Listing', $adminLangId); ?></h4>
    <?php
    if ($canEdit) {
        $ul = new HtmlElement("ul", array("class" => "actions actions--centered"));
        $li = $ul->appendElement("li", array('class' => 'droplink'));
        $li->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit', $adminLangId)), '<i class="ion-android-more-horizontal icon"></i>', true);
        $innerDiv = $li->appendElement('div', array('class' => 'dropwrap'));
        $innerUl = $innerDiv->appendElement('ul', array('class' => 'linksvertical'));
        $innerLiAddCat = $innerUl->appendElement('li');
        $innerLiAddCat->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Add_New_Social_Platform', $adminLangId), "onclick" => "addFormNew(0)"), Label::getLabel('LBL_Add_New_Social_Platform', $adminLangId), true);
        echo $ul->getHtml();
        ?>
    <?php } ?>
</div>
<div class="sectionbody">
    <div class="tablewrap" >
        <?php
        $arr_flds = array(
            'listserial' => Label::getLabel('LBL_Sr._No', $adminLangId),
            'splatform_identifier' => Label::getLabel('LBL_Title', $adminLangId),
            'splatform_url' => Label::getLabel('LBL_URL', $adminLangId),
            'splatform_active' => Label::getLabel('LBL_Status', $adminLangId),
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
            $tr = $tbl->appendElement('tr', array());
            /* $tr = $tbl->appendElement('tr',array('class' => ($row['splatform_active'] != applicationConstants::ACTIVE) ? 'fat-inactive' : '' )); */
            foreach ($arr_flds as $key => $val) {
                $td = $tr->appendElement('td');
                switch ($key) {
                    case 'listserial':
                        $td->appendElement('plaintext', array(), $sr_no);
                        break;
                    case 'splatform_identifier':
                        if ($row['splatform_title'] != '') {
                            $td->appendElement('plaintext', array(), $row['splatform_title'], true);
                            $td->appendElement('br', array());
                            $td->appendElement('plaintext', array(), '(' . $row[$key] . ')', true);
                        } else {
                            $td->appendElement('plaintext', array(), $row[$key], true);
                        }
                        break;
                    case 'splatform_active':
                        $active = "";
                        $statusAct = '';
                        if ($row['splatform_active'] == applicationConstants::YES && $canEdit === true) {
                            $active = 'checked';
                            $statusAct = 'inactiveStatus(this)';
                        }
                        if ($row['splatform_active'] == applicationConstants::NO && $canEdit === true) {
                            $active = 'unchecked';
                            $statusAct = 'activeStatus(this)';
                        }
                        $statusClass = ( $canEdit === false ) ? 'disabled' : '';
                        $str = '<label class="statustab -txt-uppercase">                 
                     <input ' . $active . ' type="checkbox" id="switch' . $row['splatform_id'] . '" value="' . $row['splatform_id'] . '" onclick="' . $statusAct . '" class="switch-labels status_' . $row['splatform_id'] . '"/>
                     <i class="switch-handles ' . $statusClass . '"></i></i></label>';
                        $td->appendElement('plaintext', array(), $str, true);
                        break;
                    case 'action':
                        $ul = $td->appendElement("ul", array("class" => "actions actions--centered"));
                        if ($canEdit) {
                            $li = $ul->appendElement("li", array('class' => 'droplink'));
                            $li->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit', $adminLangId)), '<i class="ion-android-more-horizontal icon"></i>', true);
                            $innerDiv = $li->appendElement('div', array('class' => 'dropwrap'));
                            $innerUl = $innerDiv->appendElement('ul', array('class' => 'linksvertical'));
                            $innerLiEdit = $innerUl->appendElement('li');
                            $innerLiEdit->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit', $adminLangId), "onclick" => "addFormNew(" . $row['splatform_id'] . ")"), Label::getLabel('LBL_Edit', $adminLangId), true);
                            //	$li = $ul->appendElement("li");
                            $innerLiDelete = $innerUl->appendElement('li');
                            $innerLiDelete->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green',
                                'title' => Label::getLabel('LBL_Delete', $adminLangId), "onclick" => "deleteRecord(" . $row['splatform_id'] . ")"), Label::getLabel('LBL_Delete', $adminLangId),
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
        ?>
    </div> 
</div>	