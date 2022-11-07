<?php

class InnovaController extends AdminBaseController
{

    public function assetmanager()
    {
        $calledFileArgsArr = func_get_args();
        $calledFileArgsArr = array_reverse($calledFileArgsArr);
        $fileName = $calledFileArgsArr[0];
        $ext = substr($fileName, strrpos($fileName, '.') + 1);
        $content_type = '';
        switch (strtolower($ext)) {
            case 'js':
                $content_type = 'application/x-javascript';
                break;
            case 'css':
                $content_type = 'text/css';
                break;
            case 'xml':
                $content_type = 'application/xml';
                break;
            case 'html':
            case 'htm':
                $content_type = 'text/html';
                break;
            case 'swf':
                $content_type = 'application/x-shockwave-flash';
                break;
        }
        if ($content_type != "") {
            header('Content-Type: ' . $content_type);
        }
        require_once CONF_THEME_PATH . 'assetmanager/' . implode('/', func_get_args());
    }

}
