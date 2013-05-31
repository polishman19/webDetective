<?php

class Application_Model_FBApi {
    /*
     * Required parameters
     */

    private $email = 'xxx';
    private $pass = 'xxx';
    /*
     * Optional parameters
     */
    private $uagent = 'Mozilla/4.0 (compatible; MSIE 5.0; S60/3.0 NokiaN73-1/2.0(2.0617.0.0.7) Profile/MIDP-2.0 Configuration/CLDC-1.1)';
    private $cookies = 'fbcookies.txt';

    public function parse_inputs($html) {
        $dom = new DOMDocument;
        @$dom->loadxml($html);
        $inputs = $dom->getElementsByTagName('input');
        return($inputs);
    }

    /*
     * @return form action url
     */

    public function parse_action($html) {
        $dom = new DOMDocument;
        @$dom->loadxml($html);
        $form_action = $dom->getElementsByTagName('form')->item(0)->getAttribute('action');
        if (!strpos($form_action, "//")) {
            $form_action = "https://m.facebook.com$form_action";
        }
        return($form_action);
    }

    public function search($name, $offset) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookies);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookies);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->uagent);
        curl_setopt($ch, CURLOPT_URL, "https://m.facebook.com/search/?query=$name&search=people&s=$offset");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $html = curl_exec($ch);

        $dom = new Zend_Dom_Query($html);
        $res = $dom->query(".listSelector tr");
        $results = array();
        foreach ($res as $element) {
            array_push($results, $this->getBasicData($element));
        }
        return $results;
    }
    
    public function getPhoto($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookies);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookies);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->uagent);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $html = curl_exec($ch);

        $dom = new Zend_Dom_Query($html);
        $res = $dom->query(".async_like .acbk img");
        foreach ($res as $element) {
            $result = $element->getAttributeNode("src")->value;
            break;
        }
        $result = str_replace('_t.jpg', '_b.jpg', $result);
        return $result;
    
    }
    
    public function getDetails($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookies);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookies);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->uagent);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $html = curl_exec($ch);

        $dom = new Zend_Dom_Query($html);
        $res = $dom->query(".timeline");
        $results = array();

        foreach ($res as $element) {
            $results = $this->getDetailedData($element);
        }
        return $results;
    }

    private function getDetailedData($node) {
        $result = array();

        $html = $this->getHTML($node);
        $dom = new Zend_Dom_Query($html);
        $res = $dom->query(".ib a");

        foreach ($res as $element) {
            $var = $element->getAttributeNode("href");
            $result['link'] = 'http://m.facebook.com' . $var->value;
            break;
        }
        
//        $res = $dom->query(".profpic img");
//
//        foreach ($res as $element) {
//            //$var = $element->getAttributeNode("href");
//            $result['photo'] = $this->getHTML($element);
//            break;
//        }
        
        $res = $dom->query(".profileName");

        foreach ($res as $element) {
            $result['name'] = strip_tags($this->getHTML($element));
        }
        $result['basics'] = $this->getEnumerated($dom, "#basic_info tr");
        $result['family'] = $this->getFamily($dom);
        $result['schools'] = $this->getSchools($dom);
        $result['jobs'] = $this->getJobs($dom);
        $result['bio'] = $this->getBio($dom);
        $result['contact'] = $this->getEnumerated($dom, "#contact tr");
        $result['living'] = $this->getEnumerated($dom, "#living-section tr");

        return $result;
    }
    private function getBio($dom){
        $result = array();
        
        $res = $dom->query("#bio-section .acw");

        foreach ($res as $element) {
            $result['desc'] = strip_tags($this->getHTML($element));
        }
        
        return $result;
    }
    private function getEnumerated($dom, $string) {
        $result = array();
        $res = $dom->query($string);

        foreach ($res as $element) {
            $temp = $this->getHTML($element);

            $dom2 = new Zend_Dom_Query($temp);
            $res2 = $dom2->query("th");
            foreach ($res2 as $element2) {
                $temp = $this->getHTML($element2);
                //$this->debug($temp);
                $key = strip_tags($temp);
            }
            $res2 = $dom2->query("td");
            foreach ($res2 as $element2) {
                $temp = $this->getHTML($element2);
                //$this->debug($temp);
                $value = strip_tags($temp);
            }
            $result[$key] = $value;
        }

        return $result;
    }

    private function getSchools($dom) {
        $result = array();
        $res = $dom->query(".eduwork div");
        foreach ($res as $element) {
            $temp = $this->getHTML($element);
            if (strpos($temp, 'Wykszta&#322;cenie') !== FALSE) {
                $dom2 = new Zend_Dom_Query($temp);
                $res = $dom2->query(".experience");
                foreach ($res as $element) {
                    $temp = $this->getHTML($element);
                    array_push($result, $this->parseExperience($temp));
                }
                break;
            }
        }
        return $result;
    }

    private function parseExperience($html) {
        $result = array();

        $dom = new Zend_Dom_Query($html);
        $res = $dom->query(".c strong");

        foreach ($res as $element) {
            $result['name'] = strip_tags($this->getHTML($element));
            break;
        }

        $res = $dom->query(".c span");
        $result['desc'] = array();
        foreach ($res as $element) {
            array_push($result['desc'], strip_tags($this->getHTML($element)));
        }

        return $result;
    }

    private function debug($node) {
        echo "<pre>";
        print_r($node);
        echo '</pre>';
    }

    private function getJobs($dom) {
        $result = array();
        $res = $dom->query(".eduwork div");

        foreach ($res as $element) {
            $temp = $this->getHTML($element);
            if (strpos($temp, 'Praca') !== FALSE) {
                $dom2 = new Zend_Dom_Query($temp);
                $res = $dom2->query(".experience");
                foreach ($res as $element) {
                    $temp = $this->getHTML($element);
                    array_push($result, $this->parseExperience($temp));
                }
                break;
            }
        }
        return $result;
    }

    private function getValueFromTable($row) {
        $dom = new Zend_Dom_Query($row);
        $res = $dom->query("td");

        foreach ($res as $element) {
            return strip_tags($this->getHTML($element));
        }
    }

    private function getFamily($dom) {
        $result = array();
        $res = $dom->query('#family-block .acw.apm');
        foreach ($res as $element) {
            array_push($result, $this->getFamilyMember($this->getHTML($element)));
        }
        return $result;
    }

    private function getFamilyMember($html) {
        $member = array();

        $dom = new Zend_Dom_Query($html);
        $res = $dom->query("img");

        foreach ($res as $element) {
            $member['photo'] = $this->getHTML($element);
        }

        $res = $dom->query(".name");

        foreach ($res as $element) {
            $var = $element->getAttributeNode("href");
            $member['link'] = 'http://m.facebook.com' . $var->value;
            break;
        }

        $res = $dom->query(".name");

        foreach ($res as $element) {
            $member['name'] = strip_tags($this->getHTML($element));
        }

        $res = $dom->query("div span .fcg");

        foreach ($res as $element) {
            $member['relation'] = strip_tags($this->getHTML($element));
        }

        return $member;
    }

    private function getBasicData($node) {
        $result = array();

        $html = $this->getHTML($node);
        $dom = new Zend_Dom_Query($html);
        $res = $dom->query(".pic img");

        foreach ($res as $element) {
            $result['photo'] = $this->getHTML($element);
        }

        $res = $dom->query(".name a");

        foreach ($res as $element) {
            $result['name'] = $this->getHTML($element);
            $var = $element->getAttributeNode("href");
            $temp = explode('?', $var->value);
            $result['link'] = 'http://m.facebook.com' . $temp[0] . '?v=info';
        }

        $res = $dom->query(".name span");

        foreach ($res as $element) {
            $result['desc'] = $this->getHTML($element);
        }

        return $result;
    }

    private function getHTML($node) {
        header('Content-Type: text/html; charset=utf-8');

        $newdoc = new DOMDocument();
        $cloned = $node->cloneNode(TRUE);
        $newdoc->appendChild($newdoc->importNode($cloned, TRUE));
        return $newdoc->saveHTML();
    }

    public function login() {
        /*
         * Grab login page and parameters
         */
        $loginpage = $this->grab_home();
        $form_action = $this->parse_action($loginpage);
        $inputs = $this->parse_inputs($loginpage);
        $post_params = "";
        foreach ($inputs as $input) {
            switch ($input->getAttribute('name')) {
                case 'email':
                    $post_params .= 'email=' . urlencode($this->email) . '&';
                    break;
                case 'pass':
                    $post_params .= 'pass=' . urlencode($this->pass) . '&';
                    break;
                default:
                    $post_params .= $input->getAttribute('name') . '=' . urlencode($input->getAttribute('value')) . '&';
            }
        }
        //echo "[i] Using these login parameters: $post_params";
        /*
         * Login using previously collected form parameters
         */
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookies);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookies);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->uagent);
        curl_setopt($ch, CURLOPT_URL, $form_action);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_params);
        curl_exec($ch);

        curl_close($ch);
    }

    /*
     * grab and return the homepage
     */

    public function grab_home() {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookies);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookies);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->uagent);
        curl_setopt($ch, CURLOPT_URL, 'https://m.facebook.com/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $html = curl_exec($ch);

        curl_close($ch);
        return($html);
    }

    public function logout() {
        $dom = new DOMDocument;
        @$dom->loadxml(grab_home());
        $links = $dom->getElementsByTagName('a');
        foreach ($links as $link) {
            if (strpos($link->getAttribute('href'), 'logout.php')) {
                $logout = $link->getAttribute('href');
                break;
            }
        }

        $url = 'https://m.facebook.com' . $logout;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookies);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookies);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->uagent);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $loggedout = curl_exec($ch);

        curl_close($ch);
        echo "\n[i] Logged out.\n";
    }

}

