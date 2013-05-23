<?php

class Application_Model_NKApi {

    function createPostString($aPostFields) {
        foreach ($aPostFields as $key => $value) {
            $aPostFields[$key] = urlencode($key) . '=' . urlencode($value);
        }
        return implode('&', $aPostFields);
    }

    private function init() {
        # Config
        $login = 'j_rejwert67';
        $pass = 'testDeveloper';
# End Config
        # Other..
        $this->cookie_file = '/cookies.txt';
        $this->headers = array(
            'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; pl; rv:1.9.2.16) Gecko/20110319 Firefox/3.6.16',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: pl,en-us;q=0.7,en;q=0.3',
            //'Accept-Encoding: gzip,deflate',
            'Accept-Charset: ISO-8859-2,utf-8;q=0.7,*;q=0.7'
        );
# Script
        session_start();

# Log In
        $this->c = curl_init('https://nk.pl/login');
        curl_setopt($this->c, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($this->c, CURLOPT_COOKIEFILE, dirname(__FILE__) . $this->cookie_file);
        curl_setopt($this->c, CURLOPT_COOKIEJAR, dirname(__FILE__) . $this->cookie_file);
        curl_setopt($this->c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->c, CURLOPT_HEADER, true);
        curl_setopt($this->c, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($this->c);
        curl_close($this->c);

        $post = array(
            'form_name' => 'login_form',
            'target' => 'main',
            'login' => $login,
            'password' => $pass,
            'remember' => 1,
            'manual' => 0
        );
        $this->c = curl_init('https://nk.pl/login');
        curl_setopt($this->c, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($this->c, CURLOPT_COOKIEFILE, dirname(__FILE__) . $this->cookie_file);
        curl_setopt($this->c, CURLOPT_COOKIEJAR, dirname(__FILE__) . $this->cookie_file);
        curl_setopt($this->c, CURLOPT_POSTFIELDS, $this->createPostString($post));
        curl_setopt($this->c, CURLOPT_HEADER, true);
        curl_setopt($this->c, CURLOPT_POST, true);
        curl_setopt($this->c, CURLOPT_REFERER, 'https://nk.pl/login');
        curl_setopt($this->c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->c, CURLOPT_RETURNTRANSFER, true);
        curl_exec($this->c);
        curl_close($this->c);
    }

    public function Search($name, $page = 1) {
        header('Content-Type: text/html; charset=utf-8');

        $this->init();

        $this->c = curl_init("http://nk.pl/szukaj/profile?q=$name&referer=sch_ms&page=$page");
        curl_setopt($this->c, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($this->c, CURLOPT_COOKIEFILE, dirname(__FILE__) . $this->cookie_file);
        curl_setopt($this->c, CURLOPT_COOKIEJAR, dirname(__FILE__) . $this->cookie_file);
        curl_setopt($this->c, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($this->c);
        curl_close($this->c);

        return $this->prepareResults($result);
    }

    public function getDetails($url) {
        header('Content-Type: text/html; charset=utf-8');

        $this->init();

        $this->c = curl_init($url);
        curl_setopt($this->c, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($this->c, CURLOPT_COOKIEFILE, dirname(__FILE__) . $this->cookie_file);
        curl_setopt($this->c, CURLOPT_COOKIEJAR, dirname(__FILE__) . $this->cookie_file);
        curl_setopt($this->c, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($this->c);
        curl_close($this->c);

        return $this->prepareDetails($result);
    }

    private function prepareDetails($html) {
        $results = array();

        $dom = new Zend_Dom_Query($html);

        $query = $dom->query('.profile_info_box tr .label');
        $i = 0;

        foreach ($query as $result) {
//            echo "<pre>";
//            print_r($result);
//            echo "</pre>";
            if (!isset($results[$i])) {
                $results[$i] = array();
            }
            $result->setAttributeNode(new DOMAttr("class", ""));
            array_push($results[$i], strip_tags($this->getHTML($result)));

            $i++;
        }
        $query = $dom->query('.profile_info_box tr .content');
        $i = 0;
        foreach ($query as $result) {
            if (!isset($results[$i])) {
                $results[$i] = array();
            }
            array_push($results[$i], strip_tags($this->getHTML($result)));

            $i++;
        }

        $query = $dom->query('.schools.user_schools .school_name .school');
        $j = 0;
        foreach ($query as $result) {
            $var = $result->getAttributeNode("href");
            $result->setAttributeNode(new DOMAttr("href", "http://nk.pl" . $var->value));
            if (!isset($schools[$j])) {
                $schools[$j] = array();
            }
            array_push($schools[$j], $this->getHTML($result));
            $j++;
        }

        $query = $dom->query('.schools.user_schools .classes.user_school_classes .user_class');
        $j = 0;
        foreach ($query as $result) {
            $var = $result->getAttributeNode("href");
            $result->setAttributeNode(new DOMAttr("href", "http://nk.pl" . $var->value));
            if (!isset($schools[$j])) {
                $schools[$j] = array();
            }
            array_push($schools[$j], $this->getHTML($result));
            $j++;
        }

        $query = $dom->query('.gora .profil_avatar .avatar_new_photo.avatar_single img');
        foreach ($query as $result) {
//            $var = $result->getAttributeNode("href");
//            $result->setAttributeNode(new DOMAttr("href", "http://nk.pl" . $var->value));
            $photo = $this->getHTML($result);
        }
        $assoc = array("data" => $results, "schools" => $schools, "photo" => $photo);
        return $assoc;
    }

    private function prepareResults($html) {
        $results = array();

        $dom = new Zend_Dom_Query($html);
        $query = $dom->query('.avatar.mentionable_user .avatar_inner img');
        $i = 0;
        foreach ($query as $result) {
            if (!isset($results[$i])) {
                $results[$i] = array();
            }
            array_push($results[$i], $this->getHTML($result));

            $i++;
        }
        $query = $dom->query('.avatar.mentionable_user .avatar_info .avatar_user_name');
        $i = 0;
        foreach ($query as $result) {
            array_push($results[$i], $this->getHTML($result));
            $var = $result->getAttributeNode("href");
            array_push($results[$i], $var->value);
            $i++;
        }
        $query = $dom->query('.avatar.mentionable_user .avatar_info .avatar_user_city');
        $i = 0;
        foreach ($query as $result) {
            array_push($results[$i], $this->getHTML($result));

            $i++;
        }

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

