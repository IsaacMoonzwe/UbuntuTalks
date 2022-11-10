<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="settings-group">
    <?php if ($currencies && count($currencies) > 1) { ?>
        <style>
            footer-dropdown{
                max-height: 200px;
            }
        </style>
        <div class="settings toggle-group">
            <a href="javascript:void(0)" class="btn  btn--bordered btn--bordered-inverse btn--block btn--dropdown toggle__trigger-js"><?php echo CommonHelper::getCurrencyCode(); ?></a>
            <div div-for="currency" class="settings__target -skin toggle__target-js scrollbar scrollbar-js footer-dropdown">
                <nav class="nav nav--vertical">
                    <ul>
                        <?php foreach ($currencies as $currencyId => $currency) { ?>
                            <li <?php echo ( $siteCurrencyId == $currencyId ) ? 'class="is-active"' : ''; ?>><a href="javascript:void(0)" onClick="setSiteDefaultCurrency(<?php echo $currencyId; ?>)"><?php echo $currency; ?></a></li>
                        <?php } ?>
                    </ul>
                </nav>
            </div>
        </div>
    <?php } ?>
    <?php if ($languages && count($languages) > 1) { ?>
        <div class="settings toggle-group">
            <a href="javascript:void(0)" class="btn  btn--bordered btn--bordered-inverse btn--block btn--dropdown toggle__trigger-js"> <?php echo $languages[$siteLangId]['language_name']; ?>  </a>
            <div div-for="language" class="settings__target toggle__target-js -skin">
                <nav class="nav nav--vertical">
                    <ul>
                        <?php foreach ($languages as $langId => $language) { ?>
                            <li <?php echo ( $siteLangId == $langId ) ? 'class="is-active"' : ''; ?>><a onClick="setSiteDefaultLang(<?php echo $langId; ?>)" href="javascript:void(0)"><span><?php echo $language['language_name']; ?></span> </a></li>
                        <?php } ?>
                    </ul>
                </nav>
            </div>
        </div>
    <?php } ?>
</div>