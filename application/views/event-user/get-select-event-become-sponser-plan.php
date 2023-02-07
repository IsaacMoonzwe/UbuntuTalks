<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); #step2 
?>
<style>
    .selection-tabs__title {
        border-radius: 5px;
        box-shadow: rgb(0 0 0 / 10%) 0px 0px 8px;
    }

    .corporate-title h3.sponsorship-title {
        text-align: center;
    }
</style>
<div class="box box--checkout">
    <div class="box__head">
        <div class="step-nav">
            <ul>
                <li class="step-nav_item is-process"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_1'); ?></a><span class="step-icon"></span></li>
                <li class="step-nav_item"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_2'); ?></a><span class="step-icon"></span></li>
                <li class="step-nav_item "><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_3'); ?></a></li>
                <li class="step-nav_item "><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_4'); ?></a></li>
            </ul>
        </div>
    </div>
    <div class="box__body">
        <div class="selection-tabs selection--checkout selection--duration selection--onehalf sponsorship-tabs">
            <h3 class="sponsorship-title">Which event would you like to sponsor ?</h3>
            <?php foreach ($slotDurations as $duration) {
                $newStrting = explode("Sales", $duration['events_sponsorship_categories_plan_title']);
            ?>
                <label class="selection-tabs__label">
                    <input type="radio" onchange="onSponserchange(<?php echo $duration['events_sponsorship_categories_id']; ?>)" class="selection-tabs__input" value="<?php echo $duration['events_sponsorship_categories_id']; ?>" <?php echo ($method == $duration['events_sponsorship_categories_id']) ? 'checked' : ''; ?> name="events_sponsorship_categories_plan_title" id="plan<?php echo $duration['events_sponsorship_categories_id']; ?>">
                    <div class="selection-tabs__title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                            <g>
                                <path d="M12,22A10,10,0,1,1,22,12,10,10,0,0,1,12,22Zm-1-6,7.07-7.071L16.659,7.515,11,13.172,8.174,10.343,6.76,11.757Z" transform="translate(-2 -2)" />
                            </g>
                        </svg>
                        <span>
                            <?php echo sprintf($newStrting[0]); ?>
                        </span>
                    </div>
                </label>
            <?php } ?>
        </div>
        <div class="corporate-title">
            <h3 class="sponsorship-title">Corporates Ticket Sponsor</h3>
        </div>
        <div class="selection-tabs selection--checkout selection--duration selection--onehalf sponsorship-tabs">
            <?php
            foreach ($CorporateplanResult as $value) {
                $label = explode(" ", $value['corporate_ticket_category_type']);
            ?>
                <label class="selection-tabs__label">
                    <input type="radio" onchange="onCorporateChange('<?php echo $value['corporate_ticket_category_type']; ?>');" class="selection-tabs__input" value="<?php echo $value['corporate_ticket_category_type']; ?>" <?php echo ($method == $value['corporate_ticket_id']) ? 'checked' : ''; ?> name="events_sponsorship_categories_plan_title" id="<?php echo $value['corporate_ticket_category_type']; ?>">
                    <div class="selection-tabs__title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                            <g>
                                <path d="M12,22A10,10,0,1,1,22,12,10,10,0,0,1,12,22Zm-1-6,7.07-7.071L16.659,7.515,11,13.172,8.174,10.343,6.76,11.757Z" transform="translate(-2 -2)" />
                            </g>
                        </svg>
                        <span>
                            <?php echo $value['corporate_ticket_category_type']; ?>
                        </span>
                    </div>
                </label>
            <?php } ?>
        </div>
    </div>
    <div class="box-foot">
        <div class="box-foot__right">
            <a href="javascript:void(0);" class="btn btn--primary color-white" onclick="GetEventBecomeSponserPlan();"><?php echo Label::getLabel('LBL_NEXT'); ?></a>
        </div>
    </div>
</div>
<script>
    var selected = eventCart.props.selectCorporateEventPlan;
    if (selected != null) {
        var check = parseInt(selected);
        document.getElementById(selected).checked = true;
    }

    function onCorporateChange(value) {
        eventCart.props.selectCorporateEventPlan = value;
        eventCart.props.selectCorporateTicket = null;
    }

    function onSponserchange(value) {
        eventCart.props.selectSponserEventPlan = value;
        eventCart.props.selectCorporateEventPlan = null;
    }
</script>