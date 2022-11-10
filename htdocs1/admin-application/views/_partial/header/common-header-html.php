<?php if (isset($includeEditor) && $includeEditor) { ?>
    <script   src="<?php echo CONF_WEBROOT_URL; ?>innovas/scripts/innovaeditor.js"></script>
    <script src="<?php echo CONF_WEBROOT_URL; ?>innovas/scripts/common/webfont.js" ></script>	
<?php } ?>
</head>
<body class="<?php echo $bodyClass; ?>">
    <?php
    if (FatApp::getConfig('conf_auto_restore_on', FatUtility::VAR_INT, 1) && CommonHelper::demoUrl()) {
        $this->includeTemplate('restore-system/header-bar.php');
    }
    ?>
    <div class="page-container"></div>
