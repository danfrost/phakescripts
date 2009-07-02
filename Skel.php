<?php

/**
 * .skel files are found, and any <MARKERS> are used to build the code.
 */
class Phake_Script_Skel extends Phake_Script
{
    
    /**
     * Display help
     */
    function index()
    {
        phake('help skel');
    }
    
    /**
     * Give an example of a skel file
     */
    function help()
    {
        //
    }
    
    /**
     * Find all *.skel files and parse them
     */
    function parse()
    {
        $fs = fs('*.skel', true);
        while($f = $fs->each()) {
			echo "\n".$f->getFullPath();
			$newFile = $this->parseFile($f);
			
		}
    }
    
    /**
     * Scan all .skel files and find all <MARKERS> - Does not write anything
     */
    function vars()
    {
        $fs = $this->findFiles();
        while($f = $fs->each()) {
			echo PHP_EOL."Found markers in skel file: ".$f->getFullPath();
			preg_match_all("_<[A-Z]*>_", $f->contents(), $arr);
			foreach($arr[0] as $placeholder) {
    			echo PHP_EOL."  - $placeholder";
            }
		}
    }
    
    /**
     * Shows all .skel files that will be parsed
     */
    function show()
    {
        $fs = $this->findFiles();
        while($f = $fs->each()) {
			echo "\nFound: ".$f->getFullPath();
		}
    }
    
    /**
     * Find .skel files
     */
    private function findFiles()
    {
        return fs('*.skel', true);
    }
    
    /**
     * Parse .skel file and create production file
     */
    private function parseFile($f)
    {
        echo "\nParsing: ".$f->getFullPath();
		
		// Make new file: 
	    $target = str_replace('.skel', '', $f->getFilename());
		$fTarget = f($target)->setContent($f->contents());
		
		preg_match_all("_<[A-Z]*>_", $fTarget->contents(), $arr);
		foreach($arr[0] as $placeholder) {
			echo PHP_EOL."Found: $placeholder";
			$value = $this->getFromUser($placeholder);
			$fTarget->replace($placeholder, $value);
		}
    }
    
    /**
     * Get input from user
     */
	private function getFromUser($config)
	{
	    static $userInput = array();
	    if(!array_key_exists($config, $userInput)) {
	        $value = readline("What value should we use for '$config'?".PHP_EOL);
	        $userInput[$config] = $value;
        }
	    return $userInput[$config];
	}
}


?>