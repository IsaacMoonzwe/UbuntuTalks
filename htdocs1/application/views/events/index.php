<?php
$contactFrm->setFormTagAttribute('class', 'form form--normal');
$captchaFld = $contactFrm->getField('htmlNote');
$captchaFld->htmlBeforeField = '<div class="field-set">
            <div class="caption-wraper"><label class="field_label"></label></div>
            <div class="field-wraper">
               <div class="field_cover">';
$captchaFld->htmlAfterField = '</div></div></div>';
$contactFrm->setFormTagAttribute('action', CommonHelper::generateUrl('Events', 'contactSubmit'));
$contactFrm->developerTags['colClassPrefix'] = 'col-md-';
$contactFrm->developerTags['fld_default_col'] = 12;
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

<section class="section">
   <div class="container container--narrow">
      <div class="owl-carousel owl-theme">
         <div class="item">
            <img src="<?php echo CommonHelper::generateUrl('Image', 'EventCampaign', [$siteLangId]); ?>" alt="">
         </div>
         <div class="item">
            <img src="<?php echo CommonHelper::generateUrl('Image', 'EventSecondSliderCampaign', [$siteLangId]); ?>" alt="">
         </div>
         <div class="item">
            <img src="<?php echo CommonHelper::generateUrl('Image', 'EventThirdSliderCampaign', [$siteLangId]); ?>" alt="">
         </div>
         <div class="item">
            <img src="<?php echo CommonHelper::generateUrl('Image', 'EventFourthSliderCampaign', [$siteLangId]); ?>" alt="">
         </div>
         <div class="item">
            <img src="<?php echo CommonHelper::generateUrl('Image', 'EventFifthSliderCampaign', [$siteLangId]); ?>" alt="">
         </div>
      </div>
</section>
<section class="event-page-section">
   <div class="container container--narrow login-Registration events-buttons" id="tabs">
      <li class="sidebar-btns">
         <a href="#sponsorship" class="btn btn--primary events-buttons sponsor"><?php echo Label::getLabel('LBL_Sponsor', $adminLangId); ?></a>
      </li>
      </li>
      <li class="sidebar-btns">
         <a href="#donation" class="btn btn--primary events-buttons donation"><?php echo Label::getLabel('LBL_Donate', $adminLangId); ?></a>
      </li>
      <?php if (EventUserAuthentication::isUserLogged()) {
         $str = $userDetails['user_first_name'];
         $first_character = substr($str, 0, 1);
         // echo $first_character;
      ?>
         <div class="header-dropdown header-dropwown--profile">
            <a class="header-dropdown__trigger trigger-js" href="#profile-nav">
               <div class="teacher-profile">
                  <div class="teacher__media">
                     <div class="avtar avtar--xsmall events-buttons" data-title="<?php echo $first_character; ?>"></div>
                  </div>
                  <div class="teacher__name"><?php echo $userDetails['user_first_name']; ?></div>
                  <svg class="icon icon--arrow">
                     <use xlink:href="/images/sprite.yo-coach.svg#arrow-black"></use>
                  </svg>
               </div>
            </a>
            <div id="profile-nav" class="header-dropdown__target">
               <div class="dropdown__cover">
                  <nav class="menu--inline">
                     <ul>
                        <li class="menu__item button"><a href="/dashboard-event-visitor">My Dashboard</a></li>
                        <li class="menu__item button"><a href="<?php echo CommonHelper::generateUrl('EventUser', 'logout'); ?>"><?php echo Label::getLabel('LBL_Logout'); ?></a></li>
                     </ul>
                  </nav>
               </div>
            </div>
         </div>
      <?php } else { ?>
         <li>
            <a href="javascript:void(0)" onClick="EventLogInFormPopUp();" class="btn btn--primary events-buttons"><?php echo Label::getLabel('LBL_Login'); ?><span class="svg-icon user-icon">
               </span></a>
         </li>
         <li class="">
            <a href="javascript:void(0)" onclick="EventSignUpFormPopUp();" class="btn btn--primary events-buttons"><?php echo Label::getLabel('LBL_Sign_Up'); ?></a>
         </li>

      <?php } ?>

      <div class="events-Video-mobile title-for-mobile-view">
         <h5 style="text-align:center !important;"><?php echo Label::getLabel('LBL_Welcome_to_the_ubuntu_talks_sub_saharan_african_language_symposium_2023', $adminLangId); ?>
            <br>
            <span style="color: #006313;"><?php echo Label::getLabel('LBL_Lusaka,_Zambia.', $adminLangId); ?></span>
         </h5>
      </div>
      <div class="video-media ratio ratio--16by9 events-Video-mobile">
         <iframe src="https://www.youtube.com/embed/hMAK0owYRYU&feature=youtu.be?autoplay=1&rel=0&loop=1&playlist=hMAK0owYRYU&mute=1" allowfullscreen="" width="900" height="400" frameborder="0"></iframe>
      </div>
      <div class="events-Video-mobile">
         <div id="clock">
            <div class="block"> <span class="digit" id="monthss"></span> Months
            </div>

            <div class="block"> <span class="digit" id="dayss"></span> Days
            </div>

            <div class="block"> <span class="digit" id="hourss"></span> Hours
            </div>

            <div class="block"> <span class="digit" id="minutess"></span> Minutes
            </div>

            <div class="block"> <span class="digit" id="secondss"></span> Seconds
            </div>
         </div>
      </div>
   </div>
</section>


<section class="event-page-section">
   <a id="button"></a>
   <div class="container container--narrow">
      <div class="row">
         <div class="col-lg-3">
            <div class="events-tabs">
               <ul class="nav nav-tabs" id="myTab" role="tablist">
                  <li class="nav-item">
                     <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true"><?php echo Label::getLabel('LBL_UT_Language_Symposium', $adminLangId); ?></a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link" id="programspeakers-tab" data-toggle="tab" href="#programspeakers" role="tab" aria-controls="programspeakers" aria-selected="false"><?php echo Label::getLabel('LBL_Program_&_Speakers', $adminLangId); ?></a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link" id="registration-tab" data-toggle="tab" href="#registration" role="tab" aria-controls="registration" aria-selected="false"><?php echo Label::getLabel('LBL_Registration', $adminLangId); ?></a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link" id="sponsorship-tab" data-toggle="tab" href="#sponsorship" role="tab" aria-controls="sponsorship" aria-selected="false"><?php echo Label::getLabel('LBL_Sponsorship', $adminLangId); ?></a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link" id="SymposiumDinner-tab" data-toggle="tab" href="#SymposiumDinner" role="tab" aria-controls="SymposiumDinner" aria-selected="false"><?php echo Label::getLabel('LBL_Pre-Symposium_Dinner', $adminLangId); ?></a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link" id="donation-tab" data-toggle="tab" href="#donation" role="tab" aria-controls="donation" aria-selected="false"><?php echo Label::getLabel('LBL_Benefit_Concert', $adminLangId); ?></a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link" id="agenda-tab" data-toggle="tab" href="#agenda" role="tab" aria-controls="agenda" aria-selected="false"><?php echo Label::getLabel('LBL_Agenda', $adminLangId); ?></a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link" id="accommodation-tab" data-toggle="tab" href="#accommodation" role="tab" aria-controls="accommodation" aria-selected="false"><?php echo Label::getLabel('LBL_Travel_&_Lodging', $adminLangId); ?></a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link" id="codeofconduct-tab" data-toggle="tab" href="#codeofconduct" role="tab" aria-controls="codeofconduct" aria-selected="false"><?php echo Label::getLabel('LBL_Code_Of_Conduct', $adminLangId); ?></a>
                  </li>
                  <!-- <li class="nav-item">
                     <a class="nav-link" id="DisclaimerSection-tab" data-toggle="tab" href="#DisclaimerSection" role="tab" aria-controls="DisclaimerSection" aria-selected="false"><?php echo Label::getLabel('LBL_Disclaimer_Section', $adminLangId); ?></a>
                  </li> -->
                  <li class="nav-item">
                     <a class="nav-link" id="safety-tab" data-toggle="tab" href="#safety" role="tab" aria-controls="safety" aria-selected="false"><?php echo Label::getLabel('LBL_Covid_19_Safety', $adminLangId); ?></a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link" id="press-tab" data-toggle="tab" href="#press" role="tab" aria-controls="press" aria-selected="false"><?php echo Label::getLabel('LBL_Press', $adminLangId); ?></a>
                  </li>
                  <li class="nav-item">
                     <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false"><?php echo Label::getLabel('LBL_Contact', $adminLangId); ?></a>
                  </li>
               </ul>
            </div>
            <div class="committed-bridging-box">
               <h4><?php echo Label::getLabel('LBL_Sidebar_Description', $adminLangId); ?></h4>
            </div>
            <div class="sidebar-btns">
               <a href="#sponsorship" class="more-info-btn sponsor"><?php echo Label::getLabel('LBL_Become_A_Sponsor', $adminLangId); ?></a>
               <a href="#donation" class="more-info-btn donation"><?php echo Label::getLabel('LBL_Donate', $adminLangId); ?></a>
            </div>
            <div class="sidebar-social">
               <p><?php echo Label::getLabel('LBL_Share_&_Follow', $adminLangId); ?></p>
               <ul>
                  <li>
                     <a href="#">
                        <img src="/images/social_01.svg">
                     </a>
                  </li>
                  <li>
                     <a href="#">
                        <img src="/images/social_02.svg">
                     </a>
                  </li>
                  <li>
                     <a href="#">
                        <img src="/images/social_05.svg">
                     </a>
                  </li>
                  <li>
                     <a href="#">
                        <img src="/images/social_06.svg">
                     </a>
                  </li>
               </ul>
            </div>
         </div>
         <div class="col-lg-9">
            <div class="events-tabs-content">
               <div class="tab-content" id="myTabContent">
                  <!-- UT Language Symposium -->
                  <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                     <div class="tab-detail">
                        <div class="events-tab-detail">
                           <?php echo FatUtility::decodeHtmlEntities($UTLanguageContent); ?>
                        </div>
                        <div class="main-menu">
                           <a class="speakers-tab" href="#tabs" id="main_menu"><?php echo Label::getLabel('LBL_Main_Menu', $adminLangId); ?></a>
                        </div>
                     </div>
                  </div>
                  <!-- UT Registration -->
                  <div class="tab-pane fade" id="registration" role="tabpanel" aria-labelledby="registration-tab">
                     <div class="registration">
                        <?php echo FatUtility::decodeHtmlEntities($UTRegistration); ?>
                     </div>

                     <div class="Registration-table">
                        <div class="registration_card">
                           <div class="row">
                              <?php foreach ($RegistrationPlanDetailsList as  $value) {  ?>
                                 <div class="col-lg-4">
                                    <div class="card-list">
                                       <div class="ticket-card-header">
                                          <div>
                                             <h4 class="ticket-name">
                                                <div class="lt-line-clamp lt-line-clamp--multi-line">
                                                   <?php echo $value['registration_plan_title']; ?>
                                                </div>
                                             </h4>
                                          </div>
                                       </div>
                                       <div class="ticket-card-nav">
                                          <div>
                                             <h2 class="ticket-price"><?php echo "$" . $value['registration_plan_price']; ?></h2>
                                             <div class="ticket-date">
                                                <p class="ticket-decription"><?php echo $value['registration_plan_description']; ?></p>
                                                <div class="ticket-information">
                                                   <?php if ($value['registration_plan_note'] != '') { ?>
                                                      <p class="note">Note: <?php echo $value['registration_plan_note']; ?></p>
                                                   <?php } ?>
                                                   <div class="purchase-button">
                                                      <?php
                                                      $Currentdate = date('Y-m-d h:i');
                                                      if ($value['registration_ending_date'] < $Currentdate) {
                                                      ?>
                                                         <a href="javascript:void(0)" class="btn btn--primary events-buttons not-avilable"><?php echo Label::getLabel('LBL_Not_Available'); ?></a>
                                                      <?php
                                                      } else {
                                                      ?>
                                                         <a href="javascript:void(0)" onclick="GetEventPlan();" class="btn btn--primary events-buttons"><?php echo Label::getLabel('LBL_Purchase_Now'); ?></a>
                                                      <?php
                                                      }
                                                      ?>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>

                              <?php } ?>
                           </div>
                        </div>
                     </div>
                     <div class="main-menu">
                        <a class="speakers-tab" href="#tabs" id="main_menu"><?php echo Label::getLabel('LBL_Main_Menu', $adminLangId); ?></a>
                     </div>

                  </div>
                  <!-- Progame and speakers -->
                  <div class="tab-pane fade" id="programspeakers" role="tabpanel" aria-labelledby="programspeakers-tab">
                     <div class="program_tab" id="program-speakers-tab">
                        <ul class="nav nav-tabs" id="seperrateprogramTab" role="tablist">
                           <li class="nav-item">
                              <a class="nav-link active show" id="seperrateprogram-tab" data-toggle="tab" href="#seperrateprogram" role="tab" aria-controls="seperrateprogram" aria-selected="true"><?php echo Label::getLabel('LBL_Program', $adminLangId); ?></a>
                           </li>
                           <li class="nav-item">
                              <a class="nav-link" id="speakers-tab" data-toggle="tab" href="#speakers" role="tab" aria-controls="speakers" aria-selected="false"><?php echo Label::getLabel('LBL_Speakers', $adminLangId); ?></a>
                           </li>
                           <li class="nav-item">
                              <a class="nav-link" id="entertainments-tab" data-toggle="tab" href="#entertainments" role="tab" aria-controls="entertainments" aria-selected="false"><?php echo Label::getLabel('LBL_Entertainment', $adminLangId); ?></a>
                           </li>
                        </ul>


                        <div class="tab-content" id="seperrateprogramTabContent">

                           <div class="tab-pane fade show active" id="seperrateprogram" role="tabpanel" aria-labelledby="seperrateprogram-tab">
                              <div class="seperrateprogramTab-data">
                                 <?php echo FatUtility::decodeHtmlEntities($ProgramSpeakersDescription); ?>
                              </div>
                           </div>

                           <div class="tab-pane fade" id="speakers" role="tabpanel" aria-labelledby="speakers-tab">
                              <div class="seperrateprogramTab-data">
                                 <div class="others-speakers">
                                    <div class="row topHeading-block">
                                       <div class="col-lg-12">
                                          <?php echo FatUtility::decodeHtmlEntities($ProgramSpeakersFirstKeyNoteDescription); ?>
                                       </div>
                                       <div class="col-lg-12">
                                          <?php echo FatUtility::decodeHtmlEntities($ProgramSpeakersSecondKeyNoteDescription); ?>
                                       </div>
                                       <div class="col-lg-12">
                                          <?php echo FatUtility::decodeHtmlEntities($ProgramSpeakersThirdKeyNoteDescription); ?>
                                       </div>
                                    </div>
                                    <div class="speakers-heading-main">
                                       <h4 class="speaker_heading"><?php echo Label::getLabel('LBL_Others_Speakers', $adminLangId); ?></h4>
                                       <h3 class="speaker_heading"><?php echo Label::getLabel('LBL_Complete_List_Comming_Soon', $adminLangId); ?></h3>
                                    </div>
                                    <!-- Light Box -->
                                    <div class="gallery-wrapper">
                                       <div class="row">
                                          <?php
                                          foreach ($records as  $value) {
                                             if ($value['speakers_positions_listing'] == 'Others') {
                                                if (!empty($value['speaker_image'])) {
                                                   foreach ($value['speaker_image'] as $testimonialImg) {
                                                      $htmlAfterField = '<img src="' . CommonHelper::generateFullUrl('Events', 'sponsorshipimage', array($testimonialImg['afile_record_id'], $testimonialImg['afile_lang_id'], '')) . '?' . time() . '">';
                                                   }
                                                }
                                          ?>
                                                <div class="col-lg-3">
                                                   <div class="image-wrapper">
                                                      <a href=<?php echo "#" . $value['speakers_id']; ?>>
                                                         <div class="lightbox-image-box">
                                                            <?php echo $htmlAfterField; ?>
                                                            <div class="lightbox-image-hover">
                                                               <span><?php echo Label::getLabel('LBL_View_Profile_>', $adminLangId); ?></span>
                                                            </div>
                                                         </div>
                                                         <div class="image-title">
                                                            <h3><?php echo $value['speakers_user_name']; ?></h3>
                                                            <p class="list-sub-title"><?php echo $value['speakers_positions']; ?></p>
                                                         </div>
                                                      </a>
                                                   </div>
                                                </div>
                                          <?php }
                                          } ?>
                                       </div>
                                    </div>
                                    <div class="ent-btn">
                                       <a class="speakers-tab" href="#programspeakers" id="entertainment_speaker" style="margin-top:0px !important;">Entertainment</a>
                                    </div>
                                    <div class="gallery-lightboxes">
                                       <?php foreach ($records as  $value) {
                                          if ($value['speakers_positions_listing'] == 'Others') {
                                             if (!empty($value['speaker_image'])) {
                                                foreach ($value['speaker_image'] as $testimonialImg) {
                                                   $htmlAfterField = '<img src="' . CommonHelper::generateFullUrl('Events', 'sponsorshipimage', array($testimonialImg['afile_record_id'], $testimonialImg['afile_lang_id'], 'MEDIUM')) . '?' . time() . '">';
                                                }
                                             }    ?>
                                             <div class="image-lightbox" id=<?php echo $value['speakers_id']; ?>>
                                                <div class="image-lightbox-wrapper">
                                                   <a href="#0" class="close"></a>
                                                   <div class="lightbox-detail-text">
                                                      <div class="lightbox-top-detail">
                                                         <div class="lightbox-image-box">
                                                            <?php echo $htmlAfterField; ?>
                                                         </div>
                                                         <div class="image-title">
                                                            <h3><?php echo $value['speakers_user_name']; ?></h3>
                                                            <p class="list-sub-title"><?php echo $value['speakers_positions']; ?></p>
                                                         </div>
                                                      </div>
                                                      <div class="lightbox-bottom-detail">
                                                         <div class="profile-details">
                                                            <h5 class="strong"><?php echo Label::getLabel('LBL_About_The_Speaker', $adminLangId); ?></h5>
                                                            <div class="profile-description">
                                                               <?php echo $value['speakers_description']; ?>
                                                            </div>
                                                         </div>
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                       <?php }
                                       } ?>
                                    </div>
                                    <!-- End Lightbox-->
                                    <!-- Testimonial -->
                                    <div class="speakers_cards">
                                       <?php //echo FatUtility::decodeHtmlEntities($ProgramSpeakersTestimonial); 
                                       ?>
                                    </div>
                                 </div>
                              </div>
                           </div>

                           <div class="tab-pane fade" id="entertainments" role="tabpanel" aria-labelledby="entertainments-tab">
                              <div class="seperrateprogramTab-data">
                                 <div class="others-speakers">
                                    <div class="speakers-heading-main" id="speakers">
                                       <h4 class="speaker_heading"><?php echo Label::getLabel('LBL_Symposium_Entertainment', $adminLangId); ?></h4>
                                       <h3 class="speaker_heading"><?php echo Label::getLabel('LBL_Complete_List_Comming_Soon', $adminLangId); ?></h3>
                                    </div>
                                    <!-- Light Box -->
                                    <div class="gallery-wrapper">
                                       <div class="row">
                                          <?php
                                          foreach ($records as  $value) {
                                             if ($value['speakers_positions_listing'] == 'Entertainment') {
                                                if (!empty($value['speaker_image'])) {
                                                   foreach ($value['speaker_image'] as $testimonialImg) {
                                                      $htmlAfterField = '<img src="' . CommonHelper::generateFullUrl('Events', 'sponsorshipimage', array($testimonialImg['afile_record_id'], $testimonialImg['afile_lang_id'], 'HIGH')) . '?' . time() . '">';
                                                   }
                                                }
                                          ?>
                                                <div class="col-lg-6">
                                                   <div class="image-wrapper">
                                                      <a href=<?php echo "#" . $value['speakers_id']; ?>>
                                                         <div class="lightbox-image-box entertainment-image">
                                                            <?php echo $htmlAfterField; ?>
                                                            <div class="lightbox-image-hover">
                                                               <span><?php echo Label::getLabel('LBL_View_Profile_>', $adminLangId); ?></span>
                                                            </div>
                                                         </div>
                                                         <div class="image-title">
                                                            <h3><?php echo $value['speakers_user_name']; ?></h3>
                                                            <p class="list-sub-title"><?php echo $value['speakers_positions']; ?></p>
                                                         </div>
                                                      </a>
                                                   </div>
                                                </div>
                                          <?php }
                                          } ?>
                                       </div>
                                    </div>
                                    <div class="gallery-lightboxes">
                                       <?php foreach ($records as  $value) {
                                          if ($value['speakers_positions_listing'] == 'Entertainment') {
                                             if (!empty($value['speaker_image'])) {
                                                foreach ($value['speaker_image'] as $testimonialImg) {
                                                   $htmlAfterField = '<img src="' . CommonHelper::generateFullUrl('Events', 'sponsorshipimage', array($testimonialImg['afile_record_id'], $testimonialImg['afile_lang_id'], 'MEDIUM')) . '?' . time() . '">';
                                                }
                                             }    ?>
                                             <div class="image-lightbox" id=<?php echo $value['speakers_id']; ?>>
                                                <div class="image-lightbox-wrapper">
                                                   <a href="#0" class="close"></a>
                                                   <div class="lightbox-detail-text">
                                                      <div class="lightbox-top-detail">
                                                         <div class="lightbox-image-box">
                                                            <?php echo $htmlAfterField; ?>
                                                         </div>
                                                         <div class="image-title">
                                                            <h3><?php echo $value['speakers_user_name']; ?></h3>
                                                            <p class="list-sub-title"><?php echo $value['speakers_positions']; ?></p>
                                                         </div>
                                                      </div>
                                                      <div class="lightbox-bottom-detail">
                                                         <div class="profile-details">
                                                            <h5 class="strong"><?php echo Label::getLabel('LBL_About_The_Speaker', $adminLangId); ?></h5>
                                                            <div class="profile-description">
                                                               <?php echo $value['speakers_description']; ?>
                                                            </div>
                                                         </div>
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                       <?php }
                                       } ?>
                                    </div>
                                    <!-- End Lightbox-->
                                    <!-- Testimonial -->
                                    <div class="speakers_cards">
                                       <?php //echo FatUtility::decodeHtmlEntities($ProgramSpeakersTestimonial); 
                                       ?>
                                    </div>
                                 </div>
                              </div>
                           </div>

                        </div>
                     </div>
                     <div class="main-menu">
                        <a class="speakers-tab" href="#tabs" id="main_menu"><?php echo Label::getLabel('LBL_Main_Menu', $adminLangId); ?></a>
                     </div>
                  </div>
                  <!-- Three Reasons Section -->
                  <div class="tab-pane fade" id="threereason" role="tabpanel" aria-labelledby="threereason-tab">
                     <div class="threereason-box">
                        <h1><?php echo Label::getLabel('LBL_3_REASONS_WHY_YOU_MUST_ATTEND_MLC2022', $adminLangId); ?></h1>
                        <div class="row">
                           <div class="col-lg-7">
                              <div class="threereason-left-items ThreeReasonsTab-data">
                                 <?php foreach ($ThreeReasonsCategoriesList as $value) { ?>
                                    <button class="course-accordion"> <?php echo $value['three_reasons_user_name'];  ?></button>
                                    <div class="course-panel">
                                       <p><?php echo $value['three_reasons_description']; ?></p>
                                    </div>
                                 <?php } ?>
                              </div>
                           </div>
                           <div class="col-lg-5">
                              <div class="threereason-right-items">
                                 <div class="image-box">
                                    <img src="https://ubuntutalks.com//image/editor-image/1659683718-threereson.png" alt="">
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="main-menu">
                        <a class="speakers-tab" href="#tabs" id="main_menu"><?php echo Label::getLabel('LBL_Main_Menu', $adminLangId); ?></a>
                     </div>
                  </div>
                  <!-- Sponsorship Section -->
                  <div class="tab-pane fade" id="sponsorship" role="tabpanel" aria-labelledby="sponsorship-tab">
                     <div class="sopnsorship_section">
                        <div class="col-md-12 information-section">
                           <?php echo FatUtility::decodeHtmlEntities($SponsorContent); ?>
                        </div>
                        <div class="col-md-12 donation-button">
                           <a href="javascript:void(0)" onclick="GetSelectEventBecomeSponserPlan();" class="donation-sponosor-button">Sponsor Now</a>
                        </div>
                        <div class="price_tabel">
                           <h4><?php echo Label::getLabel('LBL_Sponsorship_Categories_For_UT_Symposium_And_Benefit_Concert', $adminLangId); ?></h4>
                           <table>
                              <tr>
                                 <th class="table-title"><?php echo Label::getLabel('LBL_Sponsorship_Categories', $adminLangId); ?></th>
                                 <?php foreach ($SponsorshipCategoriesList as  $value) { ?>
                                    <th class="table-title silver-colour"><?php echo $value['sponsorshipcategories_name']; ?></th>
                                 <?php } ?>
                              </tr>
                              <tr class="price_row">
                                 <th class="table-title"><?php echo Label::getLabel('LBL_Price', $adminLangId); ?></th>
                                 <?php foreach ($SponsorshipCategoriesList as  $value) { ?>
                                    <th class="table-title"><?php echo "$" . $value['sponsorshipcategories_plan_price']; ?></th>
                                 <?php } ?>
                              </tr>
                              <tr>
                                 <td class="price_row"><?php echo Label::getLabel('LBL_Conference_Passes', $adminLangId); ?></td>
                                 <?php foreach ($SponsorshipCategoriesList as  $value) { ?>
                                    <th class="table-title"><?php echo $value['sponsorshipcategories_tickets']; ?></th>
                                 <?php } ?>
                              </tr>
                              <tr>
                                 <td class="price_row"><?php echo Label::getLabel('LBL_20%_Discount_On_Additional_Passes', $adminLangId); ?></td>
                                 <?php foreach ($SponsorshipCategoriesList as  $value) {
                                    if ($value['sponsorshipcategories_additional_passes'] == 'Yes') { ?>
                                       <td class="check-icons"><i class="fa fa-check right-icon"></i></td>
                                    <?php } else { ?>
                                       <td class="check-icons"><i class="fa fa-ban ban-icon"></i></td>
                                 <?php }
                                 } ?>
                              </tr>
                              <tr>
                                 <td class="price_row"><?php echo Label::getLabel('LBL_Exhibit_Booth', $adminLangId); ?><sup><?php echo Label::getLabel('LBL_1', $adminLangId); ?></sup></td>
                                 <?php foreach ($SponsorshipCategoriesList as  $value) { ?>
                                    <th class="table-title"><?php echo $value['sponsorshipcategories_exhitbit_booth']; ?></th>
                                 <?php } ?>
                              </tr>
                              <tr>
                                 <td class="price_row"><?php echo Label::getLabel('LBL_Logo_Link_and_Blurb_On_The_Sponsor_Page', $adminLangId); ?></td>
                                 <?php foreach ($SponsorshipCategoriesList as  $value) {
                                    if ($value['sponsorshipcategories_logo_link_blurb'] == 'Yes') { ?>
                                       <td class="check-icons"><i class="fa fa-check right-icon"></i></td>
                                    <?php } else { ?>
                                       <td class="check-icons"><i class="fa fa-ban ban-icon"></i></td>
                                 <?php }
                                 } ?>
                              </tr>
                              <tr>
                                 <td class="price_row"><?php echo Label::getLabel('LBL_Logo_In_The_Website_Footer', $adminLangId); ?></td>
                                 <?php foreach ($SponsorshipCategoriesList as  $value) {
                                    if ($value['sponsorshipcategories_logo_footer'] == 'Yes') { ?>
                                       <td class="check-icons"><i class="fa fa-check right-icon"></i></td>
                                    <?php } else { ?>
                                       <td class="check-icons"><i class="fa fa-ban ban-icon"></i></td>
                                 <?php }
                                 } ?>
                              </tr>
                              <tr>
                                 <td class="price_row"><?php echo Label::getLabel('LBL_Static_Banner_Ad_To_Rotate_On_the_confernece_website', $adminLangId); ?></td>
                                 <?php foreach ($SponsorshipCategoriesList as  $value) {
                                    if ($value['sponsorshipcategories_banner'] == 'Yes') { ?>
                                       <td class="check-icons"><i class="fa fa-check right-icon"></i></td>
                                    <?php } else { ?>
                                       <td class="check-icons"><i class="fa fa-ban ban-icon"></i></td>
                                 <?php }
                                 } ?>
                              </tr>
                              <tr>
                                 <td class="price_row"><?php echo Label::getLabel('LBL_Logo_On_Onsite_Sponsor_Signage', $adminLangId); ?></td>
                                 <?php foreach ($SponsorshipCategoriesList as  $value) {
                                    if ($value['sponsorshipcategories_logo_sponsor_signage'] == 'Yes') { ?>
                                       <td class="check-icons"><i class="fa fa-check right-icon"></i></td>
                                    <?php } else { ?>
                                       <td class="check-icons"><i class="fa fa-ban ban-icon"></i></td>
                                 <?php }
                                 } ?>
                              </tr>
                              <tr>
                                 <td class="price_row"><?php echo Label::getLabel('LBL_Company_Named_In_Pre-_Conf_Attendee_Email', $adminLangId); ?></td>
                                 <?php foreach ($SponsorshipCategoriesList as  $value) {
                                    if ($value['sponsorshipcategories_attendee_email'] == 'Yes') { ?>
                                       <td class="check-icons"><i class="fa fa-check right-icon"></i></td>
                                    <?php } else { ?>
                                       <td class="check-icons"><i class="fa fa-ban ban-icon"></i></td>
                                 <?php }
                                 } ?>
                              </tr>

                              <tr>
                                 <td class="price_row"><?php echo Label::getLabel('LBL_Full_-_Color_Ad_In_Printed_Program_Guide', $adminLangId); ?></td>
                                 <?php foreach ($SponsorshipCategoriesList as  $value) {
                                    if ($value['sponsorshipcategories_program_guide'] == 'no') { ?>
                                       <td class="check-icons"><i class="fa fa-ban ban-icon"></i></td>
                                    <?php } else { ?>
                                       <th class="table-title"><?php echo $value['sponsorshipcategories_program_guide']; ?></th>
                                 <?php }
                                 } ?>
                              </tr>
                           </table>
                        </div>
                        <div class="price_tabel dinner-table">
                           <h4><?php echo Label::getLabel('LBL_Sponsorship_Categories_For_Pre_Symposium_Dinner', $adminLangId); ?></h4>
                           <table>
                              <tr>
                                 <th class="table-title"><?php echo Label::getLabel('LBL_Sponsorship_Categories', $adminLangId); ?></th>
                                 <?php foreach ($SponsorshipCategoriesDinnerList as  $value) { ?>
                                    <th class="table-title silver-colour"><?php echo $value['sponsorshipcategories_name']; ?></th>
                                 <?php } ?>
                              </tr>
                              <tr class="price_row">
                                 <th class="table-title"><?php echo Label::getLabel('LBL_Price', $adminLangId); ?></th>
                                 <?php foreach ($SponsorshipCategoriesDinnerList as  $value) { ?>
                                    <th class="table-title"><?php echo "$" . $value['sponsorshipcategories_plan_price']; ?></th>
                                 <?php } ?>
                              </tr>
                              <tr>
                                 <td class="price_row"><?php echo Label::getLabel('LBL_Conference_Passes', $adminLangId); ?></td>
                                 <?php foreach ($SponsorshipCategoriesDinnerList as  $value) { ?>
                                    <th class="table-title"><?php echo $value['sponsorshipcategories_tickets']; ?></th>
                                 <?php } ?>
                              </tr>
                              <tr>
                                 <td class="price_row"><?php echo Label::getLabel('LBL_20%_Discount_On_Additional_Passes', $adminLangId); ?></td>
                                 <?php foreach ($SponsorshipCategoriesDinnerList as  $value) {
                                    if ($value['sponsorshipcategories_additional_passes'] == 'Yes') { ?>
                                       <td class="check-icons"><i class="fa fa-check right-icon"></i></td>
                                    <?php } else { ?>
                                       <td class="check-icons"><i class="fa fa-ban ban-icon"></i></td>
                                 <?php }
                                 } ?>
                              </tr>
                              <tr>
                                 <td class="price_row"><?php echo Label::getLabel('LBL_Exhibit_Booth', $adminLangId); ?><sup><?php echo Label::getLabel('LBL_1', $adminLangId); ?></sup></td>
                                 <?php foreach ($SponsorshipCategoriesDinnerList as  $value) { ?>
                                    <th class="table-title"><?php echo $value['sponsorshipcategories_exhitbit_booth']; ?></th>
                                 <?php } ?>
                              </tr>
                              <tr>
                                 <td class="price_row"><?php echo Label::getLabel('LBL_Logo_Link_and_Blurb_On_The_Sponsor_Page', $adminLangId); ?></td>
                                 <?php foreach ($SponsorshipCategoriesDinnerList as  $value) {
                                    if ($value['sponsorshipcategories_logo_link_blurb'] == 'Yes') { ?>
                                       <td class="check-icons"><i class="fa fa-check right-icon"></i></td>
                                    <?php } else { ?>
                                       <td class="check-icons"><i class="fa fa-ban ban-icon"></i></td>
                                 <?php }
                                 } ?>
                              </tr>
                              <tr>
                                 <td class="price_row"><?php echo Label::getLabel('LBL_Logo_In_The_Website_Footer', $adminLangId); ?></td>
                                 <?php foreach ($SponsorshipCategoriesDinnerList as  $value) {
                                    if ($value['sponsorshipcategories_logo_footer'] == 'Yes') { ?>
                                       <td class="check-icons"><i class="fa fa-check right-icon"></i></td>
                                    <?php } else { ?>
                                       <td class="check-icons"><i class="fa fa-ban ban-icon"></i></td>
                                 <?php }
                                 } ?>
                              </tr>
                              <tr>
                                 <td class="price_row"><?php echo Label::getLabel('LBL_Static_Banner_Ad_To_Rotate_On_the_confernece_website', $adminLangId); ?></td>
                                 <?php foreach ($SponsorshipCategoriesDinnerList as  $value) {
                                    if ($value['sponsorshipcategories_banner'] == 'Yes') { ?>
                                       <td class="check-icons"><i class="fa fa-check right-icon"></i></td>
                                    <?php } else { ?>
                                       <td class="check-icons"><i class="fa fa-ban ban-icon"></i></td>
                                 <?php }
                                 } ?>
                              </tr>
                              <tr>
                                 <td class="price_row"><?php echo Label::getLabel('LBL_Logo_On_Onsite_Sponsor_Signage', $adminLangId); ?></td>
                                 <?php foreach ($SponsorshipCategoriesDinnerList as  $value) {
                                    if ($value['sponsorshipcategories_logo_sponsor_signage'] == 'Yes') { ?>
                                       <td class="check-icons"><i class="fa fa-check right-icon"></i></td>
                                    <?php } else { ?>
                                       <td class="check-icons"><i class="fa fa-ban ban-icon"></i></td>
                                 <?php }
                                 } ?>
                              </tr>
                              <tr>
                                 <td class="price_row"><?php echo Label::getLabel('LBL_Company_Named_In_Pre-_Conf_Attendee_Email', $adminLangId); ?></td>
                                 <?php foreach ($SponsorshipCategoriesDinnerList as  $value) {
                                    if ($value['sponsorshipcategories_attendee_email'] == 'Yes') { ?>
                                       <td class="check-icons"><i class="fa fa-check right-icon"></i></td>
                                    <?php } else { ?>
                                       <td class="check-icons"><i class="fa fa-ban ban-icon"></i></td>
                                 <?php }
                                 } ?>
                              </tr>
                              <!-- <tr>
                                 <td class="price_row"><?php echo Label::getLabel('LBL_Lead_Retrivel_Scanners', $adminLangId); ?></td>
                                 <?php foreach ($SponsorshipCategoriesDinnerList as  $value) {
                                    if ($value['sponsorshipcategories_retrieval_scanners'] == 'No') { ?>
                                       <td class="check-icons"><i class="fa fa-ban ban-icon"></i></td>
                                    <?php } else { ?>
                                       <th class="table-title"><?php echo $value['sponsorshipcategories_retrieval_scanners']; ?></th>
                                 <?php }
                                 } ?>
                              </tr> -->
                              <tr>
                                 <td class="price_row"><?php echo Label::getLabel('LBL_Full_-_Color_Ad_In_Printed_Program_Guide', $adminLangId); ?></td>
                                 <?php foreach ($SponsorshipCategoriesDinnerList as  $value) {
                                    if ($value['sponsorshipcategories_program_guide'] == 'no') { ?>
                                       <td class="check-icons"><i class="fa fa-ban ban-icon"></i></td>
                                    <?php } else { ?>
                                       <th class="table-title"><?php echo $value['sponsorshipcategories_program_guide']; ?></th>
                                 <?php }
                                 } ?>
                              </tr>
                           </table>
                           <div class="sidebar-btns download-flyer-button">
                              <a href="https://ubuntutalks.com/image/editor-image/1662959770-NewUTSymposium2023SponsorFlyer.jpeg" download="UT_Symposium_2023_ Sponsor_Flyer.jpeg" class="more-info-btn"><?php echo Label::getLabel('LBL_UT_Symposium_Sponsor_Flyer', $adminLangId); ?></a>
                           </div>
                           <div class="sponsorpage-button">
                              <a href="#" onclick="GetSelectEventBecomeSponserPlan();" class="button-more-info-btn"><?php echo Label::getLabel('LBL_Log_In_To_Sponsor', $adminLangId); ?></a>
                           </div>


                        </div>
                     </div>
                     <div class="sopnsorship_categories">
                        <div class="categories" id="sponsers">
                           <h4 class="sopnsorship-heading"><?php echo Label::getLabel('LBL_Our_Sponsors', $adminLangId); ?></h4>
                           <?php
                           $sponserArray = array();
                           $newSponser = array();
                           foreach ($Sponsershiprecords as  $value) {
                              $name = array();
                              if (array_key_exists($value['sponsorhip_type'], $sponserArray)) {
                                 $oldData = $sponserArray[$value['sponsorhip_type']];

                                 unset($sponserArray[$value['sponsorhip_type']]['sponsorship_image']);
                                 $sponsersImage = array();
                                 foreach ($oldData['sponsorship_image'] as $oldTestimonialImg) {
                                    $oldTestimonialImg['afile_id']->sponsorship_id = $value['sponsorship_id'];
                                    $id = $oldData['sponsorship_image'][$oldTestimonialImg['afile_id']];
                                    $sponsersImage[$oldTestimonialImg['afile_id']] = $oldTestimonialImg;
                                 }
                                 foreach ($value['sponsorship_image'] as $newTestimonialImg) {

                                    $sponsersImage[$newTestimonialImg['afile_id']] = $newTestimonialImg;
                                 }

                                 array_push($name, $oldData['sponsorship_user_name']);
                                 array_push($name, $value['sponsorship_user_name']);
                                 $sponserArray[$value['sponsorhip_type']]['user_name'] = $name;
                                 $sponserArray[$value['sponsorhip_type']]['sponsorship_image'] = $sponsersImage;
                              } else {
                                 array_push($name, $value['sponsorship_user_name']);
                                 $sponserArray[$value['sponsorhip_type']] = $value;
                                 $sponserArray[$value['sponsorhip_type']]['user_name'] = $name;
                                 array_push($newSponser, $sponserArray);
                              }
                           }
                           foreach ($sponserArray as  $value) {
                              $htmlAfterField = '';
                              $textField = '';

                              if (!empty($value['sponsorship_image'])) {
                                 $index = 0;
                                 foreach ($value['sponsorship_image'] as $testimonialImg) {
                                    $htmlAfterField .= '<a href=#sponsership_' . $testimonialImg['afile_record_id'] . '  class=sopnsorship_brand_img><div class=sponsorship-section><div class=img1><img src="' . CommonHelper::generateFullUrl('Events', 'image', array($testimonialImg['afile_record_id'], $testimonialImg['afile_lang_id'], 'MEDIUM')) . '?' . time() . '"></div><div class=sopnsorship_brand_name><p>' . $value['user_name'][$index] . '</p></div></div></a>';
                                    $index++;
                                 }
                              }
                           ?>
                              <div class="row">
                                 <div class="col-lg-6">
                                    <h4 class="categories_title" style='color:<?php echo $value['sponsorship_color'] . " !important"; ?>'>
                                       <?php echo $value['sponsorhip_type']; ?>
                                    </h4>
                                    <div class="sopnsorship_brand">
                                       <div class="sopnsorship_brand_img">
                                          <?php echo $htmlAfterField; ?>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           <?php  } ?>
                        </div>

                     </div>
                     <!-- #popup descriptio  -->
                     <div class="gallery-lightboxes">
                        <?php foreach ($Sponsershiprecords as  $value) {
                           $htmlAfterField = '';
                           $textField = '';
                           if (!empty($value['sponsorship_image'])) {
                              $index = 0;
                              foreach ($value['sponsorship_image'] as $testimonialImg) {
                                 $htmlAfterField .= '<div class=sponsorship-section><div class=img1><img src="' . CommonHelper::generateFullUrl('Events', 'image', array($testimonialImg['afile_record_id'], $testimonialImg['afile_lang_id'], 'MEDIUM')) . '?' . time() . '"></div><div class=sopnsorship_brand_name><p>' . $value['user_name'][$index] . '</p></div></div>';
                                 $index++;
                              }
                           }   ?>
                           <div class="image-lightbox" id=<?php echo "sponsership_" . $value['sponsorship_id']; ?>>

                              <div class="image-lightbox-wrapper">
                                 <a href="#0" class="close sponser_close"></a>
                                 <div class="lightbox-detail-text">
                                    <div class="lightbox-top-detail">
                                       <div class="lightbox-image-box">
                                          <?php echo $htmlAfterField; ?>
                                       </div>
                                       <div class="image-title">
                                          <h3><?php echo $value['sponsorship_user_name']; ?></h3>
                                          <p class="list-sub-title"><?php echo $value['sponsorship_positions']; ?></p>
                                          <a class="list-sub-title client_link" target="_blank" href="<?php echo $value['sponsorshipcategories_url']; ?>"><?php echo $value['sponsorshipcategories_url']; ?></a>
                                       </div>
                                    </div>
                                    <div class="lightbox-bottom-detail">
                                       <div class="profile-details">
                                          <h5 class="strong"><?php echo Label::getLabel('LBL_About_Client', $adminLangId); ?></h5>
                                          <div class="profile-description">
                                             <?php echo $value['sponsorship_description']; ?>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        <?php } ?>
                     </div>
                     <div class="main-menu">
                        <a class="speakers-tab" href="#tabs" id="main_menu"><?php echo Label::getLabel('LBL_Main_Menu', $adminLangId); ?></a>
                     </div>
                  </div>
                  <!-- Pre-Symposium Dinner Section -->
                  <div class="tab-pane fade" id="SymposiumDinner" role="tabpanel" aria-labelledby="SymposiumDinner-tab">
                     <div class="col-md-12">
                        <?php echo FatUtility::decodeHtmlEntities($PreSymposiumDinner); ?>
                     </div>
                     <div class="col-md-12 donation-title">
                        <div class="registration_card">
                           <h1 class="concert-title"><?php echo Label::getLabel('LBL_Tickets', $adminLangId); ?></h1>
                           <div class="row">
                              <?php
                              $index = 0;
                              foreach ($PreSymposiumDinnersDetailsList as  $value) {  ?>
                                 <div class="col-lg-4">
                                    <div class="card-list">
                                       <div class="ticket-card-header">
                                          <div>
                                             <h4 class="ticket-name">
                                                <div class="lt-line-clamp lt-line-clamp--multi-line">
                                                   <?php echo $value['pre_symposium_dinner_plan_title']; ?>
                                                </div>
                                             </h4>
                                          </div>
                                       </div>
                                       <div class="ticket-card-nav">
                                          <div>
                                             <h2 class="ticket-price"><?php echo "$" . $value['pre_symposium_dinner_plan_price']; ?></h2>
                                             <div class="ticket-date">
                                                <p class="ticket-decription"><?php echo $value['pre_symposium_dinner_plan_description']; ?></p>
                                                <div class="ticket-information">
                                                   <?php if ($value['pre_symposium_dinner_plan_note'] != '') { ?>
                                                      <p class="note">Note: <?php echo $value['pre_symposium_dinner_plan_note']; ?></p>
                                                   <?php } ?>
                                                   <div class="purchase-button">
                                                      <?php
                                                      $Currentdate = date('Y-m-d h:i');
                                                      if ($value['pre_symposium_dinner_ending_date'] < $Currentdate) {
                                                      ?>
                                                         <a href="javascript:void(0)" class="btn btn--primary events-buttons not-avilable"><?php echo Label::getLabel('LBL_Not_Available'); ?></a>
                                                      <?php
                                                      } elseif ($ticketManagerDetails[$index]['event_user_concert_id'] == $value['pre_symposium_dinner_id'] && $ticketManagerDetails[$index]['TotalTicket'] >= $value['pre_symposium_dinner_avilable_tickets']) { ?>
                                                         <a href="javascript:void(0)" class="btn btn--primary events-buttons not-avilable"><?php echo Label::getLabel('LBL_Not_Available'); ?></a>
                                                      <?php } else {
                                                      ?>
                                                         <a href="javascript:void(0)" onclick="GetSymposiumPlan();" class="btn btn--primary events-buttons"><?php echo Label::getLabel('LBL_Purchase'); ?></a>
                                                      <?php
                                                      }
                                                      ?>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>

                              <?php $index++;
                              } ?>
                           </div>
                        </div>
                     </div>
                     <div class="main-menu">
                        <a class="speakers-tab" href="#tabs" id="main_menu"><?php echo Label::getLabel('LBL_Main_Menu', $adminLangId); ?></a>
                     </div>
                  </div>
                  <!-- Benefit Concert Section -->
                  <div class="tab-pane fade" id="donation" role="tabpanel" aria-labelledby="donation-tab">
                     <div class="col-md-12 donation-title">
                        <?php echo FatUtility::decodeHtmlEntities($DonationContent); ?>
                     </div>
                     <div class="col-md-12 donation-button">
                        <a href="javascript:void(0)" onclick="GetEventDonation();" class="donation-sponosor-button">Donate Now</a>
                     </div>
                     <div class="aboutus-joyus-section">
                        <div class="col-md-12 donation-title">
                           <div class="row">
                              <?php
                              foreach ($BenefitConcertArtistsDetailsList as $value) {
                                 $lastWords = substr($value['benefit_concert_artists_video_link'], strrpos($value['benefit_concert_artists_video_link'], '/') + 1);

                              ?>
                                 <div class="row image-section video-section-block">
                                    <div class="col-lg-6">
                                       <h2>
                                          <p class="plan-title-desktop"><?php echo $value['benefit_concert_artists_plan_title']; ?></p>
                                       </h2>
                                       <div>
                                          <span class="benefits-artist-description"><?php echo $value['benefit_concert_artists_plan_description']; ?></span>
                                       </div>
                                    </div>
                                    <div class="col-lg-6">
                                       <h2>
                                          <p class="plan-title-mobile"><?php echo $value['benefit_concert_artists_plan_title']; ?></p>
                                       </h2>
                                       <div id="video">
                                          <iframe id="frame1" src="<?php echo $value['benefit_concert_artists_video_link']; ?>&amp;feature=youtu.be?autoplay=1&amp;rel=0&amp;loop=1&amp;playlist=<?php echo $lastWords; ?>&amp;mute=1" allowfullscreen="" width="100%" height="100%" frameborder="0"></iframe>
                                       </div>
                                    </div>
                                 </div>
                              <?php } ?>
                           </div>
                        </div>
                        <div class="col-md-12 sidebar-btns benefits-sponsor-button">
                           <a href="#sponsorship" class="more-info-btn sponsor"><?php echo Label::getLabel('LBL_Sponsor', $adminLangId); ?></a>
                        </div>
                     </div>
                     <div class="aboutus-joyus-section" style="padding-top:20px ;">
                        <div class="col-md-12 donation-title">
                           <?php echo FatUtility::decodeHtmlEntities($AboutVenue); ?>
                        </div>
                     </div>
                     <div class="col-md-12 donation-title">
                        <div class="registration_card">
                           <h1 class="concert-title"><?php echo Label::getLabel('LBL_Tickets', $adminLangId); ?></h1>
                           <div class="row">
                              <?php

                              $index = 0;
                              foreach ($BenefitConcertDetailsList as  $value) {  ?>
                                 <div class="col-lg-4">
                                    <div class="card-list">
                                       <div class="ticket-card-header">
                                          <div>
                                             <h4 class="ticket-name">
                                                <div class="lt-line-clamp lt-line-clamp--multi-line">
                                                   <?php echo $value['benefit_concert_plan_title']; ?>
                                                </div>
                                          </div>
                                          </h4>
                                       </div>
                                       <div class="ticket-card-nav">
                                          <div>
                                             <h2 class="ticket-price"><?php echo "$" . $value['benefit_concert_plan_price']; ?></h2>
                                             <div class="ticket-date">
                                                <p class="ticket-decription"><?php echo $value['benefit_concert_plan_description']; ?></p>
                                                <div class="ticket-information">
                                                   <?php if ($value['benefit_concert_plan_note'] != '') { ?>
                                                      <p class="note">Note: <?php echo $value['benefit_concert_plan_note']; ?></p>
                                                   <?php } ?>
                                                   <div class="purchase-button">
                                                      <?php
                                                      $Currentdate = date('Y-m-d h:i');
                                                      if ($value['benefit_concert_ending_date'] < $Currentdate) {
                                                      ?>
                                                         <a href="javascript:void(0)" class="btn btn--primary events-buttons not-avilable"><?php echo Label::getLabel('LBL_Not_Available'); ?></a>
                                                      <?php
                                                      } elseif ($ticketManagerDetails[$index]['event_user_concert_id'] == $value['benefit_concert_id'] && $ticketManagerDetails[$index]['TotalTicket'] >= $value['benefit_concert_avilable_tickets']) { ?>
                                                         <a href="javascript:void(0)" class="btn btn--primary events-buttons not-avilable"><?php echo Label::getLabel('LBL_Not_Available'); ?></a>
                                                      <?php } else {
                                                      ?>
                                                         <a href="javascript:void(0)" onclick="GetConcertPlan();" class="btn btn--primary events-buttons"><?php echo Label::getLabel('LBL_Purchase'); ?></a>
                                                      <?php
                                                      }
                                                      ?>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>

                              <?php $index++;
                              } ?>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-12 main-menu">
                        <a class="speakers-tab" href="#tabs" id="main_menu"><?php echo Label::getLabel('LBL_Main_Menu', $adminLangId); ?></a>
                     </div>
                  </div>
                  <!-- Agenda -->
                  <div class="tab-pane fade" id="agenda" role="tabpanel" aria-labelledby="agenda-tab">
                     <div class="row">
                        <div class="col-lg-12">
                           <div class="top-tab">
                              <div class="agenda-tabs">
                                 <div class="left-item">
                                    <div class="agenda-tab-list">
                                       <ul class="nav nav-tabs" id="agenda-tab" role="tablist">
                                          <?php
                                          $num = 0;

                                          foreach ($AgendaEventsList as $value) {
                                             if ($value['available_data'] > 0) {
                                                $splitTimeStamp = explode(" ", $value['agenda_start_time']);
                                                $date = $splitTimeStamp[0];
                                                $DiffTime = $splitTimeStamp[1];
                                                $nameOfDay = date('D', strtotime($date));
                                                if ($num == 0) {
                                                   $activeClass = "active";
                                                } else {
                                                   $activeClass = "";
                                                }
                                                $Starting_date = $value['registration_starting_date'];
                                                $CreatedDate_Convert = date("d F", strtotime($Starting_date));
                                                $Starting_date_title = $value['registration_starting_days'];
                                                $Agenda_Title = $CreatedDate_Convert . "-" . $Starting_date_title;
                                          ?>
                                                <li class="nav-item">
                                                   <a class="nav-link <?php echo $activeClass; ?>" id="<?php echo "agenda" . $value['three_reasons_id'] . "-tab"; ?>" data-toggle="tab" onclick="tabClick(<?php echo $value['three_reasons_id']; ?>);" href="#<?php echo $value['three_reasons_id']; ?>" role="tab" aria-controls="agendaOne" aria-selected="true">
                                                      <h6 class="event-name"><?php echo $Agenda_Title ?></h6>
                                                      <!-- <span class="agenda-date"><?php echo $nameOfDay . ", " . $date; ?></span> -->
                                                   </a>
                                                </li>
                                                <div class="nav-item-divider"></div>
                                          <?php $num++;
                                             }
                                          } ?>
                                       </ul>
                                    </div>
                                 </div>
                              </div>
                           </div>

                           <div class="tab-content" id="agendaContent">
                              <?php
                              $agendaArray = array();
                              $agenda_index = 0;
                              foreach ($FullAgendaCategoriesList as $value) {
                                 $duration = '';
                                 $splitTimeStamp = explode(" ", $value['agenda_start_time']);
                                 $StartTime = $splitTimeStamp[1];
                                 $time_in_12_hour_format  = date("g:i a", strtotime($StartTime));
                                 $dateDiff = intval((strtotime($value['agenda_end_time']) - strtotime($value['agenda_start_time'])) / 60);
                                 $hours = intval($dateDiff / 60);
                                 $minutes = $dateDiff % 60;
                                 $origin = new DateTime($value['agenda_start_time']);
                                 $target = new DateTime($value['agenda_end_time']);
                                 $interval = $origin->diff($target);
                                 $minutes_diff = ($interval->format('%i'));
                                 $hour_diff = ($interval->format('%h'));
                                 $day_diff = (($interval->format('%d')));
                                 if ($day_diff > 0) {
                                    $duration = $day_diff . " days ";
                                 }
                                 if ($hour_diff > 0) {
                                    $duration = $hour_diff . " hours ";
                                 }
                                 if ($minutes_diff > 0) {
                                    $duration .= $minutes_diff . " minutes";
                                 }
                                 if ($duration == '') {
                                    $duration = "0 hours";
                                 }

                              ?>
                                 <div class="tab-pane tab_agenda fade show" id="<?php echo $value['event_id']; ?>" role="tabpanel" aria-labelledby="<?php echo "agenda" . $value['event_id'] . "-tab"; ?>">
                                    <div class="timeline-item">
                                       <div class="timeline">
                                          <div class="timeline-sticky timezone">
                                             <h5><?php echo $StartTime; ?></h5>
                                          </div>
                                       </div>
                                       <div class="session-type-icon">
                                          <img src="https://iili.io/SXumsS.png" alt="">
                                       </div>
                                       <div class="timeline-track">
                                          <h5 class="timeline-title">
                                             <a class="#"><?php echo $value['agenda_schedule']; ?></a>
                                          </h5>
                                          <div class="timeline-track-sub-text">
                                             <p>
                                                <span><img src="https://iili.io/SXumsS.png" alt=""><?php echo $duration; ?></span>
                                                <span><img src="https://iili.io/SXubX2.png" alt=""><?php echo $value['agenda_event_location']; ?></span>
                                             </p>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              <?php } ?>
                           </div>

                        </div>
                     </div>
                     <div class="main-menu">
                        <a class="speakers-tab" href="#tabs" id="main_menu"><?php echo Label::getLabel('LBL_Main_Menu', $adminLangId); ?></a>
                     </div>
                  </div>
                  <!-- Travel & Lodging -->
                  <div class="tab-pane fade" id="accommodation" role="tabpanel" aria-labelledby="accommodation-tab">
                     <div class="travelAccommodation">
                        <ul class="nav nav-tabs" id="travelAccommodationTab" role="tablist">
                           <li class="nav-item">
                              <a class="nav-link active" id="venue-tab" data-toggle="tab" href="#venue" role="tab" aria-controls="venue" aria-selected="true"><?php echo Label::getLabel('LBL_Venue', $adminLangId); ?></a>
                           </li>
                           <li class="nav-item">
                              <a class="nav-link" id="Accommodations-tab" data-toggle="tab" href="#Accommodations" role="tab" aria-controls="Accommodations" aria-selected="false"><?php echo Label::getLabel('LBL_Accommodations', $adminLangId); ?></a>
                           </li>
                           <li class="nav-item">
                              <a class="nav-link" id="Travel-tab" data-toggle="tab" href="#Travel" role="tab" aria-controls="Travel" aria-selected="false"><?php echo Label::getLabel('LBL_Travel', $adminLangId); ?></a>
                           </li>
                           <li class="nav-item">
                              <a class="nav-link" id="Map-tab" data-toggle="tab" href="#Map" role="tab" aria-controls="Map" aria-selected="false"><?php echo Label::getLabel('LBL_Map', $adminLangId); ?></a>
                           </li>
                        </ul>
                        <div class="tab-content" id="travelAccommodationTabContent">
                           <div class="tab-pane fade show active" id="venue" role="tabpanel" aria-labelledby="venue-tab">
                              <div class="travelAccommodationTab-data">
                                 <?php echo FatUtility::decodeHtmlEntities($VenueSection); ?>
                              </div>
                           </div>
                           <div class="tab-pane fade" id="Accommodations" role="tabpanel" aria-labelledby="Accommodations-tab">
                              <div class="travelAccommodationTab-data">
                                 <?php echo FatUtility::decodeHtmlEntities($AccommodationsSection); ?>

                                 <!-- First Section -->
                                 <button type="button" class="collapsible">
                                    <?php echo Label::getLabel('LBL_PROTEA_BY_MARRIOT', $adminLangId); ?>
                                 </button>
                                 <div class="content">
                                    <p><?php echo FatUtility::decodeHtmlEntities($AccommodationFirst); ?></p>
                                 </div>

                                 <!-- Second Section -->
                                 <button type="button" class="collapsible">
                                    <?php echo Label::getLabel('LBL_NEELKANTH_SAROVAR_PREMIERE', $adminLangId); ?>
                                 </button>
                                 <div class="content">
                                    <p><?php echo FatUtility::decodeHtmlEntities($AccommodationSecond); ?></p>
                                 </div>

                                 <!-- Third Section -->
                                 <button type="button" class="collapsible">
                                    <?php echo Label::getLabel('LBL_RADDISON_BLU', $adminLangId); ?>
                                 </button>
                                 <div class="content">
                                    <p><?php echo FatUtility::decodeHtmlEntities($AccommodationThird); ?></p>
                                 </div>

                              </div>
                           </div>
                           <div class="tab-pane fade" id="Travel" role="tabpanel" aria-labelledby="Travel-tab">
                              <div class="travelAccommodationTab-data">
                                 <?php echo FatUtility::decodeHtmlEntities($TravelSection); ?>
                              </div>
                           </div>
                           <div class="tab-pane fade" id="Map" role="tabpanel" aria-labelledby="Map-tab">
                              <div class="travelAccommodationTab-data">
                                 <?php echo FatUtility::decodeHtmlEntities($MapSection); ?>
                                 <div class="map-section row">
                                    <div class="col-lg-5">
                                       <?php echo FatUtility::decodeHtmlEntities($MapInformationSection); ?>
                                    </div>
                                    <div class="col-lg-7">
                                       <div id="map" style="min-height: 500px !important;"></div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="main-menu">
                        <a class="speakers-tab" href="#tabs" id="main_menu"><?php echo Label::getLabel('LBL_Main_Menu', $adminLangId); ?></a>
                     </div>
                  </div>
                  <!-- Code Of Conduct -->
                  <div class="tab-pane fade" id="codeofconduct" role="tabpanel" aria-labelledby="codeofconduct-tab">
                     <div class="col-md-12 donation-title">
                        <?php echo FatUtility::decodeHtmlEntities($CodeOfConductContent); ?>
                     </div>
                     <div class="main-menu">
                        <a class="speakers-tab" href="#tabs" id="main_menu"><?php echo Label::getLabel('LBL_Main_Menu', $adminLangId); ?></a>
                     </div>
                  </div>
                  <!-- Covid Information -->
                  <div class="tab-pane fade" id="safety" role="tabpanel" aria-labelledby="safety-tab">
                     <div class="tab-detail">
                        <div class="events-tab-detail">
                           <?php echo FatUtility::decodeHtmlEntities($CovidInformation); ?>
                        </div>
                        <div class="main-menu">
                           <a class="speakers-tab" href="#tabs" id="main_menu"><?php echo Label::getLabel('LBL_Main_Menu', $adminLangId); ?></a>
                        </div>
                     </div>
                  </div>
                  <!-- Press Information -->
                  <div class="tab-pane fade" id="press" role="tabpanel" aria-labelledby="press-tab">
                     <div class="tab-detail">
                        <div class="events-tab-detail">
                           <?php echo FatUtility::decodeHtmlEntities($PressInformation); ?>
                        </div>
                        <div class="main-menu">
                           <a class="speakers-tab" href="#tabs" id="main_menu"><?php echo Label::getLabel('LBL_Main_Menu', $adminLangId); ?></a>
                        </div>
                     </div>
                  </div>
                  <!-- Contact Information -->
                  <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                     <div class="tab-detail">
                        <div class="events-tab-detail">
                           <?php echo FatUtility::decodeHtmlEntities($ContactInformation); ?>
                        </div>
                        <div class="section contact-form event-form">
                           <div class="row align-items-center referral-campaign-form event-contact-form">
                              <div class="col-lg-12">
                                 <div class="banner__media ">
                                    <div class="contact-form">
                                       <?php echo $contactFrm->getFormTag() ?>
                                       <div class="row">
                                          <div class="col-md-6">
                                             <div class="field-set">
                                                <div class="caption-wraper">
                                                   <label class="field_label"><?php echo Label::getLabel('LBL_Name', $siteLangId) ?></label>
                                                </div>
                                                <div class="field-wraper">
                                                   <div class="field_cover">
                                                      <?php echo $contactFrm->getFieldHTML('name'); ?>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                          <div class="col-md-6">
                                             <div class="field-set">
                                                <div class="caption-wraper">
                                                   <label class="field_label"><?php echo Label::getLabel('LBL_Email', $siteLangId) ?></label>
                                                </div>
                                                <div class="field-wraper">
                                                   <div class="field_cover">
                                                      <?php echo $contactFrm->getFieldHTML('email'); ?>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </div>

                                       <div class="row">
                                          <div class="col-md-12">
                                             <div class="field-set">
                                                <div class="caption-wraper">
                                                   <label class="field_label"><?php echo Label::getLabel('LBL_Subject', $siteLangId) ?></label>
                                                </div>
                                                <div class="field-wraper">
                                                   <div class="field_cover">
                                                      <?php echo $contactFrm->getFieldHTML('subject'); ?>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                       <div class="row">
                                          <div class="col-md-12">
                                             <div class="field-set">
                                                <div class="caption-wraper">
                                                   <label class="field_label"><?php echo Label::getLabel('LBL_Your_Questions_Or_Commenrs', $siteLangId) ?></label>
                                                </div>
                                                <div class="field-wraper">
                                                   <div class="field_cover comment-section">
                                                      <?php echo $contactFrm->getFieldHTML('message'); ?>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                       <?php if (FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, '') != '') { ?>
                                          <div class="row">
                                             <div class="col-md-12">
                                                <div class="field-set">
                                                   <div class="field-wraper">
                                                      <div class="g-recaptcha" data-sitekey="<?php echo FatApp::getConfig('CONF_RECAPTCHA_SITEKEY', FatUtility::VAR_STRING, ''); ?>"></div>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       <?php } ?>
                                       <div class="events-tab-detail privacy-information">
                                          <?php echo FatUtility::decodeHtmlEntities($PrivacyInformation); ?>
                                       </div>
                                       <div class="row">
                                          <div class="col-md-12">
                                             <div class="field-set">
                                                <div class="field-wraper">
                                                   <div class="field_cover">
                                                      <?php echo $contactFrm->getFieldHTML('btn_submit'); ?>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                       <?php echo $contactFrm->getExternalJS(); ?>
                                       </form>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="main-menu">
                        <a class="speakers-tab" href="#tabs" id="main_menu"><?php echo Label::getLabel('LBL_Main_Menu', $adminLangId); ?></a>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script> -->

<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src='https://www.google.com/recaptcha/api.js'></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js" integrity="sha512-bPs7Ae6pVvhOSiIcyUClR7/q2OAsRiovw4vAkX+zJbw3ShAeeqezq50RIIcIURq7Oa20rW2n2q+fyXBNcU9lrw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.js" integrity="sha512-gY25nC63ddE0LcLPhxUJGFxa2GoIyA5FLym4UJqHDEMHjp8RET6Zn/SHo1sltt3WuVtqfyxECP38/daUc/WVEA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.css" integrity="sha512-UTNP5BXLIptsaj5WdKFrkFov94lDx+eBvbKyoe1YAfjeRPC+gT5kyZ10kOHCfNZqEui1sxmqvodNUx3KbuYI/A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" integrity="sha512-tS3S5qG0BlhnQROyJXvNjeEM4UpMXHrQfTGmbQ1gKmelCxlSEBUaxhRBj/EFTzpbP4RVSrpEikbmdJobCvhE3g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.css" integrity="sha512-OTcub78R3msOCtY3Tc6FzeDJ8N9qvQn1Ph49ou13xgA9VsH9+LRxoFU6EqLhW4+PKRfU+/HReXmSZXHEkpYoOA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" integrity="sha512-sMXtMNL1zRzolHYKEujM2AqCLUR9F2C4/05cdbxjjLSRvMQIciEPCQZo++nk7go3BtSuK9kfa/s+a4f4i5pLkw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDL7EmtEH4QGTYEhHkIt4tqf4QJ71YK_yY&callback=initMap"></script>

<script>
   $(document).ready(function() {
      var dateStrings = "2023/03/30"
      var deadlines = new Date(dateStrings);

      function updateClocks() {
         var todays = Date();
         var diffs = Date.parse(deadlines) - Date.parse(todays);
         if (diffs <= 0) {
            clearInterval(intervals);
         } else {
            var secondss = Math.floor((diffs / 1000) % 60);
            var minutess = Math.floor((diffs / 1000 / 60) % 60);
            var hourss = Math.floor((diffs / 1000 / 60 / 60) % 24);
            var dayss = Math.floor(diffs / (1000 * 60 * 60 * 24) % 30.5);
            var monthss = Math.floor(diffs / (1000 * 60 * 60 * 24 * 30.5) % 12);

            $("#monthss").text(('0' + monthss).slice(-2));
            $("#dayss").text(('0' + dayss).slice(-2));
            $("#hourss").text(('0' + hourss).slice(-2));
            $("#minutess").text(('0' + minutess).slice(-2));
            $("#secondss").text(('0' + secondss).slice(-2));

         } //EOF ELSE

      } //EOF FUNCTION

      var intervals = setInterval(updateClocks, 1000);

   });
   $(document).ready(function() {
      var dateString = "2023/03/30"
      var deadline = new Date(dateString);

      function updateClock() {
         var today = Date();
         var diff = Date.parse(deadline) - Date.parse(today);
         if (diff <= 0) {
            clearInterval(interval);
         } else {
            var seconds = Math.floor((diff / 1000) % 60);
            var minutes = Math.floor((diff / 1000 / 60) % 60);
            var hours = Math.floor((diff / 1000 / 60 / 60) % 24);
            var days = Math.floor(diff / (1000 * 60 * 60 * 24) % 30.5);
            var months = Math.floor(diff / (1000 * 60 * 60 * 24 * 30.5) % 12);

            $("#months").text(('0' + months).slice(-2));
            $("#days").text(('0' + days).slice(-2));
            $("#hours").text(('0' + hours).slice(-2));
            $("#minutes").text(('0' + minutes).slice(-2));
            $("#seconds").text(('0' + seconds).slice(-2));

         } //EOF ELSE

      } //EOF FUNCTION

      var interval = setInterval(updateClock, 1000);

   }); //EOF DOCUMENT.READY


   $(document).ready(function() {
      setTimeout(function() {
         $(".ytp-large-play-button").trigger("click");
         console.log("Hello");
      }, 10000);
      var btn = $('#button');

      $(window).scroll(function() {
         if ($(window).scrollTop() > 300) {
            btn.addClass('show');
         } else {
            btn.removeClass('show');
         }
      });

      btn.on('click', function(e) {
         e.preventDefault();
         $('html, body').animate({
            scrollTop: 0
         }, '300');
      });


      $(window).on("resize", function(e) {
         checkScreenSize();
      });

      checkScreenSize();

      function checkScreenSize() {
         var newWindowWidth = $(window).width();
         if (newWindowWidth < 992) {
            $(".events-tabs #myTab a").click(function() {
               $.loader.show();
               setTimeout(function(){
                  $.loader.hide();
               }, 3000);
               $('html, body').animate({
                  scrollTop: $(".sidebar-social").offset().top
               }, 500);
            });
            $(".main-menu a[href^='#']").click(function(e) {
               e.preventDefault();
               var position = $($(this).attr("href")).offset().top;
               $("body, html").animate({
                  scrollTop: 750
               } /* speed */ );
            });

            $(".symposium-detais a[href^='#'], .ent-btn a[href^='#']").click(function(e) {
               e.preventDefault();
               var position = $($(this).attr("href")).offset().top;
               $("body, html").animate({
                  scrollTop: 1500
               } /* speed */ );
            });
            $('#bottom_speaker').click(function(evt) {
               setTimeout(function() {
                  $('#speakers-tab').trigger('click');
               }, 300);
            });
            $('#entertainment_speaker').click(function(evt) {
               setTimeout(function() {
                  $('#entertainments-tab').trigger('click');
               }, 600);
            });

            $(".symposium-detais a[href^='#'], .ent-btn a[href^='#'], .main-menu a[href^='#']").click(function(e) {
               e.preventDefault();
               var position = $($(this).attr("href")).offset().top;
               $("body, html").animate({
                  scrollTop: 1500
               } /* speed */ );
            });

            // Sponsor button for Mobile
            $(".sidebar-btns a.sponsor[href^='#']").click(function(e) {
               e.preventDefault();
               var position = $($(this).attr("href")).offset().top;
               $("body, html").animate({
                  scrollTop: 1500
               } /* speed */ );
               setTimeout(function() {
                  $('#sponsorship-tab').trigger('click');
               }, 300);
            });

            // Donation button for Mobile
            $(".sidebar-btns a.donation[href^='#']").click(function(e) {
               e.preventDefault();
               var position = $($(this).attr("href")).offset().top;
               $("body, html").animate({
                  scrollTop: 1500
               } /* speed */ );
               setTimeout(function() {
                  $('#donation-tab').trigger('click');
               }, 300);
            });
         } else {
            $(".ent-btn a[href^='#']").click(function(e) {
               e.preventDefault();
               var position = $($(this).attr("href")).offset().top;
               $("body, html").animate({
                  scrollTop: 600
               } /* speed */ );
            });

            $(".symposium-detais a[href^='#']").click(function(e) {
               e.preventDefault();
               var position = $($(this).attr("href")).offset().top;
               $("body, html").animate({
                  scrollTop: 600
               } /* speed */ );
            });
            $('#bottom_speaker').click(function(evt) {
               setTimeout(function() {
                  $('#speakers-tab').trigger('click');
               }, 300);
            });
            $('#entertainment_speaker').click(function(evt) {
               setTimeout(function() {
                  $('#entertainments-tab').trigger('click');
               }, 600);
            });

            $(".main-menu a[href^='#']").click(function(e) {
               e.preventDefault();
               var position = $($(this).attr("href")).offset().top;
               $("body, html").animate({
                  scrollTop: 550
               } /* speed */ );
            });

            // Sponsor Button For Desktop
            $(".sidebar-btns a.sponsor[href^='#']").click(function(e) {
               e.preventDefault();
               var position = $($(this).attr("href")).offset().top;
               $("body, html").animate({
                  scrollTop: 500
               } /* speed */ );
               setTimeout(function() {
                  $('#sponsorship-tab').trigger('click');
               }, 300);
            });

            // Donation button for Desktop
            $(".sidebar-btns a.donation[href^='#']").click(function(e) {
               e.preventDefault();
               var position = $($(this).attr("href")).offset().top;
               $("body, html").animate({
                  scrollTop: 500
               } /* speed */ );
               setTimeout(function() {
                  $('#donation-tab').trigger('click');
               }, 300);
            });

         }
      }
   });


   function initMap() {
      // The location of Uluru
      const uluru = {
         lat: -15.3893,
         lng: 28.3133
      };

      // The map, centered at Uluru
      const map = new google.maps.Map(document.getElementById("map"), {
         zoom: 17,
         center: uluru,
      });
      // The marker, positioned at Uluru
      const marker = new google.maps.Marker({
         position: uluru,
         map: map,
      });
   }
   var initial_list_id = document.getElementsByClassName("tab_agenda")[0].id;
   tabClick(initial_list_id);

   function tabClick(id) {
      console.log("id", id);
      var list = document.getElementsByClassName("tab_agenda");
      for (var i = 0; i < list.length; i++) {
         var tab_id = list[i].getAttribute("id");
         console.log("ii", tab_id);
         if (tab_id == id) {
            list[i].classList.add("active");
         } else {
            list[i].classList.remove("active");
         }
      }
   }
   window.initMap = initMap;
   $(".cards").click(function() {
      $(".authore_information").show();
   });

   $(".x-mark").click(function() {
      $(".authore_information").hide();
   });

   //this is the button
   var acc = document.getElementsByClassName("course-accordion");
   var i;

   for (i = 0; i < acc.length; i++) {
      //when one of the buttons are clicked run this function
      acc[i].onclick = function() {
         //variables
         var panel = this.nextElementSibling;
         var coursePanel = document.getElementsByClassName("course-panel");
         var courseAccordion = document.getElementsByClassName("course-accordion");
         var courseAccordionActive = document.getElementsByClassName("course-accordion active");

         /*if pannel is already open - minimize*/
         if (panel.style.maxHeight) {
            //minifies current pannel if already open
            panel.style.maxHeight = null;
            //removes the 'active' class as toggle didnt work on browsers minus chrome
            this.classList.remove("active");
         } else { //pannel isnt open...
            //goes through the buttons and removes the 'active' css (+ and -)
            for (var ii = 0; ii < courseAccordionActive.length; ii++) {
               courseAccordionActive[ii].classList.remove("active");
            }
            //Goes through and removes 'activ' from the css, also minifies any 'panels' that might be open
            for (var iii = 0; iii < coursePanel.length; iii++) {
               this.classList.remove("active");
               coursePanel[iii].style.maxHeight = null;
            }
            //opens the specified pannel
            panel.style.maxHeight = panel.scrollHeight + "px";
            //adds the 'active' addition to the css.
            this.classList.add("active");
         }
      } //closing to the acc onclick function
   } //closing to the for loop.


   var coll = document.getElementsByClassName("collapsible");
   var i;
   for (i = 0; i < coll.length; i++) {
      coll[i].addEventListener("click", function() {
         this.classList.toggle("active");
         var content = this.nextElementSibling;
         if (content.style.display === "block") {
            content.style.display = "none";
         } else {
            content.style.display = "block";
         }
      });
   }

   $('.owl-carousel').owlCarousel({
      margin: 10,
      loop: true,
      autoplay: true,
      dots: false,
      autoplayTimeout: 5000,
      animateIn: 'fadeIn',
      nav: false,
      responsive: {
         0: {
            items: 1
         },
         600: {
            items: 1
         },
         1000: {
            items: 1
         }
      }
   })
</script>
<script src="https://unpkg.com/sweetalert2@7.8.2/dist/sweetalert2.all.js"></script>