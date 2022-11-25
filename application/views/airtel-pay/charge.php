  <?php
  // echo "<pre>";
  //   print_r($GooglePay); 
  ?>
  <style>
    header.header.nav-up {
      z-index: 9999;
    }

    .footer .section-copyright .copyright {
      justify-content: center !important;
    }

    .footer .section-copyright .copyright p {
      color: #000;
      font-size: 16px;
    }

    .message-display {
      min-height: 10rem !important;
      margin-top: 0px !important;
    }

    p.paypal-description {
      font-size: 18px;
      color: #000;
    }

    .header__logo {
      text-align: center !important;
    }

    div#payment-request-button {
      width: 100%;
      height: auto;
    }

    #start-payment-button {
      cursor: pointer;
      position: relative;
      background-color: red;
      color: #fff;
      max-width: 30%;
      padding: 15px;
      font-weight: 600;
      font-size: 16px;
      border-radius: 10px;
      border: none;
      transition: all .1s ease-in;
      width: 100%;
    }
  </style>
  <section class="section section--grey section--page -pattern">
    <div class="container container--fixed">
      <div class="page-panel -clearfix">
        <div class="page__panel-narrow">
          <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8 col-md-10">
              <div class="box -padding-30 -skin">
                <div class="header__logo">
                  <a href="/">
                    <img src="https://ubuntutalks.com/image/site-logo/1" alt="Ubuntu Talks logo">
                  </a>
                </div>
                <div class="message-display">
                  <p class="paypal-description">
                    <?php echo Label::getLabel('LBL_Order_Invoice', $siteLangId); ?> : <strong><?php echo $orderInfo["order_id"]; /* displayNotApplicable($orderInfo["order_id"]) */ ?></strong>
                  </p>
                  <p class="paypal-description"><?php echo Label::getLabel('LBL_Payable_Amount', $siteLangId); ?> : <strong><?php echo $currencyCode; ?><?php echo number_format((float)$paymentAmount, 2, '.', ''); ?></strong> </p>
                  <?php
                  $paybleAmount = number_format((float)$paymentAmount, 2, '.', '');
                  ?>
                  <button type="button" id="start-payment-button" onclick="makePayment()">Pay Now</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <script src="https://checkout.flutterwave.com/v3.js"></script>
  <script>
    var origin = window.location.href;

    function makePayment() {
      FlutterwaveCheckout({
        public_key: "FLWPUBK_TEST-8ef458df28b5db9f7199f989f1158237-X",
        tx_ref: "FLWSECK_TESTa4e451cc9340",
        amount: '<?php echo $paybleAmount; ?>',
        currency: "<?php echo $currency; ?>",
        payment_options: "card, banktransfer, ussd",
        redirect_url: origin,
        //callback();
        meta: {
          consumer_id: 23,
          consumer_mac: "92a3-912ba-1192a",
        },
        customer: {
          email: "<?php echo $orderInfo['customer_email']; ?>",
          phone_number: "08102909304",
          name: "<?php echo $orderInfo['user_first_name']; ?>",
        },
        customizations: {
          title: "UbuntuTalks",
          description: "UbuntuTalks",
          logo: "https://ubuntutalks.com/image/site-logo/1",
        },
      });
    }


    let paramString = origin.split('?')[1];
    let second = paramString.split('&');
    let queryString = new URLSearchParams(paramString);
    if (second[0] == 'status=successful') {
      callback();
    }

    function callback() {
      $.ajax({
        type: "post",
        url: '<?php echo  CommonHelper::generateFullUrl('AirtelPay', 'payment') . '?orderId=' . $orderInfo["order_id"] . '&session_id=' . $session_id; ?>',
        data: {},
        dataType: "json",
        success: function(data) {
          // console.log('hii', data.redirectUrl);
          setTimeout(() => {
            window.location.href = data.redirectUrl;
          }, 500);
        }
      }, {
        fOutMode: 'json'
      });

    }
  </script>