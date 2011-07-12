<?php

class EpicDb_Form_MetaKeys extends MW_Form {
	protected $_isNew = false;
	protected $_metaKey = null;
	/**
	 * getPost - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function getMetaKey()
	{
		return $this->_metaKey;
	}
	/**
	 * setPost($post) - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function setMetaKey($metaKey)
	{
		$this->_metaKey = $metaKey;
		$this->_isNew = $metaKey->isNewDocument();
		return $this;
	}
	
	public function init()
	{
		parent::init();
		$metaKey = $this->getMetaKey();
		$this->addElement("text", "name", array(
			'placeholder' => 'Name',
			'label' => 'Name',
			'description' => 'The attribute name that this meta key applies to.',
		));
		$this->addElement("text", "title", array(
			'placeholder' => 'Title',
			'label' => 'Title',
			'description' => 'The plain text name for this attribute.',
		));
		$this->addElement("text", "order", array(
			'label' => 'Order # for Element',
		));
		$this->addElement("multiselect", "recordType", array(
			'label' => 'Record Types', 
			'description' => 'The record types that use this meta key.',
			'multiOptions' => EpicDb_Mongo::db('record')->getTypesArray(),
		));
		$this->addElement("text", "formElementType", array(
			'label' => 'Form Element Type',
		));
		$this->addElement("text", "formElementOptions", array(
			'label' => 'Options for Element (in JSON)',
		));
		$this->addElement("text", "helper", array(
			'placeholder' => 'Helpers',
			'label' => 'Helpers',
			'description' => 'A list of all the View Helpers / Methods to call on this value before rendering.',
		));
		$this->addElement("text", "requirements", array(
			'placeholder' => 'Requirements',
			'label' => 'Requirements',
			'description' => 'The Shanty Requirements, Filters or Validators applied to this information. ie. "Filter:Int", "Document:EpicDb_Record_Item"',
		));
		$this->addElement("checkbox", "onTooltip", array(
			'label' => 'Show on Tooltip?',
			'description' => 'Check the box if you want this stat to appear in tooltips',
		));
		
		if($this->_isNew) {
			
		} else {
			if($metaKey->requirements) {
				$requirements = implode($metaKey->requirements, ",");
			} else {
				$requirements = '';
			}
			$this->setDefaults(array(
				'name' => $metaKey->name,
				'title' => $metaKey->title,
				'order' => $metaKey->order?:100,
				'helper' => $metaKey->helper,
				'recordType' => $metaKey->recordType,
				'formElementType' => $metaKey->formElement->type,
				'formElementOptions' => json_encode($metaKey->formElement->options),
				'requirements' => $requirements,
				'onTooltip' => $metaKey->onTooltip,
			));
		}
		
		$this->setButtons(array("save" => "Save"));
		
	}
	
	public function process($data) {
		if($this->isValid($data)) {
			$metaKey = $this->getMetaKey();
			// var_dump($this->name->getValue()); exit;
			$metaKey->name = $this->name->getValue();
			$metaKey->title = $this->title->getValue();
			$metaKey->order = $this->order->getValue();
			$metaKey->helper = $this->helper->getValue();
			$metaKey->onTooltip = (bool) $this->onTooltip->getValue();
			if($this->requirements->getValue() == "") {
				$requirements = array();
			} else {
				$requirements = explode(",", str_replace(" ", "", $this->requirements->getValue()));				
			}
			// var_dump($this->requirements->getValue(), $requirements); exit;
			$metaKey->recordType = $this->recordType->getValue();
			$metaKey->formElement->type = $this->formElementType->getValue();
			$metaKey->formElement->options = json_decode($this->formElementOptions->getValue(), true);
			$metaKey->requirements = $requirements;
			$metaKey->save();
			return true;
		}
		return false;
	}
	
	public function render()
	{
		foreach($this->getElements() as $element) {
			$element->setAttrib('class', 'ui-state-default');
		}
		$this->save->setAttrib('class','login r2-button ui-state-default ui-corner-all');
		$this->getDecorator('HtmlTag')->setOption('class','r2-form transparent-bg rounded')->setOption('id', 'ad-edit');
		return parent::render();
	}	
	
}
