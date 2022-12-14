<div id ="wrapper">
    <!--header start here-->
    <header id="header" class="no-print">
        <div class="headerwrap">
            <div class="one_third_grid"><a href="javascript:void(0);" class="menutrigger"><span></span></a></div>
            <div class="one_third_grid logo"><a href="<?php echo CommonHelper::generateUrl('home'); ?>"><img src="<?php echo CommonHelper::generateUrl('Image', 'siteAdminLogo', array($adminLangId)); ?>" alt=""></a></div>
            <div class="one_third_grid">
                <ul class="iconmenus">
                    <li class="viewstore">
                        <a title="<?php echo Label::getLabel('LBL_View_Portal', $adminLangId); ?>" href="<?php echo CONF_WEBROOT_FRONT_URL; ?>" target="_blank"><img src="<?php echo CONF_WEBROOT_URL; ?>images/store.svg" width="20" alt=""></a>
                    </li>
                    <li class="erase">
                        <a title="<?php echo Label::getLabel('LBL_Clear_Cache', $adminLangId); ?>" href="javascript:void(0)" onclick="clearCache()"><img class="iconerase" alt="" src="<?php echo CONF_WEBROOT_URL; ?>images/header_icon_2.svg"></a>
                    </li>
                    <li class="droplink" >
                        <a href="javascript:void(0)" title="Language"><img src="<?php echo CONF_WEBROOT_URL; ?>images/icon_langs.svg" width="20" alt=""></a>
                        <div class="dropwrap">
                            <div class="head"><?php echo Label::getLabel('LBL_Select_Language', $adminLangId); ?></div>
                            <div class="body">
                                <ul class="linksvertical">
                                    <?php foreach ($languages as $langId => $language) { ?>
                                        <li  <?php echo ( $adminLangId == $langId ) ? 'class="is--active"' : ''; ?>><a href="javascript:void(0);" onClick="setSiteDefaultLang(<?php echo $langId; ?>)"><?php echo $language['language_name']; ?></a></li>
                                    <?php } ?>
                                </ul>
                            </div>    
                        </div>
                    </li>
                    <li class="logout">
                        <a href="<?php echo CommonHelper::generateUrl('profile', 'logout'); ?>" title="<?php echo Label::getLabel('LBL_Logout', $adminLangId); ?>"><img src="<?php echo CONF_WEBROOT_URL; ?>images/header_icon_3.svg" alt=""></a>
                    </li>
                </ul>
            </div>
        </div>  
        <div class="searchwrap">
            <div class="searchform"><input type="text"></div><a href="javascript:void(0)" class="searchclose searchtoggle"></a>
        </div>
    </header>    
    <!--header end here-->
    <!--body start here-->
    <div id="body">
        <?php $this->includeTemplate('_partial/header/left-navigation.php') ?>
       