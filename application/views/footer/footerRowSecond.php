<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="row footer--row">
    <?php
    $this->includeTemplate('footer/moreLinksNavigation.php', ['siteLangId' => $siteLangId]);
    $this->includeTemplate('footer/extraLinks.php', ['siteLangId' => $siteLangId]);
    $this->includeTemplate('footer/footerSignUpNavigation.php', ['siteLangId' => $siteLangId]);
    $this->includeTemplate('footer/footerNewsLetter.php', ['siteLangId' => $siteLangId]);
    ?>
</div>