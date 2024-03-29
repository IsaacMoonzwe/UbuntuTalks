<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<style type="text/css">
  body .section.section--grey.section--page.-pattern {
    background-color: #ffffff !important;
  }

  header.header .menu ul .menu__item a,
  .header-dropdown .header-dropdown__trigger {
    color: #000 !important;
    text-decoration: none;
  }

  .header__logo {
    text-align: center !important;
  }

  form {
    width: 30vw;
    min-width: 500px;
    align-self: center;
    box-shadow: 0px 0px 0px 0.5px rgba(50, 50, 93, 0.1),
      0px 2px 5px 0px rgba(50, 50, 93, 0.1), 0px 1px 1.5px 0px rgba(0, 0, 0, 0.07);
    border-radius: 7px;
    padding: 40px;
  }

  .hidden {
    display: none;
  }

  .footer {
    border: 1px solid #eee;
  }

  #payment-message {
    color: rgb(105, 115, 134);
    font-size: 16px;
    line-height: 20px;
    padding-top: 12px;
    text-align: center;
  }

  #payment-element {
    margin-bottom: 24px;
  }

  /* Buttons and links */
  button {
    background: #5469d4;
    font-family: Arial, sans-serif;
    color: #ffffff;
    border-radius: 4px;
    border: 0;
    padding: 12px 16px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    display: block;
    transition: all 0.2s ease;
    box-shadow: 0px 4px 5.5px 0px rgba(0, 0, 0, 0.07);
    width: 100%;
  }

  button:hover {
    filter: contrast(115%);
  }

  button:disabled {
    opacity: 0.5;
    cursor: default;
  }

  /* spinner/processing state, errors */
  .spinner,
  .spinner:before,
  .spinner:after {
    border-radius: 50%;
  }

  .spinner {
    color: #ffffff;
    font-size: 22px;
    text-indent: -99999px;
    margin: 0px auto;
    position: relative;
    width: 20px;
    height: 20px;
    box-shadow: inset 0 0 0 2px;
    -webkit-transform: translateZ(0);
    -ms-transform: translateZ(0);
    transform: translateZ(0);
  }

  .spinner:before,
  .spinner:after {
    position: absolute;
    content: "";
  }

  .spinner:before {
    width: 10.4px;
    height: 20.4px;
    background: #5469d4;
    border-radius: 20.4px 0 0 20.4px;
    top: -0.2px;
    left: -0.2px;
    -webkit-transform-origin: 10.4px 10.2px;
    transform-origin: 10.4px 10.2px;
    -webkit-animation: loading 2s infinite ease 1.5s;
    animation: loading 2s infinite ease 1.5s;
  }

  .spinner:after {
    width: 10.4px;
    height: 10.2px;
    background: #5469d4;
    border-radius: 0 10.2px 10.2px 0;
    top: -0.1px;
    left: 10.2px;
    -webkit-transform-origin: 0px 10.2px;
    transform-origin: 0px 10.2px;
    -webkit-animation: loading 2s infinite ease;
    animation: loading 2s infinite ease;
  }

  section.section.section--footer {
    display: none;
  }

  .message-display {
    min-height: 35rem !important;
    margin-top: 0px !important;
  }

  form#payment-form {
    padding: 10px !important;
    max-width: 100%;
  }

  @media (max-width: 991px) {
    .message-display form {
      width: 100% !important;
      min-width: 100% !important;
    }
  }
</style>
<section class="section section--grey section--page -pattern" style="background-color:#fff">
  <div class="container container--fixed">
    <div class="page-panel -clearfix">
      <div class="page__panel-narrow">
        <div class="row justify-content-center">
          <div class="col-xl-6 col-lg-8 col-md-10">
            <div class="box -padding-30 -skin">
              <div class="stipe-top-div">
                <div class="stipe-back-btn">
                  <a href="javascript:history.go(-1)" class="btn btn--bordered color-black btn--back Cartbackbtn">
                    <svg class="icon icon--back">
                      <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#back'; ?>"></use>
                    </svg>
                    <?php echo Label::getLabel('LBL_BACK'); ?>
                  </a>
                </div>
                <div class="header__logo">
                  <a href="/">
                    <img src="https://ubuntutalks.com/image/site-logo/1" alt="Ubuntu Talks logo">
                  </a>
                </div>
              </div>
              <div class="box__data">
              </div>
              <div class="message-display">
                <h3>Select Payment</h3>
                <!-- Display a payment form -->
                <form id="payment-form">
                  <div id="payment-element">
                    <!--Stripe.js injects the Payment Element-->
                  </div>
                  <button id="submit">
                    <div class="spinner hidden" id="spinner"></div>
                    <span id="button-text">Pay now</span>
                  </button>
                  <div id="payment-message" class="hidden"></div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<script>
</script>
<script src="https://js.stripe.com/v3/"></script>
<script type="text/javascript">
  //const stripe = Stripe("pk_live_51JwGHMEBydRe3lMmmahLFCh1YNTKub0lzgFJjhXDlDYD5Jt0LadbEhxKJ2vQKoRptpyXyVXt4Sax6C2gTlkPudQn00zyBXlhX3");

  const stripe = Stripe("<?php echo $stripe['publishable_key']; ?>");
 // console.log(stripe);

  // The items the customer wants to buy
  const items = [{
    id: <?php echo $paymentAmount; ?>
  }];
  let elements;

  initialize();
  checkStatus();

  document
    .querySelector("#payment-form")
    .addEventListener("submit", handleSubmit);

  // Fetches a payment intent and captures the client secret
  async function initialize() {
    const {
      clientSecret
    } = await fetch("<?php echo  CommonHelper::generateFullUrl('StripePay', 'create', [$currency]); ?>", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        items
      }),
    }).then((r) => r.json());

    elements = stripe.elements({
      clientSecret
    });

    const paymentElement = elements.create("payment");
    paymentElement.mount("#payment-element");
  }

  async function handleSubmit(e) {
    e.preventDefault();
    setLoading(true);

    const {
      error
    } = await stripe.confirmPayment({
      elements,
      confirmParams: {
        // Make sure to change this to your payment completion page
        return_url: "<?php echo  CommonHelper::generateFullUrl('StripePay', 'payment') . '?orderId=' . $orderId . '&session_id=' . $session_id; ?>",
      },
    });

    // This point will only be reached if there is an immediate error when
    // confirming the payment. Otherwise, your customer will be redirected to
    // your `return_url`. For some payment methods like iDEAL, your customer will
    // be redirected to an intermediate site first to authorize the payment, then
    // redirected to the `return_url`.
    if (error.type === "card_error" || error.type === "validation_error") {
      showMessage(error.message);
    } else {
      showMessage("An unexpected error occured.");
    }

    setLoading(false);
  }

  // Fetches the payment intent status after payment submission
  async function checkStatus() {
    const clientSecret = new URLSearchParams(window.location.search).get(
      "payment_intent_client_secret"
    );

    if (!clientSecret) {
      return;
    }

    const {
      paymentIntent
    } = await stripe.retrievePaymentIntent(clientSecret);

    switch (paymentIntent.status) {
      case "succeeded":
        showMessage("Payment succeeded!");
        break;
      case "processing":
        showMessage("Your payment is processing.");
        break;
      case "requires_payment_method":
        showMessage("Your payment was not successful, please try again.");
        break;
      default:
        showMessage("Something went wrong.");
        break;
    }
  }

  // ------- UI helpers -------
  function showMessage(messageText) {
    const messageContainer = document.querySelector("#payment-message");

    messageContainer.classList.remove("hidden");
    messageContainer.textContent = messageText;

    setTimeout(function() {
      messageContainer.classList.add("hidden");
      messageText.textContent = "";
    }, 4000);
  }

  // Show a spinner on payment submission
  function setLoading(isLoading) {
    if (isLoading) {
      // Disable the button and show a spinner
      document.querySelector("#submit").disabled = true;
      document.querySelector("#spinner").classList.remove("hidden");
      document.querySelector("#button-text").classList.add("hidden");
    } else {
      document.querySelector("#submit").disabled = false;
      document.querySelector("#spinner").classList.add("hidden");
      document.querySelector("#button-text").classList.remove("hidden");
    }
  }
</script>