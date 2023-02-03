<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); #step2  
?>
<style>
    .single_amount_wrapper {
        justify-content: center;
        display: flex;
    }

    .single_amount {
        color: #fff;
        background-color: #00641d;
        width: 100px;
        text-align: center;
        padding: 10px 0px 10px 0px;
        border-radius: 5px;
        display: block;
        margin-left: 10px;
        cursor: pointer;
        margin-bottom: 20px;
        font-size: 19px;
    }

    .single_amount:hover {
        background-color: #ce4400 !important;
    }

    .selection--onehalf .selection-tabs__label {
        -webkit-box-flex: 0;
        -ms-flex: 0 0 50%;
        flex: 0 0 100%;
        max-width: 100%;
    }

    .amount_wrapper {
        text-align: center;
        margin-top: 15px;
    }

    .other-titles {
        font-size: 20px;
        margin-bottom: 5px;
        font-weight: 600;
    }

    input[type="text"],
    input[type="number"] {
        width: 100%;
        padding: 1px 10px;
        height: 48px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 6px;
        box-shadow: 0px 2px 0px #d2d2d2;
    }

    input#donationAmount {
        width: 50%;
    }
</style>
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
            <h3 class="sponsorship-title"><?php echo Label::getLabel('LBL_Make_A_Donation'); ?></h3><br>
            <label class="selection-tabs__label">
                <div class="donation_wrapper">
                    <div class="single_amount_wrapper">
                        <div class="single_amount" value="50">50$</div>
                        <div class="single_amount" value="75">75$</div>
                        <div class="single_amount" value="100">100$</div>
                    </div>
                    <div class="single_amount_wrapper">
                        <div class="single_amount" value="150">150$</div>
                        <div class="single_amount" value="250">250$</div>
                        <div class="single_amount" value="500">500$</div>
                    </div>
                    <div class="amount_wrapper">
                        <div class="other-titles">Other</div>
                        <input type="number" class="" required="true" min="10.00" value=<?php echo $donationAmount; ?> id="donationAmount" name="donationAmount" placeholder="$0.00">
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
            <!-- <a href="javascript:void(0);" class="btn btn--primary color-white" onclick="GetEventDonationPaymentSummary(eventCart.props.donationAmount);"><?php echo Label::getLabel('LBL_NEXT'); ?></a> -->
            <a href="javascript:void(0);" class="btn btn--primary color-white" onclick="RegisterDonationEventUser(eventCart.props.donationAmount);"><?php echo Label::getLabel('LBL_NEXT'); ?></a>
        </div>
    </div>
</div>

<script>
    //Init link with input amount
    setTimeout(function() {
        $('.donation_wrapper > .amount_wrapper > input').trigger('change');
    }, 20);
    //Update link on change or input
    // $(document).on('change input', '.donation_wrapper > .amount_wrapper > input', function() {
    //     $(this).val(parseFloat($(this).val()).toFixed(2));
    //     $(this).parent().parent().find('> a').attr('href', donate_link.replace('{amount}', parseFloat($(this).val()).toFixed(2)));
    // });
    //Change amount on button click
    $(document).on('click', '.donation_wrapper > .single_amount_wrapper > .single_amount', function() {
        $('.donation_wrapper > .amount_wrapper > input').val(parseFloat($(this).attr('value')).toFixed(2)).trigger('change');
    });
    $('#donationAmount').change(function() {
        this.value = parseFloat(this.value).toFixed(2);
        var test = eventCart.props.donationAmount = this.value;
        console.log(test);
    });
</script>