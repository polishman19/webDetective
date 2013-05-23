<?php

class Application_Model_KrsApi {

    public function getCaptcha() {
        $this->headers = array(
            'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; pl; rv:1.9.2.16) Gecko/20110319 Firefox/3.6.16',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: pl,en-us;q=0.7,en;q=0.3',
            //'Accept-Encoding: gzip,deflate',
            'Accept-Charset: ISO-8859-2,utf-8;q=0.7,*;q=0.7'
        );
        $this->cookie_file = '/cookies-krs.txt';

# Log In
        $this->c = curl_init("https://ems.ms.gov.pl/krs/wyszukiwaniepodmiotu?t:lb=t");
        curl_setopt($this->c, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($this->c, CURLOPT_COOKIEFILE, dirname(__FILE__) . $this->cookie_file);
        curl_setopt($this->c, CURLOPT_COOKIEJAR, dirname(__FILE__) . $this->cookie_file);
        curl_setopt($this->c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->c, CURLOPT_HEADER, true);
        curl_setopt($this->c, CURLOPT_SSL_VERIFYPEER, false);
        $html = curl_exec($this->c);
        curl_close($this->c);

        $dom = new Zend_Dom_Query($html);

        $res = $dom->query(".captcha_img img");
        foreach ($res as $element) {
            $var = $element->getAttributeNode("src");
            $element->setAttributeNode(new DOMAttr("src", "https://ems.ms.gov.pl" . $var->value));
            $newdoc = new DOMDocument();
            $cloned = $element->cloneNode(TRUE);
            $newdoc->appendChild($newdoc->importNode($cloned, TRUE));
            $image = $newdoc->saveHTML();
        }

        $res = $dom->query("#form div input");
        foreach ($res as $element) {
            $newdoc = new DOMDocument();
            $cloned = $element->cloneNode(TRUE);
            $newdoc->appendChild($newdoc->importNode($cloned, TRUE));
            $formHidden = $newdoc->saveHTML();
            break;
        }

        return array("image" => $image, "formHidden" => $formHidden);
    }

    public function getDetailed($name, $captcha, $hidden) {
        $this->headers = array(
            'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; pl; rv:1.9.2.16) Gecko/20110319 Firefox/3.6.16',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: pl,en-us;q=0.7,en;q=0.3',
            //'Accept-Encoding: gzip,deflate',
            'Accept-Charset: ISO-8859-2,utf-8;q=0.7,*;q=0.7'
        );
        $this->cookie_file = '/cookies-krs.txt';
        
        $post = array(
            'dataComponentOppDoDnia' => '',
            'dataComponentOppDoDnia' => '',
            'gmina' => '',
            'kaptchafield' => $captcha,
            'krs' => '',
            'miejscowosc' => '',
            'nazwa' => $name,
            'nip' => '',
            'powiat' => '',
            'regon' => '',
            'rejestrPrzedsiebiorcy' => 'on',
            'rejestrStowarzyszenia' => 'on',
            'szukaj' => 'Szukaj',
            't:formdata' => $hidden,
            't:submit' => 'szukaj',
            'wojewodztwo' => ''
        );
        $this->c = curl_init('https://ems.ms.gov.pl/krs/wyszukiwaniepodmiotu?t:lb=t');
        curl_setopt($this->c, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($this->c, CURLOPT_COOKIEFILE, dirname(__FILE__) . $this->cookie_file);
        curl_setopt($this->c, CURLOPT_COOKIEJAR, dirname(__FILE__) . $this->cookie_file);
        curl_setopt($this->c, CURLOPT_POSTFIELDS, $this->createPostString($post));
        curl_setopt($this->c, CURLOPT_HEADER, true);
        curl_setopt($this->c, CURLOPT_POST, true);
        curl_setopt($this->c, CURLOPT_REFERER, 'https://ems.ms.gov.pl/krs/wyszukiwaniepodmiotu?t:lb=t');
        curl_setopt($this->c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->c, CURLOPT_RETURNTRANSFER, true);
        $html = curl_exec($this->c);
        curl_close($this->c);
        
        $results = array();

        $dom = new Zend_Dom_Query($html);
        $query = $dom->query('#podmiotyGrid');
        $i = 0;
        foreach ($query as $result) {
            if (!isset($results[$i])) {
                $results[$i] = array();
            }
            array_push($results[$i], $this->getHTML($result));

            $i++;
        }
        echo "<pre>";
        print_r($results);
        echo "</pre>";
        
        return $results;
    }

    function createPostString($aPostFields) {
        foreach ($aPostFields as $key => $value) {
            $aPostFields[$key] = urlencode($key) . '=' . urlencode($value);
        }
        return implode('&', $aPostFields);
    }

}

