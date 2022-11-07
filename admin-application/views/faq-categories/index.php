<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class='page'>
    <div class='container container-fluid'>
        <div class="row">
            <div class="col-lg-12 col-md-12 space">
                <div class="page__title">
                    <div class="row">
                        <div class="col--first col-lg-6">
                            <span class="page__icon"><i class="ion-android-star"></i></span>
                            <h5><?php echo Label::getLabel('LBL_Manage_Faq_Categories'); ?> </h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                    </div>
                </div>
                <section class="section searchform_filter">
                    <div class="sectionhead">
                        <h4> <?php echo Label::getLabel('LBL_Search...'); ?></h4>
                    </div>
                    <div class="sectionbody space togglewrap" style="display:none;">
                        <?php
                        $searchFrm->setFormTagAttribute('onsubmit', 'searchFaqCategories(this); return(false);');
                        $searchFrm->setFormTagAttribute('class', 'web_form');
                        $searchFrm->developerTags['colClassPrefix'] = 'col-md-';
                        $searchFrm->developerTags['fld_default_col'] = 6;
                        echo $searchFrm->getFormHtml();
                        ?>
                    </div>
                </section>
                <section class="section">
                    <div class="sectionhead">
                        <h4><?php echo Label::getLabel('LBL_Faq_Category_List'); ?></h4>
                        <?php
                        if ($canEdit) {
                            $ul = new HtmlElement("ul", array("class" => "actions actions--centered"));
                            $li = $ul->appendElement("li", array('class' => 'droplink'));
                            $li->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit')), '<i class="ion-android-more-horizontal icon"></i>', true);
                            $innerDiv = $li->appendElement('div', array('class' => 'dropwrap'));
                            $innerUl = $innerDiv->appendElement('ul', array('class' => 'linksvertical'));
                            $innerLi = $innerUl->appendElement('li');
                            $innerLi->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Activate'), "onclick" => "toggleBulkStatues(1)"), Label::getLabel('LBL_Activate'),  true);
                            $innerLi = $innerUl->appendElement('li');
                            $innerLi->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Deactivate'), "onclick" => "toggleBulkStatues(0)"),  Label::getLabel('LBL_Deactivate'),  true);
                            $innerLiAddCat = $innerUl->appendElement('li');
                            $innerLiAddCat->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Add_category'), "onclick" => "addFaqCatForm(0)"),  Label::getLabel('LBL_Add_category'),  true);
                            $innerLi = $innerUl->appendElement('li');
                            $innerLi->appendElement('a',  array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Delete'), "onclick" => "deleteSelected()"),  Label::getLabel('LBL_Delete'),  true);
                            echo $ul->getHtml();
                        }
                        ?>
                    </div>
                    <div class="sectionbody">
                        <div class="tablewrap">
                            <div id="listing"> <?php echo Label::getLabel('LBL_Processing', $adminLangId); ?></div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
