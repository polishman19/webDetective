<?php

require_once 'facebook.php';

class Application_Model_FBApi {

    function __construct() {
        $config = array();
        $config['appId'] = '442947719131448';
        $config['secret'] = 'ff0cdcca3e8e2ad58a04a85eead0f0a9';
        $config['fileUpload'] = false; // optional

        $this->fb = new Facebook($config);
    }

    protected $fb;

    public function GetPeopleList($name) {
        $user_id = $this->fb->getUser();
        if ($user_id) {

            // We have a user ID, so probably a logged in user.
            // If not, we'll get an exception, which we handle below.
            try {
                $search = $this->fb->api('/search?q=' . $name . '&type=user&limit=100');
                foreach ($search['data'] as &$value) {
//                    print_r($value);
                    $value = $this->GetDetails($value);
                }
                 print_r($search);

                return $search;
            } catch (FacebookApiException $e) {
                // If the user is logged out, you can have a 
                // user ID even though the access token is invalid.
                // In this case, we'll get an exception, so we'll
                // just ask the user to login again here.
                $login_url = $this->fb->getLoginUrl();
                return 'Please <a href="' . $login_url . '">login.</a>';
//                error_log($e->getType());
//                error_log($e->getMessage());
            }
        } else {

            // No user, so print a link for the user to login
            $login_url = $this->fb->getLoginUrl();
            return 'Please <a href="' . $login_url . '">login.</a>';
        };
    }

    private function GetDetails($input) {

        $input = $this->fb->api($input['id'] . '?fields=id,name,cover,first_name,gender,install_type,languages,last_name,link,locale,location,meeting_for,middle_name,name_format,political,quotes,relationship_status,sports,username,website,hometown,work,about,address,age_range,bio,birthday,currency,devices,education,email,favorite_athletes,favorite_teams,inspirational_people,installed,interested_in,religion,significant_other,third_party_id,timezone,updated_time,verified,video_upload_limits,picture,locations,mutualfriends,statuses');
        
        return $input;
    }

}

