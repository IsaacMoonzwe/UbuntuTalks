<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); #step2 
?>
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
                    <input type="radio" onchange="eventCart.props.selectSponserEventPlan=<?php echo $duration['events_sponsorship_categories_id']; ?>" class="selection-tabs__input" value="<?php echo $duration['events_sponsorship_categories_id']; ?>" <?php echo ($method == $duration['events_sponsorship_categories_id']) ? 'checked' : ''; ?> name="events_sponsorship_categories_plan_title" id="plan<?php echo $duration['events_sponsorship_categories_id']; ?>">
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
                    <!-- <div class="total-price sponsorship-plan" style="display: none;">
                        <button class="btn btn--count" onclick="decrement(<?php echo $duration['three_reasons_id']; ?>)"><?php echo Label::getLabel('LBL_-'); ?></button>
                        <input type="text" id="planQty<?php echo $duration['three_reasons_id']; ?>" onchange="changeLessonQty(<?php echo $duration['three_reasons_id']; ?>);" name="<?php echo $duration['three_reasons_id']; ?>" min="1" max="10" value="1">
                        <button class="btn btn--count" onclick="increment(<?php echo $duration['three_reasons_id']; ?>);"><?php echo Label::getLabel('LBL_+'); ?></button>
                        <button class="btn btn--primary color-white update-qty" onclick="eventCart.getLessonQtyPrice(document.getElementById('planQty'+<?php echo $duration['three_reasons_id']; ?>).value,<?php echo $duration['three_reasons_id']; ?>);"><?php echo Label::getLabel('LBL_UPDATE_QTY'); ?></button>

                    </div> -->
                </label>
            <?php } ?>


        </div>
    </div>
    <div class="box-foot">

        <div class="box-foot__right">
            <!-- <a href="javascript:void(0);" class="btn btn--primary color-white" onclick="GetEventBecomeSponserPaymentSummary(eventCart.props.becomesponserPlan,eventCart.props.becomeSponserPlanQty);"><?php echo Label::getLabel('LBL_NEXT'); ?></a> -->


            <a href="javascript:void(0);" class="btn btn--primary color-white" onclick="GetEventBecomeSponserPlan();"><?php echo Label::getLabel('LBL_NEXT'); ?></a>
        </div>
    </div>
</div>
<script>
    var selected = eventCart.props.selectSponserEventPlan;
    if(selected!=null){
        console.log("selectSponserEventPlan",selected);
        var check=parseInt(selected);
    document.getElementById("plan" + check).checked = true;
    }
//     let entries = Object.entries(eventCart.props.becomeSponserSelectedPlan);

//     if (entries.length > 0) {

//         entries.map(([key, val] = entry) => {
//             document.getElementById("plan" + val).checked = true;
//             addBecomeSponserSelectedEvent(val);
//         });
//     }

//     function addBecomeSponserSelectedEvent(id) {
//         const checkbox = document.getElementById("plan" + id);
        
//         console.log("chec==", checkbox);
        
//         if (checkbox.checked) {
//             // qtyBox.parentNode.style.display = 'block';
//             cartData = eventCart.props.becomeSponserSelectedPlan;
//             cartData[id] = id;
//             eventCart.props.becomeSponserSelectedPlan = cartData;
            
//     }
// }
</script>