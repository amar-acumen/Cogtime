<?php
/*
	private variable and functions are prefixed with underscore('_')
*/

class TMXWriter {
	
	private $_xmlWriter = null;
	
	public function __construct($xmlFile='') {
		$this->_xmlFile = $xmlFile;
		$this->_xmlWriter = new XMLWriter();
		
	}

	private function _initialize() {
	   #echo $this->_xmlFile;
		$this->_xmlWriter->openURI($this->_xmlFile);
		$this->_xmlWriter->startDocument('1.0');
		$this->_xmlWriter->setIndent(4);
		
		$this->_xmlWriter->startElement('tmx');
		$this->_xmlWriter->writeAttribute('version', '1.4');
	}

	private function _writeHeader() {
		$this->_xmlWriter->startElement("header");
		$this->_xmlWriter->writeAttribute('creationtool', 'TMXWriter');
		$this->_xmlWriter->writeAttribute('creationtoolversion', '0.1');
		$this->_xmlWriter->writeAttribute('datatype', 'PlainText');
		$this->_xmlWriter->writeAttribute('segtype', 'sentence');
		$this->_xmlWriter->writeAttribute('adminlang', 'en-us');
		$this->_xmlWriter->writeAttribute('srclang', 'EN');
		$this->_xmlWriter->writeAttribute('o-tmf', 'ABCTransMem');
		$this->_xmlWriter->text('');
		$this->_xmlWriter->endElement();
	}

	private function _startBody() {
		$this->_xmlWriter->startElement("body");
	}

	private function _endBody() {
		$this->_xmlWriter->endElement();
	}

	private function _complete() {
		$this->_xmlWriter->endElement();
	}

	private function _startTu($tuId, $page) {
		//$tuId = htmlentities($tuId, ENT_QUOTES, 'utf-8');

		$this->_xmlWriter->startElement("tu");
		$this->_xmlWriter->writeAttribute('tuid', $tuId);
		$this->_xmlWriter->writeAttribute('datatype', 'plaintext');

		$this->_xmlWriter->startElement("prop");
		$this->_xmlWriter->writeAttribute('type', 'page');
		$this->_xmlWriter->text($page);
		$this->_xmlWriter->endElement();
	}

	private function _endTu() {
		$this->_xmlWriter->endElement();
	}

	private function _addTuv($language, $word) {
		//$word = htmlentities($word, ENT_QUOTES, 'utf-8');
		$word = base64_encode($word);

		$this->_xmlWriter->startElement("tuv");
		$this->_xmlWriter->writeAttribute('xml:lang', $language);
		$this->_xmlWriter->writeElement('seg', $word);
		$this->_xmlWriter->endElement();
	}


	/* takes TranslationContainer Object */
	public function createTranslations($tc) {
// 		echo '<pre>';
// 		print_r($tc);
// 		echo '</pre>';

		if( count($tc) ) {
			$this->_initialize();
			$this->_writeHeader();
			$this->_startBody();

			foreach($tc as $obj) {
				$this->_startTu($obj->getId(), $obj->getPage());
				if( is_array($obj->getData()) && count($obj->getData()) ) {
					foreach($obj->getData() as $key=>$data) {
						$this->_addTuv($key, $data);
					}
				}
				
				$this->_endTu();
			}

			$this->_endBody();
			$this->_complete();
		}
	}

	/* 
	argument is array( [0]=> array('id'=>'hello', 'page'=>'/views/abc.php', 'data'=> array('en'=>'hello', 'fr'=>'hullo', 'it'=>'ciao')) )
	*/

// 	public function createTranslationsArray(array $tns) {
// 		if( is_array($tns) && count($tns) ) {
// 			$this->_initialize();
// 			$this->_writeHeader();
// 			$this->_startBody();
// 
// 			foreach($tns as $tn) {
// 				$this->_startTu($tn['id'], $tn['page']);
// 				if( is_array($tn['data']) && count($tn['data']) ) {
// 					foreach($tn['data'] as $key=>$data) {
// 						$this->_addTuv($key, $data);
// 					}
// 				}
// 				
// 				$this->_endTu();
// 			}
// 
// 			$this->_endBody();
// 			$this->_complete();
// 		}
// 	}


	public function test1() {
		$this->_initialize();
		$this->_writeHeader();
		$this->_startBody();
		
		$this->_startTu('suman', '/views/abc.php');
		$this->_addTuv('en', 'suman');
		$this->_endTu();
		
		$this->_endBody();
		$this->_complete();
	}


	public function test() {
		$this->_xmlWriter->openURI($this->_xmlFile);
		$this->_xmlWriter->startDocument('1.0');
		$this->_xmlWriter->setIndent(4);
		
		$this->_xmlWriter->startElement('tmx');
		$this->_xmlWriter->writeAttribute('version', '1.4');
		
			$this->_xmlWriter->startElement("header");
			$this->_xmlWriter->writeAttribute('creationtool', 'TMXWriter');
			$this->_xmlWriter->writeAttribute('creationtoolversion', '0.1');
			$this->_xmlWriter->writeAttribute('datatype', 'PlainText');
			$this->_xmlWriter->writeAttribute('segtype', 'sentence');
			$this->_xmlWriter->writeAttribute('adminlang', 'en-us');
			$this->_xmlWriter->writeAttribute('srclang', 'EN');
			$this->_xmlWriter->writeAttribute('o-tmf', 'ABCTransMem');
			$this->_xmlWriter->text('');
			$this->_xmlWriter->endElement();
			
			$this->_xmlWriter->startElement("body");
				$this->_xmlWriter->startElement("tu");
				$this->_xmlWriter->writeAttribute('tuid', 'hello');
				$this->_xmlWriter->writeAttribute('datatype', 'plaintext');
					$this->_xmlWriter->startElement("tuv");
					$this->_xmlWriter->writeAttribute('xml:lang', 'en');
						$this->_xmlWriter->writeElement('seg', 'hello');
					$this->_xmlWriter->endElement();
					
					$this->_xmlWriter->startElement("tuv");
					$this->_xmlWriter->writeAttribute('xml:lang', 'it');
						$this->_xmlWriter->writeElement('seg', 'ciao');
					$this->_xmlWriter->endElement();
					
				$this->_xmlWriter->endElement();
			$this->_xmlWriter->endElement();

		$this->_xmlWriter->endElement();
	}
}


