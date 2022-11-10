<?php

class SponsorController extends MyAppController
{

    public function index()
    {
        $SponsorContent = LanguageSymposium::getBlockContent(LanguageSymposium::BLOCK_SPONSORSHIP_INFORMATION, $this->siteLangId);
        $this->set('SponsorContent', $SponsorContent);
        $this->_template->render();
    }
}
