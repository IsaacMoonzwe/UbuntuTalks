<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php

$tbl = new HtmlElement('table', array('width' => '100%', 'class' => 'table table-responsive'));
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($columnsArr as $val) {
    $e = $th->appendElement('th', array(), $val);
}
$sr_no = 0;
foreach ($arr_listing as $sn => $row) {
    $sr_no++;
    $tr = $tbl->appendElement('tr');
    $metaId = FatUtility::int($row['meta_id']);
    $recordId = FatUtility::int($row[$meta_record_id]);
    $tr->setAttribute("id", $metaId);
    foreach ($columnsArr as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'listserial':
                $td->appendElement('plaintext', array(), $sr_no);
                break;
            case 'has_tag_associated':
                if (!is_null($row['meta_id'])) {
                    $td->appendElement('plaintext', array(), '<svg height="25px" viewBox="0 0 512 512" width="25px" xmlns="http://www.w3.org/2000/svg"><path fill="green" d="m256 0c-141.164062 0-256 114.835938-256 256s114.835938 256 256 256 256-114.835938 256-256-114.835938-256-256-256zm129.75 201.75-138.667969 138.664062c-4.160156 4.160157-9.621093 6.253907-15.082031 6.253907s-10.921875-2.09375-15.082031-6.253907l-69.332031-69.332031c-8.34375-8.339843-8.34375-21.824219 0-30.164062 8.339843-8.34375 21.820312-8.34375 30.164062 0l54.25 54.25 123.585938-123.582031c8.339843-8.34375 21.820312-8.34375 30.164062 0 8.339844 8.339843 8.339844 21.820312 0 30.164062zm0 0"/></svg>', true);
                } else {
                    $td->appendElement('plaintext', array(), '');
                }
                break;
            case 'url':
                $td->appendElement('plaintext', array(), MetaTag::getOrignialUrlFromComponents($row));
                break;
            case 'action':
                $ul = $td->appendElement("ul", array("class" => "actions actions--centered"));
                if ($canEdit) {
                    $li = $ul->appendElement("li", array('class' => 'droplink'));
                    $li->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit', $adminLangId)), '<i class="ion-android-more-horizontal icon"></i>', true);
                    $innerDiv = $li->appendElement('div', array('class' => 'dropwrap'));
                    $innerUl = $innerDiv->appendElement('ul', array('class' => 'linksvertical'));
                    $innerLiEdit = $innerUl->appendElement('li');
                    $innerLiEdit->appendElement(
                            'a',
                            array(
                                'href' => 'javascript:void(0)', 'class' => 'button small green',
                                'title' => Label::getLabel('LBL_Edit', $adminLangId), "onclick" => "editMetaTagFormNew($metaId,'$metaType',$recordId)"
                            ),
                            Label::getLabel('LBL_Edit', $adminLangId),
                            true
                    );
                    if ($metaType == MetaTag::META_GROUP_OTHER) {
                        $innerLiEdit->appendElement(
                                'a',
                                array(
                                    'href' => 'javascript:void(0)', 'class' => 'button small green',
                                    'title' => Label::getLabel('LBL_Edit', $adminLangId), "onclick" => "deleteRecord($metaId)"
                                ),
                                Label::getLabel('LBL_Delete', $adminLangId),
                                true
                        );
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
    $tbl->appendElement('tr')->appendElement('td', array('colspan' => count($columnsArr)), Label::getLabel('LBL_No_Records_Found', $adminLangId));
}
echo $tbl->getHtml();
if (isset($pageCount)) {
    $postedData['page'] = $page;
    echo FatUtility::createHiddenFormFromData($postedData, array('name' => 'frmMetaTagSearchPaging'));
    $pagingArr = array('pageCount' => $pageCount, 'page' => $page, 'recordCount' => $recordCount, 'adminLangId' => $adminLangId);
    $this->includeTemplate('_partial/pagination.php', $pagingArr, false);
}
