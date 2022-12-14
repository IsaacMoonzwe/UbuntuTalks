<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php
$arr_flds = array(
    'dragdrop' => '',
    'listserial' => Label::getLabel('LBL_Sr._No', $adminLangId),
    'bpcategory_identifier' => Label::getLabel('LBL_Category_Name', $adminLangId),
    'child_count' => Label::getLabel('LBL_Subcategories', $adminLangId),
    'bpcategory_active' => Label::getLabel('LBL_Status', $adminLangId),
    'action' => Label::getLabel('LBL_Action', $adminLangId),
);
if (!$canEdit) {
    unset($arr_flds['dragdrop']);
}
$tbl = new HtmlElement('table', array('width' => '100%', 'class' => 'table table-responsive table--hovered', 'id' => 'bpcategory'));
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) {
    $e = $th->appendElement('th', array(), $val);
}
$sr_no = 0;
foreach ($arr_listing as $sn => $row) {
    $sr_no++;
    $tr = $tbl->appendElement('tr');
    if ($row['bpcategory_active'] == applicationConstants::ACTIVE) {
        $tr->setAttribute("id", $row['bpcategory_id']);
    }
    if ($row['bpcategory_active'] != applicationConstants::ACTIVE) {
        $tr->setAttribute("class", " nodrag nodrop");
    }
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'dragdrop':
                if ($row['bpcategory_active'] == applicationConstants::ACTIVE) {
                    $td->appendElement('i', array('class' => 'ion-arrow-move icon'));
                    $td->setAttribute("class", 'dragHandle');
                }
                break;
            case 'listserial':
                $td->appendElement('plaintext', array(), $sr_no);
                break;
            case 'bpcategory_identifier':
                if ($row['bpcategory_name'] != '') {
                    $td->appendElement('plaintext', array(), $row['bpcategory_name'], true);
                    $td->appendElement('br', array());
                    $td->appendElement('plaintext', array(), '(' . $row[$key] . ')', true);
                } else {
                    $td->appendElement('plaintext', array(), $row[$key], true);
                }
                break;
            case 'child_count':
                if ($row[$key] == 0) {
                    $td->appendElement('plaintext', array(), $row[$key], true);
                } else {
                    $td->appendElement('a', array('href' => CommonHelper::generateUrl('BlogPostCategories', 'index', array($row['bpcategory_id'])), 'title' => Label::getLabel('LBL_View_Categories', $adminLangId)), $row[$key]);
                }
                break;
            case 'bpcategory_active':
                $active = "";
                if ($row['bpcategory_active']) {
                    $active = 'checked';
                }
                $statusAct = ( $canEdit === true ) ? 'toggleStatus(event,this,' . applicationConstants::YES . ')' : 'toggleStatus(event,this,' . applicationConstants::NO . ')';
                $statusClass = ( $canEdit === false ) ? 'disabled' : '';
                $str = '<label class="statustab -txt-uppercase">                 
                     <input ' . $active . ' type="checkbox" id="switch' . $row['bpcategory_id'] . '" value="' . $row['bpcategory_id'] . '" onclick="' . $statusAct . '" class="switch-Label"/>
                                      	<i class="switch-handles ' . $statusClass . '"></i></label>';
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
                    $innerLiEdit->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit', $adminLangId), "onclick" => "addCategoryForm(" . $row['bpcategory_id'] . ")"), Label::getLabel('LBL_Edit', $adminLangId), true);
                    $innerLiDelete = $innerUl->appendElement('li');
                    $innerLiDelete->appendElement('a', array('href' => "javascript:void(0)", 'class' => 'button small green', 'title' => Label::getLabel('LBL_Delete', $adminLangId), "onclick" => "deleteRecord(" . $row['bpcategory_id'] . ")"), Label::getLabel('LBL_Delete', $adminLangId), true);
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
echo FatUtility::createHiddenFormFromData($postedData, array(
    'name' => 'frmCatSearchPaging'
));
?>
<script>
    $(document).ready(function () {

        var pcat_id = $('#bpcategory_parent').val();
        $('#bpcategory').tableDnD({
            onDrop: function (table, row) {
                fcom.displayProcessing();
                var order = $.tableDnD.serialize('id');
                order += '&pcat_id=' + pcat_id;
                fcom.ajax(fcom.makeUrl('BlogPostCategories', 'updateOrder'), order, function (res) {
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