<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class='page'>
    <div class='fixed_container'>
        <div class="row">
            <div class="space">
                <div class="page__title">
                    <div class="row">
                        <div class="col--first col-lg-6">
                            <span class="page__icon"><i class="ion-android-star"></i></span>
                            <h5><?php echo Label::getLabel('LBL_Manage_Preferences', $adminLangId); ?> </h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12">
                <section class="section searchform_filter">
                    <div class="sectionhead">
                        <h4> <?php echo Label::getLabel('LBL_Search...', $adminLangId); ?></h4>
                    </div>
                    <div class="sectionbody space togglewrap" style="display:none;">
                        <?php
                        $frmSearch->setFormTagAttribute('onsubmit', 'searchPreferences(this); return(false);');
                        $frmSearch->setFormTagAttribute('class', 'web_form');
                        $frmSearch->developerTags['colClassPrefix'] = 'col-md-';
                        $frmSearch->developerTags['fld_default_col'] = 6;
                        $btn_clear = $frmSearch->getField('btn_clear');
                        $btn_clear->addFieldTagAttribute('onclick', 'clearSearch()');
                        echo $frmSearch->getFormHtml();
                        ?>
                    </div>
                </section>
                <section class="section">
                    <div class="sectionhead">
                        <h4><?php echo Preference::getPreferenceTypeArr($adminLangId)[$type]; ?></h4>
                        <div class="label--note text-left">
                            <strong class="-color-secondary">
                                <?php echo Label::getLabel('LBL_PREFERENCES_UPDATE_NOTICE'); ?>
                            </strong>
                        </div>
                        <?php
                        if ($canEdit) {
                            $ul = new HtmlElement("ul", array("class" => "actions actions--centered"));
                            $li = $ul->appendElement("li", array('class' => 'droplink'));
                            $li->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit', $adminLangId)), '<i class="ion-android-more-horizontal icon"></i>', true);
                            $innerDiv = $li->appendElement('div', array('class' => 'dropwrap'));
                            $innerUl = $innerDiv->appendElement('ul', array('class' => 'linksvertical'));
                            $innerLiAddCat = $innerUl->appendElement('li');
                            $innerLiAddCat->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Add_Preferences', $adminLangId), "onclick" => "addPreferenceForm(0," . $type . ")"), Label::getLabel('LBL_Add_Preferences', $adminLangId), true);
                            echo $ul->getHtml();
                        }
                        ?>
                    </div>
                    <div class="sectionbody">
                        <div class="tablewrap">
                            <div id="listing"> <?php echo Label::getLabel('LBL_Processing...', $adminLangId); ?></div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>