<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
if ($languages && count($languages) > 1) {
    ?>
    <a href="javascript:void(0)"  class="nav__dropdown-trigger nav__dropdown-trigger-js"><?php echo $languages[$siteLangId]['language_name']; ?></a>
    <div class="nav__dropdown-target nav__dropdown-target-js -skin">
        <div class="closeButton -hide-desktop -show-mobile">
            <span class="closeNavigation " onClick="closeNavigation()">&times;</span>
        </div>
        <br />
        <nav class="nav nav--vertical">
            <ul>
                <?php foreach ($languages as $langId => $language) { ?>
                    <li <?php echo ( $siteLangId == $langId ) ? 'class="is-active"' : ''; ?>><a onClick="setSiteDefaultLang(<?php echo $langId; ?>)" href="javascript:void(0)"><?php echo $language['language_name']; ?></a></li>
                <?php } ?>
            </ul>
        </nav>
    </div>
<?php } ?>