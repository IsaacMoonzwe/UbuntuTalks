<?php
require_once 'shared.php';

try {
  $paymentIntent = $stripe->paymentIntents->create([
    'payment_method_types' => ['card'],
    'amount' => 1999,
    'currency' => 'usd',
  ]);
} catch (\Stripe\Exception\ApiErrorException $e) {
  http_response_code(400);
  error_log($e->getError()->message);
?>
  <h1>Error</h1>
  <p>Failed to create a PaymentIntent</p>
  <p>Please check the server logs for more information</p>
<?php
  exit;
} catch (Exception $e) {
  error_log($e);
  http_response_code(500);
  exit;
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Google Pay</title>
  <link rel="stylesheet" href="css/base.css" />
  <script src="https://js.stripe.com/v3/"></script>
  <script src="./utils.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', async () => {
      // 1. Initialize Stripe
      const stripe = Stripe('pk_test_51JwGHMEBydRe3lMmSMnKBfxpsc6QoqlBI7vQMsj53qfdPSNNq97yVUHEpUaoeckkrFIx2aFVTH8YZdYpxQSrGcya00je6gTKLD', {
        apiVersion: '2020-08-27',
      });

      // 2. Create a payment request object
      var paymentRequest = stripe.paymentRequest({
        country: 'IN',
        currency: 'inr',
        total: {
          label: 'Demo total',
          amount: 1999,
        },
        requestPayerName: true,
        requestPayerEmail: true,
      });

      // 3. Create a PaymentRequestButton element
      const elements = stripe.elements();
      const prButton = elements.create('paymentRequestButton', {
        paymentRequest: paymentRequest,
      });

      // Check the availability of the Payment Request API,
      // then mount the PaymentRequestButton
      paymentRequest.canMakePayment().then(function(result) {
        if (result) {
          prButton.mount('#payment-request-button');
        } else {
          document.getElementById('payment-request-button').style.display = 'none';
          addMessage('Google Pay support not found. Check the pre-requisites above and ensure you are testing in a supported browser.');
        }
      });

      paymentRequest.on('paymentmethod', async (e) => {
        // Make a call to the server to create a new
        // payment intent and store its client_secret.
        addMessage(`Client secret returned.`);
        let clientSecret = '<?= $paymentIntent->client_secret; ?>';

        // Confirm the PaymentIntent without handling potential next actions (yet).
        let {
          error,
          paymentIntent
        } = await stripe.confirmCardPayment(
          clientSecret, {
            payment_method: e.paymentMethod.id,
          }, {
            handleActions: false,
          }
        );

        if (error) {
          addMessage(error.message);

          // Report to the browser that the payment failed, prompting it to
          // re-show the payment interface, or show an error message and close
          // the payment interface.
          e.complete('fail');
          return;
        }
        // Report to the browser that the confirmation was successful, prompting
        // it to close the browser payment method collection interface.
        e.complete('success');

        // Check if the PaymentIntent requires any actions and if so let Stripe.js
        // handle the flow. If using an API version older than "2019-02-11" instead
        // instead check for: `paymentIntent.status === "requires_source_action"`.
        if (paymentIntent.status === 'requires_action') {
          // Let Stripe.js handle the rest of the payment flow.
          let {
            error,
            paymentIntent
          } = await stripe.confirmCardPayment(
            clientSecret
          );
          if (error) {
            // The payment failed -- ask your customer for a new payment method.
            addMessage(error.message);
            return;
          }
          addMessage(`Payment ${paymentIntent.status}: ${paymentIntent.id}`);
        }

        addMessage(`Payment ${paymentIntent.status}: ${paymentIntent.id}`);
      });

    });
  </script>
</head>

<body>
  <main>
    <div id="payment-request-button">
      <!-- A Stripe Element will be inserted here if the browser supports this type of payment method. -->
    </div>

    <div id="messages" role="alert" style="display: none;"></div>
  </main>
</body>

</html>