<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="section-copyright">
  <div class="container container--narrow">
    <div class="copyright">
      <div class="footer__logo">
        <a href="<?php echo CommonHelper::generateUrl(); ?>">
          <?php if (CommonHelper::demoUrl())
          { ?>
            <img src="<?php echo CONF_WEBROOT_FRONTEND . 'images/yocoach-logo.svg'; ?>" alt="" />
          <?php }
          else
          { ?>
            <img src="<?php echo CommonHelper::generateFullUrl('Image', 'siteLogo', array(CommonHelper::getLangId()), CONF_WEBROOT_FRONT_URL); ?>" alt="" />
          <?php } ?>
        </a>
      </div>
      <p><?php echo Label::getLabel('LBL_Footer_Section_Label'); ?>
        <!-- <?php
        if (CommonHelper::demoUrl())
        {
          echo CommonHelper::replaceStringData(Label::getLabel('LBL_COPYRIGHT_TEXT', CommonHelper::getLangId()), ['{YEAR}' => '&copy; ' . date("Y"), '{PRODUCT}' => '<a target="_blank"  href="https://yo-coach.com">Yo!Coach</a>', '{OWNER}' => '<a target="_blank"  class="underline color-primary" href="https://www.fatbit.com/">FATbit Technologies</a>']);
        }
        else
        {
          echo Label::getLabel('LBL_COPYRIGHT', CommonHelper::getLangId()) . ' &copy; ' . date("Y ") . FatApp::getConfig("CONF_WEBSITE_NAME_" . CommonHelper::getLangId(), FatUtility::VAR_STRING);
        }
        ?>.
        <span>Design <a href="http://atruthfulwitness.com/" target="_blank">ATW</a> -->
          <!--: <a href="http://atruthfulwitness.com/" target="_blank">atruthfulwitness.com</a></span>-->
      </p>
    </div>
  </div>
</div>
<style type="text/css">
  body {
    font-family: 'Nunito', sans-serif !important;
  }

  div {
    font-family: 'Nunito', sans-serif !important;
  }

  p {
    font-family: 'Nunito', sans-serif !important;
  }

  h1,
  h2,
  h3,
  h4,
  h5,
  h6 {
    font-family: 'Nunito', sans-serif !important;
  }

  ul,
  li,
  a {
    font-family: 'Nunito', sans-serif !important;
  }

  /** body{font-family: Trebuchet MS !important;}
div{font-family: Trebuchet MS !important;}
p{font-family: Trebuchet MS !important;}
h1, h2, h3, h4, h5, h6{font-family: Trebuchet MS !important;}
ul, li, a{font-family: Trebuchet MS !important;} **/

  .footer ul li {
    color: #fff !important;
    opacity: 1 !important;
  }

  .footer ul li a:hover {
    color: #006313 !important;
  }

  .btn.btn--bordered.btn--block.btn--dropdown {
    color: #fff !important;
    opacity: 1 !important;
  }

  .section.section--language {
    background: #FFEBCD !important;
  }

  .section.section--services {
    background: #fff !important;
    opacity: 1 !important;
  }

  .section.section--step {
    background: #FFEBCD !important;
  }

  .section--gray {
    background-color: #FFEBCD !important;
  }

  .flag-wrapper .flag__box:hover {
    background: #fff;
    border: 1px solid #fff !important;
    box-shadow: 0 0 30px #fff !important;
  }

  .section.padding-bottom-5 h2 {
    color: rgb(175, 88, 0);
  }

  .section.padding-bottom-5 .teacher-wrapper .tile__media {
    border-radius: 50%;
    border: 3px solid #CE4400;
  }

  .section.padding-bottom-5 .teacher-wrapper .tile__head {
    padding: 0 30px;
  }

  .section.padding-bottom-5 .teacher-wrapper .tile h4 {
    font-size: 20px;
  }

  header.header .menu ul .menu__item a {
    padding: 0 1rem;
  }

  .section.section--quote {
    display: none;
  }

  .section.section--contect {
    background: #FFEBCD !important;
  }

  .section.section--contect .intro-head h6 {
    text-transform: capitalize !important;
  }

  .section.section--grey.section--page.-pattern {
    background: #FFEBCD !important;
  }

  .step-container__head .slick-slide {
    height: auto;
  }

  .slick-track {
    height: auto !important;
  }

  .slider .slick-slide {
    height: auto !important;
  }

  #teachersListingContainer .list__media .avtar {
    background: none !important;
  }

  .section.section--cta h2 {
    text-shadow: 0px 0px 10px #000;
  }

  .section--grey {
    background-color: #FFEBCD !important;
  }

  .info-tag.ratings {
    display: none;
  }

  .footer .section-copyright .copyright {
    justify-content: center !important;
  }

  .footer .section-copyright .copyright p {
    color: #000;
    font-size: 16px;
  }

  .footer.footernewcls .section--footer {
    background: #FFEBCD !important;
    color: #000 !important;
  }

  .footer.footernewcls h3 {
    padding: 0 0 30px;
    color: #006313;
    font-size: 32px;
  }

  .footer.footernewcls h5 {
    font-size: 20px;
    text-transform: capitalize;
  }

  .footer.footernewcls .footer_contact_details svg {
    display: none;
  }

  .footer.footernewcls .footer_contact_details li {
    color: #000 !important;
    font-size: 16px;
  }

  .footer.footernewcls .btn.btn--secondary {
    display: none;
  }

  .footer.footernewcls .field-set {
    margin: 15px 0 0;
  }

  .footer.footernewcls .footer_social-links li img {
    display: none;
  }

  .footer.footernewcls .footer_social-links a {
    color: #000;
    font-size: 16px;
  }

  .footer.footernewcls .col-md-6.col-lg-3:nth-child(3) .footer-group.toggle-group .footer-group.toggle-group .footer__group-content.toggle-target-js {
    display: none;
  }

  .footer.footernewcls .field-set input {
    border: 1px solid #000;
    border-radius: 6px;
    color: #000;
  }

  .footer.footernewcls .bullet-list a {
    color: #000;
    font-size: 16px;
  }

  .footer.footernewcls .col-md-6.col-lg-3:nth-child(3) .footer-group.toggle-group .footer-group.toggle-group h5 {
    color: #006313 !important;
  }

  .container.container--narrow .contact-cta {
    display: block !important;
    text-align: center;
  }

  .container.container--narrow .contact-cta .contact__content p {
    display: none;
  }

  #faq-area {
    background: #fff !important;
    padding-bottom: 0 !important;
  }

  .container.container--narrow .contact-cta {
    margin-top: -50px;
  }

  .buttonBlock a {
    background: #D24500 !important;
  }

  .languageBlock1Min {
    padding: 50px 0 !important;
  }

  .languageBlock1 {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .languageBlock1 .rt-block {
    width: 150px;
  }

  .languageBlock1 .lt-block .flag-wrapper {
    flex-wrap: nowrap;
  }

  .languageBlock1Min .more-info a {
    background: #CE4400;
    color: #fff;
    text-decoration: none;
    padding: 8px 35px;
    border-radius: 50px;
    display: inline-block;
    margin-top: 20px;
    font-family: Open Sans !important;
    font-size: 22px;
    font-weight: 400;
    letter-spacing: 1px;
  }

  .languageBlock1Min .more-info a:hover {
    background: #006313;
  }

  .languageBlock1Min .section__head {
    margin-bottom: 10px !important;
  }


  .ButtonCenter {
    text-align: center;
    padding: 0;
    margin-top: -30px;
  }

  .ButtonCenter a {
    background: #006313;
    color: #fff;
    text-decoration: none;
    padding: 15px 35px;
    border-radius: 50px;
    display: inline-block;
    margin-bottom: 20px;
    font-family: Open Sans !important;
    font-size: 20px;
    font-weight: 400;
  }

  .ButtonCenter a:hover {
    background: #CE4400;
  }

  .section.contact-form .contact-form input[type="submit"] {
    max-width: 300px;
    border-radius: 3px;
    font-size: 18px;
    font-weight: 400;
  }

  .section.section--contect {
    padding-top: 0 !important;
  }

  .section.section--language.languageBlock1Min {
    background: #fff !important;
  }

  .section.padding-bottom-5 {
    background: #FFEBCD !important;
  }

  .section.section--language.languageBlock1Min .flag__box {
    background: #FFEBCD;
    padding-bottom: 5px !important;
  }

  .contact-page .banner__media.-hide-mobile.container img {
    max-width: 100% !important;
    height: auto !important;
    display: block !important;
  }

  .contact-page h4 {
    padding-bottom: 8px;
  }

  .section.section--cta {
    background-position: center right !important;
  }

  .field-services-banner {
    display: block !important;
  }

  .field-services-banner img {
    width: 100% !important;
    height: auto !important;
  }

  .section.section--upcoming-class .section__head.d-flex.justify-content-between.align-items-center {
    display: block !important;
  }

  .section.section--upcoming-class .section__head.d-flex.justify-content-between.align-items-center h2 {
    color: rgb(175, 88, 0);
  }

  .section.section--upcoming-class .section__head.d-flex.justify-content-between.align-items-center .view-all {
    float: right;
    margin-top: -30px;
  }

  .section.section--upcoming-class .card.card--bg {
    background: #FFEBCD !important;
  }

  .section.section--upcoming-class .card.card--bg .card__row--action {
    background: #FFEBCD !important;
  }

  .section.section--upcoming-class .card.card--bg .card__body span {
    color: #000 !important;
    opacity: 1;
    font-weight: 700;
  }

  .section.padding-0 .slideshow-content {
    position: absolute !important;
    top: 0 !important;
    left: 0;
  }

  @media only screen and (max-width: 1430px) {
    header.header .menu ul .menu__item a {
      padding: 0 8px;
      font-size: 13px;
    }
  }

  @media only screen and (max-width: 1350px) {
    header.header .menu ul .menu__item a {
      padding: 0 7px;
      font-size: 12px;
    }
  }

  @media only screen and (max-width: 1270px) {
    header.header .menu ul .menu__item a {
      padding: 0 6px;
      font-size: 11px;
    }
  }

  @media only screen and (max-width: 1199px) {
    header.header.nav-up nav.menu ul {
      display: flex !important;
      flex-wrap: wrap;
    }

    header.header.nav-up nav.menu ul .menu__item {
      width: 100%;
    }

    header.header nav.menu ul {
      display: flex !important;
      flex-wrap: wrap !important;
    }

    header.header nav.menu ul li.menu__item {
      width: 100% !important;
    }
  }

  @media only screen and (max-width: 1150px) {
    .languageBlock1 .flag-wrapper .flag__box {
      width: 110px !important;
    }

    .ButtonCenter {
      margin-top: 0;
    }
  }

  @media only screen and (max-width: 900px) {
    .languageBlock1 {
      display: block;
    }

    .languageBlock1 .rt-block {
      width: 250px;
      margin: 0 auto;
    }

    .languageBlock1 .lt-block .flag-wrapper {
      flex-wrap: wrap;
    }

    .languageBlock1 .flag-wrapper .flag__box {
      width: 140px !important;
    }
  }

  @media only screen and (max-width: 767px) {
    .footer__group-content {
      display: block !important;
    }

    .footer__group-title.toggle-trigger-js {
      padding-bottom: 0 !important;
    }

    .footer__group-content.toggle-target-js {
      padding-top: 0 !important;
    }

    .container.container--narrow .contact-cta {
      margin-top: -20px;
    }

    #how-it-works .service__media img {
      max-width: 100% !important;
      height: auto !important;
    }

    .ButtonCenter {
      margin-top: 30px;
    }

    .section.padding-bottom-5 .teacher-wrapper .tile__media.ratio {
      width: 120px;
    }

    .section.padding-bottom-5 .teacher-wrapper .tile__media.ratio img {
      width: 120px !important;
    }

    .section.section--contect .banner__media.-hide-mobile {
      display: block !important;
    }

    .footernewcls .footer__group-title.toggle-trigger-js {
      padding-left: 13px !important;
    }

    .foot-block1 .footer__group-content.toggle-target-js {
      padding-bottom: 0 !important;
    }

    .foot-block2 .footer__group-content.toggle-target-js {
      padding-bottom: 0 !important;
    }

    .foot-block2 .footer__group-content.toggle-target-js .footer__group-content.toggle-target-js {
      padding-left: 0 !important;
      padding-right: 0 !important;
    }

    .foot-block3 .footer__group-content.toggle-target-js {
      padding-bottom: 0 !important;
    }

    .foot-block3 .footer__group-content.toggle-target-js .footer__group-title.toggle-trigger-js {
      padding-left: 0 !important;
      padding-right: 0 !important;
    }

    .foot-block3 .footer__group-content.toggle-target-js .footer__group-content.toggle-target-js {
      padding-left: 0 !important;
      padding-right: 0 !important;
    }

    .section.section--cta {
      background-position: 90% !important;
    }

    .section.padding-0 .slideshow-content {
      position: relative !important;
    }
  }

  @media only screen and (max-width: 500px) {
    .section.section--cta {
      background-position: 80% !important;
    }
  }

  @media only screen and (max-width: 400px) {
    .section.section--upcoming-class .section__head.d-flex.justify-content-between.align-items-center .view-all {
      float: none;
      margin-top: 0;
    }
  }
</style>