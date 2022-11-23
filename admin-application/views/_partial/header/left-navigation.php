<!--left panel start here-->
<?php
$adminLoggedId = AdminAuthentication::getLoggedAdminId();
?>
<style>
    .leftmenu>li ul ul {
        padding: 0 0 0 15px;
        border-top: none;
    }

    .leftmenu>li ul ul:before {
        content: none;
    }

    .leftmenu>li ul ul a:before {
        width: 30px;
        left: -16px;
    }

    .leftmenu>li ul.submenu {
        display: block !important;
        border-top: 0px solid !important;
    }
</style>
<span class="leftoverlay"></span>
<aside class="leftside">
    <div class="sidebar_inner">
        <div class="profilewrap">
            <div class="profilecover">
                <figure class="profilepic"><img id="leftmenuimgtag" alt="" src="<?php echo CommonHelper::generateUrl('profile', 'profileImage', array($adminLoggedId, "THUMB", true)) . '?' . time(); ?>" alt=""></figure>
                <span class="profileinfo"><?php //echo Label::getLabel('LBL_Welcome', $adminLangId); 
                                            ?> <?php echo $adminName; ?></span>
            </div>
            <div class="profilelinkswrap">
                <ul class="leftlinks">
                    <li class=""><a href="<?php echo CommonHelper::generateUrl('profile'); ?>"><?php echo Label::getLabel('LBL_View_Profile', $adminLangId); ?></a></li>
                    <li class=""><a href="<?php echo CommonHelper::generateUrl('profile', 'changePassword'); ?>"><?php echo Label::getLabel('LBL_Change_Password', $adminLangId); ?></a></li>
                    <li class=""><a href="<?php echo CommonHelper::generateUrl('profile', 'logout'); ?>"><?php echo Label::getLabel('LBL_Logout', $adminLangId); ?></a></li>
                </ul>
            </div>
        </div>
        <ul class="leftmenu">
            <!--Dashboard-->
            <?php
            if ($objPrivilege->canViewAdminDashboard($adminLoggedId, true)) {
            ?>
                <li><a href="<?php echo CommonHelper::generateUrl(); ?>"><?php echo Label::getLabel('LBL_Dashboard', $adminLangId); ?></a></li>
            <?php } ?>
            <?php
            if (
                $objPrivilege->canViewUsers($adminLoggedId, true) ||
                $objPrivilege->canViewTeacherApprovalRequests($adminLoggedId, true) ||
                $objPrivilege->canViewWithdrawRequests($adminLoggedId, true) ||
                $objPrivilege->canViewTeacherReviews($adminLoggedId, true)
            ) {
            ?>
                <li class="haschild"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_Users', $adminLangId); ?></a>
                    <ul>
                        <?php if ($objPrivilege->canViewUsers($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('Users'); ?>"><?php echo Label::getLabel('LBL_Users', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewTeacherApprovalRequests($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('TeacherRequests'); ?>"><?php echo Label::getLabel('LBL_Teacher_Approval_Requests', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewWithdrawRequests($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('WithdrawalRequests'); ?>"><?php echo Label::getLabel('LBL_User_Withdrwal_Requests', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewTeacherReviews($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('teacherReviews'); ?>"><?php echo Label::getLabel('LBL_Teacher_Reviews', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewTeacherReviews($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('SentEmails'); ?>"><?php echo Label::getLabel('LBL_Email_Verification', $adminLangId); ?></a></li>
                        <?php } ?>
                    </ul>
                </li>
                <li class="haschild"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_Virtual_Session', $adminLangId); ?></a>
                    <ul>
                        <?php if ($objPrivilege->canViewUsers($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('VirtualMainSessions'); ?>"><?php echo Label::getLabel('LBL_Virtual_Main_Session', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewUsers($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('VirtualSession'); ?>"><?php echo Label::getLabel('LBL_Virtual_Session', $adminLangId); ?></a></li>
                        <?php } ?>
                    </ul>
                </li>
                <li><a href="<?php echo CommonHelper::generateUrl('BookedLessons', 'viewSchedules', ['all']); ?>"><?php echo Label::getLabel('LBL_Booked_Lessons', $adminLangId); ?></a></li>
                <li><a href="<?php echo CommonHelper::generateUrl('ReportAnIssue', 'viewSchedules', ['all']); ?>"><?php echo Label::getLabel('LBL_Report_An_Issue', $adminLangId); ?></a></li>
                <li class="haschild"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_About_Us', $adminLangId); ?></a>
                    <ul>
                        <?php if ($objPrivilege->canViewUsers($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('aboutusContentBlock'); ?>"><?php echo Label::getLabel('LBL_Content_Blocks', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewUsers($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('ourVision'); ?>"><?php echo Label::getLabel('LBL_Our_Vision', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewUsers($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('ourMission'); ?>"><?php echo Label::getLabel('LBL_Our_Mission', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewUsers($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('meetTheTeam'); ?>"><?php echo Label::getLabel('LBL_Meet_The_Team', $adminLangId); ?></a></li>
                        <?php } ?>
                    </ul>
                </li>
                <li class="haschild"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_UT_Events', $adminLangId); ?></a>
                    <ul>
                        <?php if ($objPrivilege->canViewUsers($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('EventUsers'); ?>"><?php echo Label::getLabel('LBL_Events_User_Listing', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewUsers($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('LanguageSymposium'); ?>"><?php echo Label::getLabel('LBL_UT_Content_Blocks', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewUsers($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('EventsReportAnIssue', 'viewSchedules', ['all']); ?>"><?php echo Label::getLabel('LBL_Events_Report_Issue', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewUsers($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('ProgramAndSpeakers'); ?>"><?php echo Label::getLabel('LBL_Program_&_Speakers', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewUsers($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('EventsSponsorshipCategories'); ?>"><?php echo Label::getLabel('LBL_Sponsorship_Categories', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewUsers($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('Sponsorship'); ?>"><?php echo Label::getLabel('LBL_Sponsorship', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewUsers($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('Agenda'); ?>"><?php echo Label::getLabel('LBL_Agenda_Details', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewUsers($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('EventRegistration'); ?>"><?php echo Label::getLabel('LBL_Registration', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewUsers($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('RegistrationPlan'); ?>"><?php echo Label::getLabel('LBL_Registration_Plan_Details', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewUsers($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('BenefitConcert'); ?>"><?php echo Label::getLabel('LBL_Benefit_Concert_Tickets_Category', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewUsers($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('PreSymposiumDinner'); ?>"><?php echo Label::getLabel('LBL_Pre_Symposium_Dinner_Tickets_Category', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewUsers($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('BenefitConcertArtists'); ?>"><?php echo Label::getLabel('LBL_Benefit_Concert_Artists', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewUsers($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('SponsorshipProgramGuide'); ?>"><?php echo Label::getLabel('LBL_Sponsorship_Program_Guide', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewUsers($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('SpeakersPosition'); ?>"><?php echo Label::getLabel('LBL_Speakers_Positions_Listing', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewUsers($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('SponsorshipCategories'); ?>"><?php echo Label::getLabel('LBL_Sponsorship_Plan', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewUsers($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('Speakers'); ?>"><?php echo Label::getLabel('LBL_Speakers_List', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewUsers($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('TravelAndAccommodations'); ?>"><?php echo Label::getLabel('LBL_Travel_&_Accommodations', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewUsers($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('Accommodations'); ?>"><?php echo Label::getLabel('LBL_Accommodation', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewUsers($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('CovidInformation'); ?>"><?php echo Label::getLabel('LBL_Covid_19_Safety', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewUsers($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('EventPress'); ?>"><?php echo Label::getLabel('LBL_Press', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewUsers($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('EventContact'); ?>"><?php echo Label::getLabel('LBL_Contact', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewUsers($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('EventFaq'); ?>"><?php echo Label::getLabel('LBL_Event_Faq', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewUsers($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('CurrencySwitcher'); ?>"><?php echo Label::getLabel('LBL_Currency_Switcher_Management', $adminLangId); ?></a></li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
            <?php if ($objPrivilege->canViewGroupClasses($adminLoggedId, true)) { ?>
                <li><a href="<?php echo CommonHelper::generateUrl('GroupClasses'); ?>"><?php echo Label::getLabel('LBL_Group_Classes', $adminLangId); ?></a></li>
            <?php } ?>
            <?php if ($objPrivilege->canViewGroupClasses($adminLoggedId, true)) { ?>
                <li><a href="<?php echo CommonHelper::generateUrl('KidsClasses'); ?>"><?php echo Label::getLabel('LBL_Kids_Classes', $adminLangId); ?></a></li>
            <?php } ?>
            <?php if ($objPrivilege->canViewPurchasedLessons($adminLoggedId, true) || $objPrivilege->canViewGiftcards($adminLoggedId, true)) { ?>
                <li class="haschild"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_Orders', $adminLangId); ?></a>
                    <ul>
                        <?php if ($objPrivilege->canViewPurchasedLessons($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('PurchasedLessons'); ?>"><?php echo Label::getLabel('LBL_Orders', $adminLangId); ?></a></li>
                            <li><a href="<?php echo CommonHelper::generateUrl('PurchasedLessons', 'viewSchedules', ['all']); ?>"><?php echo Label::getLabel('LBL_Order_Lessons', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewGiftcards($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('giftcards'); ?>"><?php echo Label::getLabel('LBL_Gift_Orders', $adminLangId); ?></a></li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
            <?php if ($objPrivilege->canViewIssuesReported($adminLoggedId, true)) { ?>
                <li class="haschild"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_Issues_Reported', $adminLangId); ?></a>
                    <ul>
                        <li><a href="<?php echo CommonHelper::generateUrl('ReportedIssues', 'escalated'); ?>"><?php echo Label::getLabel('LBL_Escalated_Issues', $adminLangId); ?></a></li>
                        <li><a href="<?php echo CommonHelper::generateUrl('ReportedIssues'); ?>"><?php echo Label::getLabel('LBL_All_Reported_Issues', $adminLangId); ?></a></li>
                    </ul>
                </li>
            <?php } ?>
            <?php if ($objPrivilege->canViewPreferences($adminLoggedId, true) || $objPrivilege->canViewSpokenLanguage($adminLoggedId, true) || $objPrivilege->canViewTeachingLanguage($adminLoggedId, true)) { ?>
                <li class="haschild"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_Teacher_Preferences', $adminLangId); ?></a>
                    <ul>
                        <?php
                        if ($objPrivilege->canViewPreferences($adminLoggedId, true)) {
                            foreach (Preference::getPreferenceTypeArr($adminLangId) as $referenceType => $preference) {
                        ?>
                                <li><a href="<?php echo CommonHelper::generateUrl('preferences', 'index', array($referenceType)); ?>"><?php echo $preference; ?></a></li>
                        <?php
                            }
                        }
                        ?>
                        <?php if ($objPrivilege->canViewSpokenLanguage($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('spokenLanguage'); ?>"><?php echo Label::getLabel('LBL_Spoken_Language', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewTeachingLanguage($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('teachingLanguage'); ?>"><?php echo Label::getLabel('LBL_Teaching_Language', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewPreferences($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('issueReportOptions'); ?>"><?php echo Label::getLabel('LBL_Issue_Report_Options', $adminLangId); ?></a></li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
            <!--CMS[-->
            <?php
            if (
                $objPrivilege->canViewContentPages($adminLoggedId, true) ||
                $objPrivilege->canViewContentBlocks($adminLoggedId, true) ||
                $objPrivilege->canViewNavigationManagement($adminLoggedId, true) ||
                $objPrivilege->canViewTimezones($adminLoggedId, true) ||
                $objPrivilege->canViewCountries($adminLoggedId, true) ||
                $objPrivilege->canViewSocialPlatforms($adminLoggedId, true) ||
                $objPrivilege->canViewDiscountCoupons($adminLoggedId, true) ||
                $objPrivilege->canViewPriceSlab($adminLoggedId, true) ||
                $objPrivilege->canViewSpokenLanguage($adminLoggedId, true) ||
                $objPrivilege->canViewLessonPackages($adminLoggedId, true) ||
                $objPrivilege->canViewLanguageLabel($adminLoggedId, true) ||
                $objPrivilege->canViewBibleContent($adminLoggedId, true) ||
                $objPrivilege->canViewFaq($adminLoggedId, true) || $objPrivilege->canViewFaqCategory($adminLoggedId, true)
            ) {
            ?>
                <li class="haschild"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_Cms', $adminLangId); ?></a>
                    <ul>
                        <?php if ($objPrivilege->canViewContentPages($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('ContentPages'); ?>"><?php echo Label::getLabel('LBL_Content_Pages', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewContentBlocks($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('ContentBlock'); ?>"><?php echo Label::getLabel('LBL_Content_Blocks', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewBibleContent($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('BibleContent'); ?>"><?php echo Label::getLabel('LBL_Bible_Content', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php /* if ($objPrivilege->canViewSlides($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('slides'); ?>"><?php echo Label::getLabel('LBL_Home_Page_Slides_Management', $adminLangId); ?></a></li>
                        <?php } */ ?>
                        <?php if ($objPrivilege->canViewPriceSlab($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('PriceSlabs'); ?>"><?php echo Label::getLabel('LBL_Price_Slabs', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewBanners($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('Banners'); ?>"><?php echo Label::getLabel('LBL_Banners', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewTestimonial($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('Testimonials'); ?>"><?php echo Label::getLabel('LBL_Testimonials', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewTestimonial($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('JobFunction'); ?>"><?php echo Label::getLabel('LBL_Job_Function_Dropdown_List', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewTestimonial($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('FieldServices'); ?>"><?php echo Label::getLabel('LBL_Field_Services_Dropdown_List', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewNavigationManagement($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('Navigations'); ?>"><?php echo Label::getLabel('LBL_Navigation_Management', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewTimezones($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('Timezones'); ?>"><?php echo Label::getLabel('LBL_Timezones_Management', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewCountries($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('Countries'); ?>"><?php echo Label::getLabel('LBL_Countries_Management', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewSocialPlatforms($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('SocialPlatform'); ?>"><?php echo Label::getLabel('LBL_Social_Platforms_Management', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewDiscountCoupons($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('DiscountCoupons'); ?>"><?php echo Label::getLabel('LBL_Discount_Coupons', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewLanguageLabel($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('Label'); ?>"><?php echo Label::getLabel('LBL_Language_Label', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewFaq($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('faq'); ?>"><?php echo Label::getLabel('LBL_Manage_FAQs', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewFaq($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('InstructorFaq'); ?>"><?php echo Label::getLabel('LBL_Manage_Instructor_FAQs', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewFaq($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('LearnerFaq'); ?>"><?php echo Label::getLabel('LBL_Manage_Learner_FAQs', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewFaq($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('FreeDays'); ?>"><?php echo Label::getLabel('LBL_Free_Trial_Days', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewFaqCategory($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('FaqCategories'); ?>"><?php echo Label::getLabel('LBL_Manage_FAQ_Category', $adminLangId); ?></a></li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
            <!-- ] -->
            <?php
            if (
                $objPrivilege->canViewBlogPostCategories($adminLoggedId, true) ||
                $objPrivilege->canViewBlogPosts($adminLoggedId, true) ||
                $objPrivilege->canViewBlogContributions($adminLoggedId, true) ||
                $objPrivilege->canViewBlogComments($adminLoggedId, true)
            ) {
            ?>
                <li class="haschild"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_Blog', $adminLangId); ?></a>
                    <ul>
                        <?php if ($objPrivilege->canViewBlogPostCategories($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('BlogPostCategories'); ?>"><?php echo Label::getLabel('LBL_Blog_Post_Categories', $adminLangId); ?></a></li>
                        <?php
                        }
                        if ($objPrivilege->canViewBlogPosts($adminLoggedId, true)) {
                        ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('BlogPosts'); ?>"><?php echo Label::getLabel('LBL_Blog_Posts', $adminLangId); ?></a></li>
                        <?php
                        }
                        if ($objPrivilege->canViewBlogContributions($adminLoggedId, true)) {
                        ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('BlogContributions'); ?>"><?php echo Label::getLabel('LBL_Blog_Contributions', $adminLangId); ?> <?php /* if($blogContrCount){ ?><span class='badge'>(<?php echo $blogContrCount; ?>)</span><?php } */ ?></a></li>
                        <?php
                        }
                        if ($objPrivilege->canViewBlogComments($adminLoggedId, true)) {
                        ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('BlogComments'); ?>"><?php echo Label::getLabel('LBL_Blog_Comments', $adminLangId); ?> <?php /* if($blogCommentsCount){ ?><span class='badge'>(<?php echo $blogCommentsCount; ?>)</span><?php } */ ?></a></li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
            <!--Settings-->
            <?php
            if (
                $objPrivilege->canViewGeneralSettings($adminLoggedId, true) ||
                $objPrivilege->canViewPaymentMethods($adminLoggedId, true) ||
                $objPrivilege->canViewCurrencyManagement($adminLoggedId, true) ||
                $objPrivilege->canViewCommissionSettings($adminLoggedId, true) ||
                $objPrivilege->canViewEmailTemplates($adminLoggedId, true)
            ) {
            ?>
                <li class="haschild"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_Settings', $adminLangId); ?></a>
                    <ul>
                        <?php if ($objPrivilege->canViewGeneralSettings($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('configurations'); ?>"><?php echo Label::getLabel('LBL_General_Settings', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewGeneralSettings($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('Pwa'); ?>"><?php echo Label::getLabel('LBL_PWA_Settings', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewPaymentMethods($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('PaymentMethods'); ?>"><?php echo Label::getLabel('LBL_Payment_Methods', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewCommissionSettings($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo FatUtility::generateUrl('Commission'); ?>"><?php echo Label::getLabel('LBL_Commission_Settings', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewCurrencyManagement($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('CurrencyManagement'); ?>"><?php echo Label::getLabel('LBL_Currency_Management', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewEmailTemplates($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('EmailTemplates'); ?>"><?php echo Label::getLabel('LBL_Email_Templates_Management', $adminLangId); ?></a></li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
            <?php
            if (
                $objPrivilege->canViewMetaTags($adminLoggedId, true) ||
                $objPrivilege->canViewUrlRewrites($adminLoggedId, true) || $objPrivilege->canViewImageAttributes($adminLoggedId, true)
            ) {
            ?>
                <li class="haschild"><a href="javascript:void(0);"><?php echo Label::getLabel('LBL_SEO', $adminLangId); ?></a>
                    <ul>
                        <?php if ($objPrivilege->canViewMetaTags($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('MetaTags'); ?>"><?php echo Label::getLabel('LBL_Meta_Tags_Management', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewUrlRewrites($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('UrlRewriting'); ?>"><?php echo Label::getLabel('LBL_Url_Rewriting', $adminLangId); ?></a></li>
                        <?php } ?>
                        <!-- <?php if ($objPrivilege->canViewImageAttributes($adminLoggedId, true)) { ?>
                                <li><a href="<?php echo CommonHelper::generateUrl('ImageAttributes'); ?>"><?php echo Label::getLabel('LBL_Image_Attributes', $adminLangId); ?></a></li>
                        <?php } ?> -->
                        <?php if ($objPrivilege->canViewRobotsSection($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('Bots'); ?>"><?php echo Label::getLabel('LBL_Robots', $adminLangId); ?></a></li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
            <!-- Report [ -->
            <?php
            if (
                $objPrivilege->canViewTopLangReport($adminLoggedId, true) ||
                $objPrivilege->canViewTeacherPerformanceReport($adminLoggedId, true) ||
                $objPrivilege->canViewCommissionReport($adminLoggedId, true)
            ) {
            ?>
                <li class="haschild">
                    <a href="javascript:void(0);"><?php echo Label::getLabel('LBL_Reports', $adminLangId); ?></a>
                    <ul>
                        <?php if ($objPrivilege->canViewTopLangReport($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('TopLanguagesReport'); ?>"><?php echo Label::getLabel('LBL_Top_Languages', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewTeacherPerformanceReport($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('TeacherPerformanceReport'); ?>"><?php echo Label::getLabel('LBL_Teacher_Performance', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewCommissionReport($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('CommissionReport'); ?>"><?php echo Label::getLabel('LBL_Commission_Report', $adminLangId); ?></a></li>
                        <?php } ?>
                        <?php if ($objPrivilege->canViewLessonStatsReport($adminLoggedId, true)) { ?>
                            <li><a href="<?php echo CommonHelper::generateUrl('LessonStats'); ?>"><?php echo Label::getLabel('LBL_Lesson_Stats', $adminLangId); ?></a></li>
                        <?php } ?>
                    </ul>
                </li>
            <?php } ?>
            <!--  ] -->
            <!--Admin Users[-->
            <?php if ($objPrivilege->canViewAdminUsers($adminLoggedId, true) || $objPrivilege->canViewAdminUsers($adminLoggedId, true)) { ?>
                <li><a href="<?php echo CommonHelper::generateUrl('AdminUsers') ?>"><?php echo Label::getLabel('LBL_Manage_Admin_Users', $adminLangId); ?></a>
                </li>
            <?php } ?>
            <!-- ] -->
            <li class="haschild">
                <a href="javascript:void(0);"><?php echo Label::getLabel('LBL_Sitemap', $adminLangId); ?></a>
                <ul>
                    <li><a href="<?php echo CommonHelper::generateUrl('Sitemap', 'generate') ?>"><?php echo Label::getLabel('LBL_Update_Sitemap', $adminLangId); ?></a></li>
                    <li><a href="<?php echo CommonHelper::generateUrl('custom', 'sitemap', array(), CONF_WEBROOT_FRONT_URL) ?>" target="_blank"><?php echo Label::getLabel('LBL_View_Html', $adminLangId); ?></a></li>
                    <li><a href="<?php echo CONF_WEBROOT_FRONT_URL ?>sitemap.xml" target="_blank"><?php echo Label::getLabel('LBL_View_XML', $adminLangId); ?></a></li>
                </ul>
            </li>
            <?php if ($objPrivilege->canViewGdprRequests(AdminAuthentication::getLoggedAdminId(), true)) { ?>
                <li><a href="<?php echo CommonHelper::generateUrl('GdprRequests') ?>"><?php echo Label::getLabel('LBL_User_Gdpr_Requests', $adminLangId); ?></a></li>
            <?php } ?>
        </ul>
    </div>
</aside>
<!--left panel end here-->