<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
//
// Usage:
//     $this->load->library('whitelist');
//     $this->whitelist->isAllowed();
//     $this->whitelist->isAllowed($ip);
//
// I used this:
//     http://www.ajagwe.com/block-or-allow-access-to-php-script-based-on-remote-ip-and-cidr-list/
// Alternatives:
//     http://pgregg.com/blog/2009/04/php-algorithms-determining-if-an-ip-is-within-a-specific-range/
//     http://snipplr.com/view/15557/cidr-class-for-ipv4/
//     (ipv6) http://stackoverflow.com/questions/9582295/refactoring-php-based-ip-filter-to-work-with-ipv6
//
class Whitelist {
	public $whitelist_domains = array(
		// 'inginsurance.datiq.net',
		// 'stage.fwd.datiq.net',
		// 'dev.fwd.datiq.net',
		// 'xiao.datiq.net',
		// 'ing-insim.dutchview.nl',
		// 'ing-internalinsim.dutchview.nl',
		//'intranet.fwd.insim.biz'
	);
	
	
	public $whitelist = array(
		/*
		'194.153.74.10/32',   // Datiq BV, Cessnalaan
		'82.136.209.246/32',  // Datiq BV, Xiao, Developer Rotterdam
		
		'203.127.7.0/24',     // Asia 1
		'203.117.180.0/24',   // Asia 2
		'202.38.157.0/24',    // Australia
		'193.178.209.0/24',   // Belgium
		'194.127.138.0/24',   // Direct Germany + Austria
		'193.26.29.0/24',     // Direct France
		'91.199.173.0/24',    // Direct Italy
		'193.41.0.0/16',      // Direct Spain
		'221.134.114.0/24',   // Vysia Bank India
		'178.251.161.0/24',   // Luxembourg
		'10.67.4.0/24',       // Luxembourg private ip!!!
		'145.221.0.0/16',     // Netherlands
		'193.193.181.0/24',   // Poland
		'193.17.195.0/24',    // Romania
		'85.158.101.0/24',    // Turkey
		'193.178.209.0/24',   // CB CWE Brussels
		'80.169.232.0/24',    // CB Switserland
		'193.178.209.0/24',   // CB Italy
		'217.127.199.0/24',   // CB Spain
		'193.178.209.0/24',   // CB France
		'10.114.0.0/16',      // CB France private ip!!!
		'193.178.209.0/24',   // CB Portugal
		'145.221.0.0/16',     // CB UK
		'24.157.48.0/24',     // CB New York
		'177.43.228.0/24',    // CB Brazil
		'189.253.139.0/24',   // CB Mexico
		'199.19.251.0/24',    // CB Argentina
		'213.215.65.0/24',    // CB Slovakia
		'193.226.203.0/24',   // CB Hungary
		'10.98.129.0/24',     // CB Bulgaria private ip!!!
		'10.98.128.0/24',     // CB Bulgaria private ip!!!
		'91.198.155.0/24'     // CB Russia
		*/
	);
	// -------------
	// IP
	// -------------
	public function setList($list)
	{
		$this->whitelist = $list;
	}
	
	public function add($item)
	{
		$this->whitelist[] = $item;
	}
	
	public function match($ip, $range)
	{
		list ($subnet, $bits) = explode('/', $range);
		$ip = ip2long($ip);
		$subnet = ip2long($subnet);
		$mask = -1 << (32 - $bits);
		$subnet &= $mask;
		return ($ip & $mask) == $subnet;
	}
	
	public function allowedIp($ip = null)
	{
		if($ip == null)
			$ip = $_SERVER['REMOTE_ADDR'];
		
		$allowed = false;
		foreach ($this->whitelist as $addr) {
			if ($this->match($ip, $addr)) {
				$allowed = true;
				break;
			}
		}
		log_message('info', '$whitelist->allowedIp('.$ip.') => ['.$allowed.']');
		return $allowed;
	}
	
	// -------------
	// Domains
	// -------------
	public function addDomain($item)
	{
		$this->whitelist_domains[] = $item;
	}
	
	public function allowedDomain($domain = null)
	{
		if($domain == null){
			if(!isset($_SERVER['HTTP_REFERER'])){
				log_message('info', '$whitelist->allowedDomain(...) => HTTP_REFERER is not set. So not allowed!');
				return false;
			}
			$http_referer = $_SERVER['HTTP_REFERER'];
			$parsed_url   = parse_url( $http_referer );
			$domain       = $parsed_url['host'];
		}
		
		$allowed = false;
		if (in_array($domain, $this->whitelist_domains)) {
			$allowed = true;
		}

		log_message('info', '$whitelist->allowedDomain('.$domain.') => ['.$allowed.']');
		return $allowed;
	}
	
	// -------------
	// Domains
	// -------------
	public function allowed($ip = null, $domain = null)
	{
		$allowedIp     = $this->allowedIp($ip);
		$allowedDomain = $this->allowedDomain($domain);
		
		return ($allowedIp || $allowedDomain); // one is enough to be allowed.
	}
}

/* End of file Whitelist.php */