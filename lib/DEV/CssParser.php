<?php
namespace DEV;

class CssParser {

    /**
     * css file path
     */
    protected $cssFilePath;

    
    /**
     * Constructor
     * initialize the data array
     */
    public function __construct() 
    {
        $this->cssFilePath= "";
    }

    /**
     * Get path
     *
     * @return path in string
     */
    public function getCssFilePath() 
    {
        return $this->cssFilePath;
    }

    /**
     * Set path
     *
     * @param string: $$path
     * @return CssParser
     */
    public function setCssFilePath($path) 
    {
        $this->cssFilePath = $path;

        return $this;
    }

    /**
     * Parse CSS
     *
     * @return the css data array
     */
    public function parseCSS() 
    {
        //get the file contents in string
        $cssString = $this->getFileContents($this->cssFilePath);
        $isError = $this->ValidateCSS($cssString);
        if ($isError) {
            return $isError;
        }
      
        //convert string to array by using /** as delimiter
        $stringToArr = explode('/**', $cssString);

        //Array to store each css rule        
        $extractedPerRule = array();
        foreach ($stringToArr as $k):
            if ($k) {
                $extractedPerRule[] = explode('*/', $k);
            }
        endforeach;

        $dataAfterAddSign = array();
        $tempVariables = array();
        $tempCssSelector = "";
        $phpClassNames = array();
        $cssSelectors = array();
        foreach ($extractedPerRule as $xp):
            //get the data after @ sign
            preg_match("/@(.*)/", $xp[0], $dataAfterAddSign);
            $tempClassName = $this->extractCSSRule($dataAfterAddSign[1]);

            if ($tempClassName) {
                $tempCssSelector = $this->extractCssSelectors($xp[1]);
                $tempVariables[] = $this->extractVariablesAndValues($dataAfterAddSign[1], $tempCssSelector);
                $phpClassNames[] = "\\".$tempClassName;
                $cssSelectors[]  = $tempCssSelector;
            }

        endforeach;
       
        $returnArr=array();      
        $count=0;
        foreach ($tempVariables as $tv): 
            $styleRule =  new $phpClassNames[$count](($cssSelectors[$count] ? $cssSelectors[$count] : ""));
            foreach($tv as $t):           
                extract($t);
                $styleRule->setName(($name ? $name : ""));
                $styleRule->setCssSelector(($cssSelector ? $cssSelector : ""));
                $styleRule->setMatchSelector(($matchSelector ? $matchSelector : ""));
                $styleRule->setConflictWith(($conflictWith ? $conflictWith : ""));
            endforeach;
            $returnArr[] = $styleRule;
            $count++;
        endforeach;
        
        return $returnArr;
    }

    
    /**
     * Valid Comments and CSS syntax
     *
     * @return error message or false for no error
     */
    public function ValidateCSS($cssData) 
    {

        $css = trim($cssData);
        if (strlen($css) == 0) {
            return "Empty file";  //checked
        }

        //check for first opening comment
        if ((strpos($css, '/*', 0) > -1) && (strpos($css, '/*', 0) < 10)) {
            $eachRule = explode('/*', $css);
        } else {
            return "Missing open comment - /*";
        }

        if ($eachRule) {
            foreach ($eachRule as $er):
                if ($er) {
                    //check for @
                    if (strpos($er, '@', 0) == TRUE) {
                        $posRuleStart = strpos($er, '{', 0);
                        $posRuleEnd   = strpos($er, '}', 0);
                        // check for another @ sign ( when opening comment is missing)
                        $posAdd = strpos($er, '@', 0);
                        $posAnotherAdd = strrpos($er, '@', -1);

                        if (($posAnotherAdd > $posAdd) 
                             && (($posAnotherAdd > $posRuleStart) 
                                  || ($posAnotherAdd > $posRuleEnd)
                             )
                        ) {
                            return "Missing open comment - /* near " . $er;
                        }
                    }
                    //check for closing comment */
                    if (strpos($er, '*/', 0) == FALSE) {
                        return "Missing close comment - */ near " . $er;
                    } else {
                        $mixedComCss[] = explode('*/', $er);
                    }
                }
            endforeach;
        } else {
            return "Missing open comment - /* near  ";
        }

        foreach ($mixedComCss as $mc):
            $cssArr[] = $mc[1];
        endforeach;

        //validation for css syntax for opening and closing {}
        foreach ($cssArr as $cs):
            if ((strpos($cs, '{', 0) || strpos($da, '}', 0)) == FALSE) {
                return "Syntax error near - " . $cs;
            }
        endforeach;


        preg_match_all('/(?ims)([a-z0-9\s\.\:#_\-@,]+)\{([^\}]*)\}/', $css, $arr);

        $isError = "";
        if ($arr) {
            // stores selector data only
            $selectorArray = $arr[1];
            // stores mixed data for both selector and declaration
            $mixedArray = $arr[0];

            $isError = $this->checkSelectorSyntax($selectorArray);
            if ($isError) {
                return $isError;
            }
            $isError = $this->checkDeclarationSyntax($mixedArray);
            if ($isError) {
                return $isError;
            }
        }

        return FALSE;
    }

    /**
     * Validate the selector 
     *
     * @return error message or false for no error
     */
    public function checkSelectorSyntax($selectorArray) 
    {
        foreach ($selectorArray as $sa):

            $sa = trim($sa);
            // check for missing . or # in front of selector name and : for events
            if (preg_match('~[.|#|:][0-9a-z]~', $sa) == FALSE) {
                return "Syntax error near - " . $sa;
            }

            if ($this->getCharacterAtNposition($sa, 0) === $this->getCharacterAtNposition($sa, 1)) {
                return "Syntax error near - " . $sa;
            }

        endforeach;
        
        return FALSE;
    }

    /**
     * Validate the css declaration pattern 
     *
     * @return error message or false for no error
     */
    public function checkDeclarationSyntax($mixedArray) 
    {
        foreach ($mixedArray as $da):

            //check for characters shouldn't be in array which occurs when closing } is missing
            if (strpos($da, '/*', 0) || strpos($da, '*/', 0) || strpos($da, '@', 0)) {
                return "Syntax error near - " . $da;
            }
            //check for closing }
            if ((strpos($da, '{', 0) && strpos($da, '}', 0)) == FALSE) {
                return "Syntax error near - " . $da;
            }

        endforeach;

        return FALSE;
    }

    /**
     * Extract file contents from the provided path
     * @param string: $path
     * @return file contents in string
     */
    public function getFileContents($path) 
    {
        //validate file 
        if (file_exists($path)) {
            $cssData = file_get_contents($path);
            if ($cssData) {
                return $cssData;
            } else {
                return "Empty File";
            }
        } else {
            return "File can't be found at the provided path";
        }
    }

    /**
     * Extract the CSSRule from the provided string
     * @param string: $validString
     * @return extracted string
     */
    public function extractCSSRule($datastring) 
    {
        $result = "";
        $stringToArr = explode('(', $datastring);

        //looking for / in the given string
        if (preg_match("/\\\\/", $stringToArr[0])) {
            $result = $stringToArr[0];
        }

        return $result;
    }

    /**
     * Extract the required variables and values and rearrange in data array format
     * @param type $datastring
     * @return array of data arrays
     */
    public function extractVariablesAndValues($datastring, $cssSelector)
    {
        $stringToArr1 = explode('(', $datastring);
        $stringToArr2 = explode(')', $stringToArr1[1]);

        $dataOnlyArr = explode('",', $stringToArr2[0]); 
        $tempArray = array();
        $counter = 0;
        foreach ($dataOnlyArr as $da) {
            $temp = explode('=', $this->removeDoubleQuotes($da));
            $tempArray[$counter] = array(trim($temp[0]) => trim($temp[1]));
            $counter++;
        }
        $tempArray[$counter] = array('cssSelector' => $cssSelector);

        return $tempArray;
    }

    /**
     * Extract the CSSRule from the provided string
     * @param string: $datastring
     * @return extracted string
     */
    public function extractCssSelectors($datastring) 
    {
        $stringToArr = explode('{', $datastring);

        return $stringToArr[0];
    }
    
    /**
     * Extract the character from the string at provided position
     * @param string: $datastring and position 
     * @return character
     */
    public function getCharacterAtNposition($datastring, $n) 
    {       
        return substr($datastring, $n, 1);
    }

    /**
     * Remove double from string
     * @param string: $datastring
     * @return string
     */
    public function removeDoubleQuotes($datastring)
    {        
        return preg_replace('/"/', '', $datastring);
    }

}
