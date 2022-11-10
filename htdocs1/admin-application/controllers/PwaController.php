<?php

class PwaController extends AdminBaseController
{

    public function index()
    {
        $frm = $this->getForm($this->adminLangId);
        $record = Configurations::getConfigurations(['CONF_ENABLE_PWA', 'CONF_PWA_SETTINGS']);
        if (!empty($record['CONF_PWA_SETTINGS'])) {
            $data = [
                'pwa_settings' => json_decode($record['CONF_PWA_SETTINGS'], true),
                'CONF_ENABLE_PWA' => $record['CONF_ENABLE_PWA']
            ];
            $frm->fill($data);
        }
        $iconData = AttachedFile::getAttachment(AttachedFile::FILETYPE_PWA_APP_ICON, 0);
        $splashIconData = AttachedFile::getAttachment(AttachedFile::FILETYPE_PWA_SPLASH_ICON, 0);
        $this->set('frm', $frm);
        $this->set('iconData', $iconData);
        $this->set('splashIconData', $splashIconData);
        $this->_template->render(true, true, 'pwa/index.php');
    }

    public function setup()
    {
        $frm = $this->getForm($this->adminLangId);
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            Message::addErrorMessage($frm->getValidationErrors());
            return $this->index();
        }
        FatApp::getDb()->startTransaction();
        if (!empty($_FILES['icon']['name'])) {
            $attchedFile = new AttachedFile();
            if ($attchedFile->getMimeType($_FILES['icon']['tmp_name']) != 'image/png') {
                FatApp::getDb()->rollbackTransaction();
                Message::addErrorMessage(sprintf(Label::getLabel('LBL_Please_upload_%s_image_for_splash_icon'), 'PNG'));
                return $this->index();
            }
            if (!$attchedFile->saveImage($_FILES['icon']['tmp_name'], AttachedFile::FILETYPE_PWA_APP_ICON, 0, 0,
                            $_FILES['icon']['name'], 0, true)) {
                FatApp::getDb()->rollbackTransaction();
                Message::addErrorMessage($attchedFile->getError());
                return $this->index();
            }
        }
        if (!empty($_FILES['splash_icon']['name'])) {
            $attchedFile = new AttachedFile();
            if ($attchedFile->getMimeType($_FILES['splash_icon']['tmp_name']) != 'image/png') {
                FatApp::getDb()->rollbackTransaction();
                Message::addErrorMessage(sprintf(Label::getLabel('LBL_Please_upload_%s_image_for_splash_icon'), 'PNG'));
                return $this->index();
            }
            if (!$attchedFile->saveImage($_FILES['splash_icon']['tmp_name'], AttachedFile::FILETYPE_PWA_SPLASH_ICON, 0, 0,
                            $_FILES['splash_icon']['name'], 0, true)) {
                FatApp::getDb()->rollbackTransaction();
                Message::addErrorMessage($attchedFile->getError());
                return $this->index();
            }
        }
        $pwaSettings = json_encode($post['pwa_settings']);
        $configurations = new Configurations();
        if (!$configurations->update(['CONF_PWA_SETTINGS' => $pwaSettings, 'CONF_ENABLE_PWA' => $post['CONF_ENABLE_PWA']])) {
            FatApp::getDb()->rollbackTransaction();
            Message::addErrorMessage(FatApp::getDb()->getError());
            return $this->index();
        }
        FatApp::getDb()->commitTransaction();
        Message::addMessage(Label::getLabel('LBL_Success'));
        FatApp::redirectUser(CommonHelper::generateUrl('Pwa'));
    }

    private function getForm(int $langId): Form
    {
        $orientationArr = PWA::orientationArr($langId);
        $displayArr = PWA::displayArr($langId);
        $frm = new Form('pwaFrm', ['action' => CommonHelper::generateUrl('Pwa', 'setup'), 'enctype' => 'multipart/form-data']);
        $frm->addCheckBox(Label::getLabel('PWALBL_Enable_PWA'), 'CONF_ENABLE_PWA', 1, [], false, 0);
        $fld = $frm->addRequiredField(Label::getLabel('PWALBL_App_Name'), 'pwa_settings[name]');
        $fld->requirements()->setLength(1, 50);
        $fld = $frm->addRequiredField(Label::getLabel('PWALBL_App_Short_Name'), 'pwa_settings[short_name]');
        $fld->requirements()->setLength(1, 15);
        $fld->htmlAfterField = '<small>' . Label::getLabel('HTMLAFTER_PWA_APP_SHORT_NAME') . '</small>';
        $fld = $frm->addTextBox(Label::getLabel('PWALBL_Description'), 'pwa_settings[description]');
        $fld->requirements()->setLength(1, 200);
        $fld->htmlAfterField = '<small>' . Label::getLabel('HTMLAFTER_PWA_Description') . '</small>';
        $fld = $frm->addFileUpload(Label::getLabel('PWALBL_App_Icon'), 'icon', ['accept' => 'image/png']);
        $fld->htmlAfterField = '<small>' . Label::getLabel('HTMLAFTER_PWA_App_Icon') . '</small>';
        $frm->addHTML('', 'icon_img', '');
        $frm->addFileUpload(Label::getLabel('PWALBL_Splash_Icon'), 'splash_icon', ['accept' => 'image/png'])
                ->htmlAfterField = '<small>' . Label::getLabel('HTMLAFTER_PWA_Spash_Icon') . '</small>';
        $frm->addHTML('', 'splash_icon_img', '');
        $frm->addRequiredField(Label::getLabel('PWALBL_Background_Color'), 'pwa_settings[background_color]')
                ->htmlAfterField = '<small>' . Label::getLabel('HTMLAFTER_PWA_Background_color') . '</small>';
        $frm->addRequiredField(Label::getLabel('PWALBL_Theme_Color'), 'pwa_settings[theme_color]')
                ->htmlAfterField = '<small>' . Label::getLabel('HTMLAFTER_PWA_Theme_Color') . '</small>';
        $frm->addRequiredField(Label::getLabel('PWALBL_Start_Page'), 'pwa_settings[start_url]')
                ->htmlAfterField = '<small>' . Label::getLabel('HTMLAFTER_PWA_Start_Page') . '</small>';
        /* $contentPages = ContentPage::getPagesForSelectBox($langId);

          $fld = $frm->addSelectBox(Label::getLabel('PWALBL_Offline_Page', $langId), 'pwa_settings[offline_page]', $contentPages);
          $fld->requirements()->setRequired();
          $fld->htmlAfterField = '<small>'.Label::getLabel('HTMLAFTER_PWA_Offline_Page').'</small>'; */
        $fld = $frm->addSelectBox(Label::getLabel('PWALBL_Orientation'), 'pwa_settings[orientation]', $orientationArr, '', [], '');
        $fld->requirements()->setRequired();
        $fld->htmlAfterField = '<small>' . Label::getLabel('HTMLAFTER_PWA_orientation') . '</small>';
        $fld = $frm->addSelectBox(Label::getLabel('PWALBL_Display'), 'pwa_settings[display]', $displayArr, '', [], '');
        $fld->requirements()->setRequired();
        $fld->htmlAfterField = '<small>' . Label::getLabel('HTMLAFTER_PWA_Display') . '</small>';
        // $frm->addTextBox(Label::getLabel('PWALBL_Cache_Strategy'), 'cache_strategy');
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save', $langId));
        return $frm;
    }

}
