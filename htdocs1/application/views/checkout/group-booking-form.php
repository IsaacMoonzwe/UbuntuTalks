<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="box box--checkout">
    <div class="box__head">
    <a href="javascript:void(0);" onclick="cart.proceedToStep({fromBack:1}, 'selectionBookingForm');" class="btn btn--bordered color-black btn--back">
                <svg class="icon icon--back">
                    <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#back'; ?>"></use>
                </svg>
                <?php echo Label::getLabel('LBL_BACK'); ?>
            </a>
        <h4><?php echo Label::getLabel('LBL_PRIVATE_CLASS_BOOKING_FORM'); ?></h4>
        <div class="step-nav">
            <ul>
                <li class="step-nav_item is-process"><a href="#"><?php echo Label::getLabel('LBL_1'); ?></a></li>
                <li class="step-nav_item"><a href="#"><?php echo Label::getLabel('LBL_2'); ?></a></li>
            </ul>
        </div>
    </div>
    <div class="box__body">
        <div class="selection-tabs selection--checkout selection--language selection--onehalf kids-booking-form">
            <div class="contact-form">
                <?php echo $contactFrm->getFormTag() ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label"><?php echo Label::getLabel('LBL_First_Name', $siteLangId) ?></label>
                                </div>
                                <div class="field-wraper">
                                    <div class="field_cover">
                                        <input type="text" name="fName" id="fName" value="<?php echo $fNames;?>" onfocusout="cart.addFirstName(document.getElementById('fName').value)" required/>
                                        <!-- <?php echo $contactFrm->getFieldHTML('firstname'); ?> -->
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
                                    <input type="text" name="lName" id="lName" value="<?php echo $lNames;?>" onfocusout="cart.addLastName(document.getElementById('fName').value,document.getElementById('lName').value)" required/>
                                        <!-- <?php echo $contactFrm->getFieldHTML('lastname'); ?> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php 
                                            $child_title = "Number of Employee";
                                            echo Label::getLabel($child_title, $siteLangId) 
                                        ?>
                                    </label>
                                </div>
                                <div class="field-wraper">
                                    <div class="field_cover">
                                        <?php $dropdown_child_title = "Select number of Employee"; //echo $contactFrm->getFieldHTML('child'); ?>
                                        <select name="child" id="child" onchange="cart.addKidsCount(document.getElementById('fName').value,document.getElementById('lName').value,document.getElementById('child').value)">
                                        <option value=1><?php echo Label::getLabel($dropdown_child_title, $siteLangId)?></option>
                                            <?php 
                                            
                                            for ($x = 1; $x <= $num_of_learners; $x++) {
                                                ?><option value=<?php echo $x; ?>><?php echo $x; ?></option>";
                                              <?php }
                                            
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php echo $contactFrm->getExternalJS(); ?>
                <!-- <input type="submit" value="<?php echo Label::getLabel('LBL_NEXT'); ?>"/> -->
            </form>
        </div>
    </div>
</div>
    <div class="box-foot">
        <div class="teacher-profile">
            <div class="teacher__media">
                <div class="avtar avtar-md">
                    <img src="<?php echo CommonHelper::generateUrl('Image', 'user', array($teacher['user_id'])) . '?' . time(); ?>" alt="<?php echo $teacher['user_first_name'] . ' ' . $teacher['user_last_name'] ?>">
                </div>
            </div>
            <div class="teacher__name"><?php echo $teacher['user_first_name'] . ' ' . $teacher['user_last_name']; ?></div>
        </div>
        <a href="javascript:void(0);" class="btn btn--primary color-white" onclick="cart.proceedToStep({fromGroup:1,teacherId: <?php echo $teacher['user_id']; ?>}, 'getPaymentSummary');"><?php echo Label::getLabel('LBL_NEXT'); ?></a>
    </div>
</div>
<script>
    cart.props.languageId = parseInt('<?php echo $languageId; ?>');
</script>