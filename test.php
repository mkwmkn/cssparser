<?php
/**
 * test file to parse the css file using CssParser library
 * and create StyleRule object array for outputs
 * 
 */

require_once __DIR__ . '/vendor/autoload.php';

use DEV\CssParser;
use CSS\StyleRule;

$cssParser = new CssParser();
$cssParser->setCssFilePath('sample.css');
$cssData= $cssParser->parseCSS();  

$outputArr=array();
if(is_array($cssData) && ($cssData)){
    foreach($cssData as $cd):
        foreach($cd as $dd):     
          // extract data array to get the required variables for the StyleRule class
          extract($dd);
        endforeach;
  
    $styleRule = new StyleRule($cssSelector); 
    $styleRule->setName($name); 
    $styleRule->setMatchSelector($matchSelector); 
    $styleRule->setConflictWith($conflictWith);    
    $outputArr[] = $styleRule;
   endforeach;

echo "<pre>";
print_r($outputArr);
}else{
    echo "Errors Occured! <br/> Details - ( ".$cssData." )";
}

?>