<?php

class LearnerScheduledLessonsController extends LearnerBaseController
{

    public function __construct($action)
    {
        parent::__construct($action);
    }

    public function index()
    {
        echo"here";exit;
        $this->_template->render();
    }
}
