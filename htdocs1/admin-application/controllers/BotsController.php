<?php

class BotsController extends AdminBaseController
{

    private $fileName;

    public function __construct($action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewRobotsSection($this->admin_id);
        $this->fileName = CONF_INSTALLATION_PATH . 'public/robots.txt';
    }

    public function index()
    {
        if (file_exists($this->fileName) && !is_readable($this->fileName)) {
            Message::addErrorMessage(Label::getLabel('Msg_Read_Permission_Denied', $this->adminLangId));
            CommonHelper::redirectUserReferer();
        }
        $frm = $this->getForm($this->adminLangId);
        $this->set('frm', $frm);
        $this->_template->render();
    }

    public function setup()
    {
        $this->objPrivilege->canEditRobotsSection($this->admin_id);
        $frm = $this->getForm($this->adminLangId);
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        (false === $post) && FatUtility::dieJsonError(current($frm->getValidationErrors()));
        if (file_exists($this->fileName) && !is_writable($this->fileName)) {
            FatUtility::dieJsonError(Label::getLabel('Msg_Write_Permission_Denied', $this->adminLangId));
        }
        if (!file_put_contents($this->fileName, $post['botsTxt'])) {
            FatUtility::dieJsonError(Label::getLabel('Msg_Something_went_Wrong', $this->adminLangId));
        }
        FatUtility::dieJsonSuccess($this->str_setup_successful);
    }

    private function getForm(int $langId)
    {
        $frm = new Form('frmRobots');
        $botsTxt = file_exists($this->fileName) ? file_get_contents($this->fileName) : '';
        $frm->addTextArea('', 'botsTxt', $botsTxt, ['title' => Label::getLabel('LBL_Robots_File_Txt', $langId)]);
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes', $langId));
        return $frm;
    }

}
