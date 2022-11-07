<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
if ($page == 1) {
    $frm->setFormTagAttribute('onSubmit', 'sendMessage(this); return false;');
    $frm->setFormTagAttribute('class', 'form');
    $frm->developerTags['colClassPrefix'] = 'col-md-';
    $frm->developerTags['fld_default_col'] = 12;
    $messageBox = $frm->getField('message_text');
    $messageBox->addFieldTagAttribute('placeholder', Label::getLabel('LBL_Type_a_message_here'));
}
$nextPage = $page + 1;
$userTimeZone = MyDate::getUserTimeZone();
$senderImage = '';
$senderName = $otherUserDetail['user_first_name'] . ' ' . $otherUserDetail['user_last_name'];
if (true == User::isProfilePicUploaded($otherUserDetail['user_id'])) {
    $senderImage = '<img src="' . CommonHelper::generateUrl('Image', 'user', array($otherUserDetail['user_id']), CONF_WEBROOT_FRONT_URL) . '?' . time() . '" alt="' . $senderName . '" />';
}
if ($page == 1) {
    ?>
    <div class="chat-room">
        <div class="chat-room__head">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="msg-list align-items-center">
                        <div class="msg-list__left">
                            <div class="avtar" data-title="<?php echo CommonHelper::getFirstChar($otherUserDetail['user_first_name']); ?>">
                                <?php echo $senderImage; ?>
                            </div>
                        </div>
                        <div class="msg-list__right">
                            <h6><?php echo $senderName; ?></h6>
                        </div>
                    </div>
                </div>
                <div>
                    <a href="javascript:void(0);" onclick='closethread();' class="close msg-close-js"></a>
                </div>
            </div>
        </div>
        <div class="chat-room__body">
            <div class="chat-list margin-top-auto">
            <?php } ?>
            <?php if ($nextPage <= $pageCount) { ?>
                <div class="load-more-js chat chat--info ">
                    <a id="loadMoreBtn" href="javascript:void(0)" onClick="getThread(<?php echo $threadId . ', ' . $nextPage; ?>);" class="loadmore box box--white" title="<?php echo Label::getLabel('LBL_Load_Previous'); ?>"><i class="fa fa-history"></i>&nbsp;<?php echo Label::getLabel('LBL_Load_Previous'); ?></a>
                </div>
            <?php } ?>
            <?php
            $date = '';
            $myDate = new MyDate();
            $myDate->setMonthAndweekName($siteLangId);
            foreach ($arrListing as $row) {
                $fromMe = ($row['message_from_user_id'] == $userId);
                $msgDate = MyDate::convertTimeFromSystemToUserTimezone('Y-m-d H:i:s', $row['message_date'], true, $userTimeZone);
                $msgDateUnix = strtotime($msgDate);
                if (empty($date) || ($date != date('Ymd', $msgDateUnix))) {
                    ?>
                    <!-- <div class="chat chat--info">
                        <span class="span"><?php //echo date('Y-m-d', $msgDateUnix); 
                    ?></span>
                    </div> -->
                    <?php
                    $date = date('Ymd', $msgDateUnix);
                }
                ?>
                <div class="chat <?php echo (!$fromMe) ? 'chat--incoming' : 'chat--outgoing'; ?>">
                    <?php if (!$fromMe) { ?>
                        <div class="chat__media">
                            <div class="avtar avtar--small" data-title="<?php echo CommonHelper::getFirstChar($row['message_from_name']); ?>">
                                <?php echo $senderImage; ?>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="chat__content">
                        <div class="chat__message"><?php echo nl2br($row['message_text']); ?></div>
                        <div class="chat__meta flex align-items--center font-xsmall color-light margin-top-3">
                            <span class="chat__user color-<?php echo ($fromMe) ? 'black' : 'primary' ?> bold-600 margin-right-2"><?php echo $row['message_from_name']; ?></span>
                            <time class="chat__time"><?php echo $myDate->convertToLocal(date('Y-m-d h:i A', $msgDateUnix)); ?></time>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if ($page == 1) { ?>
            </div>
        </div>
        <div class="chat-room__footer">
            <?php echo $frm->getFormTag(); ?>
            <div class="chat-form">
                <div class="chat-form__item">
                    <?php echo $messageBox->getHTML(); ?>
                </div>
                <div class="chat-form__item">
                    <div class="send-button">
                        <svg class="icon icon--arrow icon--small color-white" title="<?php echo Label::getLabel('LBL_Send_Message'); ?>">
                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.yo-coach.svg#up-arrow'; ?>"></use>
                        </svg>
                        <?php echo $frm->getFieldHtml('message_thread_id'); ?>
                        <?php echo $frm->getFieldHtml('btn_submit'); ?>
                    </div>
                </div>
            </div>
            </form>
            <?php echo $frm->getExternalJS(); ?>
        </div>
    </div>
    <script>
        var page = 1;
        if ($(window).width() > 1199) {
            $('.scrollbar-js').enscroll({
                verticalTrackClass: 'scrollbar-track',
                verticalHandleClass: 'scrollbar-handle'
            });
        }
        function updatesessionStorage(obj) {
            checked = false;
            if ($(obj).is(":checked")) {
                checked = true;
            }
            localStorage.setItem('is_enter', checked);
        }
        $(document).ready(function () {
            if (localStorage.getItem('is_enter') == true || localStorage.getItem('is_enter') == "true") {
                $('input[name=is_enter]').prop('checked', true);
            } else {
                $('input[name=is_enter]').prop('checked', false);
            }
            $('textarea[name=message_text]').keydown(function (event) {
                is_enter = localStorage.getItem('is_enter');
                if (event.keyCode == 13 && !event.shiftKey && (is_enter == "true" || is_enter == true)) {
                    $('#frm_fat_id_frmSendMessage').submit();
                }
            });
            $('.chat-room__body').scroll(function () {
                console.log('dsds');
                var scrollAmount = $(this).scrollTop();
                var documentHeight = $(this).height();
                if (scrollAmount == 35) {
                }
                console.log(scrollAmount, documentHeight);
            });
        })
    </script>
<?php } ?>