<?php

class FacebookController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        $name = $this->_getParam('name');
        $name = str_replace(' ', '+', $name);

        $page = $this->_getParam('page');
        if (!isset($page))
            $page = 0;

        $page *= 10;
        $model = new Application_Model_FBApi();
        $model->login();
        $this->view->FB = $model->search($name, $page);
        $page /= 10;
        $this->view->Name = $name;
        $this->view->Page = $page;
    }

    public function fbDetailsAction() {
        $url = $this->_getParam("url");
        $model = new Application_Model_FBApi();
        $model->login();
        $this->view->Results = $model->getDetails($url);
    }

    public function fbGetPhotoAction() {
        $this->_helper->layout->disableLayout();
        $url = $this->_getParam("url");
        $model = new Application_Model_FBApi();
        $model->login();
        $this->view->Photo = $model->getPhoto($url);
    }

}

