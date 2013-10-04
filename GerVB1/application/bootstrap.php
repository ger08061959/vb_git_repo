<?php
//
// In the future, we might need to force HTTPS protocol for certain links.
// 
$protocol = 'http://';
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
    || $_SERVER['SERVER_PORT'] == 443) {
    $secure_connection = true;
	$protocol = 'https://';
}

//
// Set a few default values for websites based on host
// Base is for this the client name. For off-site test/development another
// database can be used. 
//
define('THE_SITE_THEME', 'rvs');
define('THE_VIDEOBANK_NAME', 'RVS Videobank');
define('THE_SITE_LOGO', 'assets/rvs/rvs_logo.png');

define('THE_BASE_URL', $protocol.$_SERVER['HTTP_HOST'].'/');
switch ($_SERVER['HTTP_HOST']) {
		
	case 'dev.rvs.datiq.net':
		define('ENVIRONMENT', 'development');
		define('THE_SITE_NAME', '[DEV] RaadVanState');
		break;

	case 'stage.rvs.datiq.net':
		define('ENVIRONMENT', 'stageing');
		define('THE_SITE_NAME', '[Stage] RaadVanState ');
		break;

	case 'rvs.datiq.net':
		define('ENVIRONMENT', 'production'); // production
		define('THE_SITE_NAME', 'RaadVanState');
		break;

	// This is for internal and external pubblication URLs
	default: // Datiq GENERAL
		define('ENVIRONMENT', 'development'); 

		define('DB_USERNAME', 'root');
		define('DB_PASSWORD', 'gergroen');
		define('DB_DATABASE', 'Videobank');
		
		define('THE_SITE_NAME', 'Datiq (RVS version) Videobank');
		break;
}
		
if (!defined('DB_USERNAME')) {
	define('DB_USERNAME', 'devrvsusr');
	define('DB_PASSWORD', 'devrvspwd');
	define('DB_DATABASE', 'dev_rvs');
}
?>