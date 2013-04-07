<?php
include_once("functions.php");
ini_set('max_execution_time', 300); //300 seconds = 5 minutes

$path = 'dnd_compendium_html/Power';
$xml_path = 'dnd_compendium_xml/Power';
$files = listdir($path);
//$files = array('DnD-Insider-Compendium/Power/416.html');

//$test = scrape_power('DnD-Insider-Compendium/Power/416.html');
//echo '<pre>'; print_r($test); exit;

foreach ($files as $file) {
  //echo $files[$i] .'<br />';
  $filename = str_replace($path.'/', '', $file);
  $filename = str_replace('.html', '', $filename);
  //echo $filename .'<br />';
  if ($filename[0] != '.' && $filename != '') {
    //echo $filename .'<br />';
    //die($files[$i]);
    //$powers[$i]['file'] = $files[$i];
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


//header ("Content-Type:text/xml");
//header('Content-type: application/xml');
// header('Content-Type: text/html; charset=utf-8');
//header("Content-Type:text/plain");
//echo $xml_string;
