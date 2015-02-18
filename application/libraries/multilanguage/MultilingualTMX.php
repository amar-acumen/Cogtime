<?php
/*
	private variable and functions are prefixed with underscore('_')
*/

require_once('TMXParser.php');
require_once('TMXWriter.php');
require_once('TranslationContainer.php');

class MultilingualTMX {

	private $_xmlFile = null;

	private $_extentions = array('php');
	private $_directory = '';
	private $_masterLanguage = 'en';
	private $_functionName = "t";
	private $_pluralFunctionName = 'tp';
	private $_boolStripFilename = true;
	private $_stripUpto = 'application';
	
// 	private $_languages = array('fr', 'en', 'it');

	//private $_tempArr = array();
	private $_tc = null;

	public function __construct($xmlFile,$masterLanguage = '') {
		$this->_xmlFile = $xmlFile;
		if($masterLanguage != '') {
			$this->setMasterLanguage($masterLanguage);
		}
		$this->_tc = new TranslationContainer($this->_masterLanguage);
		
		
	}


	/* for backend */
	public function getPages() {
		if(is_readable($this->_xmlFile)) {
			$pages = array();

			$parser = new TMXParser($this->_xmlFile, $this->_masterLanguage);
			$tc = $parser->doParse();

			foreach($tc as $tf) {
				$pages[] = $tf->getPage();
			}
			unset($parser);
			unset($tc);
			unset($tf);
			
			$pages = array_unique($pages);
			sort($pages);
			return $pages;
		}
		else {
			return array();
		}
	}

	public function getTranslationsByPage($page) {
		$translations = array();
#echo $this->_masterLanguage; 
		$parser = new TMXParser($this->_xmlFile, $this->_masterLanguage);
		$tc = $parser->doParse();

// 		echo '<pre>';
// 		print_r($tc);
// 		echo '</pre>';

		foreach($tc as $tf) {
			$thisPage = $tf->getPage();
			if($thisPage == $page) {
				$data = $tf->getData();
				$id = $tf->getId();
				//print_r($data);
				$translations[$id] = $data;
			}
		}
		unset($parser);
		unset($tc);
		unset($tf);
		return $translations;
	}


	/* Update TMX xml, add the values in the parameters.
	*  @$strings = array('hello', 'bonjour') @languages = array('en', 'fr') 
	*/
// 	public function setTranslationArray($tuid, array $strings, array $languages) {
// 		$arg_tc = new TranslationContainer($this->_masterLanguage);
// 
// 		$count = count($strings);
// 		for($i=0; $i<$count; $i++) {
// 			$page = '';
// 			$language = $languages[$i];
// 			$word = $strings[$i];
// 			$arg_tc->addWordTuid($tuid, $word, $language, $page);
// 		}
// 
// 		if(is_readable($this->_xmlFile)) {
// 			$parser = new TMXParser($this->_xmlFile, $this->_masterLanguage);
// 			$tc_existing = $parser->doParse();
// 	
// 			$tc_existing->modifyTranslations($arg_tc);
// 	
// 	
// 			$tw = new TMXWriter($this->_xmlFile);
// 			$tw->createTranslations($tc_existing);
// 		}
// 		else {
// 			$tw = new TMXWriter($this->_xmlFile);
// 			$tw->createTranslations($arg_tc);
// 		}
// 	}


	/* Update TMX xml, add the values in the parameters.
	*  @$data = array('en'=>'hello', 'fr'=>'bonjour') 
	*/
// 	public function setTranslationData($tuid, array $data) {
// 		$arg_tc = new TranslationContainer($this->_masterLanguage);
// 
// 		$count = count($strings);
// 		foreach($data as $lang=>$word) {
// 			$page = '';
// 			$arg_tc->addWordTuid($tuid, $word, $lang, $page);
// 		}
// 
// 		if(is_readable($this->_xmlFile)) {
// 			$parser = new TMXParser($this->_xmlFile, $this->_masterLanguage);
// 			$tc_existing = $parser->doParse();
// 	
// 			$tc_existing->modifyTranslations($arg_tc);
// 	
// 	
// 			$tw = new TMXWriter($this->_xmlFile);
// 			$tw->createTranslations($tc_existing);
// 		}
// 		else {
// 			$tw = new TMXWriter($this->_xmlFile);
// 			$tw->createTranslations($arg_tc);
// 		}
// 	}


	/* Update TMX xml, add the values in the parameters.
	*  @$arg_tc = TranslationContainer object 
	*/
	public function setTranslationTC($arg_tc) {

		if(is_readable($this->_xmlFile)) {
			$parser = new TMXParser($this->_xmlFile, $this->_masterLanguage);
			$tc_existing = $parser->doParse();
	
			$tc_existing->modifyTranslations($arg_tc);
	
	
			$tw = new TMXWriter($this->_xmlFile);
			$tw->createTranslations($tc_existing);
// 			echo '<pre>';
// 			print_r($tc_existing);
// 			echo '</pre>';

		}
		else {
			$tw = new TMXWriter($this->_xmlFile);
			$tw->createTranslations($arg_tc);
		}


	}

	/* when first time someone generating TMX XML */
	public function createXML() {
		$tw = new TMXWriter($this->_xmlFile);
		$tc = $this->_scan($this->_directory);

		$tw->createTranslations($tc);
	}

	/* scan and generate xml and merge with current translations */
	public function mergeWithXML() {

		$tc_scanned = $this->_scan($this->_directory);

		if(is_readable($this->_xmlFile)) {
			$parser = new TMXParser($this->_xmlFile, $this->_masterLanguage);
			$tc_existing = $parser->doParse();
	
			$tc_existing->merge($tc_scanned);
	
	
			$tw = new TMXWriter($this->_xmlFile);
			$tw->createTranslations($tc_existing);
		}
		else {
			$tw = new TMXWriter($this->_xmlFile);
			$tw->createTranslations($tc_scanned);
		}

// 		echo '<pre>';
// 		print_r($tc_existing);
// 		echo '</pre>';
	}

	/* takes direcory name, returns TranslationContainer Object */
	private function _scan($dir='') {
		if($dir=='') {
			$dir = $this->_directory;
		}

		$dirList = new DirectoryIterator($dir);
		$arr = array();
		$tempArr = array();
		foreach ($dirList as $item) {
			if($item->isFile()) {
				
				if( in_array($this->getExt($item), $this->_extentions) ) {
					$arr = $this->_scanThisFile($dir.'/'.$item);

					if($this->_boolStripFilename) {
						$filename = preg_replace("/(.*?)({$this->_stripUpto})(.*?)/", '$3', $dir.'/'.$item);
					}
					else {
						$filename = $dir.'/'.$item;
					}

					foreach( $arr as $value ) {
						//$value = htmlentities($value, ENT_QUOTES, 'utf-8');
						$this->_tc->addWord($value, $filename );
					}
					unset($arr);
				}
			}
			if($item->isDir()) {
				if(!$item->isDot()) {
					$this->_scan($dir.'/'.$item, $this->_extentions);
				}
			}
		}
		return $this->_tc;
	}

	public function getExt($filename) {
		$matches = array();
		preg_match('/(^.*)\.([^\.]*)$/', $filename, $matches);
		$ext = "";
		if(count($matches)>0) {
			return $matches[2];
		}
		else {
			return '';
		}
	}

	private function _scanThisFile($file) {
		$str = file_get_contents($file);
		/* to catch pattern like func1('hello') or func1("bonjoir") */
		$pattern = "/[^a-zA-Z0-9]+{$this->_functionName}[\s]*\([\s]*(['\"]{1})(.*?)(\\1)[\s]*\)/";
		
		$matches = array();
		$return_arr = array();

		preg_match_all($pattern, $str, $matches);
		if( isset($matches[2]) === true ) {
			$return_arr = $matches[2];
		}

		/* to catch pattern like func2('%s car', '%s cars') */
		$pattern_plural = "/[^a-zA-Z0-9]+{$this->_pluralFunctionName}[\s]*\([\s]*(['\"]{1})(.*?)(\\1)[\s]*,[\s]*(['\"]{1})(.*?)(\\4)[\s]*,/";

		$matches_plural = array();

		preg_match_all($pattern_plural, $str, $matches_plural);
		if( isset($matches_plural[2]) === true ) {
			$return_arr = array_merge($return_arr, $matches_plural[2]);
		}

		if( isset($matches_plural[5]) === true ) {
			$return_arr = array_merge($return_arr, $matches_plural[5]);
		}

		return $return_arr;
	}

	public function setExtension($ext) {
		$this->_extentions = $ext;
	}

	public function setDirectory($dir) {
		$this->_directory = $dir;
	}

	public function setFunctionName($str) {
		$this->_functionName = $str;
	}

	public function setPluralFunctionName($str) {
		$this->_pluralFunctionName = $str;
	}

	public function setStripUpto($str) {
		$this->_stripUpto = $str;
	}

	public function setMasterLanguage($language) {
		$this->_masterLanguage = $language;
		
	}

// 	public function setLanguages(array $languages) {
// 		$this->_languages = $languages;
// 	}
}


