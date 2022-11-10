<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php if (!empty($images)) { ?>
    <ul class="grids--onethird">
        <?php
        $count = 1;
        foreach ($images as $afile_id => $row) {
            ?>
            <li id="<?php echo $row['afile_id']; ?>">
                <div class="logoWrap">
                    <div class="logothumb"> <img src="<?php echo CommonHelper::generateUrl('Image', 'countryFlag', array($row['afile_record_id'], 'THUMB'), CONF_WEBROOT_FRONT_URL) . '?' . time(); ?>" title="<?php echo $row['afile_name']; ?>" alt="<?php echo $row['afile_name']; ?>"> <?php if ($canEdit) { ?> <a class="deleteLink white" href="javascript:void(0);" title="<?php echo Label::getLabel('LBL_Delete'); ?> <?php echo $row['afile_name']; ?>" onclick="deleteFlagImage(<?php echo $row['afile_record_id']; ?>);" class="delete"><i class="ion-close-round"></i></a>
                            <?php } ?>
                    </div>
            </li>
            <?php
            $count++;
        }
        ?>
    </ul>
<?php } ?>