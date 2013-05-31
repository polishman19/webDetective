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


    public function googleAction()
    {
        // action body
        $this->_helper->layout->disableLayout();
        $name = $this->_getParam("name");
        $name = str_replace(' ', '+', $name);
        $model = new Application_Model_GoogleAPI();

        $this->view->Results = $model->search($name);
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

}