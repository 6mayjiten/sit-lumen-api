<?php
 header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization,Origin,x-requested-with,Content-Type,Accept,Content-Range,Content-Disposition,Content-Description,If-Modified-Since,x-auth-token");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");

$username = $_REQUEST['username'];
$password = $_REQUEST['password'];

// if there are no errors, request an access token 
// Define URL where the form resides
$form_url = "";
if(strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
    $form_url = "http://localhost:9000/oauth/access_token";
}
 else {
    $form_url = "http://ec2-13-57-34-23.us-west-1.compute.amazonaws.com/public/index.php/oauth/access_token";
}

//'Authorization': 'Basic YW5kcm9pZDpzZWNyZXQ='
// This is the data to POST to the form. The KEY of the array is the name of the field. The value is the value posted.
$data_to_post = array();
$data_to_post['username'] = $username;
$data_to_post['password'] = $password;
$data_to_post['client_id'] = 'Android';
$data_to_post['client_secret'] = 'sit_secret';
//$data_to_post['scope'] = 'read write';
$data_to_post['grant_type'] = 'password';

$headers =["Content-Type: application/json"];
// Initialize cURL
$curl = curl_init();

// Set the options
curl_setopt($curl,CURLOPT_URL, $form_url);

curl_setopt($curl, CURLOPT_HTTPHEADER,$headers);
// This sets the number of fields to post
curl_setopt($curl,CURLOPT_POST, sizeof($data_to_post));

// This is the fields to post in the form of an array.
curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data_to_post));

//execute the post
$result = curl_exec($curl);
//close the connection
curl_close($curl);

?>