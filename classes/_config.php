<?php

// Base configuration
define('OAUTH_INC_PATH', 'inc/'); // path to include folder
define('OAUTH_CLASS_PATH', ''); // path to class dir
define('OAUTH_UNIQUE_CLIENT', true); // "true"" requires client id's to be unique to prevent overrides

// Databse configuration
define('OAUTH_DB_TYPE', 'mongo'); // the type of database to use (mongo)
define('OAUTH_DB_NAME', 'oauth'); // database name
define('OAUTH_DB_SERVER', 'mongodb://localhost'); // the type of database to use (mongo)
define('OAUTH_DB_TIMEOUT', 100);

// Databse tables
define('OAUTH_TABLE_CLIENTS', 'clients');
define('OAUTH_TABLE_TOKENS', 'tokens');
define('OAUTH_TABLE_AUTH', 'auth_codes');

//define("MONGO_CONNECTION", "mongodb://oauth:oauth@flame.mongohq.com:27043/oauth");


// Response Types
define("OAUTH_RESPONSE_TYPE_CODE", "code"); // Denotes "code" authorization response type.
define("OAUTH_RESPONSE_TYPE_CODE_AND_TOKEN", "code-and-token"); // Denotes "code-and-token" authorization response type.
define("OAUTH_RESPONSE_TYPE_TOKEN", "token"); // Denotes "token" authorization response type.

define("OAUTH_HTTP_FOUND", "302 Found");