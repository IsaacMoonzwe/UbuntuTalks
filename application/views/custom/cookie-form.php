<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$cookieForm->setFormTagAttribute('id', 'cookieForm');
$cookieForm->setFormTagAttribute('class', 'form');
$cookieForm->developerTags['colClassPrefix'] = 'col-md-';
$cookieForm->developerTags['fld_default_col'] = 7;
$cookieForm->setFormTagAttribute('autocomplete', 'off');
$cookieForm->setFormTagAttribute('onsubmit', 'saveCookieSetting(this); return false;');
$necessary = $cookieForm->getField(UserCookieConsent::COOKIE_NECESSARY_FIELD);
$necessary->addFieldTagAttribute('disabled', true);
$submitButton = $cookieForm->getField('btn_submit');
// $submitButton->addFieldTagAttribute('form', 'cookieForm');
$necessaryField = $cookieForm->getField(UserCookieConsent::COOKIE_NECESSARY_FIELD);
$preferencesField = $cookieForm->getField(UserCookieConsent::COOKIE_PREFERENCES_FIELD);
$statisticsField = $cookieForm->getField(UserCookieConsent::COOKIE_STATISTICS_FIELD);
?>
<div class="content-panel__head">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <h5><?php echo Label::getLabel('LBL_COOKIE_CONSENT_HEADING'); ?></h5>
        </div>
        <div></div>
    </div>
</div>
<div class="content-panel__body cookie-consent">
    <div class="form">
        <?php echo $cookieForm->getFormTag(); ?>
        <div class="form__body padding-0">
            <nav class="tabs tabs--line padding-left-6 padding-right-6">
                <ul>
                    <li class="is-active"><a href="javascript:void(0);" class="tab-a" data-id="tab_necessary"><?php echo Label::getLabel('LBL_Necessary'); ?></a></li>
                    <li><a href="javascript:void(0);" class="tab-a" data-id="tab_preferences"><?php echo Label::getLabel('LBL_Preferences'); ?></a></li>
                    <li><a href="javascript:void(0);" class="tab-a" data-id="tab_statistics"><?php echo Label::getLabel('LBL_Statistics'); ?></a></li>
                </ul>
            </nav>
            <div class="tabs-data">
                <div class="padding-6 padding-bottom-0 tab-div" data-id="tab_necessary">
                    <div class="tabs-data__box">
                        <div class="tab-heading d-flex align-items-center justify-content-between margin-bottom-3">
                            <h6><?php echo Label::getLabel('LBL_Necessary'); ?></h6>
                            <div class="field_cover">
                                <?php echo $necessaryField->getHTML(UserCookieConsent::COOKIE_NECESSARY_FIELD); ?>
                            </div>
                        </div>
                        <p><?php echo Label::getLabel('LBL_NECESSARY_COOKIE_DESCRIPTION_TEXT'); ?></p>
                    </div>
                </div>
                <div class="padding-6 padding-bottom-0 tab-div d-none" data-id="tab_preferences">
                    <div class="tabs-data__box">
                        <div class="tab-heading d-flex align-items-center justify-content-between margin-bottom-3">
                            <h6><?php echo Label::getLabel('LBL_Preferences'); ?></h6>
                            <div class="field_cover">
                                <?php echo $preferencesField->getHTML(UserCookieConsent::COOKIE_PREFERENCES_FIELD); ?>
                            </div>
                        </div>
                        <p><?php echo Label::getLabel('LBL_PREFERENCES_COOKIE_DESCRIPTION_TEXT'); ?></p>
                    </div>
                </div>
                <div class="padding-6 padding-bottom-0 tab-div d-none" data-id="tab_statistics">
                    <div class="tabs-data__box">
                        <div class="tab-heading d-flex align-items-center justify-content-between margin-bottom-3">
                            <h6><?php echo Label::getLabel('LBL_Statistics'); ?></h6>
                            <div class="field_cover">
                                <?php echo $statisticsField->getHTML(UserCookieConsent::COOKIE_STATISTICS_FIELD); ?>
                            </div>
                        </div>
                        <p><?php echo Label::getLabel('LBL_STATISTICS_COOKIE_DESCRIPTION_TEXT'); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="form__actions">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                </div>
                <div>
                    <?php echo $cookieForm->getFieldHtml('btn_submit'); ?>
                </div>
            </div>
        </div>
        </form>
    </div>
    <?php echo $cookieForm->getExternalJS(); ?>
</div>
<!--end of container-->
<script>
    var necessaryField = '<?php echo UserCookieConsent::COOKIE_NECESSARY_FIELD ?>';
    $(document).ready(function () {
        $('.cookie-consent .field_cover label').each(function (index) {
            let inputHtml = $(this).html()
            spanClass = ($(this).find('input').attr('name') == necessaryField) ? 'disabled' : '';
            $(this).html('<span class="checkbox ' + spanClass + '">' + inputHtml + '<i class="input-helper"></i></span>');
        });
        $('.tab-a').click(function () {
            $(".tab-div").addClass('d-none');
            $(".tab-div[data-id='" + $(this).attr('data-id') + "']").removeClass("d-none");
            $(".tab-a").parent().removeClass('is-active');
            $(this).parent().addClass('is-active');
        });
    });
</script>