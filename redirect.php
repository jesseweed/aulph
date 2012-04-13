<?php

$token = $_GET['access_token'];
$expire = $_GET['expires_in'];
$scope = $_GET['scope'];
$state = $_GET['state'];
$url = 'http://www/lab/oauth2-php/server/examples/mongo/protected_resource.php';
$url2 = 'http://www/lab/oauth2-php/server/examples/mongo/protected_resource.php?oauth_token='.$token;
//$header = array('Authorization: '.$token);
$header = array('Authorization: OAuth ' . $token);

/*
echo 'Expires in: '.$expire.'<br>';
echo 'Scope: : '.$scope.'<br>';
echo 'State: '.$state.'<br>';
echo 'Token: '.$token.'<br><br><br>';

echo '<a href="protected_resource.php?token='.$token.'">let\'s do something</a><br><br><br>';
*/

//echo curl($url, $header);
echo json_encode(curl($url2));

function curl($url, $headers = false) {

	$curl = curl_init();

	curl_setopt($curl, CURLOPT_URL, $url); 

	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	if ($headers != false) :
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	endif;

	$result = curl_exec($curl);  
	$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	curl_close($curl);    

	if ($status == 200) :
		$return['http_code'] = $status;
		$return['response'] = json_decode($result);
	elseif ($status == 400) :
		$return['status'] = 'error';
		$return['http_code'] = $status;
		$return['msg'] = 'Bad Request';
	elseif ($status == 401) :
		$return['status'] = 'error';
		$return['http_code'] = $status;
		$return['msg'] = 'Not Authorized';
	elseif ($status == 501) :
		$return['status'] = 'error';
		$return['http_code'] = $status;
	 	$return['msg'] = 'Not Found';
	else :
		$return['status'] = 'error';
		$return['http_code'] = $status;
		$return['msg'] = 'HTTP Code: '.$status;
	endif;

	return $return;
}

exit;