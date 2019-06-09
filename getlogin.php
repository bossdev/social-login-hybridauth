<?php
session_start();
include 'vendor/autoload.php';

$provider_request = 'Facebook';
if(isset($_GET['provider'])){
    switch ($_GET['provider']) {
        case 'facebook':
            $provider_request = 'Facebook';
            break;
        case 'google':
            $provider_request = 'Google';
            break;
        default:
            $provider_request = 'Facebook';
            break;
    }
}

$config = [
    'base_url' => 'http://localhost/test/social_login/getlogin.php',
    "providers"  => [
        "Facebook" => [
            "enabled" => true,
            "keys" => [
                "id" => "xxx",
                "secret" => "xxx"
            ],
            "photo_size" => 700
        ],
        "Google" => [
            "enabled" => true,
            "keys" => [
                "id" => "xxx",
                "secret" => "xxx"
            ],
        ],
    ]
];

try {
    $hybridauth = new Hybrid_Auth($config);
    if (isset($_REQUEST['hauth_start']) || isset($_REQUEST['hauth_done'])) {
        Hybrid_Endpoint::process();
    }else{
        $adapter = $hybridauth->authenticate($provider_request);
        $user_profile = $adapter->getUserProfile();
        // print_r($user_profile);
        echo json_encode($user_profile);
    }

    
} catch(Exception $e) {
    switch( $e->getCode() ){
        case 0 : echo "Unspecified error."; break;
        case 1 : echo "Hybriauth configuration error."; break;
        case 2 : echo "Provider not properly configured."; break;
        case 3 : echo "Unknown or disabled provider."; break;
        case 4 : echo "Missing provider application credentials."; break;
        case 5 : echo "Authentification failed. "
                    . "The user has canceled the authentication or the provider refused the connection.";
                break;
        case 6 : echo "User profile request failed. Most likely the user is not connected "
                    . "to the provider and he should authenticate again.";
                $twitter->logout();
                break;
        case 7 : echo "User not connected to the provider.";
                $twitter->logout();
                break;
        case 8 : echo "Provider does not support this feature."; break;
  	}
  	// well, basically your should not display this to the end user, just give him a hint and move on..
  	echo "<br /><br /><b>Original error message:</b> " . $e->getMessage();
}