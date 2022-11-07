<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<div>
    <?php foreach($ReportIssueCategoriesList as $value){ ?>
    <div>
        <div>
            <h3>Complaints from client:</h3>
        </div>
        <div>
            <?php echo $value['report_comments_information']; ?>
        </div>
        <hr>
    </div>
    <?php } ?>
</div>