<?php

/**
 * Kontroler operacji i akcji na danych pozyskanych z portalu nk.pl.
 */
class NaszaklasaController extends Zend_Controller_Action {

    /**
     * Inicjalizacja kontrolera.
     */
    public function init() {
        /* Initialize action controller here */
    }

    /**
     * Domyślna akcja kontrolera. Jej wynikiem jest listing osób
     * o wyszukiwanym imieniu i nazwisku wraz z podstawowymi danymi
     * (zdjęcie, miasto).
     * 
     * @param string name Imię i nazwisko wyszukiwanej osoby.
     * @param int page Numer strony wyświetlanego listingu.
     */
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

    /**
     * Akcja pobierająca dokładne dane o osobie i wyświetlająca
     * je w odopowiednim widoku.
     * 
     * @param link URL do strony użytkownika nk.pl.
     */
    public function detailsAction() {
        $link = $this->_getParam("link");

        $model = new Application_Model_NKApi();
        $this->view->Results = $model->getDetails("http://nk.pl" . $link);
        $this->view->WebSite = "http://nk.pl" . $link;
    }

    /**
     * Akcja pobierająca zdjęcie użytkownika i przekazująca je
     * do odpowiedniego widoku.
     * 
     * @param link URL do strony użytkownika nk.pl
     */
    public function getPhotoAction() {
        $this->_helper->layout->disableLayout();
        $link = $this->_getParam("link");
        $model = new Application_Model_NKApi();

        $this->view->Results = $model->getImage($link);
    }

}

