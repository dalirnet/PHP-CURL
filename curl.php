<?php 
class CURL
{
	// ----------------------
	//	property
	// ----------------------
	public $session,$out;
	public $post,$get = array();
	public $browser = array(
		'firefox' => array(
			'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0',
			'Accept:"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"',
			'Accept-Language:"en-US,en;q=0.5"',
		),
		'safari' => array(
			'User-Agent: Mozilla/5.0 (iPhone; U; CPU like Mac OS X; en) AppleWebKit/420+ (KHTML, like Gecko) Version/3.0 Mobile/1A537a Safari/419.3',
			'Accept-Language: en'
		),
	);
	
	// ----------------------
	//	__construct
	// ----------------------
	public function __construct()
	{
        $this->session = curl_init();
    }
	
	// ----------------------
	//	set_browser
	// ----------------------
	public function set_browser($browser)
	{
		curl_setopt($this->session,CURLOPT_HTTPHEADER,$this->browser[$browser]); 
	}
	
	// ----------------------
	//	set_referer
	// ----------------------
	public function set_referer($url)
	{ 
		curl_setopt($this->session,CURLOPT_REFERER,$url);
	}
	
	// ----------------------
	//	set_ssl
	// ----------------------
	public function set_ssl()
	{ 
		curl_setopt($this->session,CURLOPT_SSL_VERIFYPEER,false);
	}
	
	// ----------------------
	//	post_param
	// ----------------------
	public function post_param($key,$value)
	{
		$this->post[$key] = $value;
	}
	
	// ----------------------
	//	execute
	// ----------------------
	public function execute($url,$set_cookie = true)
	{
		curl_setopt($this->session,CURLOPT_URL,$url);
		if($set_cookie == true)
		{
			curl_setopt($this->session, CURLOPT_COOKIEJAR,BASEPATH.'cookie.txt');
		}
		else
		{
			curl_setopt($this->session, CURLOPT_COOKIEFILE,BASEPATH.'cookie.txt');
		}
		curl_setopt($this->session,CURLOPT_RETURNTRANSFER,true);
		if(!empty($this->post))
		{
			curl_setopt($this->session,CURLOPT_POST,true);
			curl_setopt($this->session,CURLOPT_POSTFIELDS,$this->post);
		}
		$this->out = curl_exec($this->session);
		if ($this->out === false)
		{	 
			echo 'cURL Error: '.curl_error($this->session);
			curl_close($this->session);
		}
		else
		{
			$http_status = curl_getinfo($this->session);
			if($http_status['http_code'] != 200)
			{
				if($http_status['http_code'] == 301 || $http_status['http_code'] == 302)
				{
					$new_url = $http_status['redirect_url'];
					$this->execute($new_url);
				}
				else
				{
					curl_close($this->session);
					exit('Http Error : '.$http_status['http_code']);
				}
			}
			else
			{
				curl_close($this->session);
			}
		}	
	}
}


