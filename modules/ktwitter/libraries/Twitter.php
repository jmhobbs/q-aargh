<?
//  Kohana Twitter Oauth Library V1
//      By Tom Morton [Errant]
//
//  

// require the Oauth component
require Kohana::find_file('vendor','oauth');

/**
 * 	Twitter Oauth Class
 *
 *	Provides interfaces for executing an Oath exchange and
 *	retrieving a users key/secret. Also includes some
 *	basic API examples and helper code for constructing
 *	Twitter API requests
 *
 * 	@author Tom Morton
 *
 */
class Twitter_Core {
    
    // Stores the logged in Twitter_user_Model
    public $user = Null;
    
    // Standard API URL
    private $api_url = 'https://twitter.com';
	// API definitions
    private $urls = array(
                'authorize' => '/oauth/authorize',
                'access_token' => '/oauth/access_token',
                'request_token' => '/oauth/request_token');
    /**
     *  Class Constructor
     *
     *  Sets up the Oauth consumers and other general cfg
     *
	 *	@return nothing
	 *	@access public
     */
    public function __construct($config=False)
    {
        // set up the configuration
        $this->config = $config ? arr::overwrite(Kohana::config('twitter'),$config) : Kohana::config('twitter');
        // kohana instances setup
        $this->sess = Session::instance();
        // Oauth classes
        $this->sha1_method = new OAuthSignatureMethod_HMAC_SHA1();
        // consumer key/secret should be set up in the config
        $this->consumer = new OAuthConsumer($this->config['consumer_key'], $this->config['consumer_secret']);
    }
    
    /**
     *	Obtain request tokens from Twitter
     *
     *	Queries Twitter for some tokens to use in the Oauth exchange
	 *	then stores them in a session for later 
	 *
	 *	@access public
	 *	@return Oauth Consumer object
     *
     */
    public function getRequestTokens()
    {
        // user model
        $this->user = new Twitter_user_Model;
        
        $r = $this->oAuthRequest($this->getUrl('request_token'));
        
        $token = $this->oAuthParseResponse($r);
        
        $this->user->set_keys($token['oauth_token'],$token['oauth_token_secret']);
        
        $this->user->set_session('twitter_request',False);
        
        return $this->user->consumer;
    }
    
        
    /**
     *	Retrieve the Oauth Request Keys
	 *
	 *	Gets the request keys out of the stored session and
	 *	creates an Oauth consumer with them with which to make
	 *	the Request/Access key trade
	 *
	 *	@return Oauth Consumer
	 *	@access public
     *
     */
    public function sessionRequestTokens()
    {
        // user model
        if ($this->user == Null)
        {
            $this->user = new Twitter_user_Model;
        }
        
        $token = $this->sess->get('twitter_request',False);
        
        if(is_array($token))
        {
            $this->user->set_keys($token[0],$token[1]);
            
            return $this->user->consumer;
        }
        
        return False;   
    }
    
    /**
     *	Trades Request keys for user Access keys
     *
     *	Makes an Oauth request to Twitter to trade the
	 *	request keys for the users Access keys
	 *
     *	@return void
	 *	@access public
     *
     */
    public function tradeRequestForAccess()
    {
        // make sure we have a reasonable consumer to make use of
        if($this->user == Null) { $this->sessionRequestTokens(); }
        
        $r = $this->oAuthRequest($this->getUrl('access_token'));
        
        $token = $this->oAuthParseResponse($r);
        
        $this->user->set_keys($token['oauth_token'],$token['oauth_token_secret']);
        
    }
	
	/**
	 *	Stores Access tokens
	 *
	 *	Stores the users access tokens in the database
	 *	and creates a cookie to persist the login
	 *	if set to do so in the cfg
	 *
	 *	@return boolean
	 *	@access public
	 *
	 */
    public function storeTokens()
    {
        if($this->user == Null) { return False; }
        
        $credentials = json_decode($this->OAuthRequest('https://twitter.com/account/verify_credentials.json', array(), 'GET'));
        
        if($credentials != Null)
        {
            $this->user->set_username($credentials->screen_name);
            $this->user->store_keys();
            $this->user->set_session();
			
            // store the user cookie
			if($this->config['use_cookie'] == True)
			{
				cookie::set(arr::merge($this->config['cookie'],array('value'=>$this->user->username.'.'.sha1($this->user->access_key.$this->user->secret_key))));
			}
            
            return True;
        }
        
        
        return False;
    }
    
	/**
	 *	Revoke the current Session
	 *
	 *	Delete any stored session data. Otionally delete
	 *	any stored keys for the current user
	 *
	 *	@return void
	 *	@access public
	 *
	 */
    public function revokeSession($delete_keys=False)
    {
        if($delete_keys)
        {
            // delete the keys stored in the db
            $this->user->delete_keys();
        }
        
        // remove all session data
        $this->sess->delete('twitter_oauth','twitter_request');
        
        // delete cookie
        cookie::delete($this->config['cookie']['name']);
        
        $this->user = Null;
        
    }
	
    /**
     *	Retrieve an API formatted URL
     *
     *	Constructs a url in the correct Twitter API form.
	 *
     *	@return string
     *  @access public
     *
     */
    public function getUrl($type,$token=False)
    {
        if ( array_key_exists($type,$this->urls) )
        {
            if($token)
            {
                return $this->api_url.$this->urls[$type].'?oauth_token='.$token;   
            }
            return $this->api_url.$this->urls[$type];
        }
    }
    
	/**
	 *	Construct Authorize URL
	 *
	 *	Returns a formatted API url for making initial auth request
	 *	(convenience function)
	 *
	 *	@return string
	 *
	 */
    public function getAuthorizeUrl()
    {
        return $this->getUrl('authorize',$this->user->access_key);
    }
    
    /**
     *  Check for a current login
     *
     *  Check if there is a user access key/secret for us to load
     *  From session or cookie (currently cookie is unimplemented)
     *
	 *	@return boolean
	 *	@access public
	 *
     */
    public function check_login()
    {
        // first check if we have not already run this..
        if($this->user != Null)
        {
            return True;
        }
        // first check the session
        $tokens = $this->sess->get('twitter_oauth',False);
        if(!$tokens)
        {
            // no? well check cookies for a valid auth
            $tokens = cookie::get($this->config['cookie']['name'], False, True);
            if(!$tokens)
            {
                // no login to get :(
                return False;
            }
            else
            {
                // otherwise we need to process the cookie
                if ( ($this->user = Twitter_user_Model::get_from_cookie($tokens)) == Null)
                {
                    return False;
                }
            }
            
        }
        else
        {
            // here we process the session
            if ( !is_array($tokens) && ($this->user = Twitter_user_Model::get_from_session($tokens)) == Null)
            {
                return False;
            }
        }
        
        // finally return true
        return True;
    }
    
    /**
      * Parse a URL-encoded OAuth response
      *
	  *	Takes an Oauth URL coded response and converts it into an array format
	  *
      * @return array
	  *	@access public
	  *
      */
    function oAuthParseResponse($responseString)
    {
        $r = array();
        foreach (explode('&', $responseString) as $param)
        {
            $pair = explode('=', $param, 2);
            if (count($pair) != 2) continue;
            $r[urldecode($pair[0])] = urldecode($pair[1]);
        }
        return $r;
    }
    
    /**
      * Format and sign an OAuth / API request
	  *
	  *	@return string
      */
    function oAuthRequest($url, $args = array(), $method = NULL)
    {
        if (empty($method)) $method = empty($args) ? "GET" : "POST";
        $req = OAuthRequest::from_consumer_and_token($this->consumer, ($this->user) ? $this->user->consumer : Null, $method, $url, $args);
        $req->sign_request($this->sha1_method, $this->consumer, ($this->user) ? $this->user->consumer : Null);
        switch ($method) {
            case 'GET': return $this->http($req->to_url());
            case 'POST': return $this->http($req->get_normalized_http_url(), $req->to_postdata());
        }
    }
    
    
  /**
   * Make an HTTP request
   *
   * Uses Curl to retrieve a specified URL and return the page content if successful
   *
   * @return string
   *
   */
  function http($url, $post_data = null) {/*{{{*/
    $ch = curl_init();
    if (defined("CURL_CA_BUNDLE_PATH")) curl_setopt($ch, CURLOPT_CAINFO, CURL_CA_BUNDLE_PATH);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //////////////////////////////////////////////////
    ///// Set to 1 to verify Twitter's SSL Cert //////
    //////////////////////////////////////////////////
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    if (isset($post_data)) {
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    }
    $response = curl_exec($ch);
    $this->http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $this->last_api_call = $url;
    curl_close ($ch);
    return $response;
  }/*}}}*/
   
   /* wrap some API methods up for ease of use! */
   
   /**
    *	Set Twitter Status
    *
	*	API Call: updates the status of the current Twitter user to
	*	$message contents
	*
    *	@return array
	*	@access public
    */
   public function setStatus($message)
   {
        if($this->user == Null || !$this->user->username)
        {
            return False;
        }
        return json_decode($this->OAuthRequest('https://twitter.com/statuses/update.json', array('status' => $message), 'POST'));
    }  
	
	/**
	 *	Get Replies
	 *
	 *	API Call: Retrieve the current users @replies (allows passing
	 *	Twitter API options as arguments)
	 *
	 *	@return OauthRequest Object / array
	 */
    function getReplies( $args = array(), $type = 'json')
    {
        if($type == 'json')
        {
            return json_decode($this->OAuthRequest('http://twitter.com/statuses/replies.'.$type,$args,'GET'),True);
        }
        return $this->OAuthRequest('http://twitter.com/statuses/replies.'.$type,$args,'GET');
    }

	/**
	 *	Get Status
	 *
	 *	API Call: Retrieve the current users statuses (allows passing
	 *	Twitter API options as arguments)
	 *
	 *	@return OauthRequest Object / array
	 */
    function getStatus( $args = array(), $type = 'json')
    {
        if($type == 'json')
        {
            return json_decode($this->OAuthRequest('http://twitter.com/statuses/user_timeline.'.$type,$args,'GET'),True);
        }
        return $this->OAuthRequest('http://twitter.com/statuses/user_timeline.'.$type,$args,'GET');
    }
   
    /**
	 *	Get Rate Limits
	 *
	 *	API Call: Retrieve information on the remaing API rate limits
	 *
	 *	@return array
	 */
    public function rateLimitStatus()
    {

        if($this->user == Null || !$this->user->username)
        {
            return False;
        }
        
        return json_decode($this->OAuthRequest('http://twitter.com/account/rate_limit_status.json'));
    
    }
}



