<?php

class DonationController extends MyAppController
{

    public function index()
    {
        $DonationContent = LanguageSymposium::getBlockContent(LanguageSymposium::BLOCK_DONATION_INFORMATION, $this->siteLangId);
        $this->set('DonationContent', $DonationContent);
        $this->_template->render();
    }
}
