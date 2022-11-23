<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php
$arr_flds = array(
    'dragdrop' => '',
    'listserial' => Label::getLabel('LBL_Sr._No', $adminLangId),
    'currencies_switcher_code' => Label::getLabel('LBL_Currency', $adminLangId),
    'currencies_switcher_symbol_left' => Label::getLabel('LBL_Symbol_Left', $adminLangId),
    // 'currencies_switcher_symbol_right' => Label::getLabel('LBL_Symbol_Right', $adminLangId),
    'currencies_switcher_active' => Label::getLabel('LBL_Status', $adminLangId),
    'action' => Label::getLabel('LBL_Action', $adminLangId),
);
if (!$canEdit) {
    unset($arr_flds['dragdrop']);
}
$tbl = new HtmlElement('table', array('width' => '100%', 'class' => 'table table--hovered table-responsive', 'id' => 'currencyList'));
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) {
    $e = $th->appendElement('th', array(), $val);
}
$sr_no = 0;
foreach ($arr_listing as $sn => $row) {
    $sr_no++;
    $tr = $tbl->appendElement('tr', array());
    $tr->setAttribute("id", $row['currencies_switcher_id']);
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'dragdrop':
                if ($row['currencies_switcher_active'] == applicationConstants::ACTIVE) {
                    $td->appendElement('i', array('class' => 'ion-arrow-move icon'));
                    $td->setAttribute("class", 'dragHandle');
                }
                break;
            case 'listserial':
                $td->appendElement('plaintext', array(), $sr_no);
                break;
            case 'currencies_switcher_symbol_left':
                $td->appendElement('plaintext', array(), $row[$key], true);
                break;
            case 'currencies_switcher_symbol_right':
                $td->appendElement('plaintext', array(), $row[$key], true);
                break;
            case 'currencies_switcher_active':
                $active = "active";
                $statucAct = '';
                $strTxt = Label::getLabel('LBL_Active', $adminLangId);
                if ($row['currencies_switcher_active'] == applicationConstants::YES) {
                    $active = 'active';
                    $statucAct = 'inactiveStatus(this)';
                }
                if ($row['currencies_switcher_active'] == applicationConstants::NO) {
                    $strTxt = Label::getLabel('LBL_Inactive', $adminLangId);
                    $active = 'inactive';
                    $statucAct = 'activeStatus(this)';
                }
                $disabledClass = "";
                if ($canEdit == false || $row['currencies_switcher_is_default'] == applicationConstants::YES) {
                    $disabledClass = "disabled-switch";
                    $statucAct = "";
                }
                $str = '<label id="' . $row['currencies_switcher_id'] . '" class="statustab ' . $active . ' ' . $disabledClass . '" onclick="' . $statucAct . '">
					<span data-off="' . Label::getLabel('LBL_Active', $adminLangId) . '" data-on="' . Label::getLabel('LBL_Inactive', $adminLangId) . '" class="switch-labels status_' . $row['currencies_switcher_id'] . '"></span>
					<span class="switch-handles"></span>
					</label>';
                $td->appendElement('plaintext', array(), $str, true);
                break;
            case 'currencies_switcher_code':
                if ($row['currencies_switcher_name'] != '') {
                    $td->appendElement('plaintext', array(), $row['currencies_switcher_name'], true);
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
                    $innerLi->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit', $adminLangId), "onclick" => "editCurrencyForm(" . $row['currencies_switcher_id'] . ")"), Label::getLabel('LBL_Edit', $adminLangId), true);
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
        $('#currencyList').tableDnD({
            onDrop: function (table, row) {
                fcom.displayProcessing();
                var order = $.tableDnD.serialize('id');
                fcom.ajax(fcom.makeUrl('CurrencySwitcher', 'updateOrder'), order, function (res) {
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