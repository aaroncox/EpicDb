<?php
/**
 * undocumented class
 *
 * @package default
 * @author Aaron Cox
 **/
class EpicDb_Form_Record_Badge extends EpicDb_Form_Record
{
	public function init() {
		parent::init();
		$record = $this->getRecord();
		$this->addElement("text", "helper", array(
			"label" => "Helper",
			"required" => true,
			"value" => $record->helper,
		));
		$this->addElement("textarea", "options", array(
			"label" => "Options",
			"required" => true, 
			"value" => json_encode($record->getBadgeOptions()),
		));
	}
	public function save() {
		$record = $this->getRecord();
		$record->helper = $this->helper->getValue();
		$record->options->setFromArray(json_decode($this->options->getValue(), true));
		return parent::save();
	}
} // END class EpicDb_Form_Record_Badge extends EpicDb_Form_Record