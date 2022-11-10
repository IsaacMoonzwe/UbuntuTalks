<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); #step3?>
<div class="box box--checkout">
    <div class="box__head">
        <a href="javascript:void(0);" onclick="cart.proceedToStep({}, 'getSlotDuration');" class="btn btn--bordered color-black btn--back">
            <svg class="icon icon--back">
            <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#back'; ?>"></use>
            </svg>
            <?php echo Label::getLabel('LBL_BACK'); ?>
        </a>
        <h4><?php echo Label::getLabel('LBL_Select_Your_Lesson'); ?></h4>
        <div class="step-nav">
            <ul>
                <li class="step-nav_item is-completed"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_1'); ?></a> <span class="step-icon"></span></li>
                <li class="step-nav_item is-completed"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_2'); ?></a><span class="step-icon"></span></li>
                <li class="step-nav_item is-process"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_3'); ?></a></li>
                <li class="step-nav_item"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_4'); ?></a></li>
            </ul>
        </div>
    </div>
    <div class="box__body">
        <div class="checkout-title">
            <p><?php echo Label::getLabel('LBL_CHECKOUT_SLAB_TITLE'); ?></p>
            <p> <?php echo Label::getLabel('LBL_CHECKOUT_SLAB_DESCRIPTION'); ?></p>
        </div>
        <div class="selection-tabs selection--checkout selection--lesson selection--onethird">
            <?php
            $lessonQtyPrice = 0;
			#print_r($slabs);
            foreach ($slabs as $slab) {
                $price = FatUtility::float($slab['ustelgpr_price']);
                $percentage = CommonHelper::getPercentValue($slab['top_percentage'], $price);
                $price = $price - $percentage;
                $title = Label::getLabel('LBL_{min}_to_{max}_Lesson(s)');
                $title = str_replace(['{min}', '{max}'], [$slab['ustelgpr_min_slab'], $slab['ustelgpr_max_slab']], $title);
                if ($slab['ustelgpr_max_slab'] >= $lessonQty && $slab['ustelgpr_min_slab'] <= $lessonQty) {
                    $lessonQtyPrice = $price * $lessonQty;
                }
                ?>
                <label class="selection-tabs__label">
                    <input type="radio" class="selection-tabs__input" name="lessonQty-pack">
                    <div class="selection-tabs__title" style="line-height:25px;">
                        <?php 
                            if($slab['ustelgpr_slot'] == 180){
                        ?>
                        <span class="per-lesson">
                            <b style="text-transform: lowercase;"><?php echo " " ?></b>
                            <?php echo CommonHelper::displayMoneyFormat($price); ?>/ <?php echo Label::getLabel('LBL_Per_Course'); ?>
                        </span>
                            
                        <?php
                            } 
                            else{
                        ?>
                            <b style="text-transform: lowercase;"><?php echo $title;?></b>
                            <span class="per-lesson">
                                <?php echo CommonHelper::displayMoneyFormat($price); ?>/ <?php echo Label::getLabel('LBL_Per_Lesson'); ?>
                            </span>
                        <?php

                            }
                        ?>
                        <?php ?>
                        
                    </div>
                </label>
            <?php } ?>
        </div>
        <div class="total-price">
            <button class="btn btn--count" onclick="decrement()"><?php echo Label::getLabel('LBL_-'); ?></button>
            <input type="text" id="lessonQty" onchange="changeLessonQty();" name="lessonQty" min="<?php echo $minValue; ?>" max="<?php echo $maxValue; ?>" value="<?php echo $lessonQty; ?>">
            <button class="btn btn--count" onclick="increment();"><?php echo Label::getLabel('LBL_+'); ?></button>
            <button class="btn btn--primary color-white" onclick="cart.getLessonQtyPrice(document.getElementById('lessonQty').value);"><?php echo Label::getLabel('LBL_UPDATE_QTY'); ?></button>
            <p class="slab-price-js"><?php echo sprintf(Label::getLabel('LBL_TOTAL_PRICE_:_%s'), CommonHelper::displayMoneyFormat($lessonQtyPrice)); ?></p>
        </div>
    </div>
    <div class="box-foot">
        <div class="box-foot__left">
            <div class="teacher-profile">
                <div class="teacher__media">
                    <div class="avtar avtar-md">
                        <img src="<?php echo CommonHelper::generateUrl('Image', 'user', array($teacher['user_id'])) . '?' . time(); ?>" alt="><?php echo $teacher['user_first_name'] . ' ' . $teacher['user_last_name']; ?>">
                    </div>
                </div>
                <div class="teacher__name"><?php echo $teacher['user_first_name'] . ' ' . $teacher['user_last_name']; ?></div>
            </div>
            <div class="step-breadcrumb">
                <ul>
                    <li><a href="javascript:void(0);"><?php echo $teachLangName; ?></a></li>
                    <li><a href="javascript:void(0);"><?php echo sprintf(Label::getLabel('LBL_%s_Mins'), $lessonDuration); ?></a></li>
                </ul>
            </div>
        </div>
        <div class="box-foot__right">
            <a href="javascript:void(0);" onclick="cart.proceedToStep({}, 'getPaymentSummary');" class="btn btn--primary color-white"><?php echo LabeL::getLabel('LBL_NEXT'); ?></a>
        </div>
    </div>
</div>
<script>
    cart.props.lessonQty = parseInt('<?php echo $lessonQty; ?>');
    minLessonQty = parseInt(<?php echo $minValue; ?>);
    maxLessonQty = parseInt(<?php echo $maxValue; ?>);
    var lessonQtyInput = document.getElementById('lessonQty');
    function increment() {
        let qty = parseInt(lessonQtyInput.value);
        if (maxLessonQty > qty) {
            // ++cart.props.lessonQty;
            lessonQtyInput.value = ++qty;
        }
    }
    function decrement() {
        let qty = parseInt(lessonQtyInput.value);
        if (minLessonQty < qty) {
            // --cart.props.lessonQty;
            lessonQtyInput.value = --qty;
        }
    }
    function changeLessonQty() {
        let qty = lessonQtyInput.value;
        if (!$.Validation.getRule('integer').check(true, qty)) {
            lessonQtyInput.value = cart.props.lessonQty;
            return;
        }
        qty = parseInt(qty);
        if (maxLessonQty >= qty && minLessonQty <= qty) {
            lessonQtyInput.value = qty;
            return;
        } else {
            lessonQtyInput.value = cart.props.lessonQty;
        }
    }
</script>