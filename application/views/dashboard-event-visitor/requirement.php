<?php
$profileFrms->setFormTagAttribute('id', 'frmProfileInfoRequirementFrms');
$profileFrms->setFormTagAttribute('class', 'form form--horizontal');
$profileFrms->setFormTagAttribute('onsubmit', 'setUpProfileRequirementInfo(this, false); return(false);');
$profileFrms->developerTags['colClassPrefix'] = 'col-md-';
$profileFrms->developerTags['fld_default_col'] = 6;
$foodAllergiesNameField = $profileFrms->getField('user_food_allergies');
$foodAllergiesNameField->addFieldTagAttribute('placeholder', $foodAllergiesNameField->getCaption());
$otherFoodrestrictionField = $profileFrms->getField('user_other_food_restriction');
$otherFoodrequirementField = $profileFrms->getField('user_other_requirement');
$otherFoodrestrictionField->addFieldTagAttribute('placeholder', $otherFoodrestrictionField->getCaption());
// $dietField = $profileFrms->getField('user_food_department');
// $user_food_department = $profileFrms->getField('user_food_department');
// $user_food_department->setOptionListTagAttribute('class', 'diet-boxes');
$submitBtn = $frm->getField('btn_submit');
$submitBtn->setFieldTagAttribute('form', $frm->getFormTagAttribute('id'));


?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<div class="tab-content" id="myTabContent">
    <div class="dashboard-section">
        <div class="padding-6 events-tickets-section">
            <div class="max-width-80">
                <?php
                echo $profileFrms->getFormTag();
                // echo $profileFrms->getFieldHtml('user_phone_code');
                if ($profileFrms->getField('user_id')) {
                    echo $profileFrms->getFieldHtml('user_id');
                }
                ?>
                <div class="row">
                    <div class="col-md-12">
                        <h4 class="events-head-title"><?php echo Label::getLabel('LBL_Diet_Requirements'); ?></h4>
                    </div>
                </div>

                <div class="row diet-selection-section food-selection">
                    <div class="col-md-12">
                        <div class="field-set">
                            <div class="field-wraper">
                                <div class="field_cover">
                                    <div class="custom-cols custom-cols--onehal">
                                        <ul class="list-inline list-inline--onehalf">
                                            <?php foreach ($diet_data as $key => $value) {
                                                $user_food = $profileFrms->getField('user_food_department[' . $key . ']');
                                                $user_food->addFieldTagAttribute('data-lang-id', $key);
                                                $proficiencyKey = array_search($key, $foodData);
                                                $isLangSpeak = false;
                                                if ($proficiencyKey !== false) {
                                                    $proficiencyField->value = $userRow['user_food_department'][$proficiencyKey];
                                                    $isLangSpeak = true;
                                                }
                                            ?>
                                                <div class="diet-boxes">
                                                    <input type="checkbox" class="diet-boxes" value="<?php echo $key; ?>" name="<?php echo $user_food->getName(); ?>" <?php echo ($isLangSpeak) ? 'checked' : ''; ?>>
                                                    <span class="selection__trigger-label">
                                                        <?php echo $value; ?>
                                                    </span>
                                                </div>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <h4 class="events-head-title"><?php echo Label::getLabel('LBL_Food_Allergies'); ?></h4>
                    </div>
                </div>

                <div class="row diet-selection-section">
                    <div class="col-md-12">
                        <div class="field-set">
                            <div class="field-wraper">
                                <div class="field_cover">
                                    <div class="custom-cols custom-cols--onehal">
                                        <ul class="event-profile-name">
                                            <li class="event-first-name"><?php echo $foodAllergiesNameField->getHTML('user_food_allergies'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12">

                    </div>
                </div>

                <div class="row diet-selection-section">
                    <div class="col-md-12">
                        <h4 class="events-head-title"><?php echo Label::getLabel('LBL_Food_Restictions'); ?></h4>
                        <div class="field-set">
                            <div class="field-wraper">
                                <div class="field_cover">
                                    <div class="custom-cols custom-cols--onehal">
                                        <ul class="event-profile-name">
                                            <li class="event-first-name"><?php echo $otherFoodrestrictionField->getHTML('user_other_food_restriction'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row diet-selection-section">
                    <div class="col-md-12">
                        <h4 class="events-head-title"><?php echo Label::getLabel('LBL_Other_Requirements'); ?></h4>
                        <div class="field-set">
                            <div class="field-wraper">
                                <div class="field_cover">
                                    <div class="custom-cols custom-cols--onehal">
                                        <ul class="event-profile-name">
                                            <li class="event-first-name"><?php echo $otherFoodrequirementField->getHTML('user_other_requirement'); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-auto">
                        <div class="field-set">
                            <div class="field-wraper">
                                <div class="field_cover">
                                    <?php echo $profileFrms->getFieldHtml('btn_submit'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <?php echo $profileFrms->getExternalJS(); ?>
            </div>
        </div>
    </div>
</div>