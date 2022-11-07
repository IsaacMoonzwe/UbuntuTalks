<?php

class FatController
{

    protected $_modelName;
    protected $_controllerName;
    protected $_actionName;
    protected $_template;
    protected $_autoCreateModel = true;

    function __construct($action)
    {
        $this->_controllerName = get_class($this);
        $this->_modelName = substr($this->_controllerName, 0, (strlen($this->_controllerName)) - strlen('Controller'));
        $this->_actionName = $action;
        /**
         * @todo need to check the header
         */
        $this->setAppHeaders(); 
        $this->_template = new FatTemplate($this->_controllerName, $this->_actionName);
    }

    function set($name, $value)
    {
        $this->_template->set($name, $value);
    }

    protected function setAppHeaders()
    {
        header('X-Frame-Options: SAMEORIGIN');
        header('Strict-Transport-Security: max-age=10886400');
        header('X-XSS-Protection: 1; mode=block');
        header('X-Content-Type-Options: nosniff');
        /* header('Content-Security-Policy: policy-definition' ); */
        header('Referrer-Policy: no-referrer-when-downgrade');
        header("Pragma: no-cache");
        header('Cache-Control:Private,no-store, must-revalidate, public, max-age=0');
        header_remove('X-Powered-By');
    }

}
