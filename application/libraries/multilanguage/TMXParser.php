<?php
/*
	private variable and functions are prefixed with underscore('_')
*/

require_once('TranslationContainer.php');

class TMXParser {
	private $_parser = null;
	private $_xmlFile = null;

	private $_currentTuid = null;
	private $_boolSeg = false;
	private $_currentLanguage = null;

	private $_currentPage = null;
	private $_boolPage = false;

	private $_currentData = '';
	private $_tc = null;

	private $_masterLanguage = 'fr';
	
	

	public function __construct($xmlFile='', $masterLanguage = '') {
		if($masterLanguage != '') {
			$this->_masterLanguage = $masterLanguage;
		}

		$this->_xmlFile = $xmlFile;
	}

	public function doParse() {

		$this->_tc = new TranslationContainer($this->_masterLanguage);

		$this->_parser = xml_parser_create();
		xml_set_element_handler($this->_parser, "_startElement", "_endElement");
		xml_set_object($this->_parser, $this);
		xml_parser_set_option($this->_parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($this->_parser, XML_OPTION_TARGET_ENCODING, 'utf-8');
		xml_set_character_data_handler($this->_parser, "_contentElement");

		if ($this->_xmlFile === null) {
			throw new Exception('Translation source xml is set. Use setXML() or constructor to set source xml.');
		}

		if (!is_readable($this->_xmlFile)) {
			throw new Exception('Translation source xml is not readable.');
		}

		if (!xml_parse($this->_parser, file_get_contents($this->_xmlFile))) {
			$ex = sprintf('XML error: %s at line %d',
					xml_error_string(xml_get_error_code($this->_parser)),
					xml_get_current_line_number($this->_parser));

			xml_parser_free($this->_parser);
			throw new Exception($ex);
		}

		return $this->_tc;
	}

	private function _startElement($parser, $name, $atrrs) {
		//echo '<br>name='.$name.'<br>';
		//print_r($atrrs);
		if ($this->_boolSeg != false) {
			//echo '<p>##############</p>';
			$this->_currentData .= "<".$name;
			foreach($atrrs as $key => $value) {
				$this->_currentData .= " $key=\"$value\"";
			}
			$this->_content .= ">";
		}
		else {
			switch (strtolower($name)) {
				case 'tu':
					if(isset($atrrs['tuid']) === true){
						$this->_currentTuid = $atrrs['tuid'];
					}
					break;
				case 'tuv':
					if(isset($atrrs['xml:lang']) === true){
						$this->_currentLanguage = $atrrs['xml:lang'];
					}
					break;
				case 'prop':
					if( isset($atrrs['type']) === true && $atrrs['type'] == 'page' ){
						$this->_boolPage = true;
						$this->_currentPage = '';
					}
					break;
				case 'seg':
					$this->_boolSeg = true;
					$this->_currentData = '';
					break;
			}
		}
	}

	private function _endElement($parser, $name)  {
		//echo '<br>endname='.$name.'<br>';
		if (($this->_boolSeg != false) and ($name !== 'seg')) {
			//echo '<p>##############</p>';
			$this->_currentData .= "</".$name.">";
		}
		else {
			switch (strtolower($name)) {
				case 'tu':
					$this->_currentTuid = null;
					break;
	
				case 'tuv':
					$this->_currentLanguage = null;
					break;
	
				case 'prop':
					if( $this->_boolPage ){
						$this->_boolPage = false;
						//$this->_currentPage = '';
					}
					break;
	
				case 'seg':
					$this->_boolSeg = false;
					if( ($this->_tc !== null) || !$this->_tc->hasId($this->_currentTuid) ) {
						//$this->_currentData = html_entity_decode($this->_currentData, ENT_QUOTES, 'utf-8');
						$this->_currentData = base64_decode($this->_currentData);

						$this->_tc->addWordTuid($this->_currentTuid, $this->_currentData, $this->_currentLanguage, $this->_currentPage);
						//echo ' <br> '.$this->_currentData;
					}
	
					break;
			}
		}
	}

	private function _contentElement($parser, $data) {
		//echo ' <br>content='.$this->_currentData;
		if($this->_boolSeg && $this->_currentTuid !== null && $this->_currentLanguage !== null ) {
			$this->_currentData .= $data;
			
		}

		if( $this->_boolPage ) {
			$this->_currentPage = $data;
		}
	}


	public function getXML() {
		return $this->_xmlFile;
	}

	public function setXML($xmlFile) {
		$this->_xmlFile = $xmlFile;
	}

	public function getMasterLanguage() {
		return $this->_xmlFile;
	}

	public function setMasterLanguage($lang) {
		$this->_masterLanguage = $lang;
	}
}


