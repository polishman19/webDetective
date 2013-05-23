<?php

class Application_Model_GoogleAPI {

    public function search($name) {
        $this->headers = array(
            'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; pl; rv:1.9.2.16) Gecko/20110319 Firefox/3.6.16',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: pl,en-us;q=0.7,en;q=0.3',
            //'Accept-Encoding: gzip,deflate',
            'Accept-Charset: ISO-8859-2,utf-8;q=0.7,*;q=0.7'
        );

# Log In
        $this->c = curl_init('https://www.google.pl/search?q=' . $name);
        curl_setopt($this->c, CURLOPT_HTTPHEADER, $this->headers);
//        curl_setopt($this->c, CURLOPT_COOKIEFILE, dirname(__FILE__) . $this->cookie_file);
//        curl_setopt($this->c, CURLOPT_COOKIEJAR, dirname(__FILE__) . $this->cookie_file);
        curl_setopt($this->c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->c, CURLOPT_HEADER, true);
        curl_setopt($this->c, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($this->c);
        curl_close($this->c);

        return $this->prepareResults($result);
    }

    private function prepareResults($html) {
        $results = array();

        $dom = new Zend_Dom_Query($html);
        $query = $dom->query('#ires .g .r a');
        $i = 0;
        foreach ($query as $result) {
            if (!isset($results[$i])) {
                $results[$i] = array();
            }
                        $var = $result->getAttributeNode("href");
            $result->setAttributeNode(new DOMAttr("href", "http://www.google.pl" . $var->value));

            array_push($results[$i], $this->getHTML($result));

            $i++;
        }
        $query = $dom->query('#ires .g .s .st');
        $i = 0;
        foreach ($query as $result) {
            if (!isset($results[$i])) {
                $results[$i] = array();
            }
            array_push($results[$i], $this->getHTML($result));

            $i++;
        }
//        echo "<pre>";
//        print_r($results);
//        echo "</pre>";
        
        return $results;
    }
    private function getHTML($node) {
        header('Content-Type: text/html; charset=utf-8');

        $newdoc = new DOMDocument();
        $cloned = $node->cloneNode(TRUE);
        $newdoc->appendChild($newdoc->importNode($cloned, TRUE));
        return $newdoc->saveHTML();
    }
}

