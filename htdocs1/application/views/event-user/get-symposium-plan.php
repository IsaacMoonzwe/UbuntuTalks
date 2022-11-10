<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); #step2 
?>
<style>
    .selection-tabs__title {
        text-align: center;
        height: 100px;
    }

    .selection-tabs__title svg {
        margin-bottom: 15px;
    }

    .events-box-section .selection--checkout .selection-tabs__label .selection-tabs__title {
        padding: 35px 17px 35px 17px !important;
        display: flex;
        font-weight: 600;
        font-size: 15px !important;
    }

    .events-box-section .selection--onehalf .selection-tabs__label {
        max-width: 44% !important;
    }
</style>
<div class="box box--checkout">
    <div class="box__head">
        <div class="step-nav">
            <ul>
                <li class="step-nav_item is-process"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_1'); ?></a><span class="step-icon"></span></li>
                <li class="step-nav_item "><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_2'); ?></a></li>
                <li class="step-nav_item"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_3'); ?></a></li>
                <li class="step-nav_item"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_4'); ?></a></li>
            </ul>
        </div>
    </div>
    <div class="box__body events-box-section">
        <h3 class="tickets-title"><?php echo Label::getLabel('LBL_Select_Tickets'); ?></h3>
        <div class="selection-tabs selection--checkout selection--duration selection--onehalf">
            <?php
            $index=0;
            foreach ($slotDurations as $duration) {
                
                $newStrting = explode("Sales", $duration['pre_symposium_dinner_plan_title']);
            ?>
                <label class="selection-tabs__label">
                    <input type="radio" onchange="onPlanChange(this);" class="selection-tabs__input" value="<?php echo $duration['pre_symposium_dinner_plan_title']; ?>" <?php echo ($planSelected == $duration['pre_symposium_dinner_plan_title']) ? 'checked' : ''; ?> name="lessonDuration">
                    <div class="selection-tabs__title">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                <g>
                                    <path d="M12,22A10,10,0,1,1,22,12,10,10,0,0,1,12,22Zm-1-6,7.07-7.071L16.659,7.515,11,13.172,8.174,10.343,6.76,11.757Z" transform="translate(-2 -2)" />
                                </g>
                            </svg>
                        </div>
                        <div>
                            <span>
                                <?php echo sprintf($newStrting[0] . "- ($" . $duration['pre_symposium_dinner_plan_price'] . ")"); ?>
                            </span>
                        </div>
                    </div>
                </label>
            <?php }?>

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
            <!--  <a href="javascript:void(0);" class="btn btn--primary color-white" onclick="GetEventPaymentSummary(eventCart.props.sponsershipPlan);"><?php echo Label::getLabel('LBL_NEXT'); ?></a> -->
            <a href="javascript:void(0);" class="btn btn--primary color-white" onclick="GetSymposiumTickets(eventCart.props.symposiumPlan);"><?php echo Label::getLabel('LBL_NEXT'); ?></a>
        </div>
    </div>
</div>
<script>
    eventCart.props.symposiumPlan = "<?php echo $planSelected; ?>"
    function onPlanChange(plan){
        eventCart.props.symposiumPlan =plan.value;
        eventCart.props.symposiumTicket=1;
    }
</script>