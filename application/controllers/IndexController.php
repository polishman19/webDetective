<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function facebookAction()
    {
        $name = $this->_getParam('name');
        $name = str_replace(' ', '+', $name);
        
        $page = $this->_getParam('page');
        if(!isset($page))
            $page = 0;
        
        $page *= 10;
        $model = new Application_Model_FBApi();
        $model->login();
        $this->view->FB = $model->search($name, $page);
        $page /= 10;
        $this->view->Name = $name;
        $this->view->Page = $page;
    }

    public function nkAction()
    {
        $name = $this->_getParam("name");
        $name = str_replace(' ', '+', $name);

        $model = new Application_Model_NKApi();

        $this->view->Results = $model->Search($name);
    }

    public function detailsAction()
    {
        // action body
        $link = $this->_getParam("link");

        $model = new Application_Model_NKApi();
        $this->view->Results = $model->getDetails("http://nk.pl" . $link);
        $this->view->WebSite = "http://nk.pl" . $link;
    }

    public function googleAction()
    {
        // action body
        $this->_helper->layout->disableLayout();
        $name = $this->_getParam("name");
        $name = str_replace(' ', '+', $name);
        $model = new Application_Model_GoogleAPI();

        $this->view->Results = $model->search($name);
        
//        echo "<pre>";
//        print_r($this->view->Results);
//        echo "</pre>";
    }

    public function krsAction()
    {
        // action body
        $this->_helper->layout->disableLayout();
        $name = $this->_getParam("name");
        $name = str_replace(' ', '+', $name);
        $model = new Application_Model_KrsApi();

        $this->view->Results = $model->getCaptcha();

        //print_r($this->view->Results);
    }

    public function krsDetailsAction()
    {
        // action body
        $this->_helper->layout->disableLayout();
        $name = $this->_getParam("name");
        $captcha = $this->_getParam("captcha");
        $hidden = $this->_getParam("hidden");

        $model = new Application_Model_KrsApi();

        $this->view->Results = $model->getDetailed($name, $captcha, $hidden);
    }

    public function getPhotoAction()
    {
        $this->_helper->layout->disableLayout();
        $link = $this->_getParam("link");
        $model = new Application_Model_NKApi();

        $this->view->Results = $model->getImage($link);

        //print_r($this->view->Results);
    }

    public function getPhotoInfoAction()
    {
        $this->_helper->layout->disableLayout();
        $url = $this->_getParam("url");
        $url = str_replace('https://', 'http://', $url);
        $localURL = "temp.jpg";
        copy($url, $localURL);
        $this->view->Results = exif_read_data($localURL);
        unlink($localURL);
    }

    public function photoInfoAction()
    {
        $url = $this->_getParam("url");
        $url = str_replace('https://', 'http://', $url);
        $localURL = "temp.jpg";
        copy($url, $localURL);
        $this->view->Results = exif_read_data($localURL);
        unlink($localURL);
        $this->view->Url = $url;
    }

    public function fbDetailsAction()
    {
        $url = $this->_getParam("url");
        $model = new Application_Model_FBApi();
        $model->login();
        $this->view->Results = $model->getDetails($url);
        
//        echo "<pre>";
//        print_r($this->view->Results);
//        echo "</pre>";
    }

    public function fbGetPhotoAction()
    {
        $this->_helper->layout->disableLayout();
        $url = $this->_getParam("url");
        $model = new Application_Model_FBApi();
        $model->login();
        $this->view->Photo = $model->getPhoto($url);
    }


}







