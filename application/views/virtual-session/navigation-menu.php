<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
<!-- Header -->
<header>
    <div class="container-width">
        <div class="menu_bar ">
            <div class="toggle_bar">
                <svg class="icon icon--menu">
                    <use xlink:href="/images/sprite.yo-coach.svg#burger-menu"></use>
                </svg>
            </div>
            <nav>
                <ul class="menu-detail">
                    <?php
                    $Currentyear = date("Y");
                    ?>
                    <li><a href="/virtual-session" class="actives">Home</a></li>
                    <li class="has-sub-menu"><a href="<?php echo CommonHelper::generateUrl('VirtualSession', 'index', [CommonHelper::htmlEntitiesDecode($Currentyear)]); ?>"><?php echo $Currentyear; ?></a>
                        <ul class="sub-menu ">
                            <?php foreach ($NavigationVirtualSessionList as $value) {  ?>
                                <li>
                                    <a href="<?php echo CommonHelper::generateUrl('VirtualSession', 'sessionWiseListing', [CommonHelper::htmlEntitiesDecode($value['virtual_main_session_slug'])]); ?>">

                                        <?php echo $value['virtual_main_session_title']; ?> |
                                        <?php echo $value['virtual_main_session_sub_title']; ?>
                                    </a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <li class="has-sub-menu"><a href="javascrip:void()">PREVIOUS YEARS</a>
                        <ul class="sub-menu">
                            <?php foreach ($NavigationVirtualSessionYearList as $key => $value) { ?>
                                <li class="has-sub-menu">

                                    <a href="<?php echo CommonHelper::generateUrl('VirtualSession', 'previousYears', [CommonHelper::htmlEntitiesDecode($value['virtual_main_session_year'])]); ?>" class="year-number"><?php echo $value['virtual_main_session_year']; ?>
                                        <svg class="icon icon--arrow">
                                            <use xlink:href="/images/sprite.yo-coach.svg#arrow-black"></use>
                                        </svg>
                                    </a>
                                    <ul class="student-year">
                                        <?php foreach ($NavigationVirtualSessionAllList as $allKey => $allData) {
                                            if ($value['virtual_main_session_year'] == $allData['virtual_main_session_year']) {
                                        ?>

                                                <li> <a href="<?php echo CommonHelper::generateUrl('VirtualSession', 'sessionWiseListing', [CommonHelper::htmlEntitiesDecode($allData['virtual_main_session_slug'])]); ?>">
                                                        <?php echo $allData['virtual_main_session_title'] . ' | ' . $allData['virtual_main_session_sub_title']; ?></a></li>
                                        <?php }
                                        } ?>

                                    </ul>
                                </li>
                            <?php } ?>

                        </ul>
                    </li>
                    <div class="login-reg-section">
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
                    </div>
                </ul>
            </nav>
        </div>
    </div>
</header>
<script>
    jQuery(document).ready(function() {
        jQuery(".toggle_bar").click(function() {
            jQuery(".menu-detail").toggleClass("show_menu");
        });
        jQuery(".has-sub-menu > a").click(function() {
            jQuery(this).parent().toggleClass("active-menu");
        });
    });

    // Accordion Action
    const accordionItem = document.querySelectorAll(".accordion-item");

    accordionItem.forEach((el) =>
        el.addEventListener("click", () => {
            if (el.classList.contains("active")) {
                el.classList.remove("active");
            } else {
                accordionItem.forEach((el2) => el2.classList.remove("active"));
                el.classList.add("active");
            }
        })
    );
</script>