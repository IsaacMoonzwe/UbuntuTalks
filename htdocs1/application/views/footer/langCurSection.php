<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="col-md-6 col-lg-3">
    <div class="footer-group toggle-group">
        <div class="footer__group-title toggle-trigger-js">
            <h5 class=""><?php echo Label::getLabel('LBL_Language_&_Currency', $siteLangId); ?></h5>
        </div>
        <div class="footer__group-content toggle-target-js">
            <div class="bullet-list">
                <div class="settings-group">
                    <div class="settings toggle-group">
                        <a class="btn btn--bordered btn--block btn--dropdown settings__trigger settings__trigger-js"><?php echo $languages[$siteLangId]['language_name']; ?></a>
                        <div class="settings__target settings__target-js" style="display: none;">
                            <ul>
                                <?php foreach ($languages as $langId => $language) { ?>
                                    <li <?php echo ( $siteLangId == $langId ) ? 'class="is--active"' : ''; ?>><a onClick="setSiteDefaultLang(<?php echo $langId; ?>)" href="javascript:void(0);"><?php echo $language['language_name'] ?></a></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                    <div class="settings toggle-group">
                        <a class="btn btn--bordered btn--block btn--dropdown settings__trigger settings__trigger-js"><?php echo $currencies[$siteCurrencyId] ?></a>
                        <div class="settings__target settings__target-js" style="display: none;">
                            <ul>
                                <?php foreach ($currencies as $currencyId => $currency) { ?>
                                    <li <?php echo ($siteCurrencyId == $currencyId) ? 'class="is--active"' : ''; ?>><a href="javascript:void(0)" onClick="setSiteDefaultCurrency(<?php echo $currencyId; ?>)"><?php echo $currency; ?></a></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>