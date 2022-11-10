<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$arr_flds = array(
    'listserial' => '#',
    'record_name' => Label::getLabel('LBL_Name', $adminLangId),
    'img' => Label::getLabel('LBL_Image', $adminLangId),
    'Language' => Label::getLabel('LBL_Language', $adminLangId),
    'action' => Label::getLabel('LBL_Action', $adminLangId),
);
if (!$canEdit) {
    unset($arr_flds['select_all'], $arr_flds['action']);
}
$tbl = new HtmlElement('table', array('width' => '100%', 'class' => 'table table-responsive'));
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $key => $val) {
    if ('select_all' == $key) {
        $th->appendElement('th')->appendElement('plaintext', array(), '<label class="checkbox"><input title="' . $val . '" type="checkbox" onclick="selectAll( $(this) )" class="selectAll-js"><i class="input-helper"></i></label>', true);
    } else {
        $e = $th->appendElement('th', array(), $val);
    }
}
$sr_no = $page == 1 ? 0 : $pageSize * ($page - 1);
foreach ($arr_listing as $sn => $row) {
    $sr_no++;
    $tr = $tbl->appendElement('tr');
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'select_all':
                $td->appendElement('plaintext', array(), '<label class="checkbox"><input class="selectItem--js" type="checkbox" name="record_ids[]" value=' . $row['record_id'] . '><i class="input-helper"></i></label>', true);
                break;
            case 'listserial':
                $td->appendElement('plaintext', array(), $sr_no);
                break;
            case 'img':
                switch ($imageAttributeType) {
                    case AttachedFile::FILETYPE_HOME_PAGE_BANNER:
                        $img = '<img src="' . CommonHelper::generateUrl('slides', 'slide', array($row['record_id'], 0, $row['afile_lang_id'], 'Thumb')) . '?' . time() . '" />';
                        break;
                    case AttachedFile::FILETYPE_BANNER:
                        $img = '<img src="' . CommonHelper::generateUrl('Banners', 'Thumb', array($row['record_id'])) . '?' . time() . '" />';
                        break;
                    case AttachedFile::FILETYPE_CPAGE_BACKGROUND_IMAGE:
                        $img = '<img src="' . CommonHelper::generateUrl('contentPages', 'cpageBackgroundImage', array($row['record_id'], 1, 'THUMB')) . '?' . time() . '" />';
                        break;
                    case AttachedFile::FILETYPE_TEACHING_LANGUAGES:
                        $img = '<img src="' . CommonHelper::generateUrl('TeachingLanguage', 'thumb', array($row['record_id'], $imageAttributeType, 0, 0)) . '?' . time() . '" />';
                        break;
                    case AttachedFile::FILETYPE_FLAG_TEACHING_LANGUAGES:
                        $img = '<img src="' . CommonHelper::generateUrl('TeachingLanguage', 'thumb', array($row['record_id'], $imageAttributeType, 0, 0)) . '?' . time() . '" />';
                        break;
                    case AttachedFile::FILETYPE_BLOG_POST_IMAGE:
                        $img = '<img src="' . CommonHelper::generateUrl('image', 'blogPostAdmin', array($row['record_id'], 0, 'THUMB', 0, $row['afile_id']), '/') . '?' . time() . '" />';
                        break;
                }
                $td->appendElement('plaintext', array(), $img, true);
                break;
            case 'Language':
                $td->appendElement('plaintext', array(), $langArr[$row['afile_lang_id']], true);
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
                                'title' => Label::getLabel('LBL_Edit', $adminLangId), "onclick" => "editImageAttributeForm(" . $row['afile_id'] . "," . $row['record_id'] . "," . $imageAttributeType . ")"
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
if (count($arr_listing) == 0) {
    $tbl->appendElement('tr')->appendElement('td', array('colspan' => count($arr_flds)), Label::getLabel('LBL_No_Records_Found', $adminLangId));
}
$frm = new Form('frmImgAttributeListing', array('id' => 'frmImgAttributeListing'));
$frm->setFormTagAttribute('class', 'web_form last_td_nowrap actionButtons-js');
$frm->setFormTagAttribute('onsubmit', 'formAction(this, reloadList ); return(false);');
$frm->setFormTagAttribute('action', CommonHelper::generateUrl('UrlRewriting', 'deleteSelected'));
$frm->addHiddenField('', 'status');
$frm->addHiddenField('', 'module_type', $imageAttributeType);
echo $frm->getFormTag();
echo $frm->getFieldHtml('status');
echo $tbl->getHtml();
?>
</form>
<?php
$postedData['page'] = $page;
echo FatUtility::createHiddenFormFromData($postedData, array('name' => 'frmImgAttrPaging'));
$pagingArr = array('pageCount' => $pageCount, 'page' => $page, 'recordCount' => $recordCount, 'adminLangId' => $adminLangId);
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
?>
<script>
    var moduleType = <?php echo $imageAttributeType; ?>;
</script>