<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_View_GDPR_Request_Detail', $adminLangId); ?></h4>
    </div>
    <div class="sectionbody">
        <table class="table table--details">
            <tbody>
                <tr>
                    <td><strong><?php echo Label::getLabel('LBL_Username', $adminLangId); ?>:</strong> <?php echo $data['user_first_name'] . ' ' . $data['user_last_name']; ?></td>
                    <td><strong><?php echo Label::getLabel('LBL_User_Request_Added', $adminLangId); ?>:</strong> <?php echo MyDate::format($data['gdprdatareq_added_on'], true, true, false); ?></td>
                    <td><strong><?php echo Label::getLabel('LBL_User_Request_Modified', $adminLangId); ?>:</strong> <?php echo MyDate::format($data['user_added_on'], true); ?></td>
                </tr>
                <tr>
                    <td colspan="3"><strong><?php echo Label::getLabel('LBL_Erasure_Request_Reason', $adminLangId); ?>:</strong>
                        <?php echo $data['gdprdatareq_reason']; ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="repeatedrow">
            <br>
            <h3><?php echo Label::getLabel('LBL_Change_Status', $adminLangId); ?></h3>
            <div class="rowbody space">
                <div class="listview">
                    <?php
                    $frm->setFormTagAttribute('class', 'web_form form_horizontal');
                    $frm->setFormTagAttribute('onsubmit', 'updateStatus(this); return false;');
                    $frm->developerTags['colClassPrefix'] = 'col-sm-';
                    $frm->developerTags['fld_default_col'] = '10';
                    echo $frm->getFormHtml();
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>