<?php

$_POST = json_decode(file_get_contents('php://input'), true);
// $_POST['url'] = 'https://pages.codeadam.ca/';

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

// echo '<pre>';
// print_r($validate);

curl_close($ch);

$data['html_errors'] = substr_count($validate, 'class="error"');
$data['html_warnings'] = substr_count($validate, 'class="info warning"');

$data['block_count'] = 0;

// Count inline elements
$block = array(
  'address' => 0,
  'article' => 0,
  'aside' => 0,
  'blockquote' => 0,
  'canvas' => 0,
  'dd' => 0,
  'div' => 0,
  'dl' => 0,
  'dt' => 0,
  'fieldset' => 0,
  'figcaption => 0',
  'figure' => 0,
  'footer' => 0,
  'form' => 0,
  'h1' => 0,
  'h2' => 0,
  'h3' => 0,
  'h4' => 0,
  'h5' => 0,
  'h6' => 0,
  'header' => 0,
  'hr' => 0,
  'li' => 0,
  'main' => 0,
  'nav' => 0,
  'noscript' => 0,
  'ol' => 0,
  'p' => 0,
  'pre' => 0,
  'section' => 0,
  'table' => 0,
  'tfoot' => 0,
  'ul' => 0,
  'video' => 0
);

foreach( $block as $tag => $count )
{
  // echo $tag;
  // echo substr_count( $html, '<'.$tag );
  // echo '<br>';
  $count = substr_count( $html, '<'.$tag );
  if( $count ) 
  {
    $data['block'][$tag] = $count;
    $data['block_count'] += $count;
  }
}

$data['inline_count'] = 0;

$inline = array(
  'a' => 0,
  'abbr' => 0,
  'acronym' => 0,
  'b' => 0,
  'bdo' => 0,
  'big' => 0,
  'br' => 0,
  'button' => 0,
  'cite' => 0,
  'code' => 0,
  'dfn' => 0,
  'em' => 0,
  'i' => 0,
  'img' => 0,
  'input' => 0,
  'kbd' => 0,
  'label' => 0,
  'map' => 0,
  'object' => 0,
  'output' => 0,
  'q' => 0,
  'samp' => 0,
  'script' => 0,
  'select' => 0,
  'small' => 0,
  'span' => 0,
  'strong' => 0,
  'sub' => 0,
  'sup' => 0,
  'textarea' => 0,
  'time' => 0,
  'tt' => 0,
  'var' => 0 
);

foreach( $inline as $tag => $count )
{
  // echo $tag;
  // echo substr_count( $html, '<'.$tag );
  // echo '<br>';
  $count = substr_count( $html, '<'.$tag );
  if( $count ) 
  {
    $data['inline'][$tag] = $count;
    $data['inline_count'] += $count;
  }
}

die(json_encode($data));
