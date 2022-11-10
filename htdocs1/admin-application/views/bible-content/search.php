<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php
$arr_flds = array(
    'dragdrop' => '',
    'listserial' => 'Sr no.',
    'biblecontent_title' => 'Title',
    'biblecontent_active' => 'Status',
    'action' => 'Action',
);
if (!$canEdit) {
    unset($arr_flds['dragdrop']);
}
$tbl = new HtmlElement('table', array('width' => '100%', 'class' => 'table table-responsive', 'id' => 'bibleList'));
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) {
    $e = $th->appendElement('th', array(), $val);
}
$sr_no = $page == 1 ? 0 : $pageSize * ($page - 1);
foreach ($arr_listing as $sn => $row) {
    $sr_no++;
    $inActive = '';
    if (!$row['biblecontent_active']) {
        $inActive = "inactive";
    }
    $tr = $tbl->appendElement('tr', array('class' => ''));
    $tr->setAttribute("id", $row['biblecontent_id']);
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'dragdrop':
                if ($row['biblecontent_active'] == applicationConstants::ACTIVE) {
                    $td->appendElement('i', array('class' => 'ion-arrow-move icon'));
                    $td->setAttribute("class", 'dragHandle');
                }
                break;
            case 'listserial':
                $td->appendElement('plaintext', array(), $sr_no);
                break;
            case 'biblecontent_title':
                $heading = substr($row[$key], 0, 30);
                $td->appendElement('plaintext', array(), $heading);
                break;
            case 'marketcontent_user_type':
                $td->appendElement('plaintext', array(), $userTypesArr[$row['marketcontent_user_type']]);
                break;
            case 'marketcontent_type':
                $td->appendElement('plaintext', array(), $marketContentTypes[$row[$key]]);
                break;
            case 'marketcontent_valid_from':
            case 'marketcontent_valid_till':
                if ($row[$key] != '') {
                    $td->appendElement('plaintext', array(), $row[$key], true);
                } else {
                    $td->appendElement('plaintext', array(), $row[$key], true);
                }
                break;
            case 'biblecontent_active':
                $active = "";
                if ($row['biblecontent_active']) {
                    $active = 'active';
                }
                $statucAct = ($canEdit === true) ? 'toggleStatus(this)' : '';
                $str = '<label id="' . $row['biblecontent_id'] . '" class="statustab ' . $active . '" onclick="' . $statucAct . '">
                      <span data-off="Inactive" data-on="Active" class="switch-labels"></span>
                      <span class="switch-handles"></span>
                    </label>';
                $td->appendElement('plaintext', array(), $str, true);
                break;
            case 'action':
                $ul = $td->appendElement("ul", array("class" => "actions"));
                if ($canEdit) {
                    $li = $ul->appendElement("li");
                    $li->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => 'Edit', "onclick" => "addForm(" . $row['biblecontent_id'] . ")"), '<i class="ion-edit icon"></i>', true);
                    $li = $ul->appendElement("li");
                    $li->appendElement('a', array('href' => "javascript:void(0)", 'class' => 'button small green', 'title' => 'Delete', "onclick" => "deleteRecord(" . $row['biblecontent_id'] . ")"), '<i class="ion-android-delete icon"></i>', true);
                }
                break;
            default:
                $td->appendElement('plaintext', array(), $row[$key], true);
                break;
        }
    }
}
if (count($arr_listing) == 0) {
    $tbl->appendElement('tr')->appendElement('td', array('colspan' => count($arr_flds)), 'No records found');
}
echo $tbl->getHtml();
$postedData['page'] = $page;
echo FatUtility::createHiddenFormFromData($postedData, array('name' => 'frmPagesSearchPaging'));
$pagingArr = array('pageCount' => $pageCount, 'page' => $page, 'recordCount' => $recordCount, 'adminLangId' => $adminLangId);
$this->includeTemplate('_partial/pagination.php', $pagingArr);
?>
<script>
    $(document).ready(function () {
        $('#bibleList').tableDnD({
            onDrop: function (table, row) {
                fcom.displayProcessing();
                var order = $.tableDnD.serialize('i d');
                fcom.ajax(fcom.makeUrl('BibleConten t', 'updateOrder'), order, function (res) {
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