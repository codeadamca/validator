<?php

$_POST = json_decode(file_get_contents('php://input'), true);

$url = $_POST['url'];

if (filter_var($url, FILTER_VALIDATE_URL) === false) 
{
  die('{"status":"error","error":"Invalid URL"}');
}


die('{"status":"success"}');
