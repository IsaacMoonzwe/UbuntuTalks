<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;0,1000;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900;1,1000&display=swap" rel="stylesheet">

    <!-- icon -->
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />
</head>

<body>
    <section class="equip section">
        <div class="container container--narrow">
            <div class="row">
                <div class="col-lg-8 ticket-information">
                    <div class="equip-banner">
                        <a href="#">
                            <img src="https://localhost:444/image/event-second-slider-campaign/1" class="img-fluid" alt="">
                        </a>
                    </div>
                    <form name="event_selection">
                        <div class="row">
                            <div class="col-lg-8">
                                <!-- <a href="javascript:history.go(-1)" class="btn btn--bordered color-black btn--back">
                                    <svg class="icon icon--back">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#back'; ?>"></use>
                                    </svg>
                                    <?php echo Label::getLabel('LBL_BACK'); ?>
                                </a> -->
                                <div class="equip-left">
                                    <div class="equip-title-box">
                                        <h5><?php echo $EventsList['registration_plan_title']; ?></h5>
                                        <!-- <p>by <a href="#" class="wpem-tooltiptext">
                                                <i class="fas fa-eye"></i> 116
                                                <span class="wpem-tooltiptext">120 people viewed this event.</span>
                                            </a><span class="wpem-event-type-text">Paid</span></p> -->
                                    </div>
                                    <div class="ticket-information-box">
                                        <div class="ticket-information-title">
                                            <h3><?php echo Label::getLabel('LBL_Ticket_Information'); ?></h3>
                                        </div>
                                        <div class="ticket-body">
                                            <div class="book-ticket">
                                                <span>
                                                    <h3><?php echo $EventsList['registration_plan_title']; ?></h3>
                                                    <p><?php echo "USD" . $EventsList['registration_plan_price']; ?></p>
                                                </span>
                                                <select class="form-control" name="ticketCount" onchange="eventCart.props.countOfTickets=parseInt(this.value);">
                                                    <?php for ($i = 1; $i <= 10; $i++) {
                                                        echo '<option value="' . $i . '">' . $i . '</option>';
                                                    } ?>

                                                </select>
                                                <input type="hidden" id="planId" name="planId" value=<?php echo $planId; ?> />
                                            </div>
                                        </div>
                                        <div>

                                            <div class="selection-title currencySwitcher">
                                                <p><?php echo Label::getLabel('LBL_Currency_Switcher'); ?></p>
                                            </div>
                                            <input type="hidden" name="code" id="code" />
                                            <select name="event_currencyswitchers" id="event_currencyswitchers">
                                                <?php foreach ($currencySwitcherResultData as $value) { ?>
                                                    <option data-curr=<?php echo $value['currencies_switcher_symbol_left']; ?> value="<?php echo $value['currencies_switcher_code']; ?>"><?php echo "(" . $value['currencies_switcher_symbol_left'] . ") " . $value['currencies_switcher_code']; ?></option>
                                                <?php } ?>
                                            </select>


                                        </div>
                                        <?php
                                        $date = $EventsList['registration_booking_endiing_date'];
                                        $current = date("Y-m-d");
                                        $endingDate = explode(" ", $date);
                                        $End = $endingDate[0];
                                        ?>
                                        <div class="btn-box">
                                            <?php if ($End > $current) { ?>
                                                <a onclick="GotToCart();" class="btn-green"><?php echo Label::getLabel('LBL_Order_Now'); ?></a>
                                            <?php } ?>
                                        </div>
                                        <!-- <div class="alert alert-warning" role="alert">
                                        This is a warning alert—check it out!
                                    </div>
                                    <div class="alert alert-success" role="alert">
                                        This is a success alert—check it out!
                                    </div> -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="equip-right">
                                    <div>
                                        <h3><?php echo Label::getLabel('LBL_Date_And_Time'); ?></h3>
                                        <p><?php echo $EventsList['registration_starting_date']; ?> To <br><?php echo $EventsList['registration_ending_date']; ?></p>
                                        <!-- <p></p>
                                        <p></p> -->
                                    </div>
                                    <div>
                                        <h3><?php echo Label::getLabel('LBL_Registration_End_Date'); ?></h3>
                                        <p>
                                            <?php
                                            $date = $EventsList['registration_booking_endiing_date'];
                                            $endingDate = explode(" ", $date);
                                            $End = $endingDate[0];
                                            echo $End;
                                            ?>
                                        </p>
                                    </div>
                                    <div>
                                        <h3><?php echo Label::getLabel('LBL_Location'); ?></h3>
                                        <a href="https://goo.gl/maps/uhUBqLm44vcMoCMN9">
                                            <p>Mulungushi International Conference Centre. Lusaka, Zambia</p>
                                        </a>
                                    </div>
                                    <div>
                                        <h3><?php echo Label::getLabel('LBL_Event_Types'); ?></h3>
                                        <p class="wpem-event-type-text">Ubuntu Talks Symposium & Benfit Concert</p>
                                    </div>
                                    <div>
                                        <h3><?php echo Label::getLabel('LBL_Share_With_Friends'); ?></h3>
                                        <div class="social-media-box">
                                            <a href="https://www.facebook.com/people/Ubuntu-Talks/100083215094567/">
                                                <img src="/image/editor-image/facebook.png" alt="">
                                            </a>
                                            <a href="#">
                                                <img src="/image/editor-image/twitter.png" alt="">
                                            </a>
                                            <a href="https://www.linkedin.com/company/79482248/">
                                                <img src="/image/editor-image/linkedin.png" alt="">
                                            </a>
                                            <a href="#">
                                                <img src="/image/editor-image/xing.png" alt="">
                                            </a>
                                            <a href="#">
                                                <img src="/image/editor-image/pintrest.png" alt="">
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </section>
    <!-- Separate Popper, Bootstrap JS and Custom JS -->
</body>
<script>
    var selectedPlan = '<?php echo $planId; ?>';
    console.log("sponsershipPlan==", selectedPlan);
    eventCart.props.sponsershipPlan = selectedPlan;

    function GoToEventCart() {
        //eventCart.props.sponsershipPlan,eventCart.props.countOfTickets
        console.log("eventCart.props.countOfTickets==", parseInt(eventCart.props.countOfTickets));
        window.location.href = fcom.makeUrl('EventUser', 'goToCart', [parseInt(eventCart.props.countOfTickets), selectedPlan, eventCart.props.currencyCode, eventCart.props.currency]);
        var data = "plan=" + eventCart.props.sponsershipPlan + "&ticketQty=" + eventCart.props.countOfTickets;

    }
    $(document).ready(function() {
        eventCart.props.currency = 'USD';
        eventCart.props.currencyCode = '$';
        $('#code').val('$');

        console.log("curr==", eventCart.props.currency);
        $('#code').val(eventCart.props.currencyCode);
        $('#event_currencyswitchers option').each(function() {
            var symbols = $(this).val();
            if (symbols == eventCart.props.currency) {
                $(this).attr("selected", "selected");
                var value = $('.cur_symbol').text();
                var symbol = $('option:selected').data('curr');
                console.log("old--", $('option:selected').data('curr'));
                var oldsymbol = symbol;
                eventCart.props.currencyCode = oldsymbol;
                $('.cur_symbol').text(oldsymbol);
            }
        })

    });
    $("#event_currencyswitchers").change(function() {
        var data = $(this).val();

        $.loader.show();

        if (data == 'ZMW') {
            console.log("data", data);
            $('#paypal-payment').parents('label').addClass('hide-section');
            $('#google-pay').parents('label').addClass('hide-section');
            $('.wallet-section').addClass('hide-section');
            eventCart.props.currencyCode = 'ZK';
            $('#code').val('ZK');

        } else {
            $('#paypal-payment').parents('label').removeClass('hide-section');
            $('#google-pay').parents('label').removeClass('hide-section');
            $('.wallet-section').removeClass('hide-section');
            $('#code').val('$');
            eventCart.props.currencyCode = '$';

        }
        $.loader.hide();
        console.log("currSymbol==", $(this).data('curr'))
        eventCart.props.currency = $(this).val();
        // updateCurrency(eventCart.props.currency);
        // window.location.href=window.location.href;
        // GetPlanTicketsPaymentSummary(eventCart.props.sponsershipPlan, eventCart.props.countOfTickets);
    });
</script>

</html>