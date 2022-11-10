<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php
$arr_flds = array(
    'dragdrop' => '',
    'listserial' => Label::getLabel('LBL_Sr._No', $adminLangId),
    'pmethod_identifier' => Label::getLabel('LBL_Payment_Method', $adminLangId),
    'pmethod_type' => Label::getLabel('LBL_Type', $adminLangId),
    'pmethod_active' => Label::getLabel('LBL_Status', $adminLangId),
    'action' => Label::getLabel('LBL_Action', $adminLangId),
);
if (!$canEdit) {
    unset($arr_flds['dragdrop']);
}
$tbl = new HtmlElement('table', array('width' => '100%', 'class' => 'table table--hovered table-responsive', 'id' => 'paymentMethod'));
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) {
    $e = $th->appendElement('th', array(), $val);
}
$paymentMethodType = PaymentMethods::getTypeArray();
$sr_no = 0;
foreach ($arr_listing as $sn => $row) {
    $sr_no++;
    $tr = $tbl->appendElement('tr', array('id' => $row['pmethod_id'], 'class' => ''));
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'dragdrop':
                if ($row['pmethod_active'] == applicationConstants::ACTIVE) {
                    $td->appendElement('i', array('class' => 'ion-arrow-move icon'));
                    $td->setAttribute("class", 'dragHandle');
                }
                break;
            case 'listserial':
                $td->appendElement('plaintext', array(), $sr_no);
                break;
            case 'pmethod_type':
                $td->appendElement('plaintext', array(), $paymentMethodType[$row['pmethod_type']]);
                break;
            case 'pmethod_active':
                $active = "active";
                $statucAct = '';
                if ($row['pmethod_active'] == applicationConstants::YES && $canEdit === true) {
                    $active = 'active';
                    $statucAct = 'inactiveStatus(this)';
                }
                if ($row['pmethod_active'] == applicationConstants::NO && $canEdit === true) {
                    $active = 'inactive';
                    $statucAct = 'activeStatus(this)';
                }
                $str = '<label id="' . $row['pmethod_id'] . '" class="statustab ' . $active . '" onclick="' . $statucAct . '">
				<span data-off="' . Label::getLabel('LBL_Active', $adminLangId) . '" data-on="' . Label::getLabel('LBL_Inactive', $adminLangId) . '" class="switch-labels status_' . $row['pmethod_id'] . '"></span>
				<span class="switch-handles"></span>
				</label>';
                $td->appendElement('plaintext', array(), $str, true);
                break;
            case 'pmethod_identifier':
                if ($row['pmethod_name'] != '') {
                    $td->appendElement('plaintext', array(), $row['pmethod_name'], true);
                    $td->appendElement('br', array());
                    $td->appendElement('plaintext', array(), '(' . $row[$key] . ')', true);
                } else {
                    $td->appendElement('plaintext', array(), $row[$key], true);
                }
                break;
            case 'action':
                $ul = $td->appendElement("ul", array("class" => "actions actions--centered"));
                if ($canEdit) {
                    $li = $ul->appendElement("li", array('class' => 'droplink'));
                    $li->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit', $adminLangId)), '<i class="ion-android-more-horizontal icon"></i>', true);
                    $innerDiv = $li->appendElement('div', array('class' => 'dropwrap'));
                    $innerUl = $innerDiv->appendElement('ul', array('class' => 'linksvertical'));
                    $innerLi = $innerUl->appendElement('li');
                    $innerLi->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit', $adminLangId), "onclick" => "editGatewayForm(" . $row['pmethod_id'] . ")"), Label::getLabel('LBL_Edit', $adminLangId), true);
                    if ($row['pmethod_type'] == PaymentMethods::TYPE_PAYMENT_METHOD_PAYOUT) {
                        $innerLi = $innerUl->appendElement('li');
                        $innerLi->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Transaction_Fee', $adminLangId), "onclick" => "getPaymentMethodFee(" . $row['pmethod_id'] . ")"), Label::getLabel('LBL_Method_Fee', $adminLangId), true);
                    }
                    if (strtolower($row['pmethod_code']) != strtolower(PaymentMethods::BANK_PAYOUT_KEY)) {
                        $innerLi = $innerUl->appendElement('li');
                        $innerLi->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Settings', $adminLangId), "onclick" => "settingsForm('" . $row['pmethod_code'] . "')"), Label::getLabel('LBL_Settings', $adminLangId), true);
                    }
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
<script>
    $(document).ready(function () {
        $('#paymentMethod').tableDnD({
            onDrop: function (table, row) {
                fcom.displayProcessing();
                var order = $.tableDnD.serialize('id');
                fcom.ajax(fcom.makeUrl('PaymentMethods', 'updateOrder'), order, function (res) {
                    var ans = $.parseJSON(res);
                    if (ans.status == 1)
                    {
                        fcom.displaySuccessMessage(ans.msg);
                    } else {
                        fcom.displayErrorMessage(ans.msg);
                    }
                });
            },
            dragHandle: ".dragHandle",
        });
    });
</script>
