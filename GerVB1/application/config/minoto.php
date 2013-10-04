<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['minoto_api']               = 'http://api.minoto-video.com';
$config['minoto_publisher_secret']  = '';

// Set token and token_secret. The consumer key and secret may be requested
// from support@minotovideo.com. The token and token secret you will have to
// generate using the Minoto dashboard at:
//
//    http://dashboard.minoto-video.com/main/configuration/apicredentials
//
//
$config['minoto_signature_methods'] = array('HMAC-SHA1');
$config['minoto_consumer_key']      = '';
$config['minoto_consumer_secret']   = '';

// Set your publisher_id, to be found in the Dashboard at the same location
// as the token and token secret.
//
// @xiao: these values change depending on the publisher, so these values
// are to be stored and obtained via the database.

// -- Arie's account (1 level higher of FWD)
// Reseller 1703 -> Reseller 1788 -> Publisher 1816
// if root is 1703 then all should be quite clean.
$config['minoto_reseller_id']       = 1703; // Datiq Test Reseller
$config['minoto_token']             = '';
$config['minoto_token_secret']      = '';

// -- FWD Reseller
$config['minoto_publisher_id']      = 1816; // default publisher, currently "Datiq Dev Xiao", todo change to Nationale Nederlanden

/* End of file minotovideo.php */
/* Location: ./application/config/minotovideo.php */