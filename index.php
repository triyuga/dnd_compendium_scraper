<?php
include_once("functions.php");
ini_set('max_execution_time', 300); // 300 seconds = 5 minutes - running this code may take a long time.

$path = 'dnd_compendium_html/Power';
$xml_path = 'dnd_compendium_xml/Power';

if (!file_exists($path)){
  die('Error: source dir ' . $path . ' does not exist. Please point $path var at DnD Compendium Power dir.');
}
if (!file_exists($xml_path)) {
  if (!mkdir($xml_path, 0755, TRUE)) {
    die('Error: unable create dir ' . $xml_path);
  }
}

$files = listdir($path);
//$files = array('DnD-Insider-Compendium/Power/416.html');

//$test = scrape_power('DnD-Insider-Compendium/Power/416.html');
//echo '<pre>'; print_r($test); exit;

//echo "this might take a while... Please be patient...";

foreach ($files as $file) {
  $filename = str_replace($path.'/', '', $file);
  $filename = str_replace('.html', '', $filename);
  //echo $filename .'<br />';
  if ($filename[0] != '.' && $filename != '') {
    $powers[$filename]['filename'] = $filename;
    $powers[$filename]['fields'] = scrape_power($file);
    $powers[$filename]['fields']['power_dndinsider_id'] = $filename;
  }
}
//echo '<pre>'; print_r($powers); echo '</pre>'; exit;

// // header("Content-Type:text/plain");
// header('Content-Type: text/html; charset=utf-8');
// echo '<link rel="stylesheet" href="css/style.css" type="text/css">';

// // Compendium Styles
// echo '<link rel="stylesheet" href="css/detail.css" type="text/css">';
// echo '<link rel="stylesheet" href="css/mobile.css" type="text/css">';
// echo '<link rel="stylesheet" href="css/print.css" type="text/css">';
// echo '<link rel="stylesheet" href="css/reset.css" type="text/css">';
// echo '<link rel="stylesheet" href="css/site.css" type="text/css">';

// foreach ($powers as $power) {
//   echo file_get_contents($power['file']) . '<br />';
//   echo output_html($power['fields']) . '<br />';
//   echo '<pre>'; print_r($power['fields']); echo '</pre>';
// }
// exit;
//echo '<pre>'; print_r($powers[1]); echo '</pre>';


foreach ($powers as $power) {
  $xml = new SimpleXMLElement("<?xml version=\"1.0\"?><Power></Power>");
  array_to_xml($power['fields'], $xml);
  //saving generated xml file
  $xml->formatOutput = true;

  $file = $xml_path .'/' . $power['filename'] . '.xml';
  //die($file);
  $xml->asXML($file);
  // header("Content-Type:text/plain");
  // echo $xml->saveXML();
  // exit;
  //$xml_string = $xml->saveXML();
  //$xml_string = $xml->saveXML();
}
//echo "<br />OK, done.";

//header ("Content-Type:text/xml");
//header('Content-type: application/xml');
// header('Content-Type: text/html; charset=utf-8');
//header("Content-Type:text/plain");
//echo $xml_string;
