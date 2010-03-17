<?php defined('SYSPATH') or die('No direct script access.');
/**
 * The CURL library provides an object oriented interface
 * to the procedural CURL PHP functions, the class only
 * allows for a single CURL session per instance - as curl_init
 * is called from the constructor...
 *
 * $Id: Curl.php 16 2009-05-26 17:37:46Z samsoir $
 *
 * @package     Standard
 * @subpackage  Libraries
 * @category    cURL Abstraction
 * @author      Parnell Springmeyer <parnell@rastermedia.com>
 * @author      Sam Clark <sam@clark.name>
 * @todo        Nada
 */
class Curl_Core
{
	/**
	 * Configuration for this class
	 *
	 * @var     array
	 * @access  protected
	 */
	protected $config;

	/**
	 * Specific options for Curl
	 *
	 * @var     array
	 * @access  protected
	 */
	protected $options;

	/**
	 * Cache library for caching requests
	 *
	 * @var     Cache
	 * @access  protected
	 */
	protected $cache;

	/**
	 * Curl connectection
	 *
	 * @var     curl
	 * @access  protected
	 */
	protected $connection;
	
	/**
	 * Execution status for this instantiation
	 *
	 * @var     boolean
	 * @access  protected
	 */
	protected $executed;

	/**
	 * Cache status for this request
	 *
	 * @var     boolean
	 * @access  protected
	 */
	protected $cached_result;

	/**
	 * Headers returned from the request
	 *
	 * @var     array
	 * @access  protected
	 */
	protected $headers = array();

	/**
	 * Result of the cURL request
	 *
	 * @var     mixed
	 * @access  protected
	 */
	protected $result;

	/**
	 * Error resulting from the cURL request
	 *
	 * @var     array
	 * @access  protected
	 */
	protected $error;

	/**
	 * Information resulting from the request
	 *
	 * @var     array
	 * @access  protected
	 */
	protected $info = array();

	/**
	 * Factory pattern for chaining
	 *
	 * @param   array $config 
	 * @param   string $url 
	 * @return  Curl
	 * @access  public
	 * @static
	 */
	public static function factory(array $config = array(), $url = NULL)
	{
		return new Curl($config, $url);
	}

	/**
	 * Returns the curl version installed
	 *
	 * @param   string       key of curl version information to isolate and return [Optional]
	 * @return  array
	 * @return  mixed
	 * @throws  Kohana_User_Exception
	 * @access  public
	 * @static
	 */
	public static function version($key = NULL)
	{
		if ( ! function_exists('curl_version'))
			throw new Kohana_User_Exception(__CLASS__.'.'.__METHOD__.'()', 'Curl version information could not be ascertained. '.$e->getMessage());

		// get the curl version information
		$result = curl_version();

		// Return the specified result
		if ($key === NULL)
			return $result;
		else
			return $result[$key];
	}

	/**
	 * Constructor for the Curl class. Instantiates the Curl library
	 *
	 * @param   array        config  the configuration array
	 * @param   string       url  the url to use for this Curl request
	 * @return  void
	 * @throws  Kohana_User_Exception
	 * @access  public
	 */
	public function __construct(array $config = array(), $url = NULL)
	{
		// Merge supplied config with the default settings
		$config += Kohana::config('curl');

		// Assign config to this class
		$this->config = $config;

		if ( ! function_exists('curl_init'))
			throw new Kohana_User_Exception(__CLASS__.'.'.__METHOD__.'()', 'Curl failed to initialise. '.$e->getMessage());

		// Init curl
		$this->connection = curl_init();

		// Assign options by relation
		$this->options = & $this->config['options'];

		// If there is a URL supplied, add it to the Curl options
		if ($url !== NULL)
			$this->options[CURLOPT_URL] = $url;

		// Set this executed to FALSE;
		$this->executed = FALSE;

		// Load Cache library instance if required
		if ($this->config['cache'])
			$this->cache = Cache::instance();
	}

	/**
	 * __get() method for returning config options, curl exec information or version information
	 *
	 * @param   string $key 
	 * @return  void
	 * @author  Sam Clark
	 */
	public function __get($key)
	{
		if (in_array($key, array('executed', 'cached_result')))
			return $this->$key;

		return;
	}

	/**
	 * Quickly gets data from a URI. This only works with GET requests but
	 * can handle HTTP Basic Auth
	 *
	 * @param   string       uri  the url to pull from
	 * @param   string       username  the username for the service [Optional]
	 * @param   string       password  the password for the user [Optional]
	 * @return  string
	 * @return  void
	 * @throws  Kohana_User_Exception
	 * @access  public
	 * @static
	 **/
	static public function get($url, $username = FALSE, $password = FALSE)
	{
		if ( ! valid::url($url))
			throw new Kohana_User_Exception(__CLASS__.'.'.__METHOD__.'()', 'The URL : '.$url.' is not a valid resource');

		// Initiate a curl session based on the URL supplied
		$curl = Curl::factory(array(CURLOPT_POST => FALSE), $url);

		// If a username/password is supplied
		if ($username AND $password)
		{
			// Add the HTTP Basic Auth headers
			$curl->setopt_array(array(CURLOPT_USERPWD => $username.':'.$password));
		}

		// Run the curl request
		$curl->exec();

		// If there was an error, return null
		if ($curl->error())
			return;
		else
			return $curl->result();
	}

	/**
	 * Sets an array of options, must use curl_setopt consts
	 *
	 * @chainable
	 * @param   array        options  the array of key/value pairs
	 * @return  self
	 * @access  public
	 * @throws  Kohana_User_Exception
	 */
	public function setopt_array(array $options)
	{
		if ( ! $this->executed)
		{
			$this->options = $options + $this->options;
			return $this;
		}
		else
			throw new Kohana_User_Exception(__CLASS__.'.'.__METHOD__.'()', 'Cannot set Curl options after it has already executed');
	}

	/**
	 * Set a single option, must use curl_setopt consts
	 *
	 * @chainable
	 * @param   const          option  the option to set
	 * @param   string         value  the value to set to the option
	 * @return  self
	 * @access  public
	 */
	public function setopt($option, $value)
	{
		if ( ! $this->executed)
		{
			$this->options[$option] = $value;
			return $this;
		}
		else
			throw new Kohana_User_Exception(__CLASS__.'.'.__METHOD__.'()', 'Cannot set Curl options after it has already executed');
	}

	/**
	 * Alias for self::setopt(). Retained for backwards compatibility
	 * 
	 * @chainable
	 * @return  self
	 * @depreciated
	 */
	public function addOption($option, $value)
	{
		return $this->setopt($option, $value);
	}

	/**
	 * Execute the CURL request based using class setup
	 *
	 * @chainable
	 * @param   array        ignore_errors array of curl error numbers to ignore
	 * @return  self
	 * @access  public
	 * @throws  Kohana_User_Exception
	 */
	public function exec($ignore_errors = array())
	{
		// If this has already executed, return itself
		if ($this->executed)
			return $this;

		// Set the header to be processed by the parser and turn off header
		curl_setopt($this->connection, CURLOPT_HEADERFUNCTION, array($this, 'parse_header'));
		curl_setopt($this->connection, CURLOPT_HEADER, FALSE);

		//Some servers (like Lighttpd) will not process the curl request without this header and will return error code 417 instead.
		//Apache does not need it, but it is safe to use it there as well.
		curl_setopt($this->connection, CURLOPT_HTTPHEADER, array("Expect:"));

		// If the cache configuration is set, try and load the cache
		if ($this->config['cache'])
			$cached = $this->load_cache();

		// If this has not executed, prepare for execution
		if ( ! $this->executed)
		{
			// Throw an exception if the user defined options cannot be applied
			if ( ! curl_setopt_array($this->connection, $this->options))
				throw new Kohana_User_Exception(__CLASS__.'.'.__METHOD__.'()', 'There was a problem setting the curl_setopt_array');

			// Execute the Curl request
			$this->result = curl_exec($this->connection);

			// If the curl error number was not zero
			if (($error_number = curl_errno($this->connection)) > 0)
			{
				// Merge the ignored errors values
				$this->config['ignored_errors'] += $ignored_errors;

				// Assign the error information to this model
				$this->error = array($error_number => curl_error($this->connection));

				// If the error number is not in the ignored errors array, throw an exception
				if ( ! in_array($error_number, $this->config['ignore_errors']))
					throw new Kohana_User_Exception(__CLASS__.'.'.__METHOD__.'()', 'Curl error : ' . $error_number . ' ' . $this->error[$error_number]);
			}
			else
				$this->error = array(0 => 'Okay');

			// Get the information from this connection
			$this->info = curl_getinfo($this->connection);

			// Set executed to TRUE and cached to false
			$this->executed = TRUE;
			$this->cached_result = FALSE;

			// If this should be cached, cache it
			if ($this->config['cache'] AND ($this->cache instanceof Cache))
				$this->cache();
		}

		// Return
		return $this;
	}

	/**
	 * Execute the current CURL session with
	 * the provided options. Retained for backward compatibility
	 *
	 * @chainable
	 * @param   array        ignore_error_numbers (ex: 26, 28, etc...)
	 * @return  mixed
	 * @access  public
	 * @depreciated
	 */
	public function execute($ignore_error_numbers = array())
	{
		return $this->exec($ignore_error_numbers)->result();
	}
	
	/**
	 * Return a CURL status code, by default
	 * the HTTPCODE is set.
	 * Retained for backwards compatibility, you should use self::info(key) in future implementations
	 *
	 * @param   cURL const   code
	 * @return  mixed
	 * @access  public
	 * @depreciated
	 */
	public function status($code = CURLINFO_HTTP_CODE)
	{
		return curl_getinfo($this->connection, $code);
	}

	/**
	 * Returns the stored result of the cURL operation
	 *
	 * @return  mixed
	 * @return  void
	 * @access  public
	 */
	public function result()
	{
		if ($this->executed)
			return $this->result;
		else
			return NULL;
	}

	/**
	 * Provides access to the processed info array
	 *
	 * @param   string       key 
	 * @return  mixed
	 * @return  void
	 * @access  public
	 */
	public function info($key = NULL)
	{
		if ($this->executed)
		{
			if ($key === NULL)
				return $this->info;
			elseif (array_key_exists($key, $this->info))
				return $this->info[$key];
		}

		return NULL;
	}

	/**
	 * Provides access to the processed header array
	 *
	 * @param   string       key 
	 * @return  string
	 * @return  void
	 * @author Sam Clark
	 */
	public function header($key = NULL)
	{
		if ($this->executed)
		{
			if ($key === NULL)
				return $this->header;
			elseif (array_key_exists($key, $this->header))
				return $this->header[$key];
		}

		return NULL;
	}

	/**
	 * Clears the cache for Curl library
	 *
	 * @param   bool         all  if TRUE will delete all Curl library caches
	 * @return  Curl
	 * @access  public
	 * @throws  Kohana_User_Exception
	 */
	public function clear_cache($all = FALSE)
	{
		if ($this->config['cache'] === FALSE OR ! ($this->cache instanceof Cache))
			throw new Kohana_User_Exception(__CLASS__.'.'.__METHOD__.'()', 'Cache not enabled for this instance. Please check your settings.');

		if ($all)
			$this->cache->delete_tag($this->config['cache_tags']);
		else
			$this->cache->delete($this->create_cache_key());

		return $this;
	}

	/**
	 * Try to load a cached version of this request
	 *
	 * @return  bool
	 * @access  public
	 * @throws  Kohana_User_Exception
	 */
	protected function load_cache()
	{
		if ($this->config['cache'] === FALSE OR ! ($this->cache instanceof Cache))
			throw new Kohana_User_Exception(__CLASS__.'.'.__METHOD__.'()', 'Cache not enabled for this instance. Please check your settings.');

		$result = $this->cache->get($this->create_cache_key());

		if ($result === NULL)
			return FALSE;

		$this->info = $result['info'];
		$this->result = $result['result'];
		$this->headers = $result['headers'];

		$this->executed = TRUE;
		$this->cached_result = TRUE;

		return TRUE;
	}

	/**
	 * Caches the current result set, or loads a saved cache if key is supplied
	 *
	 * @return  boolean
	 * @access  protected
	 * @throws  Kohana_User_Exception
	 */
	protected function cache()
	{
		if ( ! $this->executed OR $this->result === NULL)
			return;

		if ($this->config['cache'] === FALSE OR ! ($this->cache instanceof Cache))
			throw new Kohana_User_Exception(__CLASS__.'.'.__METHOD__.'()', 'Cache not enabled for this instance. Please check your settings.');

		// Store the correct data
		$cache_data = array
		(
			'result'      => $this->result,
			'headers'     => $this->headers,
			'info'        => $this->info,
		);

		return $this->cache->set($this->create_cache_key(), $cache_data, $this->config['cache_tags'], $this->config['cache']);
	}

	/**
	 * Creates a hash key for caching indentification
	 *
	 * @return  string
	 * @access  protected
	 */
	protected function create_cache_key()
	{
		return hash($this->config['hash'], serialize($this->options));
	}

	/**
	 * Puts the header response into a stored variable
	 *
	 * @param   Curl         ch 
	 * @param   string       header 
	 * @return  void
	 */
	protected function parse_header($ch, $header)
	{
		$result = array();

		if (preg_match_all('/(\w[^\s:]*):[ ]*([^\r\n]*(?:\r\n[ \t][^\r\n]*)*)/', $header, $matches))
		{
			foreach ($matches[0] as $key => $value)
				$result[$matches[1][$key]] = $matches[2][$key];
		}

		if ($result)
			$this->headers += $result;

		return strlen($header);
	}

	/**
	 * Be sure to destroy our CURL session. Retained for backwards compatibility
	 * 
	 * @return  void
	 * @access  public
	 */
	public function __destroy()
	{
		$this->__destruct();
	}

	/**
	 * Destroy the curl connection gracefully
	 *
	 * @return  void
	 * @access  public
	 */
	public function __destruct()
	{
		curl_close($this->connection);
	}
}
