<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
if ($currencies && count($currencies) > 1) {
    ?>
    <a href="javascript:void(0)"  class="nav__dropdown-trigger nav__dropdown-trigger-js"><?php echo CommonHelper::getCurrencyCode(); ?></a>
    <div class="nav__dropdown-target nav__dropdown-target-js -skin">
        <div class="closeButton -hide-desktop -show-mobile">
            <span class="closeNavigation " onClick="closeNavigation()">&times;</span>
        </div>
        <br />
        <nav class="nav nav--vertical">
            <ul>
                <?php foreach ($currencies as $currencyId => $currency) { ?>
                    <li <?php echo ( $siteCurrencyId == $currencyId ) ? 'class="is-active"' : ''; ?>><a onClick="setSiteDefaultCurrency(<?php echo $currencyId; ?>)" href="javascript:void(0)"><?php echo $currency; ?></a></li>
                <?php } ?>
            </ul>
            <br />
        </nav>
    </div>
<?php } ?>