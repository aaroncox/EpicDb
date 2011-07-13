<?php
/**
 * R2Db_Form_Post_Comment
 *
 * undocumented class
 * 
 * @author Aaron Cox <aaronc@fmanet.org>
 * @param undocumented class
 * @package undocumented class
 **/
class EpicDb_Form_Seed extends EpicDb_Form
{
	protected $_isNew = false;
	protected $_recordType = 'seed';
	protected $_seed = null;
	
	/**
	 * getSeed - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function getSeed()
	{
		return $this->_seed;
	}
	/**
	 * setPost($post) - undocumented function
	 *
	 * @return void
	 * @author Aaron Cox <aaronc@fmanet.org>
	 **/
	public function setSeed($seed)
	{
		$this->_seed = $seed;
		$this->_isNew = $seed->isNewDocument();
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
		$seed = $this->getSeed();
		$this->addElement("text", "title", array(
				'required' => true,
				'validators' => array(
					array('StringLength',150,10),
				),
				'label' => 'Title of Seed Post',
				'size' => 80,
				'description' => '150 characters or less.',
			));		
		$this->addElement("text", "tag", array(
			'required' => true, 
			'filters' => array(
				'StringTrim',
			),
			'label' => 'Record is tagged with & named as...',
		));
		$this->addElement("text", "tagDb", array(
			'filters' => array(
				'StringTrim',
			),
			'label' => 'Database to get Tags from...',
			'description' => 'Leave empty if tags exist on this record type',
		));
		$this->addElement("text", "tagType", array(
			'filters' => array(
				'StringTrim',
			),
			'label' => 'Record Type of Answers',
		));
		$this->addElement("multiselect", "types", array(
			'label' => 'Affected Record Types',
			'multiOptions' => EpicDb_Mongo::db('record')->getTypesArray(),
		));
		if(!$this->_isNew) {
			$this->setDefaults(array(
				'title' => $seed->title,
				'tag' => $seed->tag,
				'tagDb' => $seed->tagDb,
				'tagType' => $seed->tagType,
			));			
			if($seed->types) {
				$this->setDefaults(array(
					'types' => $seed->types,
				));
			}
		}
		$this->setButtons(array("save" => "Save"));
	}
	public function process($data) {
		if($this->isValid($data)) {
			$seed = $this->getSeed();
			$seed->title = $this->title->getValue();
			$seed->types = $this->types->getValue();
			$seed->tag = $this->tag->getValue();
			$seed->tagDb = $this->tagDb->getValue();
			$seed->tagType = $this->tagType->getValue();
			return $seed->save();
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
} // END class R2Db_Form_Post_Comment
