<?php
/**
 * EpicDb_Form_Advertisement
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Form_Record_Description extends EpicDb_Form_Record
{
	protected $_record = null;

	/**
	 * getProfile - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function getRecord()
	{
		if($this->_record) return $this->_record;
		throw new Exception("Record for this entry is undefined.");
	}
	/**
	 * setProfile - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function setRecord($record)
	{
		if(!$record instanceOf EpicDb_Mongo_Record) {
			throw new Exception("This isn't an Record.");
		}
		$this->_record = $record;
		return $this;
	}
	/**
	 * init - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function init()
	{
		parent::init();
		$record = $this->getRecord();
		$this->addElement("markdown", "descriptionSource", array(
				'order' => 100,
				'required' => true,
				'class' => 'markDownEditor',
				'label' => 'Description',
				'description' => '',
				'cols' => 50,
				'rows' => 10,
			));
		$this->setDefaults($record->export());
		$this->setButtons(array("save" => "Save"));
	}
	
	public function process($data) {
		$record = $this->getRecord();
		if($this->isValid($data)) {
			$record->descriptionSource = $this->descriptionSource->getValue();
			$record->description = $this->descriptionSource->getRenderedValue();
			$record->save();
		}
		return true;
	}
	public function render()
	{
		$this->save->setAttrib('class','login epicdb-button ui-state-default ui-corner-all');
		$this->getDecorator('HtmlTag')->setOption('class','epicdb-form rounded')->setOption('id', 'ad-edit');
		return parent::render();
	}	
} // END class EpicDb_Form_Profile
