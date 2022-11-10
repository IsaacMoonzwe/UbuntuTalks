<?php

class TeachController extends MyAppController
{

    public function __construct($action)
    {
        parent::__construct($action);
    }

    public function index()
    {
        FatApp::redirectUser(FatUtility::generateUrl('Teach', 'apply'));
    }

    public function apply()
    {
        $this->_template->render();
    }

}
