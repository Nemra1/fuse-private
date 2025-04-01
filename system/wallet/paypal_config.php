<?php
if($data['allow_paypal'] == 1){
    if($data['paypal_mode'] == 'sandbox'){
        define('PAYPAL_CLIENT_ID', $data['paypalTestingClientKey']);
        define('PAYPAL_CLIENT_SECRET', $data['paypalTestingSecretKey']);
    }else{
        define('PAYPAL_CLIENT_ID', $data['paypalLiveClientKey']);
        define('PAYPAL_CLIENT_SECRET', $data['paypalLiveSecretKey']);
    }
    define('PAYPAL_MODE', $data['paypal_mode']); // Change to 'live' for production   
}
$apiContext = new \PayPal\Rest\ApiContext(
    new \PayPal\Auth\OAuthTokenCredential(
        PAYPAL_CLIENT_ID,
        PAYPAL_CLIENT_SECRET
    )
);

// Set the mode to 'sandbox' for testing and 'live' for production
$apiContext->setConfig(array(
    'mode' => PAYPAL_MODE, // Change to 'live' for production
));
$db_host = BOOM_DHOST; // e.g., 'localhost'
$db_name = BOOM_DNAME; // Your database name
$db_user = BOOM_DUSER; // Your database username
$db_pass = BOOM_DPASS; // Your database password
?>
