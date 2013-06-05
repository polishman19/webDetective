<?php

/**
 * Domyślny kontroler aplikacji.
 */
class IndexController extends Zend_Controller_Action {

    /**
     * Inicjalizacja kontrolera.
     */
    public function init() {
        /* Initialize action controller here */
    }

    /**
     * Domyślna akcja prezentująca ekran główny aplikacji.
     */
    public function indexAction() {
        // action body
    }

    /**
     * Akcja pobierająca dane o poszykiwanej osobie z wyników
     * wyszykiwarki google.com
     * 
     * @param name Imię i nazwisko szukanej osoby
     */
    public function googleAction() {
        // action body
        $this->_helper->layout->disableLayout();
        $name = $this->_getParam("name");
        $name = str_replace(' ', '+', $name);
        $model = new Application_Model_GoogleAPI();

        $this->view->Results = $model->search($name);
    }

    /**
     * Akcja pobierająca meta dane z plików
     * graficznych JPEG lub TIFF. Dane pibierane są z
     * nagłówków EXIF.
     * 
     * @param url Adres zdjęcia.
     */
    public function getPhotoInfoAction() {
        $this->_helper->layout->disableLayout();
        $url = $this->_getParam("url");
        $url = str_replace('https://', 'http://', $url);
        $localURL = "temp.jpg";
        copy($url, $localURL);
        $this->view->Results = exif_read_data($localURL);
        unlink($localURL);
    }

     /**
     * Akcja pobierająca meta dane z plików
     * graficznych JPEG lub TIFF. Dane pibierane są z
     * nagłówków EXIF.
     * 
     * @param url Adres zdjęcia.
     */
    public function photoInfoAction() {
        $url = $this->_getParam("url");
        $url = str_replace('https://', 'http://', $url);
        $localURL = "temp.jpg";
        copy($url, $localURL);
        $this->view->Results = exif_read_data($localURL);
        unlink($localURL);
        $this->view->Url = $url;
    }

}