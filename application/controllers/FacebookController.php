<?php

/**
 * Kontroler operacji i akcji na danych pozyskanych z portalu facebook.com.
 */
class FacebookController extends Zend_Controller_Action {

    /**
     * Inicjalizacja kontrolera.
     */
    public function init() {
        /* Initialize action controller here */
    }

    /**
     * Domyślna akcja kontrolera. Wynikiem jej wywołania jest strona
     * z listingiem osób o wyszukiwanym imieniu i nazwisku i ich podstawowymmi
     * danymi osobowymi. Służy do wyboru konkretnej osoby.
     * 
     * @param name Wyszukiwane imię i nazwisko.
     * @param page Numer strony wyświetlanego listingu.
     */
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

    /**
     * Akcja pobierająca dokładne dane o osobie i wyświetlająca
     * je w odopowiednim widoku.
     * 
     * @param url URL do strony użytkownika facebook.com.
     */
    public function fbDetailsAction() {
        $url = $this->_getParam("url");
        $model = new Application_Model_FBApi();
        $model->login();
        $this->view->Results = $model->getDetails($url);
    }

    /**
     * Akcja pobierająca zdjęcie użytkownika i przekazująca je
     * do odpowiedniego widoku.
     * 
     * @param url URL do strony użytkownika facebook.com
     */
    public function fbGetPhotoAction() {
        $this->_helper->layout->disableLayout();
        $url = $this->_getParam("url");
        $model = new Application_Model_FBApi();
        $model->login();
        $this->view->Photo = $model->getPhoto($url);
    }

}

