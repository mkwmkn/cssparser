<?php
/**
 * test file to parse the css file using CssParser library
 * and create StyleRule object array for outputs
 * 
 */

require_once __DIR__ . '/vendor/autoload.php';

use DEV\CssParser;

$cssParser = new CssParser();

$parserData= $cssParser->setCssFilePath('sample.css');
$cssData   = $cssParser->parseCSS();  

if(is_array($cssData) && ($cssData)){
    echo "<pre>";
    print_r($cssData);
}else{
    echo "Errors Occured! <br/> Details - ( ".$cssData." )";
}



?>