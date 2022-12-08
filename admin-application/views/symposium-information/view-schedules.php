<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<style>
    .Export-Button {
        text-align: end;
    }

    input.button.button-primary.user_export_button {
        cursor: pointer;
        background-color: #12bbe0;
        color: #FFF;
        padding: 10px 15px 10px 15px;
        border: none;
        font-size: 15px;
        border-radius: 5px;
        letter-spacing: 0.7px;
        font-family: 'Nunito', sans-serif !important;
    }
</style>
<div class='page'>
    <div class='fixed_container'>
        <div class="row">
            <div class="space">
                <div class="page__title">
                    <div class="row">
                        <div class="col--first col-lg-6">
                            <span class="page__icon"><i class="ion-android-star"></i></span>
                            <h5><?php echo Label::getLabel('LBL_UT_Symposium_Report_Listing', $adminLangId); ?> </h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                    </div>
                </div>
                <?php
                $pending_arry = array();
                $completed_arry = array();
                foreach ($new_class_Data as $value) {
                    if ($value['events_report_comments_status'] == 0) {
                        $pending = $value['events_report_comments_status'];
                        array_push($pending_arry, $pending);
                    }

                    if ($value['events_report_comments_status'] == 1) {
                        $completed = $value['events_report_comments_status'];
                        array_push($completed_arry, $completed);
                    }
                }
                ?>
                <div class="Export-Button">
                    <form action="/admin/symposium-information/export" method="get">
                        <input type="submit" class="button button-primary user_export_button" value="Export CSV">
                    </form>
                </div>
                <section class="section">
                    <div class="sectionbody">
                        <div class="tablewrap">
                            <div id="lessonListing">
                                <?php echo Label::getLabel('LBL_Processing...', $adminLangId); ?>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>