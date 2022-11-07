<?php

defined('SYSTEM_INIT') or die('Invalid Usage.');
$html = new HtmlElement('ul');
foreach ($data as $key => $value) {
    $li = $html->appendElement('li');
    $li->appendElement('a', array('href' => 'javascript:fillSuggetion(\'' . $value['credential_username'] . '\')'), $value['user_first_name'] . ' ' . $value['user_last_name'] . ' (' . $value['credential_username'] . ')');
}
echo $html->getHtml();
