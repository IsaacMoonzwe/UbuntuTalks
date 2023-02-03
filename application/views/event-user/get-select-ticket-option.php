<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<style>
    .selection-tabs__title {
        border-radius: 5px;
        box-shadow: rgb(0 0 0 / 10%) 0px 0px 8px;
    }

    .corporate-title h3.sponsorship-title {
        text-align: center;
    }

    .selection--checkout .selection-tabs__label .selection-tabs__title {
        text-align: center;
    }

    .selection--checkout .selection-tabs__label .selection-tabs__title svg {
        vertical-align: initial !important;
    }

    .input-number {
        width: 110px !important;
        padding: 0 12px !important;
        vertical-align: top !important;
        text-align: center !important;
        outline: none !important;
        font-size: 20px !important;
        color: #000 !important;
    }

    .input-number,
    .input-number-decrement,
    .input-number-increment {
        border: 1px solid #00641d !important;
        height: 50px !important;
        user-select: none !important;
    }

    .input-number-decrement,
    .input-number-increment {
        display: inline-block !important;
        width: 50px !important;
        line-height: 50px !important;
        background: #00641d !important;
        text-align: center !important;
        font-weight: bold !important;
        cursor: pointer !important;
    }

    .input-number-decrement:active,
    .input-number-increment:active {
        background: #ddd !important;
    }

    .input-number-decrement {
        border-radius: 4px 0 0 4px !important;
    }

    .input-number-increment {
        border-radius: 0 4px 4px 0 !important;
    }

    .number-controls {
        text-align: center;
    }

    .numbers-section {
        margin-top: 30px !important;
    }

    .numbers-section .fa {
        color: #FFF;
        font-size: 16px !important;
    }
</style>
<div class="box box--checkout">
    <div class="box__head">
        <a href="javascript:void(0);" onclick="GetSelectEventBecomeSponserPlan(1)" class="btn btn--bordered color-black btn--back">
            <svg class="icon icon--back">
                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#back'; ?>"></use>
            </svg>
            <?php echo Label::getLabel('LBL_BACK'); ?>
        </a>
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
        <div class="corporate-title">
            <h3 class="sponsorship-title"><?php echo $type; ?></h3>
        </div>
        <div class="selection-tabs selection--checkout selection--duration selection--onehalf sponsorship-tabs">
            <?php foreach ($CorporateplanResult as $value) { ?>
                <label class="selection-tabs__label">
                    <?php
                    if ($type == 'Corporate Donation') {
                    ?>
                        <span>
                            <?php echo $value['corporate_ticket_category_type'] . ' | ' . $value['corporate_ticket_discount'] . '% Dicsounts'; ?>
                        </span>
                    <?php } else {
                    ?>
                        <input type="radio" onchange="onCorporateTicketChange('<?php echo $value['corporate_ticket_id']; ?>');" class="selection-tabs__input" value="<?php echo $value['corporate_ticket_id']; ?>" <?php echo ($ticket == $value['corporate_ticket_id']) ? 'checked' : ''; ?> name="corporate_type_tickets" id="<?php echo $value['corporate_ticket_id']; ?>">
                        <div class="selection-tabs__title">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                <g>
                                    <path d="M12,22A10,10,0,1,1,22,12,10,10,0,0,1,12,22Zm-1-6,7.07-7.071L16.659,7.515,11,13.172,8.174,10.343,6.76,11.757Z" transform="translate(-2 -2)" />
                                </g>
                            </svg>
                            <span>
                                <?php echo $value['corporate_ticket_category_type'] . ' | ' . $value['corporate_ticket_discount'] . '% Dicsounts'; ?>
                            </span>
                        </div>
                    <?php  } ?>
                </label>
            <?php } ?>
        </div>
        <div>
            <?php if ($type == 'Corporate Donation') { ?>
                <div class="number-controls">
                    <h3><?php echo Label::getLabel('LBL_Select_The_Number_Of_Tickets'); ?></h3>
                    <div class="numbers-section">
                        <span class="input-number-decrement"><i class="fa fa-minus"></i></span>
                        <input class="input-number" required="true" type="text" value="1" min="1" id="countOfTickets" name="countOfTickets" placeholder="0">
                        <span class="input-number-increment"><i class="fa fa-plus"></i></span>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="box-foot">
        <div class="box-foot__right">
            <a href="javascript:void(0);" class="btn btn--primary color-white" onclick="RegisterCorporateEventUser(eventCart.props.selectCorporateEventPlan,eventCart.props.selectCorporateTicket);"><?php echo Label::getLabel('LBL_NEXT'); ?></a>
        </div>
    </div>
</div>
<script>
    var selected = eventCart.props.selectCorporateTicket;
    var corporateType = eventCart.props.selectCorporateEventPlan;
    if (selected != null && !corporateType.includes('Donation')) {
        console.log("selectSponserEventPlan", selected);
        var check = parseInt(selected);
        document.getElementById(selected).checked = true;
    } else if (corporateType.includes('Donation') && selected != null) {
        $('#countOfTickets').val(selected);
    } else if (corporateType.includes('Donation') && selected == null) {

        eventCart.props.selectCorporateTicket = 1;
    }

    function onCorporateTicketChange(value) {
        console.log("valu=ss=", value);
        eventCart.props.selectCorporateTicket = value;
    }
    $('#countOfTickets').change(function() {
        eventCart.props.selectCorporateTicket = this.value;
    });
    (function() {

        window.inputNumber = function(el) {

            var min = el.attr('min') || false;
            var max = el.attr('max') || false;

            var els = {};

            els.dec = el.prev();
            els.inc = el.next();

            el.each(function() {
                init($(this));
            });

            function init(el) {

                els.dec.on('click', decrement);
                els.inc.on('click', increment);

                function decrement() {
                    var value = el[0].value;
                    value--;
                    if (!min || value >= min) {
                        el[0].value = value;
                        eventCart.props.selectCorporateTicket = value;
                    }
                }

                function increment() {
                    var value = el[0].value;
                    console.log("valuee==", value);
                    value++;
                    if (!max || value <= max) {
                        el[0].value = value;
                        eventCart.props.selectCorporateTicket = value;
                    }
                }
            }
        }
    })();

    inputNumber($('.input-number'));
</script>