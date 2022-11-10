<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); #step2 
$remainTicket=$planResult['pre_symposium_dinner_avilable_tickets']-$ticketManagerDetails['TotalTicket'];

?>
<style>
    .intro{display: none;}
</style>
<div class="box box--checkout">
    <div class="box__head">
        <a href="javascript:void(0);" onclick="GetSymposiumPlan(1);" class="btn btn--bordered color-black btn--back">
            <svg class="icon icon--back">
                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#back'; ?>"></use>
            </svg>
            <?php echo Label::getLabel('LBL_BACK'); ?>
        </a>
        <div class="step-nav">
            <ul>
                <li class="step-nav_item is-completed"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_1'); ?></a><span class="step-icon"></span></li>
                <li class="step-nav_item is-process"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_2'); ?></a><span class="step-icon"></span></li>
                <li class="step-nav_item"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_3'); ?></a></li>
                <li class="step-nav_item"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_4'); ?></a></li>
            </ul>
        </div>
    </div>
    <div class="box__body">
        <div class="selection-tabs selection--checkout selection--duration selection--onehalf">
            <label class="selection-tabs__label">
                <h5><?php echo Label::getLabel('LBL_Select_Number_Of_Tickets'); ?></h5>
                <input type="number" class="" required="true" min="1" value="<?php echo $tickets; ?>" id="countOfTickets" name="countOfTickets">
            </label>


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
            <a href="javascript:void(0);" id="next-class" class="btn btn--primary color-white" onclick="RegisterSymposiumUser(eventCart.props.symposiumPlan,eventCart.props.symposiumTicket);"><?php echo Label::getLabel('LBL_NEXT'); ?></a>
            <!-- <a href="javascript:void(0);" class="btn btn--primary color-white" onclick="GetEventTicketsPaymentSummary(eventCart.props.sponsershipPlan,eventCart.props.countOfTickets);"><?php echo Label::getLabel('LBL_NEXT'); ?></a> -->
        </div>
    </div>
</div>
<script>
    eventCart.props.symposiumPlan = "<?php echo $planSelected; ?>";
    var remian="<?php echo $remainTicket; ?>"
    $('#countOfTickets').change(function() {
        if(parseInt(remian)>=parseInt(this.value)){
        eventCart.props.symposiumTicket = this.value;
        $("#next-class").removeClass("intro");
        }
        else{
            $.mbsmessage("You can select upto " + remian +" tickets for " +eventCart.props.concertPlan  , true, "alert alert--danger" );
            $("#next-class").addClass("intro");
        }
    });
</script>