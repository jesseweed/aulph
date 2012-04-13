<?php

/**
 * This class is part of Aulph - The PHP OAuth Library
 *
 * @version    0.1
 * @author     Jesse Weed
 * @license    MIT License
 * @copyright  2012
 * @link       http://github.com/jesseweed/aulph
 */


class OAuth_Mongo {

	public function __construct() {

		// Check constants
		if (!defined('OAUTH_DB_SERVER')) define('OAUTH_DB_SERVER', 'mongodb://localhost');
		if (!defined('OAUTH_DB_TIMEOUT')) define('OAUTH_DB_TIMEOUT', 100);
		if (!defined('OAUTH_TABLE_AUTH')) define('OAUTH_TABLE_AUTH', 'auth_codes');
		if (!defined('OAUTH_TABLE_CLIENTS')) define('OAUTH_TABLE_CLIENTS', 'clients');
		if (!defined('OAUTH_TABLE_TOKENS')) define('OAUTH_TABLE_TOKENS', 'tokens');
		if (!defined('OAUTH_UNIQUE_CLIENT')) define('OAUTH_UNIQUE_CLIENT', true);

		// DB Config
		$this->db_name = OAUTH_DB_NAME;
		$this->server = OAUTH_DB_SERVER;
		$this->timeout = OAUTH_DB_TIMEOUT;
		$this->unique = OAUTH_UNIQUE_CLIENT;

		// Collections
		$this->auth = OAUTH_TABLE_AUTH;
		$this->clients = OAUTH_TABLE_CLIENTS;
		$this->tokens = OAUTH_TABLE_TOKENS;

	}
	
	
	
	// connect to mongo database
	function connect() {
		
		if ($this->server != null) :
			$this->mongo = new Mongo($this->server, array('timeout'=> $this->timeout));
		else :
			$this->mongo = new Mongo($options = array('timeout'=> $this->timeout));
		endif;
		
		$this->db = $this->mongo->selectDB($this->db_name); // select database
		
	} // End connect()
	
	
	
	// Add new client
	function add_client($id, $secret, $redirect) {
		
		if ($this->unique == true && $this->check_client($id, $secret, $redirect) == 'success') :
		
			return 'duplicate';
		
		else :

			$this->db->{$this->clients}->insert(array(
				"_id" => $id,
				"pw" => $secret,
				"redirect_uri" => $redirect
			));
			
			if ($this->check_client($id, $secret, $redirect) == 'success') :
				return 'success';
			else :
				return 'failure';
			endif;
		
		endif;
		
	} // End add_client()
	
	
	
	// Check if client exists
	function check_client($id, $secret, $redirect) {
		
		$client = $this->db->selectCollection($this->clients)->findOne(array('_id' => $id, 'pw' => $secret, 'redirect_uri' => $redirect));
		
		if (count($client) > 0) :
			return 'success';
		else :
			return 'failure';
		endif;
		
	} // End check_client()
	
	
	
	// Check if client exists
	function get_client($id) {
		
		$client = $this->db->selectCollection($this->clients)->findOne(array('_id' => $id));
		
		return $client;
		
	} // End check_client()
	
	

} // End OAuth_Mongo