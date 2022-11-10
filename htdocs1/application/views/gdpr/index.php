<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frmRequest->developerTags['colClassPrefix'] = 'col-md-';
$frmRequest->developerTags['fld_default_col'] = 12;
?>
<section class="section section--grey section--page">
    <?php //$this->includeTemplate('_partial/dashboardTop.php');  ?>
    <div class="container container--fixed">
        <div class="page-panel -clearfix">
            <div class="page-panel__left">
                <?php $this->includeTemplate('account/_partial/dashboardNavigation.php'); ?>
            </div>
            <div class="page-panel__right">
                <!-- ] -->
                <div class="page-head">
                    <div class="d-flex justify-content-between align-items-center">
                        <h1><?php echo Label::getLabel('LBL_Request_for_Data_Erasure'); ?></h1>
                    </div>
                </div>
                <div class="page-filters">
                    <?php if ($gdpr_request_sent != 1) { ?> 
                        <?php echo $frmRequest->getFormHtml(); ?>
                    <?php } else { ?>
                        <h6><?php
                            echo Label::getLabel('LBL_Request_for_Data_Erasure_Has_Been_Sent_For_Approval!');
                        }
                        ?></h6>
                </div>
            </div>
        </div>
</section>
<div class="gap"></div>
