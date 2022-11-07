<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'web_form');
$frm->setFormTagAttribute('onsubmit', 'setupMethodFee(this); return(false);');
$frm->setFormTagAttribute('id', 'gatewayFeeForm');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 6;
$resetBtn = $frm->getField('btn_clear');
$resetBtn->addFieldTagAttribute('onClick', 'resetGatewayFeeForm()');
$feeTypeField = $frm->getField('pmtfee_type');
?>
<section class="section">
    <div class="sectionhead" id="pmFeesectionhead-js">
        <h4><?php echo Label::getLabel('LBL_Method_Fee_Setups', $adminLangId); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="row">
            <div class="col-sm-12">
                <div class="tabs_nav_container responsive flat">
                    <div class="tabs_panel_wrap">
                        <div class="tabs_panel" id="pmFeeForm-js">
                            <?php echo $frm->getFormHtml(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="pmFeeList-js">
        <div class="sectionhead">
            <h4><?php echo Label::getLabel('LBL_Method_Fee_Listing', $adminLangId); ?></h4>
        </div>
        <?php
        $arr_flds = array(
            'listserial' => Label::getLabel('LBL_Sr._No', $adminLangId),
            'currency_code' => Label::getLabel('LBL_Currency', $adminLangId),
            'pmtfee_fee' => Label::getLabel('LBL_Fee', $adminLangId),
            'action' => Label::getLabel('LBL_Action', $adminLangId),
        );
        $tbl = new HtmlElement('table', array('width' => '100%', 'class' => 'table table--hovered table-responsive', 'id' => 'paymentMethodFee'));
        $th = $tbl->appendElement('thead')->appendElement('tr');
        foreach ($arr_flds as $val) {
            $e = $th->appendElement('th', array(), $val);
        }
        $paymentMethodType = PaymentMethods::getTypeArray();
        $sr_no = 0;
        foreach ($arr_listing as $sn => $row) {
            $sr_no++;
            $tr = $tbl->appendElement('tr', array('id' => $row['pmtfee_pmethod_id'] . '-' . $row['pmtfee_currency_id'], 'class' => ''));
            foreach ($arr_flds as $key => $val) {
                $td = $tr->appendElement('td');
                switch ($key) {
                    case 'listserial':
                        $td->appendElement('plaintext', array(), $sr_no);
                        break;
                    case 'currency_code':
                        $txt = $row['currency_code'];
                        $txt .= (!empty($row['currency_name'])) ? ' (' . $row['currency_name'] . ')' : '';
                        $td->appendElement('plaintext', array(), $txt);
                        break;
                    case 'pmtfee_fee':
                        $fee = FatUtility::float($row['pmtfee_fee']) . "%";
                        if ($row['pmtfee_type'] == PaymentMethodTransactionFee::FEE_TYPE_FLAT) {
                            $fee = CommonHelper::displayMoneyFormat($row['pmtfee_fee'], true, true);
                        }
                        $td->appendElement('plaintext', array(), $fee);
                        break;
                    case 'action':
                        $ul = $td->appendElement("ul", array("class" => "actions actions--centered"));
                        if ($canEdit) {
                            $li = $ul->appendElement("li", array('class' => 'droplink'));
                            $li->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit', $adminLangId)), '<i class="ion-android-more-horizontal icon"></i>', true);
                            $innerDiv = $li->appendElement('div', array('class' => 'dropwrap'));
                            $innerUl = $innerDiv->appendElement('ul', array('class' => 'linksvertical'));
                            $innerLi = $innerUl->appendElement('li');
                            $innerLi->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit', $adminLangId), "onclick" => "editFeeForm(this," . $row['pmtfee_currency_id'] . "," . $row['pmtfee_fee'] . "," . $row['pmtfee_type'] . ")"), Label::getLabel('LBL_Edit', $adminLangId), true);
                        }
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
</section>
