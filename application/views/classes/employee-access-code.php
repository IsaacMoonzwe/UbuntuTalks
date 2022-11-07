<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); 
    // $codeFrm->setFormTagAttribute('action', CommonHelper::generateUrl('Classes', 'codeSubmit'));
    // $codeFrm->setFormTagAttribute('onsubmit', 'codeSubmit(); return(false);');
    $codeFrm->setFormTagAttribute('onSubmit', 'codeSubmit(this); return(false);');
?>
<style>
@media (min-width: 1199px){
    #facebox .content.facebox-medium {
        min-width: 920px !important;
        max-width: 920px !important;
    }
}
</style>

    <div class="box box--checkout">
        <div class="box__head">
            <h4><?php echo Label::getLabel('LBL_EMPLOYEE_ACCESS_FORM'); ?></h4>
        </div>
        <div class="box__body">
            <div class="selection-tabs selection--checkout selection--language selection--onehalf kids-booking-form">
                <div class="contact-form">
                    <?php echo $codeFrm->getFormTag() ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label"><?php echo Label::getLabel('LBL_First_Name', $siteLangId) ?></label>
                                    </div>
                                    <div class="field-wraper">
                                        <div class="field_cover">
                                            <input type="text" name="fName" id="fName" value="<?php echo $fNames;?>" onfocusout="cart.addClassFirstName(document.getElementById('fName').value)" required/>
                                            <!-- <?php echo $codeFrm->getFieldHTML('firstname'); ?> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label"><?php echo Label::getLabel('LBL_Last_Name', $siteLangId) ?></label>
                                    </div>
                                    <div class="field-wraper">
                                        <div class="field_cover">
                                        <input type="text" name="lName" id="lName" value="<?php echo $lNames;?>" onfocusout="cart.addClassLastName(document.getElementById('fName').value,document.getElementById('lName').value)" required/>
                                            <!-- <?php echo $codeFrm->getFieldHTML('lastname'); ?> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                

                        <div class="row">
                            <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label"><?php echo Label::getLabel('LBL_Access_Code', $siteLangId) ?></label>
                                        </div>
                                            <div class="field-wraper">
                                                <div class="field_cover">
                                                    <input type="text" name="code" id="code" value="" required/>
                                                    <!-- <?php //echo $codeFrm->getFieldHTML('code'); ?> -->
                                                </div>
                                            </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                            <div class="col-md-6">
                                <div class="field-set">
                                    <div class="field-wraper">
                                        <div class="field_cover join-now-submit">
                                            <?php echo $codeFrm->getFieldHTML('btn_submit'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                        
                        <?php echo $codeFrm->getExternalJS(); ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
 