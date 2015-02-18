<?php

class TranslationFormat {
	private $_id;
	private $_page;

	/* languages for one tuid */
	private $_languages = array();
	private $_data = array();

	public function __construct() {
		
	}

	public function set(array $arr) {
		if( isset($arr['id']) ) {
			$this->_id = $arr['id'];
		}
		if( isset($arr['page']) ) {
			$this->_page = $arr['page'];
		}
		if( isset($arr['data']) ) {
			$this->_data = $arr['data'];
		}

		$this->_languages = array_keys($arr['data']);
	}


	public function addData($data) {
		$this->_data = array_merge($this->_data, $data);
	}

	public function addLanguage($language) {
		$this->_languages = array_merge($this->_languages, array($language));
	}

	public function getId() {
		return $this->_id;
	}

	public function setId($id) {
		$this->_id = $id;
	}

	public function getPage() {
		return $this->_page;
	}

	public function setPage($page) {
		$this->_page = $page;
	}

	public function getLanguages() {
		return $this->_languages;
	}

	public function getData() {
		return $this->_data;
	}

	public function setData($data) {
		$this->_data = $data;
		$this->_languages = array_keys($data);
	}
}
