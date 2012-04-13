<!doctype html>
<html lang="en"><head>
<meta charset="utf-8" />
<meta name="keywords" content="" />
<meta name="description" content="" />

<title>Authorize App</title>

<link rel="stylesheet" href="css/styles.css" />
<link rel="shortcut icon" type="image/x-icon" href="img/favicon.png" />

<script src="js/mootools.core.js"></script>
<script src="js/mootools.more.js"></script>



</head>


<body>

<?php

/**
 * @file
 * Sample authorize endpoint.
 *
 * Obviously not production-ready code, just simple and to the point.
 *
 * In reality, you'd probably use a nifty framework to handle most of the crud for you.
 */

	// http://www/lab/oauth2-php/server/examples/mongo/authorize?client_id=12345&response_type=token&state=test_state

include ("classes/OAuth.php");

$oauth = new OAuth2_Server;

if (isset($_POST["accept"])) {
  $oauth->authorize($_POST["accept"] == "Yep", $_POST);
}

$auth_params = $oauth->authorize_start();

?>

	
	<?php if (!isset($_GET['app_id']) || !isset($_GET['response_type'])) : ?>
	
	<h1>Error!</h1>
	
	<?php if (!isset($_GET['app_id'])) echo '<h3>App ID is required</h3>';; ?>
	<?php if (!isset($_GET['response_type'])) echo '<h3>Response type is required</h3>';; ?>
	
	<?php else : ?>
	
	<form method="post" action="authorize.php">
      <?php foreach ($auth_params as $k => $v) { ?>
      <input type="hidden" name="<?php echo $k ?>" value="<?php echo $v ?>" />
      <?php } ?>
      Do you authorize the app to do its thing?
      <p>
        <input type="submit" name="accept" value="Yep" />
        <input type="submit" name="accept" value="Nope" />
      </p>
    </form>
		
	<?php endif; ?>
	

</body>
</html>