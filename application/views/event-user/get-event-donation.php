<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); #step2 

?>
<div class="box box--checkout">
    <div class="box__head">
        <div class="step-nav">
            <ul>
                <li class="step-nav_item is-process"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_1'); ?></a><span class="step-icon"></span></li>
                <li class="step-nav_item"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_2'); ?></a></li>
                <li class="step-nav_item"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_3'); ?></a></li>
            </ul>
        </div>
    </div>
    <div class="box__body">
        <div class="selection-tabs selection--checkout selection--duration selection--onehalf">
            <h3 class="sponsorship-title"><?php echo Label::getLabel('LBL_Please_Fill_Donation_Amount'); ?></h3>
            <label class="selection-tabs__label">
                <h5><?php echo Label::getLabel('LBL_Fill_Your_Amount_($)'); ?></h5>
                <input type="number" class="" required="true" min="1" value=<?php echo $donationAmount; ?> id="donationAmount" name="donationAmount" placeholder="$0.00">
            </label>
            <!-- <?php foreach ($slotDurations as $duration) { ?>
                <label class="selection-tabs__label">
                    <input type="radio" onchange="eventCart.props.becomesponserPlan = this.value;" class="selection-tabs__input" value="<?php echo $duration['sponsorshipcategories_name']; ?>" <?php echo ($lessonDuration == $duration['sponsorshipcategories_name']) ? 'checked' : ''; ?> name="lessonDuration">
                        <div class="selection-tabs__title" >
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                <g>
                                    <path d="M12,22A10,10,0,1,1,22,12,10,10,0,0,1,12,22Zm-1-6,7.07-7.071L16.659,7.515,11,13.172,8.174,10.343,6.76,11.757Z" transform="translate(-2 -2)" />
                                </g>
                            </svg>
                            <span>
                                <?php echo sprintf($duration['sponsorshipcategories_name']); ?>
                            </span>
                        </div>
                </label>
            <?php } ?> -->

        </div>
    </div>
    <div class="box-foot">
        <div class="box-foot__left" style="display: none;">
            <div class="teacher-profile">
                <div class="teacher__media">
                    <div class="avtar avtar-md">
                        <img src="<?php echo CommonHelper::generateUrl('Image', 'user', array($teacher['user_id'])) . '?' . time(); ?>" alt="<?php echo $teacher['user_first_name'] . ' ' . $teacher['user_last_name']; ?>">
                    </div>
                </div>
                <div class="teacher__name"><?php echo $teacher['user_first_name'] . ' ' . $teacher['user_last_name']; ?></div>
            </div>
            <div class="step-breadcrumb">
                <ul>
                    <li><a href="javascript:void(0);"><?php echo $teachLangName; ?></a></li>
                </ul>
            </div>
        </div>
        <div class="box-foot__right">
            <!-- <a href="javascript:void(0);" class="btn btn--primary color-white" onclick="GetEventDonationPaymentSummary(eventCart.props.donationAmount);"><?php echo Label::getLabel('LBL_NEXT'); ?></a> -->
            <a href="javascript:void(0);" class="btn btn--primary color-white" onclick="RegisterDonationEventUser(eventCart.props.donationAmount);"><?php echo Label::getLabel('LBL_NEXT'); ?></a>
        </div>
    </div>
</div>
<script>
    $('#donationAmount').change(function() {
        this.value = parseFloat(this.value).toFixed(2);
        eventCart.props.donationAmount = this.value;
    });
</script>