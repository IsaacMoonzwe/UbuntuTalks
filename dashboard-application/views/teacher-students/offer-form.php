<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'form');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
$frm->setFormTagAttribute('onsubmit', 'setUpOffer(this); return(false);');
?>
<div class="box -padding-20">
    <?php //echo $frm->getFormHtml(); ?>
    <?php echo $frm->getFormTag(); ?>
    <h3 class="page-heading"><?php echo sprintf(Label::getLabel("LBL_Offer_percentage_for_%s"), CommonHelper::displayName($user_info['user_first_name'], " ", $user_info['user_last_name'])) ?></h3>
    <div class="row">
        <div class="col-sm-12">
            <div class="table-box-bordered box-signle-price">
                <h5><?php echo Label::getLabel("LBL_Lesson_Offer") ?></h5>
                <table class="table-pricing">
                    <tbody>
                        <?php foreach ($userSlots as $userSlot): ?>
                            <tr>
                                <td width="50%"><?php echo $frm->getField('top_percentage[' . $userSlot . ']')->getCaption() ?></td>
                                <td>
                                    <?php echo $frm->getFieldHtml('top_percentage[' . $userSlot . ']') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>	
                </table>
            </div>				
        </div>
    </div>
    <div class="row">
        <div class="fld_wrapper-js col-md-2">
            <div class="field-set">
                <div class="caption-wraper"><label class="field_label">&nbsp;</label></div>
                <div class="field-wraper">
                    <div class="field_cover">
                        <?php echo $frm->getFieldHtml('top_learner_id') ?>
                        <?php echo $frm->getFieldHtml('btn_submit') ?>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($isOfferSet): ?>
            <div class="fld_wrapper-js col-md-4">
                <div class="field-set">
                    <div class="caption-wraper"><label class="field_label">&nbsp;</label></div>
                    <div class="field-wraper">
                        <div class="field_cover">
                            <a href="javascript:;" class="btn btn--primary" onClick="return unlockOffer(<?php echo $user_info['user_id']; ?>);"><?php echo Label::getLabel('LBL_Unlock') ?></a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        </form>
        <?php echo $frm->getExternalJs(); ?>
    </div>
