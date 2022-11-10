<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
defined('SYSTEM_INIT') or die('Invalid Usage.');
$agendafrm->setFormTagAttribute('class', 'web_form form_horizontal');
$agendafrm->setFormTagAttribute('onsubmit', 'agendaSetupTestimonial(this); return(false);');
$agendafrm->developerTags['colClassPrefix'] = 'col-md-';
$agendafrm->developerTags['fld_default_col'] = 6;
?>
<style>
    .section {
        padding: 0px !important;
    }

    .save-button input[type="submit"] {
        background: #333;
        display: -webkit-inline-box;
        display: -ms-inline-flexbox;
        display: inline-flex;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        padding: 0 var(--padding-6);
        position: relative;
        cursor: pointer;
        border: none;
        height: 2.8rem;
        line-height: 2.8rem;
        color: var(--color-white);
        font-weight: 500;
        -webkit-transition: .3s all ease-in-out;
        -o-transition: .3s all ease-in-out;
        transition: .3s all ease-in-out;
        white-space: nowrap;
        border: 1px solid transparent;
    }

    .tabs_nav_container.flat {
        box-shadow: none !important;
    }

    .form_horizontal .field-set .field-wraper {
        display: contents;
    }

    .row.save-button .field-set {
        margin-top: 15px;
        text-align: end;
    }

    .wp-block-latest-comments .avatar,
    .wp-block-latest-comments__comment-avatar {
        border-radius: 1.5em;
        display: block;
        float: left;
        height: 2.5em;
        margin-right: 0.80em !important;
        width: 2.5em;
    }

    .save-button input[type="submit"]:hover {
        background-color: #006313;
    }
</style>

<div>
    <?php
    foreach ($ReportIssueCategoriesList as $value) {
    ?>
        <div>
            <h3>Report from client :</h3>
            <img alt="" src="http://1.gravatar.com/avatar/7638f76a5d508b888a846e8f907da410?s=48&d=mm&r=g" class="avatar avatar-48 photo wp-block-latest-comments__comment-avatar" height="48" width="48" loading="lazy">
            <?php echo $value['events_report_comments_information']; ?>
        </div>
        <hr>
        <?php if ($value['events_report_comments_admin_information'] != '') {
        ?>
            <div class="row">
                <div class="col-md-1">
                </div>
                <div class="col-md-11">
                    <img alt="" src="http://1.gravatar.com/avatar/7638f76a5d508b888a846e8f907da410?s=48&d=mm&r=g" class="avatar avatar-48 photo wp-block-latest-comments__comment-avatar" height="48" width="48" loading="lazy">
                    <h3>Reply from Admin :</h3>
                    <?php echo $value['events_report_comments_admin_information']; ?>
                </div>
            </div>

        <?php } ?>
        <div class="row">
            <div class="col-sm-12">
                <div class="tabs_nav_container responsive flat">
                    <div class="tabs_panel_wrap">
                        <div class="tabs_panel">
                            <?php echo $agendafrm->getFormTag() ?>
                            <div class="col-md-12">
                                <div class="field-wraper">
                                    <div class="field_cover">
                                        <?php echo $agendafrm->getFieldHTML('events_report_comments_admin_information'); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row save-button">
                                <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php //echo $agendafrm->getFieldHTML('events_report_comments_id'); 
                                                ?>

                                                <?php if ($value['events_report_comments_status'] == '0') { ?>
                                                    <input type="button" onclick="ChangeStatus(<?php echo $value['events_report_comments_id']; ?>)" id="change_status" name="events_report_comments_id" value="Comment Already Added">
                                                <?php } ?>

                                                <input type="hidden" id="events_report_comments_id" name="events_report_comments_id" value="<?php echo $value['events_report_comments_id']; ?>">
                                                <input type="hidden" id="user_id" name="user_id" value="<?php echo $value['user_id']; ?>">
                                                <?php echo $agendafrm->getFieldHTML('btn_submit'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr>
    <?php } ?>
</div>