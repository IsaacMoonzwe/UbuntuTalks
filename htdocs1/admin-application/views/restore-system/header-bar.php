<style>
    .-fixed-wrap {
        position: fixed;
        bottom: 10rem;
        right: 1rem;
        z-index: 9999;
    }
    .-fixed-wrap a {
        position: relative;
        display: inline-block;
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
        border: none;
        border-radius: 2px;
        padding: 2.25rem 1rem 0.5rem;
        vertical-align: middle;
        -webkit-border-radius: 5px;
        border-radius: 5px;
        text-align: center;
        text-overflow: ellipsis;
        text-transform: uppercase;
        color: #fff;
        background: #666;
        text-decoration: none;
        font-size: 1.5rem;
        letter-spacing: 0.15em;
        overflow: hidden;
        min-width: 150px;
    }
    .-fixed-wrap a small {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        display: block;
        padding: 0.5rem 1rem;
        font-size: 0.5rem;
        letter-spacing: 0.05em;
        white-space: nowrap;
        background-color: rgba(0, 0, 0, 0.2);
    }
    .restore-demo-bg {
        background-image: url('<?php echo CommonHelper::generateFullUrl('', '', array(), CONF_WEBROOT_FRONT_URL) . '/images/catalog-bg.png'; ?>') !important;
        background-color: #fff !important;
        background-repeat: no-repeat !important;
        background-position: 130% top !important;
    }
    .restore-demo .demo-data-inner>ul,
    .restore-demo .demo-data-inner .heading {
        max-width: 500px;
        margin-right: 250px;
    }
    .demo-data-inner {
        margin: 20px;
        color: #4c4c4c;
    }
    .demo-data-inner .heading {
        font-size: 4rem;
        font-weight: 600;
        text-transform: uppercase;
        position: relative;
        line-height: 1.2;
        margin-bottom: 40px;
        color: inherit;
    }
    .demo-data-inner .heading:after {
        background: var(--color-primary);
        width: 60px;
        height: 3px;
        position: absolute;
        bottom: -10px;
        content: "";
        display: block;
    }
    .demo-data-inner .heading span {
        display: block;
        font-size: 0.8rem;
        text-transform: none;
    }
    .demo-data-inner ul li {
        position: relative;
        margin: 10px 0;
        padding: 0 15px;
        display: block;
        font-size: 0.9rem;
    }
    .demo-data-inner ul li:before {
        width: 5px;
        height: 5px;
        content: "";
        display: block;
        position: absolute;
        left: 0;
        top: 8px;
        transform: rotate(45deg);
        background: #4c4c4c;
    }
    .demo-data-inner ul ul {
        margin-inline-start: 15px;
        margin-bottom: 20px;
    }
    .restore-demo {
        min-height: 300px;
    }
    .restore-demo a {
        color: var(--color-primary);
    }
    .restore-demo p {
        font-size: 1.1rem;
        font-weight: 400;
        line-height: 1.5;
    }
    #facebox .restore-demo.fbminwidth {
        min-width: 350px;
        min-height: 150px;
    }
    #facebox .restore-demo {
        display: block;
        width: 100%;
        padding: 15px;
        background-color: #fff;
        border-radius: 4px;
        margin: 0 auto;
        position: relative;
    }
    .demo-data-inner ul li {
        position: relative;
        margin: 10px 0;
        padding: 0 15px;
        display: block;
        font-size: 0.9rem;
        line-height: 1.5;
    }
    /*demo header*/
    .sticky-demo-header body {
        padding: 0;
        padding-bottom: 100px;
    }
    .sticky-demo-header #wrapper {
        margin-top: 50px;
    }
    .sticky-demo-header .demo-header {
        background: #fff;
        position: -webkit-sticky;
        position: sticky;
        left: 0;
        right: 0;
        top: 0;
        z-index: 99;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-pack: justify;
        -ms-flex-pack: justify;
        justify-content: space-between;
        padding: 0 2rem;
        line-height: 4rem;
        -webkit-box-shadow: 0px 0px 5px 0px rgba(0, 0, 0, 0.1);
        box-shadow: 0px 0px 5px 0px rgba(0, 0, 0, 0.1);
        width: 100%;
    }
    .sticky-demo-header #header {}
    .restore-wrapper {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
    }
    .restore-wrapper > a {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -ms-flex-direction: column;
        flex-direction: column;
        padding: 10px 0;
    }
    .restore-wrapper .restore__counter {
        padding: 0px 8px;
        font-size: 1rem;
        color: var(--first-color);
        margin: 0.10rem 0;
        font-weight: 800;
        line-height: 1;
        letter-spacing: 4px;
    }
    .restore__progress {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        height: 4px;
        width: 100%;
        overflow: hidden;
        font-size: 0.75rem;
        background-color: #e9ecef;
        border-radius: 2rem;
        margin: 0.25rem 0;
        max-width: 96px;
    }
    .restore-wrapper .restore__content {
        font-size: 0.675rem;
        color: var(--body-color);
        font-weight: 600;
        margin-bottom: 0.25rem;
        line-height: 1.5;
    }
    .switch-interface {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
    }
    .switch-interface li {
        margin: 0 1rem;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
    }
    .switch-interface li a {
        position: relative;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
    }
    .switch-interface li.is-active a:before,
    .switch-interface li a:hover:before {
        height: 2px;
        background: var(--first-color);
        position: absolute;
        bottom: 0;
        content: "";
        width: 100%;
    }
    .switch-interface .icn svg {
        width: 2rem;
        height: 2rem;
        fill: #8c8c8c;
    }
    .switch-interface li.is-active .icn svg,
    .switch-interface li a:hover .icn svg {
        fill: var(--first-color);
    }
    .demo-cta {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: justify;
        -ms-flex-pack: justify;
        justify-content: space-between;
    }
    .demo-cta .close-layer {
        position: relative;
        right: auto;
        top: auto;
        -webkit-margin-start: 1rem;
        -moz-margin-start: 1rem;
        margin-inline-start: 1rem;
    }
    .restore-demo-bg {
        background-image: url('../images/catalog-bg.png') !important;
        background-color: #fff !important;
        background-repeat: no-repeat !important;
        background-position: 130% top !important;
    }
    .restore-demo .demo-data-inner > ul,
    .restore-demo .demo-data-inner .heading {
        max-width: 500px;
        margin-right: 250px;
    }
    .demo-data-inner {
        margin: 20px;
        color: #4c4c4c;
    }
    .demo-data-inner .heading {
        font-size: 4rem;
        font-weight: 600;
        text-transform: uppercase;
        position: relative;
        line-height: 1.2;
        margin-bottom: 40px;
        color: inherit;
    }
    .demo-data-inner .heading:after {
        background: var(--second-color);
        width: 60px;
        height: 3px;
        position: absolute;
        bottom: -10px;
        content: "";
        display: block;
    }
    .demo-data-inner .heading span {
        display: block;
        font-size: 0.8rem;
        text-transform: none;
    }
    .demo-data-inner ul li {
        position: relative;
        margin: 10px 0;
        padding: 0 15px;
        display: block;
        font-size: 0.9rem;
    }
    .demo-data-inner ul li:before {
        width: 5px;
        height: 5px;
        content: "";
        display: block;
        position: absolute;
        left: 0;
        top: 8px;
        -webkit-transform: rotate(45deg);
        transform: rotate(45deg);
        background: #4c4c4c;
    }
    .demo-data-inner ul ul {
        -webkit-margin-start: 15px;
        -moz-margin-start: 15px;
        margin-inline-start: 15px;
        margin-bottom: 20px;
    }
    @media(max-width:1200px) {
        .sticky-demo-header .sidebar {
            top: 60px;
        }
        .sticky-demo-header header#header-dashboard {
            top: 60px;
        }
    }
    @media(max-width:767px) {
        .demo-header {
            display: none;
        }
    }
    .-fixed-wrap {
        position: fixed;
        bottom: 1rem;
        left: 1rem;
        z-index: 9999;
    }
    .-fixed-wrap a {
        position: relative;
        display: inline-block;
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
        border: none;
        border-radius: 2px;
        padding: 2.25rem 1rem 0.5rem;
        vertical-align: middle;
        border-radius: 5px;
        text-align: center;
        text-overflow: ellipsis;
        text-transform: uppercase;
        color: #fff;
        background: #666;
        text-decoration: none;
        font-size: 2rem;
        letter-spacing: 0.15em;
        overflow: hidden;
        min-width: 200px;
    }
    .-fixed-wrap a small {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        display: block;
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        white-space: nowrap;
        background-color: rgba(0, 0, 0, 0.2);
    }
</style>
<div class="demo-header no-print">
    <div class="restore-wrapper">
        <a href="javascript:void(0)" onclick="showRestorePopup()">
            <p class="restore__content">Database Restores in</p>
            <div class="restore__progress">
                <div class="restore__progress-bar" role="progressbar" style="width:25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                </div>
            </div>
            <span class="restore__counter" id="restoreCounter">00:00:00</span>
        </a>
    </div>
    <div class="demo-cta">
        <a target="_blank" href="https://www.fatbit.com/online-learning-consultation-marketplace-platform.html" class="btn btn-primary btn-sm ripplelink" rel="noopener"><?php echo Label::getLabel('LBL_START_YOUR_MARKETPLACE'); ?></a>
        &nbsp;
        <a target="_blank" href="https://www.yo-coach.com#demo" class="btn btn-outline-primary btn-sm ripplelink" rel="noopener"><?php echo Label::getLabel('LBL_Request_demo'); ?></a>
        <a href="javascript:void(0)" class="close-layer" id="demoBoxClose"></a>
    </div>
</div>
<?php
$dateTime = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' +2 hours'));
$restoreTime = FatApp::getConfig('CONF_RESTORE_SCHEDULE_TIME', FatUtility::VAR_STRING, $dateTime);
?>
<script>
    function showRestorePopup() {
        $.facebox('<div class="demo-data-inner"><div class="heading">Yo!Coach<span></span></div> <p>To enhance your demo experience, we periodically  restore our database every 4 hours.</p><br> <p>For technical issues :-</p> <ul> <li><strong>Call us at: </strong>+1 469 844 3346, +91 85919 19191, +91 95555 96666, +91 73075 70707, +91 93565 35757</li> <li><strong>Mail us at : </strong> <a href="mailto:sales@fatbit.com">sales@fatbit.com</a></li> </ul> <br> Create Your Online Tutoring & Consultation Platform With Yo!Coach <a href="https://www.fatbit.com/website-design-company/requestaquote.html" target="_blank">Click here</a></li></div>', 'restore-demo restore-demo-bg fbminwidth');
    }
    function restoreSystem() {
        $.mbsmessage('Restore is in process..', false, 'alert--process alert');
        fcom.updateWithAjax(fcom.makeUrl('RestoreSystem', 'index', '', '<?php echo CONF_WEBROOT_FRONT_URL; ?>'), '', function (resp) {
            setTimeout(function () {
                window.location.reload();
            }, 3000);
        }, false, false);
    }
    $(document).on("click", "#demoBoxClose", function (e) {
        $('.demo-header').hide();
        $('html').removeClass('sticky-demo-header');
    });
    // Set the date we're counting down to
    var countDownDate = new Date('<?php echo $restoreTime; ?>').getTime();
    // Update the count down every 1 second
    var x = setInterval(function () {
        // Get today's date and time
        //var now = new Date().getTime();
        var date = new Date();
        var utcDate = new Date(date.toLocaleString('en-US', {
            timeZone: "UTC"
        }));
        var now = utcDate.getTime();
        // Find the distance between now and the count down date
        var distance = countDownDate - now;
        // Time calculations for days, hours, minutes and seconds
        // var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);
        var str = ('0' + hours).slice(-2) + ":" + ('0' + minutes).slice(-2) + ":" + ('0' + seconds).slice(-2);
        // Display the result in the element with id="demo"
        document.getElementById("restoreCounter").innerHTML = str;
        var progressPercentage = 100 - (parseFloat(hours + '.' + parseFloat(minutes / 15 * 25)) * 100 / 4);
        $('.restore__progress-bar').css('width', progressPercentage + '%');
        // If the count down is finished, write some text
        if (distance < 0) {
            clearInterval(x);
            document.getElementById("restoreCounter").innerHTML = "Process...";
            showRestorePopup();
            restoreSystem();
        }
    }, 1000);
</script>