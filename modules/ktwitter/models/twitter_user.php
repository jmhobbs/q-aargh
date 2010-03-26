<?

/**
 *	Twitter User Model
 *
 *	Used to manage, load and store data about a twitter user
 *
 * @author      Tom Morton
 * @version     v1.1
 *
 */
class Twitter_user_Model extends Model {
    
	// Stores the Users Oauth access key
    public $access_key;
	// Stores the Users Oauth secret key
    public $secret_key;
    // Stores the Twitter username
    public $username;
    // Stores the Oauth consumer for this user
    public $consumer = Null;
    
	/**
	 *	Twitter User Model
	 *
	 *	Creates a new model instance
	 *
	 *	@return void
	 */
    public function __construct()
    {
        parent::__construct();
        
        $this->sess = Session::instance();
    }
    
	/**
	 *	Set Users Oauth keys
	 *
	 *	Assign $access_key and $secret_key as a users keys and generate 
	 *	new consumer from them (assigned to $this->consumer). Keys can
	 *	either be Request or Access.
	 *
	 *	@return Oauth Consumer
	 *
	 */
    public function set_keys($access_key,$secret_key)
    {
        $this->access_key = $access_key;
        $this->secret_key = $secret_key;
        $this->consumer = new OAuthConsumer($access_key,$secret_key);
		
		return $this->consumer;
    }
    
	/**
	 *	Set Users Twitter username
	 *
	 *	Associates a Twitter username with the object instance
	 *
	 *	@return void
	 *
	 */
    public function set_username($username)
    {
        $this->username = $username;
    }
    
	/**
	 *	Saves Oauth keys in a session variable
	 *
	 *	Saves the users assigned Oauth keys. During stage one
	 *	of the key transfer this will be the request keys the 
	 * 	session variable used can be changed to reflect this,.
	 *	
	 *	@return void
	 *
	 */
    public function set_session($session='twitter_oauth',$encrypt=True)
    {
        if($this->access_key && $this->secret_key)
        {
            $this->sess->set($session,$encrypt ? $this->username.'.'.sha1($this->access_key.$this->secret_key) : array($this->access_key,$this->secret_key));
        }
    }

	/**
	 *	Store Oauth Keys
	 *
	 *	Store the users Oauth access keys in the database along with
	 *	their username for later retrieval
	 *
	 *	@return void
	 *
	 */
    public function store_keys()
    {
        if($this->access_key && $this->secret_key && $this->username)
        {
            $r = $this->db->getwhere(Kohana::config('twitter.database_table'),array('user'=>$this->username));
            
            if($r->count() > 0)
            {
                $this->db->update(Kohana::config('twitter.database_table'),array('access_key'=>$this->access_key,'secret_key'=>$this->secret_key),array('user'=>$this->username));
            }
            else
            {
                $this->db->insert(Kohana::config('twitter.database_table'),array('user'=>$this->username,'access_key'=>$this->access_key,'secret_key'=>$this->secret_key));
            }
        }
        
    }
    
	/**
	 *	Delete users Oauth keys
	 *
	 *	Delete the stored keys from the database if they exist
	 *
	 *	@return void
	 *
	 */
    public function delete_keys()
    {
        $this->db->delete(Kohana::config('twitter.database_table'),array('user'=>$this->username));
    }
    
    /**
     *  Retrieve user information from the cookie
     *
     *  Takes the data from the stored cookie and figures out the user
	 *	from it, then obtains the correct keys from the DB.
     *
     *  @return Twitter_User_Model / Null
     */
    public static function get_from_cookie($cookie_string)
    {
        $cookie_array = explode('.',$cookie_string);
        
        if( ($user = Twitter_user_Model::retrieve_user_from_db($cookie_array[0],$cookie_array[1])) != Null)
        {
            // set the session up too so in future the session retrieves the data
            $user->set_session();
            return $user;
        }
        return Null;
    }
     
    /**
     *  Obtain user information from session
     *
     *  Queries the session variables for information about the
	 *	current logged in user. Queries the DB to verify info.
     *
     *  @return Twitter_user_Model / Null
     */
    public static function get_from_session($session_tokens)
    {
        $session_array = explode('.',$session_tokens);

        if( ($user = Twitter_user_Model::retrieve_user_from_db($session_array[0],$session_array[1])) != Null)
        {
            return $user;
        }
        return Null;
    }
    
    /**
	 *	Retrieve a users keys from the DB
	 *
	 *	Retrieves a users details from the DB and verifies either
	 *	encrypted cookie or session data to verify the user is valid
	 *
	 *	@return Twitter_User_Model / Null
	 *
	 */
    public static function retrieve_user_from_db($username,$tokens_hash)
    {
        $r = Database::instance()->getwhere(Kohana::config('twitter.database_table'),array('user'=>$username));

        if($r->count() == 1)
        {
            $user = $r->current();

            // hash and compare - this is a bit hacky but it shouldnt matter too much
            // TODO: make something unique to that one cookie so you cant spoof access
            if(sha1($user->access_key.$user->secret_key) == $tokens_hash)
            {
                $user_model = new Twitter_user_Model;
                $user_model->set_username($user->user);
                $user_model->set_keys($user->access_key,$user->secret_key);
                return $user_model;
            }
        }
        return Null;
    }
}