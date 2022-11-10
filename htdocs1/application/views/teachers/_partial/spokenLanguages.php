<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$spokenLanguagesArr = explode(",", $spoken_language_names);
$totalSpokenLanguages = count($spokenLanguagesArr) - 1;
$spokenLanguagesProficiencyArr = explode(",", $spoken_languages_proficiency);
?>
<?php foreach ($spokenLanguagesArr as $index => $spokenLangName) { ?>
	<span class="txt-inline__tag"><?php echo $spokenLangName; ?><strong> (<?php echo $proficiencyArr[$spokenLanguagesProficiencyArr[$index]]; ?>)</strong></span><?php echo ($index < $totalSpokenLanguages) ? ',' : ''; ?>
<?php } ?>