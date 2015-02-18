<?php

require_once('TranslationFormat.php');

class TranslationContainer implements Iterator{
	
	private $_tns = array();
	private $_masterLanguage = 'fr';
	//private $_position = 0;

	public function __construct( $masterLanguage = '') {
		if($masterLanguage != '') {
			$this->_masterLanguage = $masterLanguage;
		}
		//$this->_position = 0;
	}

	/* Iterator interface's functions */
	public function current() {
		//return $this->_tns[$this->_position];
		return current($this->_tns);
	}

	public function key() {
		//return $this->_position;
		return key($this->_tns);
	}

	public function next() {
		//++$this->_position;
		next($this->_tns);
	}

	public function rewind() {
		//$this->_position = 0;
		reset($this->_tns);
	}

	public function valid() {
		//return isset($this->_tns[$this->_position]);
		return (current($this->_tns) !== FALSE);
	}
	/* End Iterator functions */


	public function hasId($tuid) {
		return array_key_exists($tuid, $this->_tns);
	}

	public function getById($tuid) {
		if( isset($this->_tns[$tuid]) ) {
			return $this->_tns[$tuid];
		}
		else {
			return null;
		}
	}

	/* $this is master TranslationContainer and arg $tc is slave TranslationContainer 
	* Can be used when new scaned translations will be merged with existing translations in TMX xml
	*/
	public function merge($tc) {
		foreach($tc->_tns as $key=>$tf) {
// 			echo '<br>key:'.$key.' hasid:';
// 			echo $this->hasId($key);
// 			echo '<pre>';
// 			print_r($tf);
// 			echo '</pre>';
			if( ! $this->hasId($key) ) {
				$this->addWord($key, $tf->getPage());
			}
		}
	}

	/* $this is master TranslationContainer and arg $tc is slave TranslationContainer 
	* Can be used when new submitted translations ($tc) will be merged with existing translations ($this) in TMX xml
	*/
	public function modifyTranslations($tc) {
		foreach($tc->_tns as $key=>$new_tf) {
			if( $this->hasId($key) ) {
				$tf = $this->getById($key);
				$new_tf->setPage($tf->getPage());
				$this->_tns[$key] = $new_tf;
			}
		}
	}

	/* when first time someone generating xml */
	public function addWord($tuid, $page, $masterLanguage = '') {
		if($masterLanguage == '') {
			$masterLanguage = $this->_masterLanguage;
		}

		if( isset($this->_tns[$tuid]) !== true ) {
			$tf = new TranslationFormat();
			$tf->set( array('id'=>$tuid, 'page'=>$page, 'data' => array($masterLanguage=>$tuid)) );
			
			$this->_tns[$tuid] = $tf;
	
			return true;
		}
		else {
			return false;
		}
	}

	public function addWordTuid($tuid, $word, $language, $page, $masterLanguage = '') {
		if($masterLanguage == '') {
			$masterLanguage = $this->_masterLanguage;
		}

		/* id not exists */
		if( isset($this->_tns[$tuid]) !== true ) {
			$tf = new TranslationFormat();
			$tf->set( array('id'=>$tuid, 'page'=>$page, 'data' => array($language=>$word)) );
			
			$this->_tns[$tuid] = $tf;

			return true;
		}

		/* id exists language not exists */
		else if( !in_array($language, $this->_tns[$tuid]->getLanguages()) ) {
			$this->_tns[$tuid]->addData(array($language=>$word));
			$this->_tns[$tuid]->addLanguage($language);
			//echo '<br> 2nd:';
			//print_r($this->_tns[$tuid]);

			return true;
		}
		/* id exists language exists */
// 		else {
// 			
// 		}
	}

	public function removeWord($tuid) {
		unset($this->_tns[$tuid]);
	}

	public function update() {
		
	}
}
