<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);


function scrape_power($file) {
  /* Power Fields: */
  // power_name (Name)
  // power_class_and_level (Class & Level)
  // power_flavor (Flavor)
  // power_frequency (Frequency)
  // power_keywords (Keywords)
  // power_action_type (Action Type)
  // power_range_and_aoe (Range/AoE)
  // power_rules (Rules)
  // power_rules_html (Rules HTML)
  // power_published_in (Published In)

  $doc = new DOMDocument();
  $doc->loadHTMLFile($file);
  $xpath = new DOMXpath($doc);

  // power_name (Name)
  $items = runxpathquery($xpath, "//div/h1");
  $fields['power_name'] = end($items);
  end($fields); $output_html .= key($fields) . ': ' . end($fields) . '<br />';

  // power_class_and_level (Class & Level)
  $items = runxpathquery($xpath, "//div/h1/span");
  $fields['power_class_and_level'] = implode(", ", array_filter($items));

  // power_flavor (Flavor)
  $items = runxpathquery($xpath, "//div/p[1]");
  $fields['power_flavor'] = implode(", ", array_filter($items));

  // power_frequency (Frequency)
  $items = runxpathquery($xpath, "//div/p[2]/b[1]");
  $fields['power_frequency'] = implode(", ", array_filter($items));

  // power_keywords (Keywords)
  $items = runxpathquery($xpath, "//div/p[2]/b[position()!=last() and position()!=last()-1 and position()!=1]");
  $fields['power_keywords'] = implode(", ", array_filter($items));

  // power_action_type (Action Type)
  $items = runxpathquery($xpath, "//p[2]/b[last()-1]");
  $fields['power_action_type'] = implode(", ", array_filter($items));

  // power_range_and_aoe (Range/AoE)
  $items = runxpathquery($xpath, "//p[2]/b[last()]");
  $range = implode(", ", array_filter($items));
  $items = runxpathquery($xpath, "//p[2]");
  $AoE .= end($items);
  $fields['power_range_and_aoe'] = $range . ' ' . $AoE;

  // power_rules (Rules)
  $items = runxpathquery($xpath, "//div/p[position()!= 1 and position()!= 2 and position()!=last()]");
  $items = cleanuprulesfield($items);
  $fields['power_rules'] = implode("\n", array_filter($items));

  // power_rules_html (Rules HTML)
  $items = runxpathquery($xpath, "//div/p[position()!= 1 and position()!= 2 and position()!=last()]");
  $items = cleanuprulesfield($items, TRUE);
  $rules_flvor_items = runxpathquery($xpath, "//div/p[@class='flavor'][position()!=1]");
  $rules_flvor_items = cleanuprulesfield($items, TRUE);
  foreach ($items as $item_key=>$item_value) {
    foreach ($rules_flvor_items as $rfi_key=>$rfi_value) {
      if($item_value == $rfi_value) {
        if(!empty($rules_flvor_items[$rfi_key])) {
          $fields['power_rules_html'] .= '<span class="flavor">' . $rules_flvor_items[$rfi_key] ."</span>\n";
          unset($rules_flvor_items[$rfi_key]);
          unset($items[$item_key]);
        }
      }
      else {
        if(!empty($items[$item_key])) {
          $fields['power_rules_html'] .= '<span>' . $items[$item_key] ."</span>\n";
          unset($items[$item_key]);
        }

      }
    }
  }

  // power_published_in (Published In)
  $items = runxpathquery($xpath, "//div/p[@class='publishedIn']");
  $items = cleanuppublishedinfield($items);
  $fields['power_published_in'] = implode('', array_filter($items));
  //die($fields['power_published_in']);
  //echo '<pre>'; print_r($items); exit;

  return $fields;
}

/**
 * helper function.
 * cleans up problematic characters.
 */
function htmlallentities($str){
  $res = '';
  $strlen = strlen($str);
  for($i=0; $i<$strlen; $i++){
    $byte = ord($str[$i]);
    if($byte < 128) // 1-byte char
      $res .= $str[$i];
    elseif($byte < 192); // invalid utf8
    elseif($byte < 224) // 2-byte char
      $res .= '&#'.((63&$byte)*64 + (63&ord($str[++$i]))).';';
    elseif($byte < 240) // 3-byte char
      $res .= '&#'.((15&$byte)*4096 + (63&ord($str[++$i]))*64 + (63&ord($str[++$i]))).';';
    elseif($byte < 248) // 4-byte char
      $res .= '&#'.((15&$byte)*262144 + (63&ord($str[++$i]))*4096 + (63&ord($str[++$i]))*64 + (63&ord($str[++$i]))).';';
  }
  return $res;
}
/**
 * helper function.
 * runs xpath query
 */
function runxpathquery($xpath, $query) {
  $items = array();
  $elements = $xpath->query($query);
  if (!is_null($elements)) {
    foreach ($elements as $element) {
      $nodes = $element->childNodes;
      foreach ($nodes as $node) {
        if(!empty($node->nodeValue)) {
          $items[] = trim(htmlallentities($node->nodeValue));
        }
      }
    }
  }
  return $items;
}

/**
 * helper function.
 * fix for rules field php xpath deficiency.
 * concatenates field beginning with ':' with the proceeding field.
 */
function cleanuprulesfield($rules_items, $add_html_tags = FALSE) {
  $rules_items = array_filter($rules_items);
  $processed_fields = array();
  // trim all...
  foreach ($rules_items as $key=>$value) {
    $rules_items[$key] = trim($rules_items[$key]);
  }
  // correct placement of ':' character.
  foreach ($rules_items as $key=>$value) {
    if ($rules_items[$key+1][0] == ':') { // check first character of string...
      $rules_items[$key] = trim($rules_items[$key]).': ';
      $rules_items[$key+1] = ltrim($rules_items[$key+1],':');
      $rules_items[$key+1] = ltrim($rules_items[$key+1]);
      $rules_items[$key+1] = trim($rules_items[$key+1]);
      if ($add_html_tags) {
        $processed_fields[] = '<b>' . $rules_items[$key] . '</b>' . $rules_items[$key+1];
      } else {
        $processed_fields[] = $rules_items[$key] . $rules_items[$key+1];
      }
      unset($rules_items[$key]);
      unset($rules_items[$key+1]);
    } else {
      $processed_fields[] = trim($rules_items[$key]);
    }
  }
  $processed_fields = array_filter($processed_fields);
  $processed_fields = array_unique($processed_fields);
  return $processed_fields;
}

/**
 * helper function.
 * fix for publishedIn field php xpath deficiency.
 */
function cleanuppublishedinfield($publishedin_items, $add_html_tags = FALSE) {
  $items = array_filter($publishedin_items);

  // trim all...
  foreach ($items as $key=>$value) {
    $items[$key] = trim($items[$key]);
  }
  foreach ($items as $key=>$value) {
    if (strtolower($items[$key]) == 'published in') {
      unset($items[$key]);
    }
    // correct placement of ',' character.
    if ($items[$key][0] == ',') {
      $items[$key] .= ' ';
    }
  }
  return $items;
}


/**
 * Return fields as html to print to screen.
 * Usefule for debugging.
 */
function output_html($fields) {
  $html = '<div class="block">';
  foreach ($fields as $key=>$value) {
    $html .= '<span class="label">' . $key . ':</span> <span class="value">' . nl2br($value) . '</span><br />';
  }
  $html .= '</div>';
  return $html;
}

/**
 * helper function.
 * scan dir and output filenames.
 */
function listdir($dir='.') {
  if (!is_dir($dir)) {
    return false;
  }
  $files = array();
  listdiraux($dir, $files);
  return $files;
}

function listdiraux($dir, &$files) {
  $handle = opendir($dir);
  while (($file = readdir($handle)) !== false) {
  if ($file == '.' || $file == '..') {
    continue;
  }
  $filepath = $dir == '.' ? $file : $dir . '/' . $file;
  if (is_link($filepath))
    continue;
  if (is_file($filepath))
    $files[] = $filepath;
  else if (is_dir($filepath))
     listdiraux($filepath, $files);
  }
  closedir($handle);
}

/**
 * helper function.
 * convert array to xml.
 */
function array_to_xml($array, $xml) {
  foreach($array as $key => $value) {
    if(is_array($value)) {
      if(!is_numeric($key)){
        $subnode = $xml->addChild("$key");
        array_to_xml($value, $subnode);
      }
      else{
        array_to_xml($value, $xml);
      }
    }
    else {
      $xml->addChild("$key","$value");
    }
  }
}

