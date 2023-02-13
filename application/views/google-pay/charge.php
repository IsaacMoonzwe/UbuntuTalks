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
  </style>
  <section class="section section--grey section--page -pattern">
    <div class="container container--fixed">
      <div class="page-panel -clearfix">
        <div class="page__panel-narrow">
          <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-8 col-md-10">
              <div class="box -padding-30 -skin">
              <a href="javascript:history.go(-1)" class="btn btn--bordered color-black btn--back Cartbackbtn">
                        <svg class="icon icon--back">
                            <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#back'; ?>"></use>
                        </svg>
                        <?php //echo Label::getLabel('LBL_BACK'); 
                        ?>
                    </a>
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
                  <div id="container"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script async src="https://pay.google.com/gp/p/js/pay.js" onload="onGooglePayLoaded()"></script>
  <script>
    /**
     * Define the version of the Google Pay API referenced when creating your
     * configuration
     *
     * @see {@link https://developers.google.com/pay/api/web/reference/request-objects#PaymentDataRequest|apiVersion in PaymentDataRequest}
     */
    const baseRequest = {
      apiVersion: 2,
      apiVersionMinor: 0
    };

    /**
     * Card networks supported by your site and your gateway
     *
     * @see {@link https://developers.google.com/pay/api/web/reference/request-objects#CardParameters|CardParameters}
     * @todo confirm card networks supported by your site and gateway
     */
    const allowedCardNetworks = ["AMEX", "DISCOVER", "INTERAC", "JCB", "MASTERCARD", "VISA"];

    /**
     * Card authentication methods supported by your site and your gateway
     *
     * @see {@link https://developers.google.com/pay/api/web/reference/request-objects#CardParameters|CardParameters}
     * @todo confirm your processor supports Android device tokens for your
     * supported card networks
     */
    const allowedCardAuthMethods = ["PAN_ONLY", "CRYPTOGRAM_3DS"];

    /**
     * Identify your gateway and your site's gateway merchant identifier
     *
     * The Google Pay API response will return an encrypted payment method capable
     * of being charged by a supported gateway after payer authorization
     *
     * @todo check with your gateway on the parameters to pass
     * @see {@link https://developers.google.com/pay/api/web/reference/request-objects#gateway|PaymentMethodTokenizationSpecification}
     */
    const tokenizationSpecification = {
      type: 'PAYMENT_GATEWAY',
      parameters: {
        'gateway': 'gestpay',
        'gatewayMerchantId': 'GESPAYXXXXX'
      }
    };

    /**
     * Describe your site's support for the CARD payment method and its required
     * fields
     *
     * @see {@link https://developers.google.com/pay/api/web/reference/request-objects#CardParameters|CardParameters}
     */
    const baseCardPaymentMethod = {
      type: 'CARD',
      parameters: {
        allowedAuthMethods: allowedCardAuthMethods,
        allowedCardNetworks: allowedCardNetworks
      }
    };

    /**
     * Describe your site's support for the CARD payment method including optional
     * fields
     *
     * @see {@link https://developers.google.com/pay/api/web/reference/request-objects#CardParameters|CardParameters}
     */
    const cardPaymentMethod = Object.assign({},
      baseCardPaymentMethod, {
        tokenizationSpecification: tokenizationSpecification
      }
    );

    /**
     * An initialized google.payments.api.PaymentsClient object or null if not yet set
     *
     * @see {@link getGooglePaymentsClient}
     */
    let paymentsClient = null;

    /**
     * Configure your site's support for payment methods supported by the Google Pay
     * API.
     *
     * Each member of allowedPaymentMethods should contain only the required fields,
     * allowing reuse of this base request when determining a viewer's ability
     * to pay and later requesting a supported payment method
     *
     * @returns {object} Google Pay API version, payment methods supported by the site
     */
    function getGoogleIsReadyToPayRequest() {
      return Object.assign({},
        baseRequest, {
          allowedPaymentMethods: [baseCardPaymentMethod]
        }
      );
    }

    /**
     * Configure support for the Google Pay API
     *
     * @see {@link https://developers.google.com/pay/api/web/reference/request-objects#PaymentDataRequest|PaymentDataRequest}
     * @returns {object} PaymentDataRequest fields
     */
    function getGooglePaymentDataRequest() {
      const paymentDataRequest = Object.assign({}, baseRequest);
      paymentDataRequest.allowedPaymentMethods = [cardPaymentMethod];
      paymentDataRequest.transactionInfo = getGoogleTransactionInfo();
      console.log(paymentDataRequest.transactionInfo);
      paymentDataRequest.merchantInfo = {
        // @todo a merchant ID is available for a production environment after approval by Google
        // See {@link https://developers.google.com/pay/api/web/guides/test-and-deploy/integration-checklist|Integration checklist}
        // merchantId: 'BCR2DN4T2SE4HXA7',
        // merchantName: 'UbuntuTalks'
        merchantId: '<?php echo $paymentSettings['merchant_Id']; ?>',
        merchantName: '<?php echo $paymentSettings['merchant_Name']; ?>,'
      };

      paymentDataRequest.callbackIntents = ["PAYMENT_AUTHORIZATION"];

      return paymentDataRequest;
    }

    /**
     * Return an active PaymentsClient or initialize
     *
     * @see {@link https://developers.google.com/pay/api/web/reference/client#PaymentsClient|PaymentsClient constructor}
     * @returns {google.payments.api.PaymentsClient} Google Pay API client
     */
    function getGooglePaymentsClient() {
      if (paymentsClient === null) {
        paymentsClient = new google.payments.api.PaymentsClient({
          environment: '<?php echo $paymentSettings['merchant_environment']; ?>',
          paymentDataCallbacks: {
            onPaymentAuthorized: onPaymentAuthorized
          }
        });
      }
      return paymentsClient;
    }

    /**
    * Handles authorize payments callback intents.
    *
    * @param {object} paymentData response from Google Pay API after a payer approves payment through user gesture.
    * @see {@link https://developers.google.com/pay/api/web/reference/response-objects#PaymentData object reference}
    *
    * @see {@link https://developers.google.com/pay/api/web/reference/response-objects#PaymentAuthorizationResult}
    * @returns Promise<{object}> Promise of PaymentAuthorizationResult object to acknowledge the payment authorization status.


    */
    function callback() {
      $.ajax({
        type: "post",
        url: '<?php echo  CommonHelper::generateFullUrl('GooglePay', 'payment') . '?orderId=' . $orderInfo["order_id"] . '&session_id=' . $session_id; ?>',
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

    function onPaymentAuthorized(paymentData) {
      return new Promise(function(resolve, reject) {
        // handle the response
        processPayment(paymentData)
          .then(function() {
            resolve({
              transactionState: 'SUCCESS'
            });
            console.log("SUCCESS");
            callback();
          })
          .catch(function() {
            resolve({
              transactionState: 'ERROR',
              error: {
                intent: 'PAYMENT_AUTHORIZATION',
                message: 'Insufficient funds, try again. Next attempt should work.',
                reason: 'PAYMENT_DATA_INVALID'
              }
            });
          });
      });
    }

    /**
     * Initialize Google PaymentsClient after Google-hosted JavaScript has loaded
     *
     * Display a Google Pay payment button after confirmation of the viewer's
     * ability to pay.
     */
    function onGooglePayLoaded() {
      const paymentsClient = getGooglePaymentsClient();
      paymentsClient.isReadyToPay(getGoogleIsReadyToPayRequest())
        .then(function(response) {
          if (response.result) {
            addGooglePayButton();
          }
        })
        .catch(function(err) {
          // show error in developer console for debugging
          console.error(err);
        });
    }

    /**
     * Add a Google Pay purchase button alongside an existing checkout button
     *
     * @see {@link https://developers.google.com/pay/api/web/reference/request-objects#ButtonOptions|Button options}
     * @see {@link https://developers.google.com/pay/api/web/guides/brand-guidelines|Google Pay brand guidelines}
     */
    function addGooglePayButton() {
      const paymentsClient = getGooglePaymentsClient();
      const button =
        paymentsClient.createButton({
          onClick: onGooglePaymentButtonClicked
        });
      document.getElementById('container').appendChild(button);
    }

    /**
     * Provide Google Pay API with a payment amount, currency, and amount status
     *
     * @see {@link https://developers.google.com/pay/api/web/reference/request-objects#TransactionInfo|TransactionInfo}
     * @returns {object} transaction info, suitable for use as transactionInfo property of PaymentDataRequest
     */
    function getGoogleTransactionInfo() {
      return {
        countryCode: 'US',
        currencyCode: "<?php echo $currency; ?>",
        totalPriceStatus: "FINAL",
        totalPrice: "<?php echo $paymentAmount; ?>",
        totalPriceLabel: "Total"
      };
    }


    /**
     * Show Google Pay payment sheet when Google Pay payment button is clicked
     */
    function onGooglePaymentButtonClicked() {
      const paymentDataRequest = getGooglePaymentDataRequest();
      paymentDataRequest.transactionInfo = getGoogleTransactionInfo();

      const paymentsClient = getGooglePaymentsClient();
      paymentsClient.loadPaymentData(paymentDataRequest);
    }

    let attempts = 0;
    /**
     * Process payment data returned by the Google Pay API
     *
     * @param {object} paymentData response from Google Pay API after user approves payment
     * @see {@link https://developers.google.com/pay/api/web/reference/response-objects#PaymentData|PaymentData object reference}
     */
    function processPayment(paymentData) {
      return new Promise(function(resolve, reject) {
        setTimeout(function() {
          // @todo pass payment token to your gateway to process payment
          paymentToken = paymentData.paymentMethodData.tokenizationData.token;

          if (attempts++ % 2 == 0) {
            reject(new Error('Every other attempt fails, next one should succeed'));
          } else {
            resolve({});
          }
        }, 500);
      });
    }
  </script>