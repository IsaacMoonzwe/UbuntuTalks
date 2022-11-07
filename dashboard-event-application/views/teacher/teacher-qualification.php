<div class="content-panel__head">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h5><?php echo Label::getLabel('LBL_Experience'); ?></h5>
        </div>
        <div><a href="javascript:void(0);" onclick="teacherQualificationForm(0);" class="btn btn--small btn--bordered color-secondary"><?php echo Label::getLabel('LBL_Add_New'); ?></a></div>
    </div>
</div>
<div class="content-panel__body">
    <div class="form">
        <div class="form__body padding-0">
            <div class="table-scroll">
                <table class="table table--bordered table--responsive">
                    <tr class="title-row">
                        <th><?php echo Label::getLabel('LBL_Resume_Information'); ?></th>
                        <th><?php echo Label::getLabel('LBL_Start/End'); ?></th>
                        <th><?php echo Label::getLabel('LBL_Attachment'); ?></th>
                        <th><?php echo Label::getLabel('LBL_Actions'); ?></th>
                    </tr>
                    <?php foreach ($qualificationData as $qualificationData) { ?>
                        <tr id="qualification-<?php echo $qualificationData['uqualification_id']; ?>">
                            <td>
                                <div class="flex-cell">
                                    <div class="flex-cell__label"><?php echo Label::getLabel('LBL_Resume_Information:'); ?></div>
                                    <div class="flex-cell__content">
                                        <div class="data-group">
                                            <span class="bold-600"><?php echo $qualificationData['uqualification_title']; ?></span><br>
                                            <span><?php echo Label::getLabel('LBL_Location') . ' - ' . $qualificationData['uqualification_institute_address']; ?></span><br>
                                            <span><?php echo Label::getLabel('LBL_Institution') . ' - ' . $qualificationData['uqualification_institute_name']; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="flex-cell">
                                    <div class="flex-cell__label"><?php echo Label::getLabel('LBL_Start/End'); ?></div>
                                    <div class="flex-cell__content"><?php echo $qualificationData['uqualification_start_year']; ?> - <?php echo $qualificationData['uqualification_end_year']; ?></div>
                                </div>
                            </td>
                            <td>
                                <div class="flex-cell">
                                    <div class="flex-cell__label"><?php echo Label::getLabel('LBL_Attachment'); ?></div>
                                    <div class="flex-cell__content">
                                        <?php
                                        if (empty($qualificationData['afile_name'])) {
                                            echo CommonHelper::displayNotApplicable('');
                                        } else {
                                            ?>
                                            <a href="<?php echo CommonHelper::generateFullUrl('Teacher', 'qualificationFile', array(0, $qualificationData['uqualification_id'])) ?>" download="<?php echo $qualificationData['afile_name']; ?>" target="_blank" class="attachment-file">
                                                <svg class="icon icon--issue icon--attachement icon--small color-primary">
                                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#attach' ?>"></use>
                                                </svg>
                                                <?php echo $qualificationData['afile_name']; ?>
                                            </a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="flex-cell">
                                    <div class="flex-cell__label"><?php echo Label::getLabel('LBL_Actions'); ?></div>
                                    <div class="flex-cell__content">
                                        <div class="actions-group">
                                            <a href="javascript:void(0);" onclick="teacherQualificationForm('<?php echo $qualificationData['uqualification_id']; ?>');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                                <svg class="icon icon--issue icon--small">
                                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#edit'; ?>"></use>
                                                </svg>
                                                <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_Edit'); ?></div>
                                            </a>
                                            <a href="javascript:void(0);" onclick="deleteTeacherQualification('<?php echo $qualificationData['uqualification_id']; ?>');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                                <svg class="icon icon--issue icon--small">
                                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#trash'; ?>"></use>
                                                </svg>
                                                <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_Delete'); ?></div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
        <div class="form__actions">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <input type="button" onclick="$('.teacher-tech-lang-price-js').trigger('click');" value="<?php echo label::getLabel('LBL_back'); ?>">
                </div>
                <div>
                    <input type="button" value="<?php echo label::getLabel('LBL_next'); ?>" onclick="$('.teacher-preferences-js').trigger('click');">
                </div>
            </div>
        </div>
    </div>
</div>