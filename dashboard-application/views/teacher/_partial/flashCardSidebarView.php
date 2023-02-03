<?php
$frmSrchFlashCard->addFormTagAttribute('onsubmit', 'searchFlashCards(this); return false;');
$fldBtnSubmit = $frmSrchFlashCard->getField('btn_submit');
$keyword = $frmSrchFlashCard->getField('keyword');
$keyword->addFieldTagAttribute('onBlur', 'searchFlashCards(this.form); return false;');
;
echo $frmSrchFlashCard->getFormTag();
echo $frmSrchFlashCard->getFieldHtml('lesson_id');
echo $frmSrchFlashCard->getFieldHtml('page');
?>
<!-- [ Flashcard-search ========= -->
<div class="fcard-search">
    <div class="fcard-search__head">
        <h6><?php echo Label::getLabel('LBL_Flashcards'); ?></h6>
        <a  href="javascript:void(0);" onclick="flashCardForm(<?php echo $lessonRow['slesson_id'] ?>, 0);" class="color-secondary underline padding-top-3 padding-bottom-3 flash-card-add-js">  <?php echo Label::getLabel('LBL_Add'); ?></a>
    </div>
    <div class="fcard-search__body">    
        <?php echo $frmSrchFlashCard->getFieldHtml('keyword'); ?>
        <span class="form__action-wrap">
            <span class="svg-icon">
                <svg><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#search'; ?>"></use></svg>
            </span>
        </span>
    </div>
</div>
</form>
<!-- ] -->
<div id="flashCardListing">
</div>