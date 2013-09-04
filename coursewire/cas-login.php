<?php
// Full Hostname of your CAS Server
$cas_host = 'esapps.wellesley.edu';

// Context of the CAS Server
$cas_context = '/cas';

// Port of your CAS server. Normally for a https server it's 443
$cas_port = 443;

// Load the CAS lib
require_once 'CAS.php';

// Uncomment to enable debugging
phpCAS::setDebug();

// Initialize phpCAS
phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);

// For production use set the CA certificate that is the issuer of the cert
// on the CAS server and uncomment the line below
// phpCAS::setCasServerCACert($cas_server_ca_cert_path);

// THIS SETTING IS NOT RECOMMENDED FOR PRODUCTION.
// VALIDATING THE CAS SERVER IS CRUCIAL TO THE SECURITY OF THE CAS PROTOCOL!
phpCAS::setNoCasServerValidation();

if(isset($_GET['loginCAS'])) {
  // get current page url without ?loginCAS appended to the end
  $current_page = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], "?loginCAS"));
  phpCAS::forceAuthentication();

  $_SESSION['loggedIn'] = true;
  $_SESSION['user'] = phpCAS::getUser();
  // this clears the "loginCAS" from the URL
  header('Location: '.$current_page);
} else if( isset($_GET['logoutCAS'])) {
  phpCAS::logout();

  $_SESSION['loggedIn'] = false;
  $_SESSION['user'] = 'no user';
  // this clears the "logoutCAS" from the URL
  header('Location: /~coursewire/courserec/');
}

// CAS will start a session, so test before trying
$PHPSESSID = session_id();
if( empty($PHPSESSID)) {
  session_start();
  $PHPSESSID = session_id();
}

$loggedIn = isset($_SESSION['loggedIn']) ? $_SESSION['loggedIn'] : false;
$user = isset($_SESSION['user']) ? $_SESSION['user'] : 'no user';

?>