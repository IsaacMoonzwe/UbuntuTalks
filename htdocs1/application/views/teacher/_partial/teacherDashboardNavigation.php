<?php if (User::canViewTeacherTab() && User::canViewLearnerTab() && $showSwitcher) { ?>
    <div class="tab-swticher">
        <a href="<?php echo CommonHelper::generateUrl('Teacher'); ?>" class="btn btn--large is-active"><?php echo User::getUserDashboardArr()[User::USER_TEACHER_DASHBOARD]; ?></a>
        <a href="<?php echo CommonHelper::generateUrl('Learner'); ?>" class="btn btn--large"><?php echo User::getUserDashboardArr()[User::USER_LEARNER_DASHBOARD]; ?></a>
    </div>
<?php } ?>
<nav class="menu-vertical">
    <ul>
        <li class="<?php echo ( $controllerName == "Teacher" ) ? 'is-active' : ''; ?>">
            <a href="<?php echo CommonHelper::generateUrl('Teacher'); ?>">
                <span class="menu-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 105 105">
                        <path fill="#000" d="M87.58,53.53H48.605V14.563a2.823,2.823,0,0,0-2.835-2.834A44.636,44.636,0,1,0,90.415,56.364,2.85,2.85,0,0,0,87.58,53.53Zm-41.81,41.8A38.966,38.966,0,0,1,42.935,17.5V56.364A2.823,2.823,0,0,0,45.77,59.2H84.64A39.009,39.009,0,0,1,45.77,95.331Z" transform="translate(1.375 1.5)"/>
                        <path class="-color-fill" d="M101.125,45.489A44.633,44.633,0,0,0,56.48,1C54.905,1,54,2.425,54,4V46c0,1.575,1.425,2,3,2H98c1.575,0,3.125-.79,3.125-2.364V45.489ZM59,43V7A39.458,39.458,0,0,1,85.69,19.875,39.022,39.022,0,0,1,95,43H59Z" transform="translate(1.375 1.5)"/>
                    </svg>
                </span>
                <?php echo Label::getLabel('LBL_Dashboard'); ?></a>
        </li>
        <li class="<?php echo ( $controllerName == "TeacherScheduledLessons" ) ? 'is-active' : ''; ?>">
            <a href="<?php echo CommonHelper::generateUrl('TeacherScheduledLessons'); ?>">
                <span class="menu-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 105 105">
                        <path class="-color-fill" d="M27,27H75v5H27V27Zm0,11H75v5H27V38Zm0,11H75v5H27V49Zm0,11H75v5H27V60Zm0,11H75v5H27V71Z" transform="translate(1.5 1.5)"/>
                        <path fill="#000"  d="M6,99V3H96V99H6ZM91,8H11V94H91V8Z" transform="translate(1.5 1.5)"/>
                    </svg>
                </span>
                <?php echo Label::getLabel('LBL_Lessons'); ?></a>
        </li>
        <li class="<?php echo ( $controllerName == "TeacherGroupClasses" ) ? 'is-active' : ''; ?>">
            <a href="<?php echo CommonHelper::generateUrl('TeacherGroupClasses'); ?>">
                <span class="menu-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 105 105">
                        <path class="-color-fill" d="M27,27H75v5H27V27Zm0,11H75v5H27V38Zm0,11H75v5H27V49Zm0,11H75v5H27V60Zm0,11H75v5H27V71Z" transform="translate(1.5 1.5)"/>
                        <path fill="#000"  d="M6,99V3H96V99H6ZM91,8H11V94H91V8Z" transform="translate(1.5 1.5)"/>
                    </svg>
                </span>
                <?php echo Label::getLabel('LBL_Group_Classes'); ?></a>
        </li>
        <li class="<?php echo ( $controllerName == "TeacherStudents" ) ? 'is-active' : ''; ?>">
            <a href="<?php echo CommonHelper::generateUrl('TeacherStudents'); ?>">
                <span class="menu-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 105 105">
                        <path fill="#000" d="M50.633,54.728h0.639c5.847-.1,10.576-2.147,14.068-6.063,7.682-8.628,6.405-23.418,6.266-24.83-0.5-10.6-5.527-15.665-9.678-18.031A22.25,22.25,0,0,0,51.192,3H50.853A22.288,22.288,0,0,0,40.117,5.724c-4.19,2.366-9.3,7.435-9.8,18.111-0.14,1.412-1.417,16.2,6.266,24.83C40.057,52.58,44.787,54.628,50.633,54.728Zm-14.986-30.4c0-.06.02-0.119,0.02-0.159C36.326,9.918,46.483,8.387,50.833,8.387h0.239c5.388,0.119,14.547,2.306,15.165,15.785a0.385,0.385,0,0,0,.02.159c0.02,0.139,1.417,13.657-4.929,20.775-2.514,2.823-5.867,4.215-10.276,4.254h-0.2c-4.39-.04-7.762-1.431-10.256-4.254C34.271,38.028,35.628,24.45,35.648,24.331ZM91.959,79.259V79.2c0-.159-0.02-0.318-0.02-0.5-0.12-3.936-.379-13.141-9.039-16.083-0.06-.02-0.14-0.04-0.2-0.06a57.667,57.667,0,0,1-16.562-7.515,2.687,2.687,0,1,0-3.093,4.394,62.254,62.254,0,0,0,18.218,8.29c4.65,1.65,5.168,6.6,5.308,11.133a3.969,3.969,0,0,0,.02.5,35.944,35.944,0,0,1-.419,6.143c-3.233,1.829-15.9,8.151-35.18,8.151-19.2,0-31.947-6.342-35.2-8.171a34.026,34.026,0,0,1-.419-6.143c0-.159.02-0.318,0.02-0.5,0.14-4.532.658-9.483,5.308-11.133a62.832,62.832,0,0,0,18.218-8.29,2.686,2.686,0,1,0-3.093-4.393A57.039,57.039,0,0,1,19.265,62.54c-0.08.02-.14,0.04-0.2,0.06-8.66,2.962-8.92,12.167-9.039,16.083a3.968,3.968,0,0,1-.02.5v0.06c-0.02,1.034-.04,6.342,1.018,9.006A2.552,2.552,0,0,0,12.061,89.5c0.6,0.4,14.946,9.5,38.951,9.5s38.352-9.125,38.951-9.5A2.663,2.663,0,0,0,91,88.245C92,85.6,91.979,80.293,91.959,79.259Z" transform="translate(1.5 1.5)"/>
                        <rect class="-color-fill" x="49.5" y="74.5" width="29" height="5" rx="2" ry="2"/>
                    </svg>
                </span>
                <?php echo Label::getLabel('LBL_Students'); ?>
            </a>
        </li>
        <li class="<?php echo ( $controllerName == "TeacherIssueReported" ) ? 'is-active' : ''; ?>">
            <a href="<?php echo CommonHelper::generateUrl('TeacherIssueReported'); ?>">
                <span class="menu-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 105 105">
                        <path fill="#000" d="M73.725,3H6V99H97V26.338ZM75,12L89,25H75V12ZM11,94V8H71V30H92V94H11Z" transform="translate(1 1.5)"/>
                        <path class="-color-fill" d="M25,27H57v5H25V27Zm0,11H73v5H25V38Zm0,11H73v5H25V49Zm0,11H73v5H25V60Zm0,11H73v5H25V71Z" transform="translate(1 1.5)"/>
                    </svg>
                </span>
                <?php echo Label::getLabel('LBL_Issue_Reported'); ?>
            </a>
        </li>
        <?php if (FatApp::getConfig('CONF_ENABLE_FLASHCARD', FatUtility::VAR_BOOLEAN, true)) { ?>
            <li class="<?php echo ( $controllerName == "FlashCards" ) ? 'is-active' : ''; ?>">
                <a href="<?php echo CommonHelper::generateUrl('FlashCards'); ?>">
                    <span class="menu-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 105 105">
                            <path fill="#000" d="M5,13H97V88H5V13ZM92,83V18H10V83H92Z" transform="translate(1.5 2)"/>
                            <path class="-color-fill" d="M76,40H58L70,20H41L28,53H43L34,83ZM35,49L45,24H62L50,44H65L43,67l8-18H35Z" transform="translate(1.5 2)"/>
                        </svg>
                    </span>
                    <?php echo Label::getLabel('LBL_FlashCards'); ?>
                </a>
            </li>
        <?php } ?>
        <li class="<?php echo ( $controllerName == "Giftcard" ) ? 'is-active' : ''; ?>">
            <a href="<?php echo CommonHelper::generateUrl('Giftcard'); ?>">
                <span class="menu-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 105 105">
                        <path fill="#000" d="M98,16H4c0.057,0.094-.031.006,0,3.084V90c0.031-.079,1.372,0,3.065,0H94.935A23.324,23.324,0,0,1,98,90V19.084C98,17.381,98.068,16.094,98,16ZM40.783,40.667a6.13,6.13,0,1,1,6.13,6.167H41.091a15.515,15.515,0,0,1-.308-3.083V40.667h0ZM35,84l-24.87-.167V53H31.579a15.282,15.282,0,0,1-12.252,6.167,3.083,3.083,0,0,0,0,6.167A22,22,0,0,0,35,59V84h0ZM34.652,43.75a15.516,15.516,0,0,1-.308,3.083H28.522a6.167,6.167,0,1,1,6.13-6.167V43.75h0Zm0-13.757a12.126,12.126,0,0,0-6.13-1.659,12.338,12.338,0,0,0-10.611,18.5H10.13L10,22l24.652,0.167v7.826h0ZM92,84H41V59a20.7,20.7,0,0,0,15.109,6.333,3.083,3.083,0,0,0,0-6.167A15.282,15.282,0,0,1,43.856,53H92V84h0Zm0-37-34.476-.166a12.338,12.338,0,0,0-10.612-18.5,12.125,12.125,0,0,0-6.13,1.659V22.167L92,22V47h0Z" transform="translate(1.484 -0.5)"></path>
                        <path class="-color-fill" d="M63,62H87v6H63V62Zm0,11H87v6H63V73Z" transform="translate(1.484 -0.5)"></path>
                    </svg>
                </span>
                <?php echo Label::getLabel('LBL_Buy_Giftcard'); ?>
            </a>
        </li>
        <li class="<?php echo ( $controllerName == "Wallet" ) ? 'is-active' : ''; ?>">
            <a href="<?php echo CommonHelper::generateUrl('Wallet'); ?>">
                <span class="menu-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 105 105">
                        <path class="-color-fill" d="M69,69V49H98V69H69ZM93,54H74V64H93V54ZM66,15L51,25H42l7-5-6-6L31,25H22L41.778,7.628a2.047,2.047,0,0,1,2.989,0l8.558,8.947L66.351,8.713a2.061,2.061,0,0,1,2.887.809L78,25H71Z" transform="translate(1 2.516)"/>
                        <path class="cls-2" d="M89,45V30H10V88H89V73h5V93H5V25H94V45H89Z" transform="translate(1 2.516)"/>
                    </svg>
                </span>
                <?php echo Label::getLabel('LBL_Wallet'); ?>
            </a>
        </li>
        <li class="<?php echo ( $controllerName == "Teacher" AND $actionName == "orders" ) ? 'is-active' : ''; ?>">
            <a href="<?php echo CommonHelper::generateUrl('Teacher', 'orders'); ?>">
                <span class="menu-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 105 105">
                        <path fill="#000" d="M7,6H62V29H83V40h5V25L66.254,1H2.009V101H64V96H7V6Zm60,5L79,24H67V11Z" transform="translate(1 1.5)"/>
                        <path class="-color-fill" d="M100.629,53.865l-9.51-9.493a1.277,1.277,0,0,0-1.807,0L81.25,52.417,56.259,77.348a1.307,1.307,0,0,0-.385.564L51.055,92.3a1.29,1.29,0,0,0,.807,1.627,1.246,1.246,0,0,0,.807,0l14.405-4.817a1.307,1.307,0,0,0,.564-0.384L92.58,63.742l8.049-8.071A1.277,1.277,0,0,0,100.629,53.865ZM56,89l3-9,5,5Zm11-6-6-5L83,57l5,6ZM91,60l-6-5,5-6,6,6Z" transform="translate(1 1.5)"/>
                        <path fill="#000" d="M16,30H51v5H16V30Zm0,11H60v5H16V41Zm0,11H60v5H16V52Zm0,11H60v5H16V63Zm0,11H36.216v5H16V74Z" transform="translate(1 1.5)"/>
                    </svg>
                </span>
                <?php echo Label::getLabel('LBL_Orders'); ?>
            </a>
        </li>
        <li class="<?php echo ( $controllerName == "TeacherLessonsPlan" ) ? 'is-active' : ''; ?>">
            <a href="<?php echo CommonHelper::generateUrl('TeacherLessonsPlan'); ?>">
                <span class="menu-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 105 105">
                        <path fill="#000" d="M67,56V35H16V85H53V56M52,40H63V51H52V40ZM21,40H31V51H21V40ZM31,80H21V70H31V80Zm0-15H21V56H31v9ZM48,51H36V40H48V51Zm43-2V9c0-.925-2.075-2-3-2H80L79.931,2.672A1.671,1.671,0,0,0,78.259,1H66.552a1.671,1.671,0,0,0-1.672,1.672L65,7H28l0.086-4.328A1.671,1.671,0,0,0,26.414,1H14.707a1.671,1.671,0,0,0-1.672,1.672L13,7H5C4.075,7,3,8.437,3,9.362V94.655C3,95.58,4.075,97,5,97H56a50.613,50.613,0,0,1,0-6H8V28H86V49M70,6h5v7H70V6ZM18,6h5v7H18V6ZM8,12h5l0.034,4.052A2.2,2.2,0,0,0,15,18H26a2.323,2.323,0,0,0,2.086-1.948L28,12H65l-0.121,4.052A2.36,2.36,0,0,0,67,18H78a2.163,2.163,0,0,0,1.931-1.948L80,12h6V23H8V12ZM36,56H48v9H36V56Zm0,14H48V80H36V70Z" transform="translate(2 2)"/>
                        <path class="-color-fill" d="M77.5,100A20.5,20.5,0,1,1,98,79.5,20.5,20.5,0,0,1,77.5,100Zm0-35.46A14.96,14.96,0,1,0,92.46,79.5,14.96,14.96,0,0,0,77.5,64.54ZM73,85V71h5v9h9v5H73Z" transform="translate(2 2)"/>
                    </svg>
                </span>
                <?php echo Label::getLabel('LBL_Lesson_Plan'); ?>
            </a>
        </li>
        <!--<li class="-hide-desktop -show-responsive">-->
        <li class="<?php echo ( $controllerName == "Account" ) ? 'is-active' : ''; ?>">
            <a href="<?php echo CommonHelper::generateUrl('Account', 'ProfileInfo'); ?>">
                <span class="menu-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 105 105">
                        <path fill="#000" d="M93.139,41.527L86.471,40.4a37.658,37.658,0,0,0-2.68-6.469l3.93-5.5a5.851,5.851,0,0,0-.635-7.561l-5.914-5.914a5.827,5.827,0,0,0-4.148-1.726,5.765,5.765,0,0,0-3.393,1.091l-5.517,3.929a37.272,37.272,0,0,0-6.708-2.758L60.294,8.9A5.863,5.863,0,0,0,54.5,4H46.144a5.862,5.862,0,0,0-5.795,4.9L39.2,15.649a36.71,36.71,0,0,0-6.45,2.719l-5.458-3.929a5.862,5.862,0,0,0-7.561.635L13.8,20.988a5.875,5.875,0,0,0-.635,7.561l3.969,5.577a36.729,36.729,0,0,0-2.64,6.489L7.9,41.726A5.863,5.863,0,0,0,3,47.521v8.355a5.863,5.863,0,0,0,4.9,5.795l6.747,1.151a36.748,36.748,0,0,0,2.719,6.449l-3.909,5.438a5.85,5.85,0,0,0,.635,7.561l5.914,5.914a5.835,5.835,0,0,0,7.541.635l5.576-3.969A37.531,37.531,0,0,0,39.4,87.43L40.508,94.1A5.863,5.863,0,0,0,46.3,99h8.375a5.863,5.863,0,0,0,5.795-4.9L61.6,87.43a37.621,37.621,0,0,0,6.47-2.679l5.5,3.929a5.828,5.828,0,0,0,3.414,1.091h0a5.825,5.825,0,0,0,4.148-1.727l5.914-5.914a5.874,5.874,0,0,0,.635-7.561l-3.929-5.517a37.391,37.391,0,0,0,2.679-6.469L93.1,61.472A5.863,5.863,0,0,0,98,55.677V47.322A5.792,5.792,0,0,0,93.139,41.527Zm-0.457,14.15a0.514,0.514,0,0,1-.437.516L83.91,57.582a2.664,2.664,0,0,0-2.143,1.965,31.776,31.776,0,0,1-3.453,8.315,2.683,2.683,0,0,0,.119,2.917l4.9,6.906a0.541,0.541,0,0,1-.06.675l-5.914,5.914a0.5,0.5,0,0,1-.377.159,0.487,0.487,0,0,1-.3-0.1l-6.886-4.9a2.683,2.683,0,0,0-2.918-.119,31.767,31.767,0,0,1-8.315,3.453A2.634,2.634,0,0,0,56.6,84.91l-1.409,8.335a0.514,0.514,0,0,1-.516.436H46.323a0.513,0.513,0,0,1-.516-0.436L44.418,84.91a2.664,2.664,0,0,0-1.965-2.143,32.991,32.991,0,0,1-8.137-3.334,2.747,2.747,0,0,0-1.349-.357,2.613,2.613,0,0,0-1.548.5l-6.946,4.941a0.587,0.587,0,0,1-.3.1,0.531,0.531,0,0,1-.377-0.159l-5.914-5.914a0.538,0.538,0,0,1-.059-0.675l4.882-6.846a2.717,2.717,0,0,0,.119-2.937,31.474,31.474,0,0,1-3.493-8.3,2.718,2.718,0,0,0-2.143-1.965L8.794,56.392a0.514,0.514,0,0,1-.436-0.516V47.521A0.514,0.514,0,0,1,8.794,47l8.276-1.389a2.683,2.683,0,0,0,2.163-1.985A31.722,31.722,0,0,1,22.627,35.3a2.65,2.65,0,0,0-.139-2.9l-4.941-6.946a0.541,0.541,0,0,1,.059-0.675l5.914-5.914a0.505,0.505,0,0,1,.377-0.159,0.487,0.487,0,0,1,.3.1l6.847,4.882a2.717,2.717,0,0,0,2.937.119,31.475,31.475,0,0,1,8.3-3.493,2.718,2.718,0,0,0,1.965-2.143l1.429-8.395a0.514,0.514,0,0,1,.516-0.436h8.355a0.514,0.514,0,0,1,.516.436l1.389,8.276a2.683,2.683,0,0,0,1.985,2.163,32.207,32.207,0,0,1,8.514,3.493,2.683,2.683,0,0,0,2.917-.119l6.847-4.922a0.591,0.591,0,0,1,.3-0.1,0.532,0.532,0,0,1,.377.159L83.3,24.639a0.539,0.539,0,0,1,.06.675l-4.9,6.886a2.682,2.682,0,0,0-.119,2.917,31.775,31.775,0,0,1,3.453,8.315A2.634,2.634,0,0,0,83.93,45.4l8.335,1.409a0.514,0.514,0,0,1,.437.516v8.355h-0.02Z" transform="translate(2 1)"/>
                        <path class="-color-fill" d="M50.511,30.99a20.5,20.5,0,1,0,20.5,20.5A20.514,20.514,0,0,0,50.511,30.99Zm0,35.642A15.142,15.142,0,1,1,65.652,51.49,15.152,15.152,0,0,1,50.511,66.632Z" transform="translate(2 1)"/>
                    </svg>
                </span>
                <?php echo Label::getLabel('LBL_Settings'); ?>
            </a>
        </li>
        <li  class="<?php echo ( $controllerName == "TeacherReports" ) ? 'is-active' : ''; ?>">
            <a href="<?php echo CommonHelper::generateUrl('TeacherReports'); ?>">
                <span class="menu-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 105 105">
                        <path fill="#000" d="M73.725,3H6V99H97V26.338ZM75,12L89,25H75V12ZM11,94V8H71V30H92V94H11Z" transform="translate(1 1.5)"/>
                        <path class="-color-fill" d="M25,27H57v5H25V27Zm0,11H73v5H25V38Zm0,11H73v5H25V49Zm0,11H73v5H25V60Zm0,11H73v5H25V71Z" transform="translate(1 1.5)"/>
                    </svg>
                </span>
                <?php echo Label::getLabel('LBL_Reports'); ?>
            </a>
        </li>
        <li class="-hide-desktop -show-responsive">
            <a href="<?php echo CommonHelper::generateUrl('GuestUser', 'logout'); ?>">
                <span class="menu-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 105 105">
                        <path fill="#000" d="M52,95A41.878,41.878,0,0,1,10,53.245C10,34.075,23.29,17.9,41,13v6A35.535,35.535,0,0,0,16,53c0,19.837,15.8,35,36,35S88,72.837,88,53A35.535,35.535,0,0,0,63,19V13c17.71,4.9,31,21.075,31,40.245A41.878,41.878,0,0,1,52,95Z" transform="translate(0.5 1.5)"/>
                        <rect class="-color-fill" x="49.5" y="8.5" width="6" height="44"/>
                    </svg>
                </span>
                <?php echo Label::getLabel('LBL_Logout'); ?>
            </a>
        </li>
        <li class="<?php echo ( $controllerName == "Gdpr" ) ? 'is-active' : ''; ?>">
            <a href="<?php echo CommonHelper::generateUrl('Gdpr'); ?>">
                <span class="menu-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 105 105">
                        <path class="-color-fill" d="M27,27H75v5H27V27Zm0,11H75v5H27V38Zm0,11H75v5H27V49Zm0,11H75v5H27V60Zm0,11H75v5H27V71Z" transform="translate(1.5 1.5)"/>
                        <path fill="#000"  d="M6,99V3H96V99H6ZM91,8H11V94H91V8Z" transform="translate(1.5 1.5)"/>
                    </svg>
                </span>
                <?php echo Label::getLabel('LBL_Request_to_Delete_Account'); ?>
            </a>
        </li>
    </ul>
</nav>