<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$referer = preg_replace("(^https?://)", "", $referer);
MyDate::setUserTimeZone();
$user_timezone = MyDate::getUserTimeZone();
$date = new DateTime("now", new DateTimeZone($user_timezone));
$curDate = $date->format('Y-m-d');
$nextDate = date('Y-m-d', strtotime('+1 days', strtotime($curDate)));
$curDateTime = MyDate::convertTimeFromSystemToUserTimezone('Y/m/d H:i:s', date('Y-m-d H:i:s'), true, $user_timezone);
if (count($lessonPackages)) {
    $lessonPackage = array_shift($lessonPackages);
}
foreach ($lessonArr as $key => $lessons) {
    ?>
    <div class="col-list-group">
        <?php if ($key != '0000-00-00') { ?>
            <h6><?php
                if (strtotime($curDate) == strtotime($key)) {
                    echo Label::getLabel('LBL_Today');
                } elseif (strtotime($nextDate) == strtotime($key)) {
                    echo Label::getLabel('LBL_Tommorrow');
                } else {
                    echo date('l, F d, Y', strtotime($key));
                }
                ?></h6>
        <?php } ?>
        <div class="col-list-container">
            <?php
            foreach ($lessons as $lesson) {
                $action = '';
                if ($lesson['is_trial'] == 1) {
                    $action = 'free_trial';
                }
                ?>
                <div class="col-list">
                    <div class="d-lg-flex align-items-center">
                        <div class="col-xl-4 col-lg-4 col-md-12">
                            <div class="avtar avtar--normal" data-title="<?php echo CommonHelper::getFirstChar($lesson['teacherFname']); ?>">
                                <?php
                                if (true == User::isProfilePicUploaded($lesson['teacherId'])) {
                                    $img = CommonHelper::generateUrl('Image', 'user', array($lesson['teacherId']), CONF_WEBROOT_FRONT_URL) . '?' . time();
                                    echo '<img src="' . $img . '" />';
                                }
                                ?>
                            </div>
                            <h6><?php echo $lesson['teacherFname']; ?></h6>
                            <p><?php echo $lesson['teacherCountryName']; ?></p>
                        </div>
                        <div class="col-xl-4 col-lg-4 col-md-12">
                            <div class="schedule-list">
                                <ul>
                                    <?php if ($lesson['slesson_grpcls_id'] > 0): ?>
                                        <li>
                                            <span class="span-left"><?php echo Label::getLabel('LBL_Class'); ?></span>
                                            <span class="span-right"><?php echo $lesson['grpcls_title']; ?></span>
                                        </li>
                                    <?php endif; ?>
                                    <?php
                                    if ($lesson['sldetail_learner_status'] == ScheduledLesson::STATUS_NEED_SCHEDULING && $lesson['is_trial'] == 1) {
                                        ?>
                                        <li>
                                            <h6 class="-color-secondary"><?php echo Label::getLabel('LBL_Trial_Lesson'); ?></h6>
                                        </li>
                                        <?php
                                    }
                                    $date = DateTime::createFromFormat('Y-m-d', $lesson['slesson_date']);
                                    if ($date && ($date->format('Y-m-d') === $lesson['slesson_date'])) {
                                        ?>
                                        <li>
                                            <span class="span-left"><?php echo Label::getLabel('LBL_Schedule'); ?></span>
                                            <span class="span-right">
                                                <h4>
                                                    <?php
                                                    echo MyDate::convertTimeFromSystemToUserTimezone('h:i A', $lesson['slesson_start_time'], true, $user_timezone);
                                                    ?>
                                                </h4>
                                                <?php
                                                echo MyDate::convertTimeFromSystemToUserTimezone('l, F d, Y', $lesson['slesson_date'] . " " . $lesson['slesson_start_time'], true, $user_timezone);
                                                ?>
                                            </span>
                                        </li>
                                    <?php } ?>
                                    <li>
                                        <span class="span-left"><?php echo Label::getLabel('LBL_Status'); ?></span>
                                        <span class="span-right"><?php echo ($lesson['slesson_grpcls_id'] > 0 && $lesson['grpcls_status'] != TeacherGroupClasses::STATUS_ACTIVE) ? $grpStatusesAr[$lesson['grpcls_status']] : $statusArr[$lesson['sldetail_learner_status']]; ?></span>
                                    </li>
                                    <?php if ($lesson['issrep_id']) { ?>
                                        <li>
                                            <span class="span-left"><?php echo Label::getLabel('LBL_Issue_Status'); ?></span>
                                            <span class="span-right"><?php echo IssuesReported::getStatusArr()[$lesson['issrep_status']]; ?></span>
                                        </li>
                                    <?php } ?>
                                    <li>
                                        <span class="span-left"><?php echo Label::getLabel('LBL_Details'); ?></span>
                                        <span class="span-right">
                                            <?php echo empty($teachLanguages[$lesson['slesson_slanguage_id']]) ? '' : $teachLanguages[$lesson['slesson_slanguage_id']]; ?><br>
                                            <?php
                                            if ($lesson['slesson_date'] != "0000-00-00") {
                                                $str = Label::getLabel('LBL_{n}_minutes_of_{trial-or-paid}_Lesson');
                                                $arrReplacements = array(
                                                    '{n}' => $lesson['op_lesson_duration'],
                                                    '{trial-or-paid}' => ($lesson['is_trial']) ? Label::getLabel('LBL_Trial') : '',
                                                );
                                                foreach ($arrReplacements as $key => $val) {
                                                    $str = str_replace($key, $val, $str);
                                                }
                                                echo $str;
                                            }
                                            ?>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <?php if ($lesson['sldetail_learner_status'] != ScheduledLesson::STATUS_CANCELLED) { ?>
                            <div class="col-xl-4 col-lg-4 col-md-12 col-positioned">
                                <div class="schedule-list">
                                    <ul>
                                        <?php
                                        $lessonsStartTime = $lesson['slesson_date'] . " " . $lesson['slesson_start_time'];
                                        $timerEndTimer = MyDate::convertTimeFromSystemToUserTimezone('Y/m/d H:i:s', $lessonsStartTime, true, $user_timezone);
                                        $endTime = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', $lesson['slesson_end_date'] . ' ' . $lesson['slesson_end_time'], true, $user_timezone);
                                        if ($lesson['sldetail_learner_status'] == ScheduledLesson::STATUS_SCHEDULED) {
                                            if (strtotime($timerEndTimer) > strtotime($curDateTime)) {
                                                ?>
                                                <li class="lesson-listing-timer timer">
                                                    <span class="span-right">
                                                        <span class="-color-secondary label"> <?php echo Label::getLabel('LBL_Class_Starts_in'); ?></span>
                                                        <span class="countdowntimer" id="countdowntimer-<?php echo $lesson['slesson_id'] ?>" data-startTime="<?php echo $curDateTime; ?>"  data-endTime="<?php echo $timerEndTimer; ?>"></span>
                                                    </span>
                                                </li>
                                                <?php
                                            } else {
                                                if (strtotime($endTime) > strtotime($curDateTime)) {
                                                    $lessonInfoLblKey = 'LBL_Lesson_ongoing';
                                                } else {
                                                    $lessonInfoLblKey = 'LBL_Lesson_time_has_passed';
                                                }
                                                ?>
                                                <li class="span-right">
                                                    <span class="-color-secondary"><?php echo Label::getLabel($lessonInfoLblKey); ?></span>
                                                </li>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </ul>
                                </div>
                                <ul class="actions">
                                    <li>
                                        <a href="<?php echo CommonHelper::generateUrl('LearnerScheduledLessons', 'view', [$lesson['sldetail_id']]); ?>" class="" title="<?php echo Label::getLabel('LBL_View'); ?>">
                                            <svg version="1.1" width="20px"  xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                 viewBox="0 0 511.999 511.999" style="enable-background:new 0 0 511.999 511.999;" xml:space="preserve">
                                            <g>
                                            <g>
                                            <path d="M508.745,246.041c-4.574-6.257-113.557-153.206-252.748-153.206S7.818,239.784,3.249,246.035
                                                  c-4.332,5.936-4.332,13.987,0,19.923c4.569,6.257,113.557,153.206,252.748,153.206s248.174-146.95,252.748-153.201
                                                  C513.083,260.028,513.083,251.971,508.745,246.041z M255.997,385.406c-102.529,0-191.33-97.533-217.617-129.418
                                                  c26.253-31.913,114.868-129.395,217.617-129.395c102.524,0,191.319,97.516,217.617,129.418
                                                  C447.361,287.923,358.746,385.406,255.997,385.406z"/>
                                            </g>
                                            </g>
                                            <g>
                                            <g>
                                            <path d="M255.997,154.725c-55.842,0-101.275,45.433-101.275,101.275s45.433,101.275,101.275,101.275
                                                  s101.275-45.433,101.275-101.275S311.839,154.725,255.997,154.725z M255.997,323.516c-37.23,0-67.516-30.287-67.516-67.516
                                                  s30.287-67.516,67.516-67.516s67.516,30.287,67.516,67.516S293.227,323.516,255.997,323.516z"/>
                                            </g>
                                            </g>
                                            </svg>
                                        </a>
                                    </li>
                                    <?php if ($referer == preg_replace("(^https?://)", "", CommonHelper::generateFullUrl('LearnerGroupClasses'))) { ?>
                                        <?php if ($lesson['sldetail_learner_status'] == ScheduledLesson::STATUS_COMPLETED && $lesson['issrep_id'] < 1 || $lesson['issrep_status'] == IssuesReported::STATUS_RESOLVED && ($lesson['issrep_issues_resolve_by'] != IssuesReported::RESOLVE_TYPE_LESSON_COMPLETED_HALF_REFUND && $lesson['issrep_issues_resolve_by'] != IssuesReported::RESOLVE_TYPE_LESSON_COMPLETED_FULL_REFUND)) { ?>
                                            <li>
                                                <a href="javascript:void(0);" onclick="issueReported('<?php echo $lesson['sldetail_id']; ?>')" class="" title="<?php echo Label::getLabel('LBL_Issue_Reported'); ?>">
                                                    <svg width="20px" viewBox="0 0 60 60" width="512" xmlns="http://www.w3.org/2000/svg"><g id="Page-1" fill="none" fill-rule="evenodd"><g id="070---Laptop-Message-Not-Sent" fill="rgb(0,0,0)" fill-rule="nonzero"><path id="Shape" d="m58 48h-2v-33c-.0033061-2.7600532-2.2399468-4.9966939-5-5h-1v-7c0-1.65685425-1.3431458-3-3-3h-34c-1.6568542 0-3 1.34314575-3 3v7h-1c-2.76005315.0033061-4.99669388 2.2399468-5 5v33h-2c-1.1045695 0-2 .8954305-2 2v3c.00495836 3.8639376 3.13606244 6.9950416 7 7h46c3.8639376-.0049584 6.9950416-3.1360624 7-7v-3c0-1.1045695-.8954305-2-2-2zm-46-45c0-.55228475.4477153-1 1-1h34c.5522847 0 1 .44771525 1 1v28c0 .5522847-.4477153 1-1 1h-24c-.5522847 0-1 .4477153-1 1s.4477153 1 1 1h.8l-5.8 4.01v-4.01h1c.5522847 0 1-.4477153 1-1s-.4477153-1-1-1h-6c-.5522847 0-1-.4477153-1-1zm-6 12c0-1.6568542 1.34314575-3 3-3h1v19c0 1.6568542 1.3431458 3 3 3h3v4.01c.0055034 1.1006623.899324 1.9900138 2 1.99.4068132-.0004921.803803-.1250311 1.138-.357l8.174-5.643h19.688c1.6568542 0 3-1.3431458 3-3v-19h1c1.6568542 0 3 1.3431458 3 3v33h-48zm15 35h18v1c0 1.1045695-.8954305 2-2 2h-14c-1.1045695 0-2-.8954305-2-2zm37 3c-.0033061 2.7600532-2.2399468 4.9966939-5 5h-46c-2.76005315-.0033061-4.99669388-2.2399468-5-5v-3h17v1c0 2.209139 1.790861 4 4 4h14c2.209139 0 4-1.790861 4-4v-1h17z"/><path id="Shape" d="m15 52h-2c-.5522847 0-1 .4477153-1 1s.4477153 1 1 1h2c.5522847 0 1-.4477153 1-1s-.4477153-1-1-1z"/><circle id="Oval" cx="5" cy="53" r="1"/><circle id="Oval" cx="9" cy="53" r="1"/><path id="Shape" d="m30 30c7.1797017 0 13-5.8202983 13-13 0-7.17970175-5.8202983-13-13-13s-13 5.82029825-13 13c.008266 7.1762751 5.8237249 12.991734 13 13zm0-24c6.0751322 0 11 4.9248678 11 11s-4.9248678 11-11 11-11-4.9248678-11-11c.0071635-6.0721626 4.9278374-10.9928365 11-11z"/><path id="Shape" d="m25.7 24.019c.1510172.6758719.6385372 1.2268337 1.291 1.459.9655907.3454686 1.9834689.5220496 3.009.522.7093957-.0007512 1.4163592-.0829563 2.107-.245 2.4813348-.5949487 4.5927859-2.2166431 5.8076453-4.4605466 1.2148595-2.2439034 1.4185843-4.8984494.5603547-7.3014534-.2308809-.6525752-.7807135-1.1408537-1.456-1.293-.6678543-.1529236-1.367337.0497373-1.85.536l-8.933 8.933c-.4854126.4831671-.6879357 1.1821742-.536 1.85zm10.9-9.352c.4529815 1.2787496.527288 2.6610594.214 3.981-.4622316 1.9319167-1.7262636 3.5750969-3.4750127 4.5173479s-3.816293 1.0941715-5.6839873.4176521z"/><path id="Shape" d="m23.423 21.35c.5285959-.0002743 1.035296-.2111595 1.408-.586l8.933-8.933c.4854126-.4831671.6879357-1.1821742.536-1.85-.1510172-.67587187-.6385372-1.22683366-1.291-1.459-3.2707615-1.16856777-6.9220918-.34781853-9.3781538 2.1080297-2.4560621 2.4558483-3.2771292 6.1071071-2.1088462 9.3779703.2876055.8027571 1.0472816 1.3393283 1.9 1.342zm-.231-6c.60786-2.5542076 2.6019103-4.5486446 5.156-5.157.5387455-.1273922 1.0903984-.1921543 1.644-.193.804837-.00046099 1.6036627.1385976 2.361.411l-8.948 8.92c-.453213-1.2787345-.527181-2.6612067-.213-3.981z"/></g></g></svg>
                                                </a>
                                            </li>
                                        <?php } ?>
                                        <?php if ($lesson['sldetail_learner_status'] == ScheduledLesson::STATUS_COMPLETED) { ?>
                                            <li>
                                                <a width="20px" href="javascript:void(0);" onclick="lessonFeedback('<?php echo $lesson['sldetail_id']; ?>');" class="" title="<?php echo Label::getLabel('LBL_Rate_Lesson'); ?>">
                                                    <svg  enable-background="new 0 0 512 512"  viewBox="0 0 512 512" width="30px" xmlns="http://www.w3.org/2000/svg"><g><path d="m489.456 0h-99.735c-4.151 0-7.515 3.364-7.515 7.515s3.364 7.515 7.515 7.515h99.735c4.144 0 7.515 3.371 7.515 7.515v151.253c0 4.144-3.371 7.515-7.515 7.515h-206.769v-3.613c0-14.66-11.926-26.585-26.585-26.585s-26.585 11.926-26.585 26.585v3.613h-76.549c-4.15 0-7.515 3.364-7.515 7.515s3.365 7.515 7.515 7.515h76.549v132.432l-17.849-20.004c-11.018-12.348-26.828-19.43-43.376-19.43-6.167 0-11.73 3.165-14.881 8.467s-3.271 11.702-.323 17.119l29.928 54.979c7.419 13.629 16.882 25.966 28.126 36.665l31.894 30.347v16.098c-7.801 1.621-13.68 8.546-13.68 16.821v24.983c0 9.474 7.707 17.181 17.181 17.181h118.487c9.474 0 17.182-7.707 17.182-17.181v-24.983c0-8.096-5.635-14.884-13.184-16.693v-16.705c6.849-8.64 28.086-40.348 28.086-101.259v-45.9c0-14.659-11.926-26.584-26.585-26.584-4.899 0-9.493 1.332-13.439 3.653-3.917-9.807-13.513-16.755-24.702-16.755-4.899 0-9.492 1.332-13.438 3.653-3.917-9.807-13.513-16.755-24.702-16.755-4.139 0-8.06.95-11.556 2.645v-42.792h206.769c12.431 0 22.544-10.114 22.544-22.544v-151.257c.001-12.431-10.112-22.544-22.543-22.544zm-195.213 251.518c6.372 0 11.556 5.184 11.556 11.556v13.103c0 4.151 3.364 7.515 7.515 7.515s7.515-3.364 7.515-7.515c0-6.372 5.184-11.556 11.556-11.556 6.371 0 11.555 5.184 11.555 11.556v13.102c0 4.151 3.364 7.515 7.515 7.515s7.515-3.364 7.515-7.515c0-6.372 5.184-11.555 11.556-11.555s11.556 5.183 11.556 11.555v45.9c0 64.562-25.791 93.094-26.025 93.346-1.324 1.395-2.062 3.246-2.062 5.17v18.959h-18.155c-4.151 0-7.515 3.364-7.515 7.515s3.364 7.515 7.515 7.515h29.185c1.187 0 2.153.966 2.153 2.153v24.983c0 1.187-.966 2.152-2.153 2.152h-118.488c-1.187 0-2.152-.965-2.152-2.152v-24.983c0-1.187.965-2.153 2.152-2.153h59.244c4.151 0 7.515-3.364 7.515-7.515s-3.364-7.515-7.515-7.515h-47.715v-18.959c0-2.058-.844-4.025-2.335-5.444l-34.229-32.569c-10.109-9.619-18.616-20.709-25.286-32.963l-29.928-54.979c-.707-1.896-.001-3.085 2.003-3.371 12.27 0 23.992 5.25 32.161 14.406l30.685 34.39c4.354 5.122 13.617 1.855 13.407-5.003v-170.462c0-6.372 5.184-11.556 11.556-11.556 6.371 0 11.555 5.184 11.555 11.556v85.374c0 4.151 3.364 7.515 7.515 7.515s7.515-3.364 7.515-7.515c.002-6.372 5.186-11.556 11.558-11.556z"/><path d="m122.909 181.312h-100.365c-4.144 0-7.515-3.371-7.515-7.515v-151.253c0-4.144 3.371-7.515 7.515-7.515h337.118c4.151 0 7.515-3.364 7.515-7.515s-3.364-7.514-7.515-7.514h-337.118c-12.431 0-22.544 10.113-22.544 22.544v151.253c0 12.43 10.113 22.544 22.544 22.544h100.365c4.15 0 7.515-3.364 7.515-7.515s-3.364-7.514-7.515-7.514z"/><path d="m305.453 92.526c4.415-4.303 5.975-10.619 4.069-16.483-1.905-5.864-6.88-10.058-12.982-10.945l-17.497-2.542c-.343-.05-.64-.265-.794-.576l-7.826-15.856c-2.728-5.529-8.253-8.964-14.42-8.964-6.166 0-11.692 3.434-14.421 8.964l-7.826 15.857c-.154.31-.45.526-.791.575l-17.501 2.542c-6.102.887-11.076 5.081-12.981 10.945s-.346 12.18 4.069 16.483l12.663 12.344c.248.242.361.589.302.931l-2.989 17.428c-1.042 6.076 1.409 12.103 6.397 15.727 4.988 3.625 11.478 4.094 16.936 1.225l15.652-8.229c.306-.162.672-.162.978 0l15.653 8.229c2.374 1.247 4.942 1.864 7.498 1.864 3.32 0 6.619-1.041 9.438-3.089 4.987-3.624 7.438-9.651 6.395-15.727l-2.988-17.428c-.059-.341.054-.689.302-.931zm-23.154 1.583c-3.791 3.695-5.52 9.016-4.624 14.233l2.989 17.43c.029.176.11.644-.419 1.027-.527.384-.951.162-1.108.08l-15.651-8.228c-2.344-1.233-4.913-1.848-7.484-1.848-2.57 0-5.14.616-7.483 1.847l-15.651 8.229c-.158.082-.579.306-1.109-.08-.529-.384-.449-.852-.418-1.028l2.989-17.429c.895-5.218-.834-10.539-4.625-14.233l-12.662-12.343c-.128-.125-.468-.457-.267-1.078.202-.622.673-.691.849-.716l17.501-2.542c5.239-.762 9.764-4.052 12.106-8.797l7.826-15.856c.079-.161.289-.586.943-.586s.863.426.943.586l7.826 15.855c2.341 4.747 6.868 8.036 12.109 8.799l17.498 2.542c.177.026.648.094.849.716.202.621-.138.953-.266 1.078z"/><path d="m145.641 123.23-2.989-17.428c-.059-.341.054-.689.303-.931l12.662-12.344c4.415-4.303 5.974-10.619 4.069-16.483s-6.879-10.058-12.982-10.945l-17.498-2.542c-.343-.05-.639-.265-.793-.576l-7.826-15.856c-2.729-5.529-8.254-8.964-14.42-8.964s-11.692 3.434-14.421 8.964l-7.825 15.856c-.154.311-.451.526-.792.576l-17.5 2.542c-6.102.887-11.076 5.081-12.981 10.945s-.346 12.18 4.069 16.483l12.663 12.344c.248.242.361.589.302.931l-2.989 17.428c-1.042 6.077 1.409 12.104 6.397 15.727 4.989 3.626 11.478 4.093 16.936 1.225l15.652-8.229c.306-.161.672-.161.979 0l15.653 8.229c2.374 1.247 4.942 1.864 7.498 1.864 3.32 0 6.619-1.041 9.438-3.089 4.986-3.624 7.437-9.651 6.395-15.727zm-13.176-29.122c-3.792 3.695-5.521 9.016-4.626 14.234l2.989 17.429c.03.176.111.644-.418 1.028-.528.386-.95.162-1.108.08l-15.652-8.229c-2.343-1.232-4.913-1.847-7.483-1.847s-5.141.616-7.483 1.847l-15.651 8.229c-.158.082-.579.305-1.108-.08-.529-.383-.449-.852-.418-1.028l2.989-17.429c.895-5.218-.834-10.539-4.625-14.233l-12.662-12.344c-.128-.125-.468-.457-.267-1.078.202-.622.673-.691.849-.716l17.501-2.542c5.238-.762 9.764-4.051 12.107-8.797l7.826-15.856c.079-.161.289-.586.943-.586s.864.426.943.586l7.826 15.855c2.342 4.747 6.867 8.036 12.108 8.799l17.499 2.542c.177.026.647.094.849.716.202.621-.138.953-.266 1.078z"/><path d="m455.289 92.526c4.415-4.303 5.975-10.619 4.069-16.483-1.905-5.864-6.88-10.058-12.982-10.945l-17.497-2.542c-.344-.05-.64-.265-.794-.576l-7.826-15.856c-2.729-5.529-8.254-8.964-14.42-8.964s-11.692 3.434-14.421 8.964l-7.825 15.855c-.155.311-.451.527-.793.577l-17.5 2.542c-6.103.887-11.076 5.081-12.981 10.946-1.905 5.864-.345 12.18 4.069 16.482l12.663 12.344c.247.242.361.589.301.931l-2.987 17.428c-1.043 6.077 1.408 12.104 6.396 15.728 4.986 3.625 11.476 4.094 16.935 1.225l15.652-8.229c.305-.16.673-.162.978 0l15.653 8.229c2.374 1.247 4.942 1.864 7.498 1.864 3.32 0 6.619-1.041 9.438-3.089 4.987-3.624 7.438-9.651 6.395-15.727l-2.988-17.428c-.059-.341.054-.689.302-.931zm-23.154 1.583c-3.791 3.695-5.52 9.016-4.624 14.233l2.989 17.43c.029.176.11.644-.419 1.027-.527.384-.951.162-1.108.08l-15.651-8.228c-2.343-1.233-4.913-1.848-7.483-1.848s-5.141.616-7.484 1.847l-15.652 8.229c-.156.082-.578.305-1.107-.08-.528-.383-.448-.851-.418-1.028l2.987-17.426c.896-5.218-.832-10.54-4.623-14.236l-12.662-12.345c-.127-.124-.468-.456-.266-1.077.203-.622.673-.691.849-.716l17.501-2.542c5.237-.762 9.762-4.051 12.107-8.797l7.826-15.856c.079-.161.29-.586.943-.586s.863.426.942.586l7.826 15.855c2.341 4.747 6.868 8.036 12.109 8.799l17.498 2.542c.177.026.648.094.849.716.203.621-.138.953-.266 1.078z"/></g></svg>
                                                </a>
                                            </li>
                                        <?php } ?>
                                        <?php if ($lesson['sldetail_learner_status'] == ScheduledLesson::STATUS_ISSUE_REPORTED || $lesson['issrep_id'] > 0) { ?>
                                            <li>
                                                <a href="javascript:void(0);" onclick="issueDetails('<?php echo $lesson['sldetail_id']; ?>')" class="" title="<?php echo Label::getLabel('LBL_Issue_Details'); ?>">
                                                    <svg version="1.1"  width="35px"  xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                         viewBox="0 0 363 363" style="enable-background:new 0 0 363 363;" xml:space="preserve">
                                                    <path d="M277.73,94.123c0,10.997-8.006,17.685-13.852,22.593c-2.214,1.859-6.335,5.251-6.324,6.518
                                                          c0.04,4.97-3.956,8.939-8.927,8.939c-0.025,0-0.05,0-0.075,0c-4.936,0-8.958-3.847-8.998-8.792
                                                          c-0.079-9.747,7.034-15.584,12.75-20.383c4.485-3.766,7.426-6.416,7.426-8.841c0-4.909-3.994-8.903-8.903-8.903
                                                          c-4.911,0-8.906,3.994-8.906,8.903c0,4.971-4.029,9-9,9s-9-4.029-9-9c0-14.834,12.069-26.903,26.904-26.903
                                                          C265.661,67.253,277.73,79.288,277.73,94.123z M248.801,140.481c-4.971,0-8.801,4.029-8.801,9v0.069
                                                          c0,4.971,3.831,8.966,8.801,8.966s9-4.064,9-9.035S253.772,140.481,248.801,140.481z M67.392,203.174c-4.971,0-9,4.029-9,9
                                                          s4.029,9,9,9h0.75c4.971,0,9-4.029,9-9s-4.029-9-9-9H67.392z M98.671,203.174c-4.971,0-9,4.029-9,9s4.029,9,9,9h0.749
                                                          c4.971,0,9-4.029,9-9s-4.029-9-9-9H98.671z M363,59.425v101.301c0,23.985-19.232,43.448-43.217,43.448H203.066
                                                          c-2.282,0-4.161-0.013-5.733-0.046c-1.647-0.034-3.501-0.047-4.224,0.033c-0.753,0.5-2.599,2.191-4.378,3.83
                                                          c-0.705,0.649-1.503,1.363-2.364,2.149l-33.022,30.098c-2.634,2.403-6.531,3.025-9.793,1.587c-3.262-1.439-5.552-4.669-5.552-8.234
                                                          v-95.417H43.72c-14.062,0-25.72,11.523-25.72,25.583v101.301c0,14.061,11.659,25.116,25.72,25.116h130.374
                                                          c2.245,0,4.345,1.031,6.003,2.545L207,317.523v-85.539c0-4.971,4.029-9,9-9s9,4.029,9,9v105.938c0,3.565-2.04,6.747-5.303,8.186
                                                          c-1.167,0.515-2.339,0.718-3.566,0.718c-2.204,0-4.378-0.905-6.069-2.449l-39.457-36.204H43.72c-23.986,0-43.72-19.13-43.72-43.116
                                                          V163.757c0-23.985,19.734-43.583,43.72-43.583H138V59.425c0-23.986,19.885-43.251,43.871-43.251h137.913
                                                          C343.768,16.174,363,35.439,363,59.425z M345,59.425c0-14.061-11.157-25.251-25.217-25.251H181.871
                                                          C167.81,34.174,156,45.364,156,59.425v69.833v83.934l18.095-16.353c0.838-0.765,1.777-1.465,2.462-2.097
                                                          c8.263-7.614,10.377-8.831,21.155-8.609c1.47,0.031,3.221,0.042,5.354,0.042h116.717c14.06,0,25.217-11.388,25.217-25.448V59.425z"
                                                          />
                                                    </svg>
                                                </a>
                                            </li>
                                        <?php } ?>
                                        <?php if ($lesson['sldetail_learner_status'] == ScheduledLesson::STATUS_NEED_SCHEDULING || ScheduledLesson::STATUS_SCHEDULED && strtotime($timerEndTimer) >= strtotime($curDateTime)) { ?>
                                            <li>
                                                <a href="javascript:void(0);" onclick="cancelLesson('<?php echo $lesson['sldetail_id']; ?>')" class="" title="<?php echo Label::getLabel('LBL_Cancel_Lesson'); ?>">
                                                    <svg  width="14px" viewBox="0 0 329.26933 329" width="329pt" xmlns="http://www.w3.org/2000/svg"><path d="m194.800781 164.769531 128.210938-128.214843c8.34375-8.339844 8.34375-21.824219 0-30.164063-8.339844-8.339844-21.824219-8.339844-30.164063 0l-128.214844 128.214844-128.210937-128.214844c-8.34375-8.339844-21.824219-8.339844-30.164063 0-8.34375 8.339844-8.34375 21.824219 0 30.164063l128.210938 128.214843-128.210938 128.214844c-8.34375 8.339844-8.34375 21.824219 0 30.164063 4.15625 4.160156 9.621094 6.25 15.082032 6.25 5.460937 0 10.921875-2.089844 15.082031-6.25l128.210937-128.214844 128.214844 128.214844c4.160156 4.160156 9.621094 6.25 15.082032 6.25 5.460937 0 10.921874-2.089844 15.082031-6.25 8.34375-8.339844 8.34375-21.824219 0-30.164063zm0 0"/></svg>
                                                </a>
                                            </li>
                                        <?php } ?>
                                    <?php } ?>
                                </ul>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php
}
if (empty($lessons)) {
    $this->includeTemplate('_partial/no-record-found.php');
} else {
    echo FatUtility::createHiddenFormFromData($postedData, array(
        'name' => 'frmSLnsSearchPaging'
    ));
    if ($referer == preg_replace("(^https?://)", "", CommonHelper::generateFullUrl('LearnerGroupClasses'))) {
        $this->includeTemplate('_partial/pagination.php', $pagingArr, false);
    } else {
        echo "<div class='load-more -align-center'><a href='" . CommonHelper::generateFullUrl('LearnerGroupClasses') . "' class='btn btn--bordered btn--xlarge'>" . Label::getLabel('LBL_View_all') . "</a></div>";
    }
}
?>