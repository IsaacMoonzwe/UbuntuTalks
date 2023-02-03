<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php if ($rows) { ?>
    <div class="toggle-group">
        <h5 class="toggle__trigger toggle__trigger-js"><?php echo Label::getLabel('LBL_Social'); ?></h5>
        <div class="toggle__target toggle__target-js">
            <div class="social-links">
                <ul>
                    <?php
                    foreach ($rows as $row) {
                        $img = AttachedFile::getAttachment(AttachedFile::FILETYPE_SOCIAL_PLATFORM_IMAGE, $row['splatform_id']);
                        $title = ( $row['splatform_title'] != '' ) ? $row['splatform_title'] : $row['splatform_identifier'];
                        ?>
                        <li><a title="<?php echo $title; ?>" <?php if ($row['splatform_url'] != '') { ?>target="_blank" <?php } ?> href="<?php echo ($row['splatform_url'] != '') ? $row['splatform_url'] : 'javascript:void(0)'; ?>">
                                <?php
                                if ($img) {
                                    echo '<img src = "' . CommonHelper::generateUrl('Image', 'SocialPlatform', array($row['splatform_id']), CONF_WEBROOT_FRONT_URL) . '" alt=""/>';
                                } else {
                                    ?>
                                    <img src="<?php echo CONF_WEBROOT_FRONT_URL; ?>images/social_1.svg" alt="">
                                <?php } ?>
                            </a></li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
<?php } ?>
