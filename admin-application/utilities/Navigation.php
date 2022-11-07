<?php

class Navigation
{

    public static function setLeftNavigationVals($template)
    {
        $db = FatApp::getDb();
        $langId = CommonHelper::getLangId();
        $template->set('adminLangId', CommonHelper::getLangId());
        $template->set('objPrivilege', AdminPrivilege::getInstance());
        $template->set('adminName', AdminAuthentication::getLoggedAdminAttribute("admin_name"));
    }

}
