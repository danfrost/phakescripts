<?php

class Phake_Script_Markdown extends Phake_Script {
	
	/**
	 * Usage: phake markdown filename.md output.md
	 */
	function index() {
		phake('help markdown');
	}
	
	function html($file, $output)
	{
	    if(!trim($output)) {
	        $this->error('No output specified');
	    }
	    require_once dirname(__FILE__).'/markdown.inc.php';
		$my_html = Markdown(f($file)->contents());
		f($output)->setcontent($my_html);
	}
	
	function wc($file)
	{
	    require_once dirname(__FILE__).'/markdown.inc.php';
		
		$my_html = Markdown(f($file)->contents());
		
		// strip code (blockquote)
		$code = array();
		$human = array();
		
		$is_code = false;
		
		$x = explode(PHP_EOL, $my_html);
		foreach($x as $line) {
			if(trim($line)=='<blockquote>') {
				$is_code = true;
			} else 
			if(trim($line)=='</blockquote>') {
				$is_code = false;
			} else {
				if($is_code) {
					$code[] = $line;
				} else {
					$human[] = $line;
				}
			}
		}
		
		$code = implode($code);
		echo "\nCode words in .md: ".str_word_count($code);
		
		$human = implode($human);
		echo "\nHuman words in md: ".str_word_count($human);
		
		echo "\n % of article taken up by code: ".round((str_word_count($code)/str_word_count($human))*100, 2);
		
		
		$x = explode(PHP_EOL, $my_html);
		
		#$lines = file_get_contents($f);
		#$x = explode(PHP_EOL, $lines);
		
		echo PHP_EOL.'Tasks remaining: '.PHP_EOL;
		foreach($x as $line) {
			$line = strip_tags($line);
			#echo substr(trim($line), 0, 5);
			if(substr(trim($line), 0, 5)=='TODO:') {
				echo PHP_EOL."  ".$line;
			}
		}
		
		
		echo PHP_EOL;
		
		return;
	}
}

?>