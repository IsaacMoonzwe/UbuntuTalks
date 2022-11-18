<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); #step2 
?>
<style>
    .selection-tabs__title {
        border-radius: 5px;
        box-shadow: rgb(0 0 0 / 10%) 0px 0px 8px;
    }
</style>
<div class="box box--checkout">
    <div class="box__head">
        <a href="javascript:void(0);" onclick="GetSelectEventBecomeSponserPlan();" class="btn btn--bordered color-black btn--back">
            <svg class="icon icon--back">
                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#back'; ?>"></use>
            </svg>
            <?php echo Label::getLabel('LBL_BACK'); ?>
        </a>
        <div class="step-nav">
            <ul>
                <li class="step-nav_item is-completed"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_1'); ?></a><span class="step-icon"></span></li>
                <li class="step-nav_item is-process"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_2'); ?></a><span class="step-icon"></span></li>
                <li class="step-nav_item "><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_3'); ?></a></li>
                <li class="step-nav_item "><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_4'); ?></a></li>
            </ul>
        </div>
    </div>
    <div class="box__body">
        <div class="selection-tabs selection--checkout selection--duration selection--onehalf sponsorship-tabs">
            <h3 class="sponsorship-title">Please Select Sponsorship Plan</h3>

            <?php foreach ($slotDurations as $duration) { ?>
                <label class="selection-tabs__label">
                    <input type="checkbox" onchange="addBecomeSponserPlan(<?php echo $duration['sponsorshipcategories_id']; ?>);" class="selection-tabs__input" value="<?php echo $duration['sponsorshipcategories_id']; ?>" <?php echo ($method == $duration['sponsorshipcategories_id']) ? 'checked' : ''; ?> name="<?php echo $duration['sponsorshipcategories_name']; ?>" id="plan<?php echo $duration['sponsorshipcategories_id']; ?>">
                    <div class="selection-tabs__title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                            <g>
                                <path d="M12,22A10,10,0,1,1,22,12,10,10,0,0,1,12,22Zm-1-6,7.07-7.071L16.659,7.515,11,13.172,8.174,10.343,6.76,11.757Z" transform="translate(-2 -2)" />
                            </g>
                        </svg>
                        <span>
                            <?php echo sprintf($duration['sponsorshipcategories_name']); ?>
                        </span>
                    </div>
                    <div class="total-price sponsorship-plan" style="display: none;">
                        <button class="btn btn--count" onclick="decrement(<?php echo $duration['sponsorshipcategories_id']; ?>)"><?php echo Label::getLabel('LBL_-'); ?></button>
                        <input type="text" id="planQty<?php echo $duration['sponsorshipcategories_id']; ?>" onchange="changeLessonQty(<?php echo $duration['sponsorshipcategories_id']; ?>);" name="<?php echo $duration['sponsorshipcategories_id']; ?>" min="1" max="10" value="1">
                        <button class="btn btn--count" onclick="increment(<?php echo $duration['sponsorshipcategories_id']; ?>);"><?php echo Label::getLabel('LBL_+'); ?></button>
                        <button class="btn btn--primary color-white update-qty" onclick="eventCart.getLessonQtyPrice(document.getElementById('planQty'+<?php echo $duration['sponsorshipcategories_id']; ?>).value,<?php echo $duration['sponsorshipcategories_id']; ?>);"><?php echo Label::getLabel('LBL_UPDATE_QTY'); ?></button>

                    </div>
                </label>
            <?php } ?>


        </div>
    </div>
    <div class="box-foot">

        <div class="box-foot__right">
            <!-- <a href="javascript:void(0);" class="btn btn--primary color-white" onclick="GetEventBecomeSponserPaymentSummary(eventCart.props.becomesponserPlan,eventCart.props.becomeSponserPlanQty);"><?php echo Label::getLabel('LBL_NEXT'); ?></a> -->


            <a href="javascript:void(0);" class="btn btn--primary color-white" onclick="RegisterEventUser(eventCart.props.becomesponserPlan,eventCart.props.becomeSponserPlanQty);"><?php echo Label::getLabel('LBL_NEXT'); ?></a>
        </div>
    </div>
</div>
<script>
    eventCart.props.lessonQty = parseInt('<?php echo $lessonQty; ?>');
    minLessonQty = 1;
    maxLessonQty = 10;

    function increment(id = 0) {
        var lessonQtyInput = document.getElementById('planQty' + id);

        let qty = parseInt(lessonQtyInput.value);
        if (maxLessonQty > qty) {
            // ++cart.props.lessonQty;
            lessonQtyInput.value = ++qty;
        }
    }

    function decrement(id = 0) {
        var lessonQtyInput = document.getElementById('planQty' + id);

        let qty = parseInt(lessonQtyInput.value);
        if (minLessonQty < qty) {
            // --cart.props.lessonQty;
            lessonQtyInput.value = --qty;
        }
    }

    function changeLessonQty(id = 0) {
        var lessonQtyInput = document.getElementById('planQty' + id);

        let qty = lessonQtyInput.value;
        if (!$.Validation.getRule('integer').check(true, qty)) {
            lessonQtyInput.value = eventCart.props.lessonQty;
            return;
        }
        qty = parseInt(qty);
        if (maxLessonQty >= qty && minLessonQty <= qty) {
            lessonQtyInput.value = qty;
            return;
        } else {
            lessonQtyInput.value = eventCart.props.lessonQty;
        }
    }
    let entries = Object.entries(eventCart.props.becomesponserPlan);

    if (entries.length > 0) {

        entries.map(([key, val] = entry) => {
            document.getElementById("plan" + val).checked = true;
            addBecomeSponserPlan(val);
        });
    }

    function addBecomeSponserPlan(id) {
        const checkbox = document.getElementById("plan" + id);
        const qtyBox = document.getElementById('planQty' + id);
        console.log("chec==", checkbox);
        console.log("qty==", qtyBox);
        if (checkbox.checked) {
            qtyBox.parentNode.style.display = 'block';
            cartData = eventCart.props.becomesponserPlan;
            cartData[id] = id;
            eventCart.props.becomesponserPlan = cartData;
            if (Object.keys(eventCart.props.becomeSponserPlanQty).length > 0) {
                if (id in eventCart.props.becomeSponserPlanQty) {
                    eventCart.props.becomeSponserPlanQty[id] = eventCart.props.becomeSponserPlanQty[id];
                    var lessonQtyInput = document.getElementById('planQty' + id);
                    lessonQtyInput.value = eventCart.props.becomeSponserPlanQty[id];
                } else {
                    eventCart.props.becomeSponserPlanQty[id] = minLessonQty;
                }
            } else {
                eventCart.props.becomeSponserPlanQty[id] = minLessonQty;
            }

        } else {
            if (id in eventCart.props.becomesponserPlan) {
                delete eventCart.props.becomesponserPlan[id];

            }
            if (Object.keys(eventCart.props.becomeSponserPlanQty).length > 0) {

                if (id in eventCart.props.becomeSponserPlanQty) {
                    delete eventCart.props.becomeSponserPlanQty[id];
                }
            }

            qtyBox.parentNode.style.display = 'none';
        }
    }
</script>