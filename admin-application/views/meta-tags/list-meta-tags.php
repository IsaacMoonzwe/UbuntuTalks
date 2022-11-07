<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="tabs_panel meta-tag-tbl">
    <div class="row">
        <div class="col-sm-12">
            <?php if (!empty($frmSearch)) { ?>
                <?php if ($showFilters) { ?>
                    <section class="section searchform_filter">
                        <div class="sectionhead">
                            <h4> <?php echo Label::getLabel('LBL_Search...', $adminLangId); ?></h4>
                        </div>
                        <div class="sectionbody space togglewrap" style="display:none;">
                            <?php
                            $frmSearch->addFormTagAttribute('class', 'web_form');
                            $frmSearch->addFormTagAttribute('onsubmit', 'searchMetaTag(this);return false;');
                            $frmSearch->setFormTagAttribute('id', 'frmSearch');
                            $frmSearch->developerTags['colClassPrefix'] = 'col-md-';
                            $frmSearch->developerTags['fld_default_col'] = 6;
                            ($frmSearch->getField('keyword')) ? $frmSearch->getField('keyword')->addFieldtagAttribute('class', 'search-input') : NUll;
                            ($frmSearch->getField('hasTagsAssociated')) ? $frmSearch->getField('hasTagsAssociated')->addFieldtagAttribute('class', 'search-input') : NUll;
                            $submitBtn = $frmSearch->getField('btn_submit');
                            $clearbtn = $frmSearch->getField('btn_clear');
                            $submitBtn->attachField($clearbtn);
                            $clearbtn->addFieldtagAttribute('onclick', 'clearSearch();');
                            echo $frmSearch->getFormHtml();
                            ?>
                        </div>
                    </section>
                    <?php
                } else {
                    echo $frmSearch->getFormHtml();
                }
                ?>
            <?php } ?>
        </div>
        <div class="col-sm-12">
            <section class="section">
                <div class="sectionhead">
                    <h4><?php echo Label::getLabel('LBL_Meta_Tags_Listing', $adminLangId); ?></h4>
                    <?php
                    if (isset($canAdd) && $canAdd == true) {
                        $ul = new HtmlElement("ul", array("class" => "actions actions--centered"));
                        $li = $ul->appendElement("li", array('class' => 'droplink'));
                        $li->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit', $adminLangId)), '<i class="ion-android-more-horizontal icon"></i>', true);
                        $innerDiv = $li->appendElement('div', array('class' => 'dropwrap'));
                        $innerUl = $innerDiv->appendElement('ul', array('class' => 'linksvertical'));
                        $innerLiAddCat = $innerUl->appendElement('li');
                        $innerLiAddCat->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Add_Meta_Tag', $adminLangId), "onclick" => "addMetaTagForm(0,'" . $metaType . "',0)"), Label::getLabel('LBL_Add_Meta_Tag', $adminLangId), true);
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