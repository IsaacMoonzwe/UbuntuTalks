<?php

class MaintenanceController extends MyAppController
{

    public function index()
    {
        if (FatApp::getConfig("CONF_MAINTENANCE", FatUtility::VAR_INT, 0) == 0) {
            FatApp::redirectUser(CommonHelper::generateUrl('home'));
        }
        $this->set('maintenanceText', FatApp::getConfig("CONF_MAINTENANCE_TEXT_" . $this->siteLangId, FatUtility::VAR_STRING, ''));
        $this->_template->render();
    }

}
