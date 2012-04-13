<?php

/**
 * Aulph - PHP OAuth Library
 * Based on Oauth2-PHP (http://code.google.com/p/oauth2-php/)
 * 
 * @version    0.1
 * @author     Jesse Weed
 * @license    MIT License
 * @copyright  2012
 * @link       http://github.com/jesseweed/aulph
 */
 
 
require_once ('_config.php');

class OAuth2_Server {
	
	function __construct() {
		$this->set_variables();
		$this->connect();
	} // End __construct
	
	
	// Set variables & check constants
	function set_variables() {
		
		// Check constants
		if (!defined('OAUTH_DB_TYPE')) define('DB_TYPE', null);
		if (!defined('OAUTH_CLASS_PATH')) define('DB_TYPE', 'classes/');
		if (!defined('OAUTH_INC_PATH')) define('DB_NAME', 'inc/');
		
		// Set variables
		$this->db_type = OAUTH_DB_TYPE;
		$this->class = OAUTH_CLASS_PATH;
		$this->inc = OAUTH_INC_PATH;
		
	} // End set_variables()
	
	
	// Connect to Database
	function connect() {
		
		$return['status'] = null;
		
		// Connect to Mongo
		if ($this->db_type == 'mongo') :
			require_once (OAUTH_CLASS_PATH.'mongo.php');
			$this->mongo = new OAuth_Mongo;
			$this->mongo->connect();
		// Fail if no database is defined
		else :
			$return['status'] = 'failure';
			$return['status_msg'] = 'Invalid database configuration';
		endif;
		
		return $return;
		
	} // End connect()
	
	
	// Add client to database
	public function add_client($id, $secret, $redirect) {
		
		$return['status'] = null;
		
		// Connect to Mongo
		if (strtolower(OAUTH_DB_TYPE) == 'mongo') :
			$return['status'] = $this->mongo->add_client($id, $secret, $redirect);
		else :
			$return['status'] = 'failure';
			$return['status_msg'] = 'Invalid database configuration';
		endif;
		
		// Set status
		if ($return['status'] == 'success') :
			$return['status'] = 'success';
			$return['status_msg'] = 'Client added to database';
		elseif ($return['status'] == 'duplicate') :
			$return['status'] = 'duplicate';
			$return['status_msg'] = 'Client already exists';
		elseif ($return['status'] == null) :
			$return['status'] = 'failure';
			$return['status_msg'] = 'Error adding client to database';
		endif;

		return $return;

	} // End add_client()
	
	
	
	// Get Paramaters to authorize client
	public function authorize_start() {
		
		$info = NULL;	
		
		// State
		if (isset($_GET['state'])) $return['state'] = $_GET['state'];
		
		
		// Set scope
		if (isset($_GET['scope'])) $return['scope'] = $_GET['scope'];
		
		
		// Response_type
		if (isset($_GET['response_type'])) :	
			$return['response_type'] = $_GET['response_type'];
		else :
			$return['status'] = 'failure';
			$return['status_msg'] = 'Response type has not been set';
		endif;
		
		
		// Client id
		if (isset($_GET['app_id'])) :	
			$return['client'] = $_GET['app_id'];
		else :
			$return['status'] = 'failure';
			$return['status_msg'] = 'Client id is not set';
		endif;
		
		
		if (isset($_GET['app_id'])) $info = $this->get_client($return['client']);
		
		// redirect_uri
		if (isset($_GET['redirect'])) :	
			$return['redirect_uri'] = $_GET['redirect'];
		else :
			
			if ($info == NULL) {
				$return['status'] = 'failure';
				$return['status_msg'] = 'Redirect URI is not set';
			} else {
				$return['redirect_uri'] = $info['redirect_uri'];
			}
			
		endif;
		
		return $return;		
		
	} // End authorize_init
	
	
	 public function authorize($is_authorized, $params = array()) {
    $params += array(
      'scope' => NULL,
      'state' => NULL,
    );
		
//		print_r($params); exit;
		
    extract($params);

    if ($state !== NULL)
      $result["query"]["state"] = $state;

    if ($is_authorized === FALSE) :
      $result["query"]["error"] = OAUTH2_ERROR_USER_DENIED;
    else :
	
		if ($response_type == OAUTH_RESPONSE_TYPE_CODE || $response_type == OAUTH_RESPONSE_TYPE_CODE_AND_TOKEN)
				$result["query"]["code"] = $this->auth_code($client, $redirect_uri, $scope);

      if ($response_type == OAUTH_RESPONSE_TYPE_TOKEN || $response_type == OAUTH_RESPONSE_TYPE_CODE_AND_TOKEN)
				$result["fragment"] = $this->access_token($client, $scope);
		
		endif;
	
		$this->redirect($redirect_uri, $result);
		
  } // End authorize()
	
	
	private function redirect($redirect_uri, $params) {
    header("HTTP/1.1 ". OAUTH_HTTP_FOUND);
    header("Location: " . $this->build_uri($redirect_uri, $params));
    exit;
	} // End redirect()
	
	
	private function build_uri($uri, $params) {
    $parse_url = parse_url($uri);

    // Add our params to the parsed uri
    foreach ($params as $k => $v) {
      if (isset($parse_url[$k]))
        $parse_url[$k] .= "&" . http_build_query($v);
      else
        $parse_url[$k] = http_build_query($v);
    }

    // Put humpty dumpty back together
    return
      ((isset($parse_url["scheme"])) ? $parse_url["scheme"] . "://" : "")
      . ((isset($parse_url["user"])) ? $parse_url["user"] 
			. ((isset($parse_url["pass"])) ? ":" . $parse_url["pass"] : "") . "@" : "")
      . ((isset($parse_url["host"])) ? $parse_url["host"] : "")
      . ((isset($parse_url["port"])) ? ":" . $parse_url["port"] : "")
      . ((isset($parse_url["path"])) ? $parse_url["path"] : "")
      . ((isset($parse_url["query"])) ? "?" . $parse_url["query"] : "")
      . ((isset($parse_url["fragment"])) ? "&" . $parse_url["fragment"] : "");
  }
	
	
	
	// Fetch an authorization code
	private function auth_code() {
		return $this->create_token();
	} // End auth_code
	
	// Fetch an access token
	private function access_token() {
		return $this->create_token();
	} // End access_token
	
	// Generate an MD5 token
	private function create_token() {
		return md5(base64_encode(pack('N6', mt_rand(), mt_rand(), mt_rand(), mt_rand(), mt_rand(), uniqid())));
	} // End create_token
	
	
	public function get_client($id) {
	
		// Connect to Mongo
		if (strtolower(OAUTH_DB_TYPE) == 'mongo') :
			$return = $this->mongo->get_client($id);
		else :
			$return = 'error';
		endif;
		
		return $return;
	
	} // End get_client

	
	
} // End OAuth Class