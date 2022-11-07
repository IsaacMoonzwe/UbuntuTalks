<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('id', 'frmSettings');
$frm->setFormTagAttribute('class', 'form');
$frm->setFormTagAttribute('onsubmit', 'setUpTeacherSettings(this, false); return(false);');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 3;
$defaultSlot = FatApp::getConfig('conf_default_paid_lesson_duration', FatUtility::VAR_STRING, 60);
$lessonDurations = CommonHelper::getPaidLessonDurations();
$userTeachLangData = array_column($userToTeachLangRows, 'teachLangName', 'utl_id');
$updatePrice = $frm->getField('price_update');
$backBtn = $frm->getField('back_btn');
$backBtn->addFieldTagAttribute('onclick', "$('.teacher-lang-form-js').trigger('click');");
$nextBtn = $frm->getField('next_btn');
$nextBtn->addFieldTagAttribute('onclick', "setUpTeacherSettings(this.form, true); return(false);");
$placeHolder = CommonHelper::getPlaceholderForAmtField();
$defaultCurLbl = Label::getLabel('LBL_ENTER_AMOUNT_TO_BE_ADDED_[{currency-code}]');
$defaultCurLbl = str_replace('{currency-code}', CommonHelper::getSystemCurCode(), $defaultCurLbl);
?>
<div class="content-panel__head">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h5><?php echo Label::getLabel('LBL_Manage_Prices'); ?></h5>
            <p class="margin-0">
                <?php
                $labelText = '';
                if (!empty($slabDifference) && $priceSum > 0) {
                    $syncActionbtn = '<a href="javascript:void(0);" onclick="teacherSettingsForm(true);" class="color-secondary underline padding-top-3 padding-bottom-3 expand-js">' . Label::getLabel('LBL_SYNC_WITH_NEW') . '</a>';
                    $labelText = Label::getLabel('LBL_ADMIN_ADD_NEW_SLABS_TEXT_{sync-with-new-action}');
                    $labelText = str_replace('{sync-with-new-action}', $syncActionbtn, $labelText);
                }
                if ($showAdminSlab) {
                    $syncActionbtn = '<a href="javascript:void(0);" onclick="teacherSettingsForm(false);" class="color-secondary underline padding-top-3 padding-bottom-3 expand-js">' . Label::getLabel('LBL_VIEW_YOUR_EXISTING_SLABS') . '</a>';
                    $labelText = Label::getLabel('LBL_ADMIN_ADD_NEW_SLABS_TEXT_{sync-with-new-action}');
                    $labelText = str_replace('{sync-with-new-action}', $syncActionbtn, $labelText);
                }
                echo $labelText;
                ?>
            </p>
        </div>
        <div>
            <p class="color-secondary"><?php echo $defaultCurLbl; ?></p>
        </div>
    </div>
</div>
<div class="content-panel__body">
    <?php
    // if (!empty($userTeachLangData)) {
    echo $frm->getFormTag();
    echo $frm->getFieldHtml('showAdminSlab');
    ?>
    <div class="action-bar d-flex justify-content-center">
        <div class="selection-tabs">
            <?php
            $slectedDuration = [];
            foreach ($lessonDurations as $key => $value) {
            
                $durationFld = $frm->getField('duration[' . $value . ']');
               
                $durationFld->developerTags['noCaptionTag'] = true;
                $checkedStr = '';
                if ($durationFld->checked) {
                    $slectedDuration[$value] = $value;
                    $checkedStr = 'checked';
                }
                $dataFatreq = $durationFld->requirements()->getRequirementsArray();
                $dataFatreq = FatUtility::convertToJson($dataFatreq, JSON_HEX_QUOT);
                $dataFatreqChange = $durationFld->requirements()->getOnchangeRequirementUpdatesArray();
                $dataFatreqChange = FatUtility::convertToJson($dataFatreqChange, JSON_HEX_QUOT);
                ?>
                <label class="selection-tabs__label <?php echo ($defaultSlot == $value) ? 'selection-disabled' : ''; ?>">
                    <input type="checkbox" value="<?php echo $durationFld->value; ?>" onclick="updateSlots(this);" onchange="fatUpdateRequirement(this);" data-field-caption='<?php echo $durationFld->getCaption(); ?>' data-fat-req-change='<?php echo $dataFatreqChange; ?>' data-fatreq='<?php echo $dataFatreq; ?>' name="<?php echo $durationFld->getName(); ?>" <?php echo $checkedStr; ?> <?php echo ($defaultSlot == $value) ? 'disabled' : ''; ?> class="selection-tabs__input">
                    <div class="selection-tabs__title"><?php echo $value; ?><span><?php echo Label::getLabel('Lbl_Mins'); ?></span></div>
                </label>
            <?php }
            ?>
        </div>
    </div>
    <div class="">
        <!-- pricing-wrapper  -->
        <div class="form__body">
            <div class="row justify-content-center">
                <div class="col-md-12 col-lg-10 col-xl-12">
                    <?php foreach ($lessonDurations as $lessonDuration) {
                        ?>
                        <div class="price-box is--active <?php echo 'price-box-' . $lessonDuration . '-js'; ?> <?php echo (!array_key_exists($lessonDuration, $slectedDuration)) ? 'd-none' : ''; ?>">
                            <div class="price-box__head">
                                <div>
                                    <span><?php echo sprintf(Label::getLabel('LBL_Time_Slot_(%d_Mins)'), $lessonDuration); ?></span>
                                </div>
                                <div>
                                    <div class="common-slot-price d-flex align-items-center">
                                        <label class="field_label mb-0"><?php echo Label::getLabel('Lbl_add_price') ?></label>
                                        <?php
                                        $dataFatreq = $updatePrice->requirements()->getRequirementsArray();
                                        $dataFatreq = FatUtility::convertToJson($dataFatreq, JSON_HEX_QUOT);
                                        $name = $updatePrice->getName() . '_' . $lessonDuration;
                                        $updatePrice->addFieldTagAttribute('name', $name);
                                        ?>
                                        <input type="text" class="add-price-js" onchange="updatePrice(this, this.form);" data-field-caption='<?php echo $updatePrice->getCaption(); ?>' name='<?php echo $name; ?>' data-fatreq='<?php echo $dataFatreq; ?>' placeholder='<?php echo $placeHolder; ?>'>
                                    </div>
                                </div>
                                <span class="arrow-toggle is-active"></span>
                            </div>
                            <div class="price-box__body">
                                <?php foreach ($slabs as $slab) {
                                    $slab_data = array();
                                    if($lessonDuration == 60 && $slab['minMaxKey'] == '1-22'){
                                        array_push($slab_data,$slab);
                                    }
                                    if($lessonDuration == 90 && $slab['minMaxKey'] == '1-14'){
                                        array_push($slab_data,$slab);
                                    }
                                    if($lessonDuration == 120 && $slab['minMaxKey'] == '1-11'){
                                        array_push($slab_data,$slab);
                                    }
                                    if($lessonDuration == 180 && $slab['minMaxKey'] == '1-10'){
                                        array_push($slab_data,$slab);
                                    }
                                ?>
                                    <div class="slab-wrapper">
                                        <?php  foreach($slab_data as $slab_data_minwise){
                                            
                                        ?>
                                        <div class="slab__head">
                                            <h6><?php echo sprintf(Label::getLabel('LBL_Slab_%d_to_%d_Lessons'), $slab['minSlab'], $slab['maxSlab']) ?></h6>
                                        </div>
                                        <?php } ?>
                                        <div class="slab__body">
                                            <div class="row align-items-center">
                                                <?php
                                                 if($slab_data){                                   
                                                foreach ($userTeachLangData as $uTeachLangId => $uTeachLang) {
                                                        $filedName = 'ustelgpr_price[' . $lessonDuration . '][' . $slab['minMaxKey'] . '][' . $uTeachLangId . ']';
                                                    $priceField = $frm->getField($filedName);
                                                    $priceField->addFieldTagAttribute('class', 'slab-price-js');
                                                    $priceField->addFieldTagAttribute('placeholder', $placeHolder);
                                                   
                                                    ?>
                                                    <div class="col-12 col-md-4 col-sm-12 col-lg-4 col-xl-4">
                                                        <div class="field-wrapper">
                                                            <label class="field_label"><?php echo $uTeachLang; ?></label>
                                                            <?php echo $priceField->getHTML(); ?>
                                                        </div>
                                                    </div>
                                                <?php } }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php }
                    ?>
                </div>
            </div>
        </div>
        <div class="form__actions">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <?php echo $backBtn->getHTML(); ?>
                </div>
                <div>
                    <?php
                    echo $frm->getFieldHtml('submit');
                    echo $nextBtn->getHTML();
                    ?>
                </div>
            </div>
        </div>
    </div>
</form>
<?php
echo $frm->getExternalJs();
// } 
?>
</div>
<script>
    $(".price-box.is--active.price-box-180-js .slab__head h6").text("Per Course");
    var confirmLabel = `<?php echo Label::getLabel('Lbl_Are_You_Sure_To_Update_This_Price!'); ?>`;
    function updateSlots(slotFieldObj) {
        
        let slot = $(slotFieldObj).val();
        let isChecked = $(slotFieldObj).is(":checked");
        let slotSection = $('.price-box-' + slot + '-js');
        slotSection.find('.slab-price-js,.add-price-js').val('');
        if (isChecked) {
            $('.price-box__body').addClass('d-none');
            $('.price-box .arrow-toggle').removeClass('is-active');
            slotSection.removeClass('d-none');
            slotSection.find('.price-box__body').removeClass('d-none');
            slotSection.find('.arrow-toggle').addClass('is-active');
            $("html, body").animate({
                scrollTop: slotSection.offset().top
            }, 1200);
        } else {
            if (!confirm(langLbl.confirmRemove)) {
                $(slotFieldObj).prop('checked', true);
                return;
            }
            slotSection.addClass('d-none');
            slotSection.find('.arrow-toggle').removeClass('is-active');
        }
    }
    function updatePrice(fieldObj, form) {
        let price = $(fieldObj).val();
        let formValid = $(form).validate();
        if (!$.Validation.getRule('floating').check(true, price)) {
            if (!formValid)
                return;
        }
        price = parseFloat(price);
        if (1 > price) {
            return;
        }
        price = (price == '') ? 0.00 : price;
        price = parseFloat(price).toFixed(2);
        if (!confirm(confirmLabel))
            return;
        $(fieldObj).parents('.price-box').find('.slab-price-js').val(price);
        $(form).validate();
    }
    ;
    $(".price-box .arrow-toggle").click(function () {
        $(this).toggleClass('is-active');
        $(this).parents('.price-box').find('.price-box__body').toggleClass('d-none');
    });
</script>