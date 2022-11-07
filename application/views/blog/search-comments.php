<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
if ($commentsCount) {
    foreach ($blogPostComments as $comment) {
        ?>
        <div class="comments-list">
            <div class="avtar avtar--small avtar--centered" data-title="<?php echo CommonHelper::getFirstChar($comment['bpcomment_author_name']); ?>">
                <?php
                if (true == User::isProfilePicUploaded($comment['bpcomment_user_id'])) {
                    $img = CommonHelper::generateUrl('Image', 'user', array($comment['bpcomment_user_id'])) . '?' . time();
                    echo '<img src="' . $img . '" />';
                }
                ?>
            </div>
            <span class="date"><?php echo FatDate::format($comment['bpcomment_added_on']); ?></span>
            <h5><strong><?php echo CommonHelper::displayName($comment['bpcomment_author_name']); ?></strong> <?php echo Label::getLabel('LBL_Says:'); ?></h5>   
            <div class="comment__desc">
                <?php echo nl2br($comment['bpcomment_content']); ?>
            </div>
            <!--a href="#" class="link--underlined">Reply</a-->
        </div>
        <?php
    }
    echo FatUtility::createHiddenFormFromData($postedData, array('name' => 'frmSearchCommentsPaging'));
} else {
    ?>
    <div class="comment box box--white box--space">
        <?php if (!UserAuthentication::isUserLogged()) { ?>
            <span class=""><a href="<?php echo CommonHelper::generateUrl('GuestUser', 'loginForm'); ?>" ><?php echo Label::getLabel('Lbl_Login', $siteLangId); ?> </a> <?php echo Label::getLabel('Lbl_Login_required_to_post_comment', $siteLangId); ?></span>
            <?php
        } else {
            echo Label::getLabel('Msg_No_Comments_on_this_blog_post', $siteLangId);
            ?> <!--<a href="javascript:undefined" class="link--post-comment-form" ><?php // echo Label::getLabel('Lbl_Submit_your_comment',$siteLangId);?></a>-->
        <?php }
        ?>
    </div>
    <?php
}