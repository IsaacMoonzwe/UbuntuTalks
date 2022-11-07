<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class='page'>
    <div class='fixed_container'>
        <div class="row">
            <div class="space">
                <div class="page__title">
                    <div class="row">
                        <div class="col--first col-lg-6">
                            <span class="page__icon"><i class="ion-android-star"></i></span>
                            <h5><?php echo Label::getLabel('LBL_Image_Attributes', $adminLangId); ?> </h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                    </div>
                </div>
                <div class="tabs_nav_container vertical">
                    <ul class="tabs_nav">
                        <?php
                        $itr = 0;
                        foreach ($tabsArr as $imageAttributeKey => $imageAttributeVal) {
                            ?>
                            <li><a class="<?php echo ($activeTab == $imageAttributeKey) ? 'active' : '' ?>" href="javascript:void(0)" onClick="listImageAttributes(<?php echo "'$imageAttributeKey'"; ?>)"><?php echo $imageAttributeVal; ?></a></li>
                            <?php
                            $itr++;
                        }
                        ?>
                    </ul>
                    <div class="tabs_panel_wrap">
                        <div class="tabs_nav_container" id="frmBlock">
                            <?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
                            <div class="tabs_panel meta-tag-tbl">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <?php if (!empty($frmSearch)) { ?>
                                            <section class="section searchform_filter">
                                                <div class="sectionhead">
                                                    <h4> <?php echo Label::getLabel('LBL_Search...', $adminLangId); ?></h4>
                                                </div>
                                                <div class="sectionbody space togglewrap" style="display:none;">
                                                    <?php
                                                    $frmSearch->addFormTagAttribute('class', 'web_form');
                                                    $frmSearch->addFormTagAttribute('onsubmit', 'searchImageAttributes(this);return false;');
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
                                        <?php } ?>
                                    </div>
                                    <div class="col-sm-12">
                                        <section class="section">
                                            <div class="sectionhead">
                                                <h4><?php echo Label::getLabel('LBL_Image_Attributes_Listing', $adminLangId); ?></h4>
                                                <div class="label--note text-right">
                                                    <strong class="-color-secondary span-right">
                                                        <?php echo Label::getLabel('LBL_Specific_Language_Alter_Tags_Note', $adminLangId) ?>
                                                    </strong>
                                                </div>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>