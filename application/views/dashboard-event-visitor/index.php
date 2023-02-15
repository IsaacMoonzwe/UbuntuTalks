<?php
defined('SYSTEM_INIT') or exit('Invalid Usage.');
$activeMettingTool = FatApp::getConfig('CONF_ACTIVE_MEETING_TOOL', FatUtility::VAR_STRING, ApplicationConstants::MEETING_COMET_CHAT);
$contactFrm->setFormTagAttribute('class', 'form form--normal');
$captchaFld = $contactFrm->getField('htmlNote');
$captchaFld->htmlBeforeField = '<div class="field-set">
		   <div class="caption-wraper"><label class="field_label"></label></div>
		   <div class="field-wraper">
			   <div class="field_cover">';
$captchaFld->htmlAfterField = '</div></div></div>';
$contactFrm->setFormTagAttribute('action', CommonHelper::generateUrl('contact', 'contactSubmit'));
$contactFrm->developerTags['colClassPrefix'] = 'col-md-';
$contactFrm->developerTags['fld_default_col'] = 12;

?>
<style>
    .attend{
        text-align: center;
        text-decoration: underline;
    }
    /* DivTable.com */
    .divTable {
        display: table;
        width: 100% !important;
    }

    .divTableRow {
        display: table-row;
    }

    .divTableHeading {
        background-color: #EEE;
        display: table-header-group;
    }

    .divTableCell,
    .divTableHead {
        border: 1px solid #999999;
        display: table-cell;
        padding: 3px 10px;
    }

    .divTableHeading {
        background-color: #EEE;
        display: table-header-group;
        font-weight: bold;
    }

    .divTableFoot {
        background-color: #EEE;
        display: table-footer-group;
        font-weight: bold;
    }

    .divTableBody {
        display: table-row-group;
    }

    .page-item.active .page-link {
        z-index: 0 !important;
    }
</style>
<script>
    var isCometChatMeetingToolActive = '<?php echo $activeMettingTool == ApplicationConstants::MEETING_COMET_CHAT ?>';
</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<div class="site events-dashboard-page">
    <aside class="sidebar">
        <div class="sidebar__secondary">
            <nav class="menu menu--secondary">
                <ul>
                    <li class="menu__item menu__item-toggle">
                        <a href="#primary-nav" class="menu__item-trigger trigger-js for-responsive" title="<?php echo Label::getLabel('LBL_Menu'); ?>">
                            <span class="icon icon--menu">
                                <span class="toggle"><span></span></span>
                            </span>
                            <span class="sr-only"><?php echo Label::getLabel('LBL_Menu'); ?></span>
                        </a>
                        <a href="#sidebar__primary" class="menu__item-trigger fullview-js for-desktop" title="<?php echo Label::getLabel('LBL_Menu'); ?>">
                            <span class="icon icon--menu">
                                <span class="toggle"><span></span></span>
                            </span>
                            <span class="sr-only"><?php echo Label::getLabel('LBL_Menu'); ?></span>
                        </a>
                    </li>
                    <li class="menu__item menu__item-home">
                        <a href="https://ubuntutalks.com" class="menu__item-trigger" title="<?php echo Label::getLabel('LBL_Home'); ?>">
                            <svg class="icon icon--home">
                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#home'; ?>"></use>
                            </svg>
                            <span class="sr-only"><?php echo Label::getLabel('LBL_Home'); ?></span>
                        </a>
                    </li>
                    <li class="menu__item menu__item-logout">
                        <a href="<?php echo CommonHelper::generateUrl('EventUser', 'logout', [], CONF_WEBROOT_FRONT_URL); ?>" class="menu__item-trigger" title="<?php echo Label::getLabel('LBL_Logout'); ?>">
                            <svg class="icon icon--logout">
                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#logout'; ?>"></use>
                            </svg>
                            <span class="sr-only"><?php echo Label::getLabel('LBL_Logout'); ?></span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <!-- [ SIDE BAR PRIMARY ========= -->
        <div id="sidebar__primary" class="sidebar__primary">
            <div class="sidebar__head">
                <!-- [ PROFILE ========= -->
                <div class="profile">
                    <a href="#profile-target" class="trigger-js profile__trigger">
                        <div class="profile__meta d-flex align-items-center">
                            <!-- <div class="profile__media margin-right-4 mobile-icons">
                                <?php
                                $str = $userDetails['user_first_name'];
                                $first_character = substr($str, 0, 1);
                                ?>
                                <div class="avtar" data-title="<?php echo $first_character; ?>">
                                </div>
                                <svg class="icon icon--arrow">
                                    <use xlink:href="/images/sprite.yo-coach.svg#arrow-black"></use>
                                </svg>
                            </div> -->
                            <div class="avtar" data-title="<?php echo CommonHelper::getFirstChar($userFirstName); ?>">
                                <?php

                                if ($isProfilePicUploaded) {
                                    echo '<img src="' . CommonHelper::generateUrl('Image', 'user', array($userId, 'MEDIUM'), CONF_WEBROOT_FRONT_URL) . '?' . time() . '"  alt="' . $userFirstName . '" />';
                                }
                                ?>
                            </div>
                            <div class="profile__details">
                                <h6 class="profile__title"><?php echo $userDetails['user_full_name']; ?></h6>
                                <small class="color-black"><?php echo Label::getLabel('LBL_Logged'); ?></small>
                            </div>
                        </div>
                    </a>
                    <div id="profile-target" class="profile__target">
                        <div class="profile__target-details">
                            <table class="event-user-location">
                                <tbody>
                                    <tr>
                                        <th>Location</th>
                                        <td><?php echo $userDetails['countryName']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Time Zone</th>
                                        <td><?php echo $userDetails['user_timezone']; ?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="menu__item button btn btn--primary events-buttons">
                                <a href="<?php echo CommonHelper::generateUrl('EventUser', 'logout'); ?>">
                                    <?php echo Label::getLabel('LBL_Logout'); ?></a>
                            </div>
                            <span class="-gap-10"></span>
                            <div class="btns-group">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ] -->
            </div>
            <div class="sidebar__body" style="height: calc(100% - 175px);">
                <div class="sidebar__scroll">
                    <div id="primary-nav" class="menu-offset">
                        <!-- Display flashcard list on left sidebar in lesson view page  -->
                        <div class="menu-group">

                            <nav class="menu menu--primary">
                                <h6 class="heading-6"><?php echo Label::getLabel('LBL_Profile'); ?></h6>
                                <ul class="events-menu nav nav-tabs">
                                    <li class="menu__item is-active nav-item">
                                        <a class="nav-link active show" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">
                                            <svg class="icon icon--dashboard margin-right-2">
                                                <use xlink:href="/dashboard/images/sprite.yo-coach.svg#dashboard"></use>
                                            </svg>
                                            <span class="mobile-view-toggle"><?php echo Label::getLabel('LBL_My_Dashboard'); ?></span>
                                        </a>
                                    </li>
                                    <li class="menu__item">
                                        <a class="nav-link" id="accountinformation-tab" data-toggle="tab" href="#accountinformation" role="tab" aria-controls="accountinformation" aria-selected="false">
                                            <svg class="icon icon--settings margin-right-2">
                                                <use xlink:href="/dashboard/images/sprite.yo-coach.svg#settings"></use>
                                            </svg>
                                            <span class="mobile-view-toggle"><?php echo Label::getLabel('LBL_Account_Settings'); ?></span>
                                        </a>
                                    </li>
                                    <li class="menu__item">
                                        <a class="nav-link" id="requirementinformation-tab" data-toggle="tab" href="#requirementinformation" role="tab" aria-controls="requirementinformation" aria-selected="false">
                                            <svg class="icon icon--settings margin-right-2">
                                                <use xlink:href="/dashboard/images/sprite.yo-coach.svg#group-classes"></use>
                                            </svg>
                                            <span class="mobile-view-toggle"><?php echo Label::getLabel('LBL_Requirements'); ?></span>
                                        </a>
                                    </li>
                                    <li class="menu__item">
                                        <a class="nav-link" id="dailyschedule-tab" data-toggle="tab" href="#dailyschedule" role="tab" aria-controls="dailyschedule" aria-selected="false">
                                            <svg class="icon icon--settings margin-right-2">
                                                <use xlink:href="/dashboard/images/sprite.yo-coach.svg#calendar"></use>
                                            </svg>
                                            <span class="mobile-view-toggle"><?php echo Label::getLabel('LBL_Daily_Schedule'); ?></span>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="second-section-menu">
                                            <h6 class="heading-6"><?php echo Label::getLabel('LBL_Links'); ?></h6>
                                        </div>
                                    </li>
                                    <li class="menu__item nav-item">
                                        <a class="nav-link" id="helpdesk-tab" data-toggle="tab" href="#helpdesk" role="tab" aria-controls="helpdesk" aria-selected="false">
                                            <svg class="icon icon--lesson margin-right-2">
                                                <use xlink:href="/dashboard/images/sprite.yo-coach.svg#lessons"></use>
                                            </svg>
                                            <span class="mobile-view-toggle"><?php echo Label::getLabel('LBL_Help_Desk'); ?></span>
                                        </a>
                                    </li>
                                    <li class="menu__item ">
                                        <a class="nav-link" id="reportanissue-tab" data-toggle="tab" href="#reportanissue" role="tab" aria-controls="reportanissue" aria-selected="false">
                                            <svg class="icon icon--lesson margin-right-2">
                                                <use xlink:href="/dashboard/images/sprite.yo-coach.svg#issue"></use>
                                            </svg>
                                            <span class="mobile-view-toggle"><?php echo Label::getLabel('LBL_Report_An_Issue'); ?></span>
                                        </a>
                                    </li>
                                    <li class="menu__item">
                                        <a class="nav-link" id="eventfaq-tab" data-toggle="tab" href="#eventfaq" role="tab" aria-controls="eventfaq" aria-selected="false">
                                            <svg class="icon icon--students margin-right-2">
                                                <use xlink:href="/dashboard/images/sprite.yo-coach.svg#students"></use>
                                            </svg>
                                            <span class="mobile-view-toggle"><?php echo Label::getLabel('LBL_FAQ'); ?></span>
                                        </a>
                                    </li>
                                    <li class="menu__item">
                                        <a class="nav-link" id="disclaimer-tab" data-toggle="tab" href="#disclaimer" role="tab" aria-controls="disclaimer" aria-selected="false">
                                            <svg class="icon icon--students margin-right-2">
                                                <use xlink:href="/dashboard/images/sprite.yo-coach.svg#wallet"></use>
                                            </svg>
                                            <span class="mobile-view-toggle"><?php echo Label::getLabel('LBL_Disclaimer_Section'); ?></span>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ] -->
    </aside>
    <main class="page event-dashboard-page">
        <div class="container container--fixed">
            <div class="dashboard">
                <div class="dashboard__primary">
                    <div class="page__head">
                        <h1><?php echo Label::getLabel('LBL_My_Dashboard'); ?></h1>
                    </div>

                    <div class="page__body">
                        <div class="stats-row margin-bottom-6">
                            <div class="row align-items-center">
                                <div class="col-lg-3 col-md-6 col-sm-6">
                                    <div class="stat">
                                        <div class="stat__amount">
                                            <span><?php echo Label::getLabel('LBL_Sponsorship_Plan'); ?></span>
                                            <h5>
                                                <?php
                                                $total_events = 0;
                                                if (!empty($PurchaseSponserShip)) {
                                                    foreach ($PurchaseSponserShip as $key => $value) {
                                                        $total_events = $total_events + $value;
                                                    }
                                                }
                                                echo $total_events;
                                                ?>
                                            </h5>
                                        </div>
                                        <div class="stat__media bg-yellow">
                                            <svg class="icon icon--money icon--40 color-white">
                                                <use xlink:href="/dashboard/images/sprite.yo-coach.svg#orders"></use>
                                            </svg>
                                        </div>
                                        <a href="javascript:void(0)" class="stat__action"></a>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-6 col-sm-6">
                                    <div class="stat">
                                        <div class="stat__amount">
                                            <span><?php echo Label::getLabel('LBL_Event_Plan'); ?></span>
                                            <h5>
                                                <?php
                                                $total_events = sizeOf($EventplanResult);
                                                echo $total_events;
                                                ?></h5>
                                        </div>
                                        <div class="stat__media bg-secondary">
                                            <svg class="icon icon--money icon--40 color-white">
                                                <use xlink:href="/dashboard/images/sprite.yo-coach.svg#planning"></use>
                                            </svg>
                                        </div>
                                        <a href="javascript:void(0)" class="stat__action"></a>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-6 col-sm-6">
                                    <div class="stat">
                                        <div class="stat__amount">
                                            <span><?php echo Label::getLabel('LBL_Benefit_Concert'); ?></span>
                                            <h5>
                                                <?php
                                                $total = 0;
                                                $total_events = sizeOf($BenefitConcertplanResult);
                                                echo $total_events;
                                                ?></h5>
                                        </div>
                                        <div class="stat__media bg-secondary">
                                            <svg class="icon icon--money icon--40 color-white">
                                                <use xlink:href="/dashboard/images/sprite.yo-coach.svg#stats_1"></use>
                                            </svg>
                                        </div>
                                        <a href="javascript:void(0)" class="stat__action"></a>
                                    </div>
                                </div>

                                <div class="col-lg-3 col-md-6 col-sm-6">
                                    <div class="stat">
                                        <div class="stat__amount">
                                            <span><?php echo Label::getLabel('LBL_Donation_Amount'); ?></span>
                                            <h5>
                                                <?php
                                                $total = 0;
                                                foreach ($DonationplanResult as $value) {
                                                    $total = $total + $value['event_user_donation_amount'];
                                                }
                                                echo "$" . $total;
                                                ?>
                                            </h5>
                                        </div>
                                        <div class="stat__media bg-primary">
                                            <svg class="icon icon--money icon--40 color-white">
                                                <use xlink:href="/dashboard/images/sprite.yo-coach.svg#stats_2"></use>
                                            </svg>
                                        </div>
                                        <a href="javascript:void(0)" id="myBtn" class="stat__action"></a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="donation-section">
                            <div id="myModal" class="modal">
                                <!-- Modal content -->
                                <div class="modal-content">
                                    <span class="closes">&times;</span>
                                    <table id="example" class="table table-striped table-bordered donation-table display nowrap" style="width:100%">
                                        <h2 class="title"><?php echo Label::getLabel('LBL_Donation_Information'); ?></h2>
                                        <thead>
                                            <tr>
                                                <th><?php echo Label::getLabel('LBL_Donation_Amount'); ?></th>
                                                <th><?php echo Label::getLabel('LBL_Recieved'); ?></th>
                                                <th><?php echo Label::getLabel('LBL_Donation_Receipt'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($DonationplanResult as $value) { ?>
                                                <tr>
                                                    <td><?php echo "$" . $value['event_user_donation_amount']; ?></td>
                                                    <td>
                                                        <?php
                                                        if ($value['event_user_donation_status'] == '1') {
                                                            echo "Paid";
                                                        } else {
                                                            echo "Pending";
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><a href="<?php echo $value['event_user_receipt_url']; ?>" download="<?php echo "Donation-receipt" . '.jpeg'; ?>"><i class="fa fa-ticket" style="font-size:24px;color:red"></i></a></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="page-content">
                            <div class="results" id="listItemsLessons">
                                <!-- Dashboard Section -->
                                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                    <div class="tab-content" id="myTabContent">
                                        <div class="message-display dashboard-section">
                                            <!-- Event Listing -->
                                            <div class="row events-tickets-section" id="event-listing">
                                                <div class="col-lg-12">
                                                    <div>
                                                        <h4 class="events-head-title"><?php echo Label::getLabel('LBL_Active_Events_Listing'); ?></h4>
                                                    </div>
                                                    <table id="plan" class="table event-listing table-striped table-bordered donation-table display nowrap" style="width:100%">
                                                        <thead>
                                                            <tr>
                                                                <th><?php echo Label::getLabel('LBL_Events'); ?></th>
                                                                <th><?php echo Label::getLabel('LBL_Starting_Date'); ?></th>
                                                                <th><?php echo Label::getLabel('LBL_Ending_Date'); ?></th>
                                                                <th><?php echo Label::getLabel('LBL_#_of_Tickets'); ?></th>
                                                                <th><?php echo Label::getLabel('LBL_Price'); ?></th>
                                                                <th><?php echo Label::getLabel('LBL_Coupon_Code'); ?></th>
                                                                <th><?php echo Label::getLabel('LBL_Attendee_Details'); ?></th>
                                                                <th><?php echo Label::getLabel('LBL_Tickets'); ?></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $sr_no = 1;
                                                            if (!empty($EventplanResult)) {
                                                                foreach ($EventplanResult as $key => $value) {
                                                                    $Currentdate = date('Y-m-d H:i');
                                                            ?>
                                                                    <tr>
                                                                        <?php
                                                                        if ($value['plan_end_date'] < $Currentdate) {
                                                                        ?>
                                                                            <td><?php echo $value['plan_name'] . '<span class="expiry"> (Expired)</span>'; ?></td>
                                                                            <td><?php echo $value['plan_start_date']; ?></td>
                                                                            <td><?php echo $value['plan_end_date']; ?></td>
                                                                            <td><?php echo $value['event_user_ticket_count']; ?></td>
                                                                            <td> - </td>
                                                                            <td> - </td>
                                                                            <td class="expiry">Event Is Expired</td>
                                                                        <?php
                                                                        } else {
                                                                        ?>
                                                                            <td><?php echo $value['plan_name']; ?></td>
                                                                            <td><?php echo $value['plan_start_date']; ?></td>
                                                                            <td><?php echo $value['plan_end_date']; ?></td>
                                                                            <td><?php echo $value['event_user_ticket_count']; ?></td>
                                                                            <td><?php echo $value['order_currency_code'] . " " . $value['order_net_amount']; ?></td>
                                                                            <td class="ccode">
                                                                                <?php
                                                                                if (!empty($value['coupon_code'])) {
                                                                                    echo Label::getLabel('LBL_Activated');
                                                                                } else {
                                                                                    echo Label::getLabel('LBL_Not_Activated');
                                                                                }
                                                                                ?>
                                                                            </td>
                                                                            <td><a class="attend" href="<?php echo "#" . $value['event_user_ticket_plan_id']; ?>">
                                                                                    Attendee Details
                                                                                </a></td>
                                                                            <td><a href="<?php echo $value['event_user_ticket_download_url']; ?>" download="<?php echo $value['plan_name'] . '.jpeg'; ?>"><i class="fa fa-ticket" style="font-size:24px;color:red"></i></a></td>
                                                                        <?php
                                                                        }
                                                                        ?>

                                                                    </tr>
                                                                    <?php
                                                                    $sr_no++;
                                                                    ?>
                                                                    <div class="gallery-lightboxes mc-lightbox">
                                                                        <div class="image-lightbox" id="<?php echo $value['event_user_ticket_plan_id']; ?>">
                                                                            <div class="image-lightbox-wrapper"><a href="#0" class="close"></a>
                                                                                <div class="lightbox-detail-text">
                                                                                    <h1>Attendee Information</h1>
                                                                                    <div class="divTable" style="width: 1%;">
                                                                                        <div class="divTableBody">
                                                                                            <div class="divTableRow">
                                                                                                <div class="divTableCell">Sr.No</div>
                                                                                                <div class="divTableCell">Name</div>
                                                                                                <div class="divTableCell">Email</div>
                                                                                                <div class="divTableCell">Phone</div>
                                                                                                <div class="divTableCell">Gender</div>
                                                                                                <div class="divTableCell">Church</div>
                                                                                                <div class="divTableCell">Food</div>
                                                                                            </div>
                                                                                            <?php
                                                                                            $i = 1;
                                                                                            foreach ($value['attendee_details'] as $value) { ?>
                                                                                                <div class="divTableRow">
                                                                                                    <div class="divTableCell">
                                                                                                        <p><?php echo $i; ?></p>
                                                                                                    </div>
                                                                                                    <div class="divTableCell">
                                                                                                        <p><?php echo $value['attendee_full_name']; ?></p>
                                                                                                    </div>
                                                                                                    <div class="divTableCell">
                                                                                                        <p><?php echo $value['attendee_email']; ?></p>
                                                                                                    </div>
                                                                                                    <div class="divTableCell">
                                                                                                        <p><?php echo $value['attendee_phone']; ?></p>
                                                                                                    </div>
                                                                                                    <div class="divTableCell">
                                                                                                        <p><?php echo $value['attendee_gender']; ?></p>
                                                                                                    </div>
                                                                                                    <div class="divTableCell">
                                                                                                        <p><?php echo $value['attendee_church']; ?></p>
                                                                                                    </div>
                                                                                                    <div class="divTableCell">
                                                                                                        <p><?php echo $value['attendee_food']; ?></p>
                                                                                                    </div>
                                                                                                </div>
                                                                                            <?php
                                                                                                $i++;
                                                                                            }
                                                                                            ?>
                                                                                        </div>
                                                                                    </div>
                                                                                    <!-- DivTable.com -->

                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                            <?php
                                                                }
                                                            } else {
                                                                echo Label::getLabel('LBL_No_Records');
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <!-- Sponsorship Listing -->
                                            <div class="row events-tickets-section" id="sponsorship-listing">
                                                <div class="col-lg-12">
                                                    <div>
                                                        <h4 class="events-head-title"><?php echo Label::getLabel('LBL_Active_Sponsorship_Listing'); ?></h4>
                                                    </div>
                                                    <table id="sponsorship" class="table event-listing table-striped table-bordered donation-table display nowrap" style="width:100%">
                                                        <thead>
                                                            <tr>
                                                                <th><?php echo Label::getLabel('LBL_Events_(Sponsor)'); ?></th>
                                                                <th><?php echo Label::getLabel('LBL_Date'); ?></th>
                                                                <th><?php echo Label::getLabel('LBL_Sponsorship_Plan'); ?></th>
                                                                <th><?php echo Label::getLabel('LBL_Coupon_Code'); ?></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $sr_no = 1;
                                                            if (!empty($sponserEventData)) {
                                                                foreach ($sponserEventData as $key => $value) {
                                                                    $Currentdate = date('Y-m-d H:i');
                                                                    $expired = '';
                                                                    if ($value['event_ending_time'] < $Currentdate) {
                                                                        $expired = '(Expired)';
                                                                    }

                                                            ?>
                                                                    <tr>
                                                                        <td><?php echo $value['event_name']; ?><span class='expiry'><?php echo $expired; ?></span></td>
                                                                        <td><?php echo $value['event_ending_time']; ?></td>
                                                                        <td><?php echo $value['plan']; ?></td>
                                                                        <td class="ccode">
                                                                            <?php
                                                                            if (!empty($value['coupon_code'])) {
                                                                                echo Label::getLabel('LBL_Activated');
                                                                            } else {
                                                                                echo Label::getLabel('LBL_Not_Activated');
                                                                            }
                                                                            ?>
                                                                        </td>
                                                                    </tr>
                                                            <?php
                                                                    $sr_no++;
                                                                }
                                                            } else {
                                                                echo Label::getLabel('LBL_No_Records');
                                                            }
                                                            ?>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <!-- Benefit Concert Tickets Listing -->
                                            <div class="row events-tickets-section" id="benefit-concert-listing">
                                                <div class="col-lg-12">
                                                    <div>
                                                        <h4 class="events-head-title"><?php echo Label::getLabel('LBL_Benefit_Concert_Ticket_Listing'); ?></h4>
                                                    </div>
                                                    <table id="benefit-concert" class="table event-listing table-striped table-bordered donation-table display nowrap" style="width:100%">
                                                        <thead>
                                                            <tr>
                                                                <th><?php echo Label::getLabel('LBL_Concert_Tickets_Category'); ?></th>
                                                                <th><?php echo Label::getLabel('LBL_Starting_Date'); ?></th>
                                                                <th><?php echo Label::getLabel('LBL_Ending_Date'); ?></th>
                                                                <th><?php echo Label::getLabel('LBL_#_of_Tickets'); ?></th>
                                                                <th><?php echo Label::getLabel('LBL_Price'); ?></th>
                                                                <th><?php echo Label::getLabel('LBL_Coupon_Code'); ?></th>
                                                                <th><?php echo Label::getLabel('LBL_Tickets'); ?></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $sr_no = 1;
                                                            if (!empty($BenefitConcertplanResult)) {
                                                                foreach ($BenefitConcertplanResult as $key => $value) {
                                                                    $Currentdate = date('Y-m-d H:i');
                                                            ?>
                                                                    <tr>
                                                                        <?php
                                                                        if ($value['plan_end_date'] < $Currentdate) {
                                                                        ?>
                                                                            <td><?php echo $value['plan_name'] . '<span class="expiry"> (Expired)</span>'; ?></td>
                                                                            <td><?php echo $value['plan_start_date']; ?></td>
                                                                            <td><?php echo $value['plan_end_date']; ?></td>
                                                                            <td><?php echo $value['event_user_ticket_count']; ?></td>
                                                                            <td class="expiry">Event Is Expired</td>
                                                                        <?php
                                                                        } else {
                                                                        ?>
                                                                            <td><?php echo $value['plan_name']; ?></td>
                                                                            <td><?php echo $value['plan_start_date']; ?></td>
                                                                            <td><?php echo $value['plan_end_date']; ?></td>
                                                                            <td><?php echo $value['event_user_ticket_count']; ?></td>
                                                                            <td><?php echo $value['order_currency_code'] . " " . $value['order_net_amount']; ?></td>
                                                                            <td class="ccode">
                                                                                <?php
                                                                                if (!empty($value['coupon_code'])) {
                                                                                    echo Label::getLabel('LBL_Activated');
                                                                                } else {
                                                                                    echo Label::getLabel('LBL_Not_Activated');
                                                                                }
                                                                                ?>
                                                                            </td>
                                                                            <td><a href="<?php echo $value['event_user_ticket_download_url']; ?>" download="<?php echo $value['plan_name'] . '.jpeg'; ?>"><i class="fa fa-ticket" style="font-size:24px;color:red"></i></a></td>

                                                                        <?php
                                                                        }
                                                                        ?>

                                                                    </tr>
                                                            <?php
                                                                    $sr_no++;
                                                                }
                                                            } else {
                                                                echo Label::getLabel('LBL_No_Records');
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <!-- Pre-Symposium Dinner Tickets Listing -->
                                            <div class="row events-tickets-section" id="benefit-concert-listing">
                                                <div class="col-lg-12">
                                                    <div>
                                                        <h4 class="events-head-title"><?php echo Label::getLabel('LBL_Pre_Symposium_Dinner_Ticket_Listing'); ?></h4>
                                                    </div>
                                                    <table id="pre-symposium-dinner" class="table event-listing table-striped table-bordered donation-table display nowrap" style="width:100%">
                                                        <thead>
                                                            <tr>
                                                                <th><?php echo Label::getLabel('LBL_Concert_Tickets_Category'); ?></th>
                                                                <th><?php echo Label::getLabel('LBL_Starting_Date'); ?></th>
                                                                <th><?php echo Label::getLabel('LBL_Ending_Date'); ?></th>
                                                                <th><?php echo Label::getLabel('LBL_#_of_Tickets'); ?></th>
                                                                <th><?php echo Label::getLabel('LBL_Price'); ?></th>
                                                                <th><?php echo Label::getLabel('LBL_Coupon_Code'); ?></th>
                                                                <th><?php echo Label::getLabel('LBL_Tickets'); ?></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $sr_no = 1;
                                                            if (!empty($PreSymposiumDinnerplanResult)) {
                                                                foreach ($PreSymposiumDinnerplanResult as $key => $value) {

                                                                    $Currentdate = date('Y-m-d H:i');
                                                            ?>
                                                                    <tr>
                                                                        <?php
                                                                        if ($value['plan_end_date'] < $Currentdate) {
                                                                        ?>
                                                                            <td><?php echo $value['plan_name'] . '<span class="expiry"> (Expired)</span>'; ?></td>
                                                                            <td><?php echo $value['plan_start_date']; ?></td>
                                                                            <td><?php echo $value['plan_end_date']; ?></td>
                                                                            <td><?php echo $value['event_user_ticket_count']; ?></td>
                                                                            <td> - </td>
                                                                            <td> - </td>
                                                                            <td class="expiry">Event Is Expired</td>
                                                                        <?php
                                                                        } else {
                                                                        ?>
                                                                            <td><?php echo $value['plan_name']; ?></td>
                                                                            <td><?php echo $value['plan_start_date']; ?></td>
                                                                            <td><?php echo $value['plan_end_date']; ?></td>
                                                                            <td><?php echo $value['event_user_ticket_count']; ?></td>
                                                                            <td><?php echo $value['order_currency_code'] . " " . $value['order_net_amount']; ?></td>
                                                                            <td class="ccode">
                                                                                <?php
                                                                                if (!empty($value['coupon_code'])) {
                                                                                    echo Label::getLabel('LBL_Activated');
                                                                                } else {
                                                                                    echo Label::getLabel('LBL_Not_Activated');
                                                                                }
                                                                                ?>
                                                                            </td>
                                                                            <td><a href="<?php echo $value['event_user_ticket_download_url']; ?>" download="<?php echo $value['plan_name'] . '.jpeg'; ?>"><i class="fa fa-ticket" style="font-size:24px;color:red"></i></a></td>

                                                                        <?php
                                                                        }
                                                                        ?>

                                                                    </tr>
                                                            <?php
                                                                    $sr_no++;
                                                                }
                                                            } else {
                                                                echo Label::getLabel('LBL_No_Records');
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            <!-- Corporate Ticket Listing -->
                                            <div class="row events-tickets-section" id="event-listing">
                                                <div class="col-lg-12">
                                                    <div>
                                                        <h4 class="events-head-title"><?php echo Label::getLabel('LBL_Active_Corporate_Ticket_Listing'); ?></h4>
                                                    </div>
                                                    <table id="corporate" class="table event-listing table-striped table-bordered donation-table display nowrap" style="width:100%">
                                                        <thead>
                                                            <tr>
                                                                <th><?php echo Label::getLabel('LBL_Corporate_Type'); ?></th>
                                                                <th><?php echo Label::getLabel('LBL_Number_of_Tickets'); ?></th>
                                                                <th><?php echo Label::getLabel('LBL_Price'); ?></th>
                                                                <th><?php echo Label::getLabel('LBL_Discount'); ?></th>
                                                                <th><?php echo Label::getLabel('LBL_Paid_Amount'); ?></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $sr_no = 1;
                                                            if (!empty($CorportePlanDataResult)) {
                                                                foreach ($CorportePlanDataResult as $key => $value) {
                                                                    $Qty = $value['event_user_sponsership_qty'];
                                                                    $Ticket_Price = $value['price'];
                                                                    $Discount = $value['discount'];
                                                                    $Total =  $Qty * $Ticket_Price;
                                                                    $DiscountAmount = $Total * $Discount / 100;
                                                                    $TotalAmount = $Total - $DiscountAmount;
                                                            ?>
                                                                    <tr>
                                                                        <td><?php echo $value['type']; ?></td>
                                                                        <td><?php echo $value['event_user_sponsership_qty']; ?></td>
                                                                        <td><?php echo $value['price']; ?></td>
                                                                        <td><?php echo $value['discount'] . '%'; ?></td>
                                                                        <td><?php echo '$' . $TotalAmount; ?></td>
                                                                    </tr>
                                                            <?php
                                                                    $sr_no++;
                                                                }
                                                            } else {
                                                                echo Label::getLabel('LBL_No_Records');
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Dasboard Section -->

                                <!-- Account Information Section -->
                                <div class="tab-pane fade" id="accountinformation" role="tabpanel" aria-labelledby="accountinformation-tab">
                                </div>
                                <!-- End Account Information Section -->


                                <!-- Requirement Information Section -->
                                <div class="tab-pane fade" id="requirementinformation" role="tabpanel" aria-labelledby="requirementinformation-tab">
                                </div>
                                <!-- End Account Information Section -->

                                <!-- Requirement Information Section -->
                                <div class="tab-pane fade" id="dailyschedule" role="tabpanel" aria-labelledby="dailyschedule-tab">
                                </div>
                                <!-- End Account Information Section -->

                                <!-- Help Desk -->
                                <div class="tab-pane fade" id="helpdesk" role="tabpanel" aria-labelledby="helpdesk-tab">
                                    <?php
                                    $this->includeTemplate('dashboard-event-visitor/help-desk.php', ['siteLangId' => $siteLangId]);
                                    ?>
                                </div>
                                <!-- End Help Desk -->

                                <!-- Report An Issue Section -->
                                <div class="tab-pane fade" id="reportanissue" role="tabpanel" aria-labelledby="reportanissue-tab">
                                </div>
                                <!-- End Report An Issue Section -->

                                <!-- Start Event Faq Section -->
                                <div class="tab-pane fade" id="eventfaq" role="tabpanel" aria-labelledby="eventfaq-tab">
                                    <div class="padding-6 events-tickets-section">
                                    </div>
                                </div>
                                <!-- End Event Faq Section -->

                                <!-- Start Event Faq Section -->
                                <div class="tab-pane fade" id="disclaimer" role="tabpanel" aria-labelledby="disclaimer-tab">
                                    <div class="padding-6 events-tickets-section">
                                        <?php echo FatUtility::decodeHtmlEntities($DisclaimerSection); ?>
                                    </div>
                                </div>
                                <!-- End Event Faq Section -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>


<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap4.min.js"></script>

<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script>
    $(document).ready(function() {
        var table = $('#corporate').DataTable({
            pageLength: 10,
            scrollY: 300,
            responsive: true
        });
        var table = $('#example').DataTable({
            pageLength: 10,
            scrollY: 200,
            responsive: true
        });

        var table = $('#plan').DataTable({
            pageLength: 10,
            scrollY: 300,
            responsive: true
        });

        var table = $('#sponsorship').DataTable({
            pageLength: 10,
            scrollY: 300,
            responsive: true
        });
        var table = $('#benefit-concert').DataTable({
            pageLength: 10,
            scrollY: 300,
            responsive: true
        });
        var table = $('#pre-symposium-dinner').DataTable({
            pageLength: 10,
            scrollY: 300,
            responsive: true
        });
    });
    $(document).ready(function() {
        jQuery(document).on('click', '.mobile-view-toggle', function() {
            $("#primary-nav").removeClass("is-visible");
            $("a.menu__item-trigger.trigger-js.for-responsive").removeClass("is-active");
            $("html").removeClass("is-toggle");
        });
    });
</script>
<script>
    // Get the modal
    var modal = document.getElementById("myModal");

    // Get the button that opens the modal
    var btn = document.getElementById("myBtn");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("closes")[0];

    // When the user clicks the button, open the modal 
    btn.onclick = function() {
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>