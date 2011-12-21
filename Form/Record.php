<?php

class EpicDb_Form_Record extends EpicDb_Form {
	public $description = "Empty Description";
	public $title = "Empty Title";
	private $_record = null;
	private $_metaElements = array();
	
	public function getRecord() {
		if($this->_record) return $this->_record;
		throw new Exception("Record has not been set on this form.");
	}
	
	public function setRecord($record) {
		if(!$record instanceOf EpicDb_Mongo_Record) throw new Exception("Record passed is not instance of a record.");
		return $this->_record = $record;
	}
		
	public function init() {
		$record = $this->getRecord();
		parent::init();
		$this->addElement('text', 'name', array(
			'label' => 'Name'
		));
		$this->addElement('markdown', 'descriptionSource', array(
			'filter' => array('StringTrim'),
			'label' => 'Description',
			'order' => 900,
		));

		if($record->_tempPlacement) {
			$this->addElement('text', 'position', array(
				'label' => 'Tree Position',
				'value' => $record->_tempPlacement,
			));			
		}

		$defaults = array(
			'name' => $record->name,
			'descriptionSource' => $record->descriptionSource,
		);


		$this->_metaElements = $metaElements = EpicDb_Mongo::db('metaKeys')->getFormElementsArray($record->_type);
		foreach($metaElements as $key => $element) {
			if($this->$key) continue;
			$this->addElement($element['type'], $key, $element['options']);
			$defaults[$key] = $record->attribs->$key;
		}
		$this->setDefaults($defaults);
		$this->setButtons(array('save' => 'Save'));
	}

	public function process($data) {
		if($this->isValid($data)) {			
			return $this->save($data);
		}
		return false;
	}

	public function save() {
		$record = $this->getRecord();
		$record->newRevision();
		
		// Used in the data entry from text thign
		if($this->position) {
			$record->_tempPlacement = (float) $this->position->getValue();			
		}
		$record->name = $this->name->getValue();
		$record->descriptionSource = $this->descriptionSource->getValue();
		$record->description = $this->descriptionSource->getRenderedValue();
		
		foreach($this->_metaElements as $key => $element) {
			$element = $this->$key;
			$value = $element->getValue();
			if($value) {
				if($key == 'damage') {
					// var_dump($element, $value); exit;
				}
				$record->attribs->$key = $value;
			} else {
				unset($record->attribs->$key);
			}
		}
		if(!$record->_created) $record->_created = time();
		// var_dump($record->export(), $record->revisions->export()); exit;
		$record->save();
	}
}
	