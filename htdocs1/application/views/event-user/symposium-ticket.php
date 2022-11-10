<?php
defined('SYSTEM_INIT') or exit('Invalid Usage.');
?>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
<link rel="stylesheet" href="https://unpkg.com/@coreui/coreui/dist/css/coreui.min.css" />
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> -->
<style>
  body {
    overflow: hidden;
  }

  .loader {
    border: 16px solid green;
    border-radius: 50%;
    border-top: 16px solid #3498db;
    width: 120px;
    height: 120px;
    -webkit-animation: spin 2s linear infinite;
    /* Safari */
    animation: spin 2s linear infinite;
  }

  /* Safari */
  @-webkit-keyframes spin {
    0% {
      -webkit-transform: rotate(0deg);
    }

    100% {
      -webkit-transform: rotate(360deg);
    }
  }

  @keyframes spin {
    0% {
      transform: rotate(0deg);
    }

    100% {
      transform: rotate(360deg);
    }
  }

  .card-header.bg-info img {
    text-align: center;
    justify-content: center;
    align-items: center;
  }

  .card-header.bg-info img {
    width: 200px;
    height: auto;
    border-radius: 5px;
    object-fit: cover;
  }

  .event-image-ticket {
    width: 600px;
    height: auto;
  }

  .bg-info {
    background: rgb(206, 68, 0) !important;
    background: radial-gradient(circle, rgba(206, 68, 0, 1) 0%, rgba(0, 99, 19, 1) 100%) !important;
  }

  .events-tickets-description span {
    font-size: 17px;
    color: #FFF;
  }

  .events-tickets-description {
    text-align: left;
    padding-bottom: 15px;
  }

  .card-body {
    padding: 0px !important;
  }

  .event-image-ticket .card-header {
    display: flex;
  }

  .event-image-ticket .card-details {
    margin-left: 20px;
  }

  button {
    border-radius: 0 0 5px 5px !important;
    background: #0d6112 !important;
    border-color: #fff0 !important;
  }

  .stage {
    position: relative;
    top: -12px;
  }

  .ticket-title p {
    text-align: center;
    font-size: 33px;
    font-family: cursive;
    color: #ce4400;
    margin: 0;
    font-weight: 500;
  }

  #imgItem {
    display: inline-block;
  }

  .dot-flashing {
    position: relative;
    width: 10px;
    height: 10px;
    border-radius: 10px;
    background-color: #9880ff;
    color: #9880ff;
    animation: dotFlashing 1s infinite linear alternate;
    animation-delay: .5s;
    left: 23px;
    top: 2px;
  }

  .dot-flashing::before,
  .dot-flashing::after {
    content: '';
    display: inline-block;
    position: absolute;
    top: 0;
  }

  .dot-flashing::before {
    left: -15px;
    width: 10px;
    height: 10px;
    border-radius: 10px;
    background-color: #9880ff;
    color: #9880ff;
    animation: dotFlashing 1s infinite alternate;
    animation-delay: 0s;
  }

  .dot-flashing::after {
    left: 15px;
    width: 10px;
    height: 10px;
    border-radius: 10px;
    background-color: #9880ff;
    color: #9880ff;
    animation: dotFlashing 1s infinite alternate;
    animation-delay: 1s;
  }

  @keyframes dotFlashing {
    0% {
      background-color: #006313;
    }

    50%,
    100% {
      background-color: #ce4400;
    }
  }

  .ticket-box {
    background-color: #FFF;
    display: block ruby !important;
    padding: 70px 70px 70px 70px !important;
    box-shadow: 0 0 5px 4px rgb(0 0 0 / 5%);
    display: flex;
    align-items: flex-end;
    border-radius: 2px;
  }

  .disclaimer-section {
    color: #FFF;
    font-size: 10px;
  }

  .disclaimer {
    margin-top: 35px !important;
  }

  .card-header {
    padding: 25px;
  }
</style>
<div style="background-color:#FFEBCD !important ;height: 100% !important">
  <p>Generating Tickets...
</div>
<div class="container" style="z-index: 0">
  <div id="imgItem" class="">
    <div class="card event-image-ticket">
      <div class="card-header bg-info">
        <img src="<?php echo $protocol . $_SERVER['SERVER_NAME'] .'/image/editor-image/1660102886-img1.jpg'; ?>" height='100' width='100'>
        <div class="card-details">
          <div class="events-tickets-description">
            <span class="ticket-title"><b>Event Id : </b><?php echo $orderId; ?> </span>
          </div>
          <?php
          $newStrting = $_SESSION['symposiumPlan'];
          ?>
          <div class="events-tickets-description">
            <span class="ticket-title"><b>Event Title : </b><?php echo $newStrting; ?></span>
          </div>

          <div class="events-tickets-description">
            <span class="ticket-title"><b>Date : </b>2nd December,2022 At 6:30 PM </span>
          </div>

          <div class="events-tickets-description">
            <span class="ticket-title"><b>Total Tickets : </b><?php echo $_SESSION['symposium_ticket']; ?> </span>
          </div>

        </div>
      </div>
    </div>
    <!-- <button onclick="changeToImg()" class="btn btn-warning text-white">To image</button> -->
  </div>
</div>
<div class="container">
  <div class="card">
    <div id="sreenshot" class="card-body sreenshot">
    </div>
  </div>
</div>
<script type="text/javascript">
  jQuery("body").prepend('<div class="ticket-title" style="z-index:9999;background-color: #FFEBCD !important;height: 100vh !important;display: flex;align-items: center;justify-content: center;"><div class="ticket-box"><p>Generating Tickets</p><div class="stage"><div class="dot-flashing"></div></div></div></div>');

  $(document).ready(function() {

    changeToImg();
    // EventTicket();
  });

  function EventTicket() {
    var ticketUrl = localStorage.getItem('ticket');
    var download_ticketUrl = localStorage.getItem('ticket_download');
    var data = "ticketUrl=hiii";
    var jsonData = {
      ticketUrl: ticketUrl,
      download_ticketUrl: download_ticketUrl
    };
    $.ajax({
      type: "post",
      url: '/event-user/event-ticket-success/<?php echo $orderId; ?>',
      data: JSON.stringify(jsonData),
      dataType: "json",
      success: function(data) {}
    }, {
      fOutMode: 'json'
    });
  }

  function changeToImg() {
    const toImgArea = document.getElementById('imgItem');

    // To avoid the image will be cut by scroll, we need to scroll top before html2canvas.
    window.pageYOffset = 0;
    document.documentElement.scrollTop = 0
    document.body.scrollTop = 0

    // transform to canvas
    html2canvas(toImgArea, {
      allowTaint: true,
      taintTest: false,
      type: "view",
    }).then(function(canvas) {
      const sreenshot = document.getElementById('sreenshot');
      const downloadIcon = document.getElementById('download');
      localStorage.setItem('ticket', canvas.toDataURL("image/jpeg", "10.0"));
      localStorage.setItem('ticket_download', canvas.toDataURL("image/jpeg").replace("image/jpeg", "image/octet-stream"));

      // setting the canvas width  
      canvas.style.width = "800px";
      var ticketUrl = canvas.toDataURL("image/jpeg", "10.0");
      var download_ticketUrl = canvas.toDataURL("image/jpeg").replace("image/jpeg", "image/octet-stream");

      // append the canvas in the place that you want to show this image.  
      sreenshot.appendChild(canvas);
      var jsonData = {
        ticketUrl: ticketUrl,
        download_ticketUrl: download_ticketUrl
      };
      $.ajax({
        type: "post",
        url: '/event-user/symposium-ticket-success/<?php echo $orderId; ?>',
        data: JSON.stringify(jsonData),
        dataType: "json",

        success: function(data) {
          window.location.href = data.redirectUrl;
        }
      }, {
        fOutMode: 'json'
      });
    });
  }

  // clean the showing area
  function resetTheImageArea() {
    document.getElementById('sreenshot').innerHTML = "";
  }
</script>