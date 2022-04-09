<?php

$_POST = json_decode(file_get_contents('php://input'), true);

if(isset($_POST['url'])) $html_url = $_POST['url'];
else $html_url = 'https://codeadam.ca/';

if (filter_var($html_url, FILTER_VALIDATE_URL) === false) 
{
  die('{"status":"error","error":"Invalid URL"}');
}

$data['status'] = 'success';

// Fetch HTML from URL
$ch = curl_init();
curl_setopt_array($ch, array(
  CURLOPT_URL => $html_url,
  CURLOPT_RETURNTRANSFER => 1,
));
$html = curl_exec($ch);
curl_close($ch);

// Validate HTML
$validate_url = 'https://validator.w3.org/nu/';

$ch = curl_init();
curl_setopt_array($ch, array(
    CURLOPT_URL => $validate_url,
    CURLOPT_ENCODING => '',
    CURLOPT_USERAGENT => 'PHP/'.PHP_VERSION.' libcurl/'.(curl_version()['version']),
    CURLOPT_POST => 1,
    CURLOPT_POSTFIELDS => array(
        'showsource' => 'yes',
        'content' => $html,
    ),
    CURLOPT_RETURNTRANSFER => 1,
));

$validate = curl_exec($ch);
curl_close($ch);

$data['html_errors'] = substr_count($validate, 'class="error"');
$data['html_warnings'] = substr_count($validate, 'class="info warning"');

die(json_encode($data));
