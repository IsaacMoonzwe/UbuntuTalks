<?php

class ZoomController extends LoggedUserController
{

    public function meeting()
    {
        $this->_template->addJs('js/zoom_tool.js');
        $this->_template->render();
    }

    public function leave()
    {
        $this->_template->render();
    }

}
