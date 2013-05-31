<?php

class NaszaklasaController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        $name = $this->_getParam("name");
        $name = str_replace(' ', '+', $name);

        $page = $this->_getParam('page');
        if (!isset($page))
            $page = 0;

        $model = new Application_Model_NKApi();

        $this->view->Results = $model->search($name, $page + 1);
        $this->view->Name = $name;
        $this->view->Page = $page;
    }

    public function detailsAction() {
        $link = $this->_getParam("link");

        $model = new Application_Model_NKApi();
        $this->view->Results = $model->getDetails("http://nk.pl" . $link);
        $this->view->WebSite = "http://nk.pl" . $link;
    }

    public function getPhotoAction() {
        $this->_helper->layout->disableLayout();
        $link = $this->_getParam("link");
        $model = new Application_Model_NKApi();

        $this->view->Results = $model->getImage($link);
    }

}

