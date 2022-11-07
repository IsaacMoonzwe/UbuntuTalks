<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class='page'>
    <div class='fixed_container'>
        <div class="row">
            <div class="space">
                <div class="page__title">
                    <div class="row">
                        <div class="col--first col-lg-6">
                            <span class="page__icon"><i class="ion-android-star"></i></span>
                            <h5><?php echo Label::getLabel('LBL_Manage_Url_Rewriting', $adminLangId); ?> </h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                    </div>
                </div>
                <section class="section searchform_filter">
                    <div class="sectionhead">
                        <h4><?php echo Label::getLabel('LBL_Search...', $adminLangId); ?></h4>
                    </div>
                    <div class="sectionbody space togglewrap" style="display:none;">
                        <?php
                        $srchFrm->setFormTagAttribute('onsubmit', 'searchUrls(this); return(false);');
                        $srchFrm->setFormTagAttribute('class', 'web_form');
                        $srchFrm->developerTags['colClassPrefix'] = 'col-md-';
                        $srchFrm->developerTags['fld_default_col'] = 6;
                        $submitBtn = $srchFrm->getField('btn_submit');
                        $cancelBtn = $srchFrm->getField('btn_clear');
                        $submitBtn->attachField($cancelBtn);
                        $cancelBtn->addFieldtagAttribute('onclick', 'clearSearch();');
                        echo $srchFrm->getFormHtml();
                        ?>
                    </div>
                </section>
                <section class="section">
                    <div class="sectionhead">
                        <h4><?php echo Label::getLabel('LBL_Url_List', $adminLangId); ?> </h4>
                        <?php
                        if ($canEdit) {
                            $ul = new HtmlElement("ul", array("class" => "actions actions--centered"));
                            $li = $ul->appendElement("li", array('class' => 'droplink'));
                            $li->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit', $adminLangId)), '<i class="ion-android-more-horizontal icon"></i>', true);
                            $innerDiv = $li->appendElement('div', array('class' => 'dropwrap'));
                            $innerUl = $innerDiv->appendElement('ul', array('class' => 'linksvertical'));
                            $innerLiAddNew = $innerUl->appendElement('li');
                            $url = CommonHelper::generateUrl('FaqCategories');
                            $innerLiAddNew->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Add_New', $adminLangId), "onclick" => "urlForm(0)"), Label::getLabel('LBL_Add_New', $adminLangId), true);
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