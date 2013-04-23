<?php

class IndexController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        // action body
    }

    public function facebookAction() {
        $name = $this->_getParam("name");
        $name = str_replace(' ', '+', $name);
        //echo $name;
        $model = new Application_Model_FBApi();
        $this->view->FB = $model->GetPeopleList($name);
    }

    public function nkAction() {
        // action body
    }

}

