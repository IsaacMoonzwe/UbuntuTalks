<?php

define('CONF_DEVELOPMENT_MODE', false);
define('CONF_LIB_HALDLE_ERROR_IN_PRODUCTION', true);
define('CONF_URL_REWRITING_ENABLED', true);
define('PASSWORD_SALT', 'ewgfhgfhgfhgkuyajflfdsaf');
define('CONF_INSTALLATION_PATH', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
define('CONF_UPLOADS_PATH', CONF_INSTALLATION_PATH . 'user-uploads' . DIRECTORY_SEPARATOR);
define('CONF_FRONT_END_APPLICATION_DIR', 'application/');
define('GIFTCARD_EXPIRATION_DAYS', "+ 30 days");
define('GIFTCARD_ORDER_LIMIT', "5");
define('ENCRYPTION_SALT', "YOCOACH");

require_once(CONF_INSTALLATION_PATH . 'public/settings.php');
define('CONF_CORE_LIB_PATH', CONF_INSTALLATION_PATH . 'library/core/');
define('CONF_USE_FAT_CACHE', false);
define('CONF_DEF_CACHE_TIME', 2592000);     // in seconds (2592000 = 30 days)
define('CONF_IMG_CACHE_TIME', 14400);       // in seconds (1400 = 4 hours)
define('CONF_HOME_PAGE_CACHE_TIME', 28800); // in seconds (28800 = 8 hours)
define('ALLOW_EMAILS', false);
define('CONF_ZOOM_VERSION', '1.9.0');
define('SEARCH_MAX_COUNT', 1000);
define('ENCRYPTION_KEY', 'vt%qkpCDRWB*bq@R&#4e');
define('ENCRYPTION_IV', 'r&!qmJ#zvQaIK9VKsnZa');
