<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); #step2 
$remainTicket = $planResult['benefit_concert_avilable_tickets'] - $ticketManagerDetails['TotalTicket'];

?>
<style>
    .intro {
        display: none;
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

    .selection--onehalf .selection-tabs__label {
        -webkit-box-flex: 0;
        -ms-flex: 0 0 50%;
        flex: 0 0 100%;
        max-width: 100%;
    }
</style>
<div class="box box--checkout">
    <div class="box__head">
        <a href="javascript:void(0);" onclick="GetConcertPlan(1);" class="btn btn--bordered color-black btn--back">
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
                <div class="number-controls">
                    <h3><?php echo Label::getLabel('LBL_Select_The_Number_Of_Tickets'); ?></h3>
                    <!-- <input type="number" class="" required="true" min="1" value="<?php echo $tickets; ?>" id="countOfTickets" name="countOfTickets"> -->
                    <div class="numbers-section">
                        <span class="input-number-decrement"><i class="fa fa-minus"></i></span>
                        <input class="input-number" required="true" type="text" value="<?php echo $tickets; ?>" min="1" id="countOfTickets" name="countOfTickets">
                        <span class="input-number-increment"><i class="fa fa-plus"></i></span>
                    </div>
                </div>
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
            <a href="javascript:void(0);" id="next-class" class="btn btn--primary color-white" onclick="RegisterConcertUser(eventCart.props.concertPlan,eventCart.props.concertTicket);"><?php echo Label::getLabel('LBL_NEXT'); ?></a>
            <!-- <a href="javascript:void(0);" class="btn btn--primary color-white" onclick="GetEventTicketsPaymentSummary(eventCart.props.sponsershipPlan,eventCart.props.countOfTickets);"><?php echo Label::getLabel('LBL_NEXT'); ?></a> -->
        </div>
    </div>
</div>
<script>
    eventCart.props.concertPlan = "<?php echo $planSelected; ?>";
    var remian = "<?php echo $remainTicket; ?>"
    $('input[name=countOfTickets]').change(function() {
        console.log("hi");
        if (parseInt(remian) >= parseInt(this.value)) {
            eventCart.props.concertTicket = this.value;
            $("#next-class").removeClass("intro");
        } else {
            $.mbsmessage("You can select upto " + remian + " tickets for " + eventCart.props.concertPlan, true, "alert alert--danger");
            $("#next-class").addClass("intro");
        }
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
                eventCart.props.concertTicket = value;
            }
        }

        function increment() {
            var value = el[0].value;
            value++;
            if (!max || value <= max) {
                el[0].value = value;
                eventCart.props.concertTicket = value;
            }
        }
    }
}
})();

    inputNumber($('.input-number'));
</script>